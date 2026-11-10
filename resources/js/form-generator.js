function fgPhpString(value) {
    if (value === null || value === undefined) {
        return "''";
    }

    return "'" + String(value).replace(/\\/g, '\\\\').replace(/'/g, "\\'") + "'";
}

function fgCollectTypes(nodes, bucket) {
    for (const node of nodes) {
        bucket.add(node.type);
        if (node.children?.length) {
            fgCollectTypes(node.children, bucket);
        }
    }
}

function fgRenderModifiers(node, level, indentUnit) {
    const mIndent = indentUnit.repeat(level + 1);
    const lines = [];

    if (node.label) lines.push(`${mIndent}->label(${fgPhpString(node.label)})`);
    if (node.placeholder) lines.push(`${mIndent}->placeholder(${fgPhpString(node.placeholder)})`);
    if (node.helperText) lines.push(`${mIndent}->helperText(${fgPhpString(node.helperText)})`);
    if (node.required) lines.push(`${mIndent}->required()`);
    if (node.disabled) lines.push(`${mIndent}->disabled()`);
    if (node.readOnly) lines.push(`${mIndent}->readOnly()`);
    if (node.default !== undefined && node.default !== '') {
        lines.push(`${mIndent}->default(${fgPhpString(node.default)})`);
    }
    if (node.columnSpan === 'full') {
        lines.push(`${mIndent}->columnSpanFull()`);
    } else if (node.columnSpan && parseInt(node.columnSpan, 10) > 1) {
        lines.push(`${mIndent}->columnSpan(${parseInt(node.columnSpan, 10)})`);
    }

    return lines;
}

function fgRenderNode(node, level, indentUnit, useFqcn) {
    const indent = indentUnit.repeat(level);
    const mIndent = indentUnit.repeat(level + 1);
    const cls = useFqcn ? `Forms\\Components\\${node.type}` : node.type;

    if (node.type === 'Section') {
        const lines = [`${indent}${cls}::make(${fgPhpString(node.title || 'Section')})`];
        if (node.description) lines.push(`${mIndent}->description(${fgPhpString(node.description)})`);
        if (node.collapsible) {
            lines.push(`${mIndent}->collapsible()`);
            if (node.collapsed) lines.push(`${mIndent}->collapsed()`);
        }
        if (node.columns && parseInt(node.columns, 10) > 1) {
            lines.push(`${mIndent}->columns(${parseInt(node.columns, 10)})`);
        }

        const children = node.children?.length
            ? fgRenderNodeList(node.children, level + 2, indentUnit, useFqcn)
            : '';
        lines.push(children ? `${mIndent}->schema([\n${children}\n${mIndent}])` : `${mIndent}->schema([])`);
        return lines.join('\n');
    }

    if (node.type === 'Grid') {
        const lines = [`${indent}${cls}::make(${parseInt(node.columns, 10) || 2})`];
        const children = node.children?.length
            ? fgRenderNodeList(node.children, level + 2, indentUnit, useFqcn)
            : '';
        lines.push(children ? `${mIndent}->schema([\n${children}\n${mIndent}])` : `${mIndent}->schema([])`);
        return lines.join('\n');
    }

    const lines = [`${indent}${cls}::make(${fgPhpString(node.name || 'field')})`];
    lines.push(...fgRenderModifiers(node, level, indentUnit));

    if (node.type === 'TextInput') {
        const subType = node.subType || 'text';
        if (subType === 'email') lines.push(`${mIndent}->email()`);
        if (subType === 'numeric') lines.push(`${mIndent}->numeric()`);
        if (subType === 'password') {
            lines.push(`${mIndent}->password()`);
            lines.push(`${mIndent}->revealable()`);
        }
        if (subType === 'tel') lines.push(`${mIndent}->tel()`);
        if (subType === 'url') lines.push(`${mIndent}->url()`);
    }

    if (node.type === 'Textarea' && node.rows) {
        lines.push(`${mIndent}->rows(${parseInt(node.rows, 10)})`);
    }

    if ((node.type === 'Select' || node.type === 'Radio') && node.options?.length) {
        const optIndent = indentUnit.repeat(level + 2);
        const optLines = node.options.map(
            (opt) => `${optIndent}${fgPhpString(opt.value)} => ${fgPhpString(opt.label)}`,
        );
        lines.push(`${mIndent}->options([\n${optLines.join(',\n')},\n${mIndent}])`);
    }

    if (node.type === 'Select' && node.searchable) lines.push(`${mIndent}->searchable()`);
    if (node.type === 'Radio' && node.inline) lines.push(`${mIndent}->inline()`);

    return lines.join('\n');
}

function fgRenderNodeList(nodes, level, indentUnit, useFqcn) {
    return nodes.map((node) => fgRenderNode(node, level, indentUnit, useFqcn)).join(',\n\n');
}

function generateCodeLocal(nodes, wrapInMethod = false, useFqcn = false) {
    if (!nodes?.length) {
        return "// No components added yet.\n// Drag and drop components from the palette into the canvas.";
    }

    const indentUnit = '    ';
    const baseLevel = wrapInMethod ? 2 : 1;
    const schemaCode = fgRenderNodeList(nodes, baseLevel, indentUnit, useFqcn);

    if (!wrapInMethod) {
        let header = '';
        if (!useFqcn) {
            const types = new Set();
            fgCollectTypes(nodes, types);
            header = [...types].sort().map((type) => `use Filament\\Forms\\Components\\${type};`).join('\n') + '\n\n';
        }
        return `${header}[\n${schemaCode}\n]`;
    }

    let header = "use Filament\\Forms;\nuse Filament\\Forms\\Form;\n";
    if (!useFqcn) {
        const types = new Set();
        fgCollectTypes(nodes, types);
        header += [...types].sort().map((type) => `use Filament\\Forms\\Components\\${type};`).join('\n') + '\n';
    }

    return `${header}
public static function form(Form $form): Form
{
    return $form
        ->schema([
${schemaCode}
        ]);
}
`;
}

export default function formGenerator() {
    return {
        activeTab: 'canvas',
        selectedNodeId: null,
        copied: false,
        dragOverCanvas: false,
        draggingItem: null,
        reorderDragId: null,
        generatedCode: '',
        jsonDraft: '',
        codeToken: 0,
        codeLoading: false,
        isFullscreen: false,
        immersive: false,

        codeSettings: {
            wrapInMethod: false,
            useFqcn: false,
        },

        layoutPalette: [
            {
                type: 'Section',
                label: 'Section',
                description: 'Group fields under a heading',
                iconSvg: '<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>'
            },
            {
                type: 'Grid',
                label: 'Columns',
                description: 'Multi-column layout',
                iconSvg: '<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>'
            }
        ],

        fieldPalette: [
            {
                type: 'TextInput',
                subType: 'text',
                label: 'Text Input',
                iconSvg: '<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16m-7 6h7" /></svg>'
            },
            {
                type: 'TextInput',
                subType: 'email',
                label: 'Email',
                iconSvg: '<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" /></svg>'
            },
            {
                type: 'TextInput',
                subType: 'numeric',
                label: 'Number',
                iconSvg: '<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" /></svg>'
            },
            {
                type: 'TextInput',
                subType: 'password',
                label: 'Password',
                iconSvg: '<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>'
            },
            {
                type: 'Textarea',
                label: 'Multi-line',
                iconSvg: '<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 10h16M4 14h16M4 18h16" /></svg>'
            },
            {
                type: 'Select',
                label: 'Dropdown',
                iconSvg: '<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 9l4-4 4 4m0 6l-4 4-4-4" /></svg>'
            },
            {
                type: 'Toggle',
                label: 'Toggle',
                iconSvg: '<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h8a5 5 0 015 5 5 5 0 01-5 5H8a5 5 0 01-5-5 5 5 0 015-5z" /><circle cx="15" cy="12" r="3" fill="currentColor"/></svg>'
            },
            {
                type: 'Checkbox',
                label: 'Checkbox',
                iconSvg: '<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'
            },
            {
                type: 'Radio',
                label: 'Radio',
                iconSvg: '<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="12" cy="12" r="9" stroke-width="1.5"/><circle cx="12" cy="12" r="4" fill="currentColor"/></svg>'
            },
            {
                type: 'DatePicker',
                label: 'Date',
                iconSvg: '<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>'
            }
        ],

        nodes: [],

        init() {
            this.nodes = [];

            this._syncFullscreen = () => this.syncFullscreenState();
            document.addEventListener('fullscreenchange', this._syncFullscreen);

            this.$watch('nodes', () => this.queueCodeRefresh(), { deep: true });
            this.$watch('codeSettings', () => this.queueCodeRefresh(), { deep: true });
            this.queueCodeRefresh();
        },

        resolveLivewire() {
            if (this.$wire?.generateCode) {
                return this.$wire;
            }

            const host = this.$el?.closest('[wire\\:id]');
            const id = host?.getAttribute('wire:id');
            if (id && window.Livewire?.find) {
                const component = window.Livewire.find(id);
                if (component?.generateCode) {
                    return component;
                }
            }

            return null;
        },

        syncFullscreenState() {
            const el = this.$refs.workspace;
            this.isFullscreen = !!el && (document.fullscreenElement === el || this.immersive);
        },

        async toggleFullscreen() {
            const el = this.$refs.workspace;
            if (!el) {
                return;
            }

            if (document.fullscreenElement === el) {
                await document.exitFullscreen();
                this.syncFullscreenState();
                return;
            }

            if (this.immersive) {
                this.immersive = false;
                this.syncFullscreenState();
                return;
            }

            if (typeof el.requestFullscreen === 'function') {
                try {
                    await el.requestFullscreen();
                    this.syncFullscreenState();
                    return;
                } catch (error) {
                    // Fall back to in-page immersive layout when the browser blocks native fullscreen.
                }
            }

            this.immersive = true;
            this.syncFullscreenState();
        },

        get selectedNode() {
            if (!this.selectedNodeId) {
                return null;
            }

            return this.findNodeRecursive(this.nodes, this.selectedNodeId);
        },

        queueCodeRefresh() {
            clearTimeout(this._codeTimer);
            this._codeTimer = setTimeout(() => this.refreshCode(), 120);
        },

        async refreshCode() {
            const token = ++this.codeToken;
            this.codeLoading = true;

            const payload = JSON.parse(JSON.stringify(this.nodes));
            const wrapInMethod = !!this.codeSettings.wrapInMethod;
            const useFqcn = !!this.codeSettings.useFqcn;
            let code = '';

            try {
                const wire = this.resolveLivewire();
                if (wire) {
                    const response = await wire.generateCode(payload, wrapInMethod, useFqcn);
                    if (typeof response === 'string' && response.length) {
                        code = response;
                    }
                }
            } catch (error) {
                // Fall back to browser-side generation below.
            }

            if (!code) {
                code = generateCodeLocal(payload, wrapInMethod, useFqcn);
            }

            if (token === this.codeToken) {
                this.generatedCode = code;
                this.codeLoading = false;
            }
        },

        totalComponentsCount() {
            let count = 0;
            const countFn = (items) => {
                for (const item of items) {
                    count++;
                    if (item.children && item.children.length) {
                        countFn(item.children);
                    }
                }
            };
            countFn(this.nodes);
            return count;
        },

        isLayoutType(type) {
            return type === 'Section' || type === 'Grid' || type === 'Fieldset';
        },

        selectNode(id) {
            this.selectedNodeId = id;
        },

        async showCodeTab() {
            this.activeTab = 'code';
            await this.refreshCode();
        },

        showJsonTab() {
            this.jsonDraft = JSON.stringify(this.nodes, null, 2);
            this.activeTab = 'json';
        },

        createNode(type, subType = 'text') {
            const id = 'node_' + Math.random().toString(36).slice(2, 11);
            const baseName = type.toLowerCase() + '_' + Math.floor(Math.random() * 100);

            if (type === 'Section') {
                return {
                    id,
                    type: 'Section',
                    title: 'General Information',
                    description: 'Enter general details below.',
                    columns: 2,
                    collapsible: false,
                    children: []
                };
            }

            if (type === 'Grid') {
                return {
                    id,
                    type: 'Grid',
                    columns: 2,
                    children: []
                };
            }

            const node = {
                id,
                type,
                subType,
                name: baseName,
                label: this.humanize(baseName),
                placeholder: '',
                helperText: '',
                required: false,
                disabled: false,
                readOnly: false,
                columnSpan: '1',
                default: ''
            };

            if (type === 'Select' || type === 'Radio') {
                node.options = [
                    { value: 'option_1', label: 'Option 1' },
                    { value: 'option_2', label: 'Option 2' }
                ];
            }

            return node;
        },

        addComponent(type, subType = 'text') {
            const node = this.createNode(type, subType);
            this.nodes.push(node);
            this.selectNode(node.id);
        },

        deleteNode(id) {
            this.nodes = this.removeNodeRecursive(this.nodes, id);
            if (this.selectedNodeId === id) {
                this.selectedNodeId = null;
            }
        },

        duplicateNode(id) {
            const located = this.duplicateInTree(this.nodes, id);
            if (!located.found) {
                return;
            }

            this.nodes = located.nodes;
            this.selectNode(located.newId);
        },

        duplicateInTree(items, id) {
            const next = [];
            let found = false;
            let newId = null;

            for (const item of items) {
                if (item.children && item.children.length && !found) {
                    const childResult = this.duplicateInTree(item.children, id);
                    if (childResult.found) {
                        next.push({ ...item, children: childResult.nodes });
                        found = true;
                        newId = childResult.newId;
                        continue;
                    }
                }

                next.push(item);

                if (!found && item.id === id) {
                    const clone = this.cloneNode(item);
                    next.push(clone);
                    found = true;
                    newId = clone.id;
                }
            }

            return { nodes: next, found, newId };
        },

        cloneNode(node) {
            const clone = JSON.parse(JSON.stringify(node));
            const assignIds = (item, rename) => {
                item.id = 'node_' + Math.random().toString(36).slice(2, 11);
                if (rename && item.name) {
                    item.name = item.name + '_copy';
                }
                if (item.children) {
                    item.children.forEach((child) => assignIds(child, false));
                }
            };
            assignIds(clone, true);
            return clone;
        },

        clearAll() {
            if (confirm('Are you sure you want to clear the entire form canvas?')) {
                this.nodes = [];
                this.selectedNodeId = null;
            }
        },

        addOptionToSelected() {
            if (!this.selectedNode) {
                return;
            }
            if (!this.selectedNode.options) {
                this.selectedNode.options = [];
            }
            const idx = this.selectedNode.options.length + 1;
            this.selectedNode.options.push({
                value: 'option_' + idx,
                label: 'Option ' + idx
            });
        },

        removeOptionFromSelected(idx) {
            if (!this.selectedNode || !this.selectedNode.options) {
                return;
            }
            this.selectedNode.options.splice(idx, 1);
        },

        onDragStart(event, type, subType = 'text') {
            this.draggingItem = { type, subType };
            this.reorderDragId = null;
            event.dataTransfer.setData('text/plain', JSON.stringify(this.draggingItem));
            event.dataTransfer.effectAllowed = 'copy';
        },

        onDropOnCanvas(event) {
            this.dragOverCanvas = false;
            if (this.reorderDragId) {
                this.reorderDragId = null;
                return;
            }
            if (this.draggingItem) {
                this.addComponent(this.draggingItem.type, this.draggingItem.subType);
                this.draggingItem = null;
            }
        },

        onDropInsideContainer(event, containerId) {
            if (this.reorderDragId) {
                const dragId = this.reorderDragId;
                this.reorderDragId = null;

                if (dragId === containerId) {
                    return;
                }

                const moving = this.findNodeRecursive(this.nodes, dragId);
                const container = this.findNodeRecursive(this.nodes, containerId);

                if (!moving || !container || !this.isLayoutType(container.type) || this.containsNode(moving, containerId)) {
                    return;
                }

                const removed = this.removeNodeRecursive(this.nodes, dragId);
                const target = this.findNodeRecursive(removed, containerId);
                if (!target) {
                    return;
                }
                if (!target.children) {
                    target.children = [];
                }
                target.children.push(moving);
                this.nodes = removed;
                this.selectNode(moving.id);
                return;
            }

            if (!this.draggingItem) {
                return;
            }

            const container = this.findNodeRecursive(this.nodes, containerId);
            if (container && this.isLayoutType(container.type)) {
                if (!container.children) {
                    container.children = [];
                }
                const newNode = this.createNode(this.draggingItem.type, this.draggingItem.subType);
                container.children.push(newNode);
                this.selectNode(newNode.id);
            }
            this.draggingItem = null;
        },

        onNodeReorderStart(event, nodeId) {
            this.reorderDragId = nodeId;
            this.draggingItem = null;
            event.dataTransfer.setData('text/plain', nodeId);
            event.dataTransfer.effectAllowed = 'move';
        },

        onNodeReorderDrop(event, targetId) {
            if (!this.reorderDragId || this.reorderDragId === targetId) {
                this.reorderDragId = null;
                return;
            }

            const dragIdx = this.nodes.findIndex((node) => node.id === this.reorderDragId);
            const targetIdx = this.nodes.findIndex((node) => node.id === targetId);

            if (dragIdx > -1 && targetIdx > -1) {
                const [dragged] = this.nodes.splice(dragIdx, 1);
                this.nodes.splice(targetIdx, 0, dragged);
            }

            this.reorderDragId = null;
        },

        containsNode(node, id) {
            if (!node || node.id === id) {
                return node?.id === id;
            }
            return (node.children || []).some((child) => this.containsNode(child, id));
        },

        loadJsonFromInput() {
            try {
                const parsed = JSON.parse(this.jsonDraft);
                if (!Array.isArray(parsed)) {
                    throw new Error('Schema must be an array.');
                }
                this.nodes = parsed;
                this.selectedNodeId = null;
                this.activeTab = 'canvas';
            } catch (error) {
                alert('Invalid JSON Schema format.');
            }
        },

        async copyPhpCode() {
            await this.refreshCode();
            await navigator.clipboard.writeText(this.generatedCode);
            this.copied = true;
            setTimeout(() => {
                this.copied = false;
            }, 2500);
        },

        humanize(str) {
            return str
                .replace(/^[\s_]+|[\s_]+$/g, '')
                .replace(/[_\s]+/g, ' ')
                .replace(/^[a-z]/, function (match) { return match.toUpperCase(); });
        },

        findNodeRecursive(items, id) {
            for (const item of items) {
                if (item.id === id) {
                    return item;
                }
                if (item.children && item.children.length) {
                    const found = this.findNodeRecursive(item.children, id);
                    if (found) {
                        return found;
                    }
                }
            }
            return null;
        },

        removeNodeRecursive(items, id) {
            return items
                .filter((item) => item.id !== id)
                .map((item) => {
                    if (item.children && item.children.length) {
                        return { ...item, children: this.removeNodeRecursive(item.children, id) };
                    }
                    return item;
                });
        },

    };
}
