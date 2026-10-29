<x-filament-panels::page>
    <div 
        x-data="formGenerator()" 
        x-init="init()"
        class="flex flex-col gap-4 font-sans text-gray-900 dark:text-gray-100"
    >
        {{-- Top Toolbar --}}
        <div class="flex flex-wrap items-center justify-between gap-3 p-4 bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800">
            <div class="flex items-center gap-2">
                <button 
                    type="button"
                    @click="activeTab = 'canvas'"
                    :class="activeTab === 'canvas' ? 'bg-primary-50 dark:bg-primary-950/40 text-primary-600 dark:text-primary-400 border-primary-500' : 'text-gray-600 dark:text-gray-400 border-transparent hover:bg-gray-100 dark:hover:bg-gray-800'"
                    class="flex items-center gap-2 px-3 py-1.5 text-sm font-medium rounded-lg border transition"
                >
                    <x-filament::icon icon="heroicon-o-squares-plus" class="w-4 h-4" />
                    <span>Visual Canvas</span>
                    <span 
                        x-text="totalComponentsCount()"
                        class="px-1.5 py-0.5 text-xs rounded-full bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-semibold"
                    ></span>
                </button>

                <button 
                    type="button"
                    @click="activeTab = 'code'"
                    :class="activeTab === 'code' ? 'bg-primary-50 dark:bg-primary-950/40 text-primary-600 dark:text-primary-400 border-primary-500' : 'text-gray-600 dark:text-gray-400 border-transparent hover:bg-gray-100 dark:hover:bg-gray-800'"
                    class="flex items-center gap-2 px-3 py-1.5 text-sm font-medium rounded-lg border transition"
                >
                    <x-filament::icon icon="heroicon-o-code-bracket" class="w-4 h-4" />
                    <span>Generated PHP Code</span>
                </button>

                <button 
                    type="button"
                    @click="activeTab = 'json'"
                    :class="activeTab === 'json' ? 'bg-primary-50 dark:bg-primary-950/40 text-primary-600 dark:text-primary-400 border-primary-500' : 'text-gray-600 dark:text-gray-400 border-transparent hover:bg-gray-100 dark:hover:bg-gray-800'"
                    class="flex items-center gap-2 px-3 py-1.5 text-sm font-medium rounded-lg border transition"
                >
                    <x-filament::icon icon="heroicon-o-document-text" class="w-4 h-4" />
                    <span>JSON Schema</span>
                </button>
            </div>

            <div class="flex items-center gap-2">
                {{-- Quick Templates Dropdown --}}
                <div x-data="{ open: false }" class="relative">
                    <button 
                        type="button" 
                        @click="open = !open" 
                        @click.away="open = false"
                        class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700"
                    >
                        <x-filament::icon icon="heroicon-o-sparkles" class="w-3.5 h-3.5 text-amber-500" />
                        <span>Templates</span>
                        <x-filament::icon icon="heroicon-m-chevron-down" class="w-3 h-3 text-gray-400" />
                    </button>
                    <div 
                        x-show="open" 
                        x-transition 
                        class="absolute right-0 mt-1 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50 py-1 text-xs"
                    >
                        <button 
                            type="button"
                            @click="loadTemplate('user'); open = false"
                            class="w-full text-left px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 flex items-center gap-2"
                        >
                            <x-filament::icon icon="heroicon-o-user" class="w-4 h-4 text-primary-500" />
                            <span>User Profile Form</span>
                        </button>
                        <button 
                            type="button"
                            @click="loadTemplate('contact'); open = false"
                            class="w-full text-left px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 flex items-center gap-2"
                        >
                            <x-filament::icon icon="heroicon-o-envelope" class="w-4 h-4 text-emerald-500" />
                            <span>Contact & Feedback</span>
                        </button>
                        <button 
                            type="button"
                            @click="loadTemplate('product'); open = false"
                            class="w-full text-left px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-200 flex items-center gap-2"
                        >
                            <x-filament::icon icon="heroicon-o-shopping-bag" class="w-4 h-4 text-purple-500" />
                            <span>E-Commerce Product</span>
                        </button>
                    </div>
                </div>

                {{-- Copy Code Button --}}
                <button 
                    type="button"
                    @click="copyPhpCode()"
                    class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-primary-600 text-white hover:bg-primary-500 shadow-sm transition active:scale-95"
                >
                    <x-filament::icon icon="heroicon-o-clipboard-document" class="w-3.5 h-3.5" />
                    <span x-text="copied ? 'Copied to Clipboard!' : 'Copy PHP Code'"></span>
                </button>

                {{-- Clear All Button --}}
                <button 
                    type="button"
                    @click="clearAll()"
                    class="p-1.5 text-gray-400 hover:text-red-500 rounded-lg hover:bg-red-50 dark:hover:bg-red-950/30 transition"
                    title="Clear All Fields"
                >
                    <x-filament::icon icon="heroicon-o-trash" class="w-4 h-4" />
                </button>
            </div>
        </div>

        {{-- TAB 1: VISUAL CANVAS --}}
        <div x-show="activeTab === 'canvas'" class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">
            
            {{-- Left: Component Palette (3 columns) --}}
            <div class="lg:col-span-3 bg-white dark:bg-gray-900 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-800 flex flex-col gap-4">
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-2">
                        Layout Containers
                    </h3>
                    <div class="grid grid-cols-1 gap-2">
                        <template x-for="item in layoutPalette" :key="item.type">
                            <div 
                                draggable="true"
                                @dragstart="onDragStart($event, item.type)"
                                @click="addComponent(item.type)"
                                class="flex items-center gap-2.5 p-2.5 rounded-lg border border-dashed border-gray-300 dark:border-gray-700 hover:border-primary-500 dark:hover:border-primary-500 bg-gray-50 dark:bg-gray-800/60 hover:bg-primary-50/50 dark:hover:bg-primary-950/30 cursor-grab active:cursor-grabbing transition group select-none"
                            >
                                <span class="p-1.5 rounded-md bg-white dark:bg-gray-800 shadow-xs text-primary-500 group-hover:scale-110 transition">
                                    <span x-html="item.iconSvg"></span>
                                </span>
                                <div>
                                    <div class="text-xs font-semibold text-gray-800 dark:text-gray-200" x-text="item.label"></div>
                                    <div class="text-[10px] text-gray-400" x-text="item.description"></div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-2">
                        Form Fields
                    </h3>
                    <div class="grid grid-cols-2 gap-2">
                        <template x-for="item in fieldPalette" :key="item.type + (item.subType || '')">
                            <div 
                                draggable="true"
                                @dragstart="onDragStart($event, item.type, item.subType)"
                                @click="addComponent(item.type, item.subType)"
                                class="flex flex-col items-center justify-center p-2.5 rounded-lg border border-gray-200 dark:border-gray-800 hover:border-primary-500 dark:hover:border-primary-500 bg-white dark:bg-gray-800/40 hover:bg-primary-50/50 dark:hover:bg-primary-950/30 cursor-grab active:cursor-grabbing transition text-center group select-none"
                            >
                                <span class="p-1.5 rounded-md text-gray-600 dark:text-gray-300 group-hover:text-primary-500 group-hover:scale-110 transition">
                                    <span x-html="item.iconSvg"></span>
                                </span>
                                <span class="text-xs font-medium text-gray-700 dark:text-gray-300 mt-1" x-text="item.label"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="text-[11px] text-gray-400 dark:text-gray-500 text-center border-t border-gray-100 dark:border-gray-800 pt-3">
                    💡 Click or drag any element to add it to your canvas.
                </div>
            </div>

            {{-- Center: Canvas (6 columns) --}}
            <div class="lg:col-span-6 flex flex-col gap-3">
                <div 
                    @dragover.prevent="dragOverCanvas = true"
                    @dragleave.prevent="dragOverCanvas = false"
                    @drop.prevent="onDropOnCanvas($event)"
                    :class="dragOverCanvas ? 'ring-2 ring-primary-500 bg-primary-50/20 dark:bg-primary-950/20' : ''"
                    class="min-h-[500px] p-4 bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 transition flex flex-col gap-3"
                >
                    {{-- Empty State --}}
                    <div 
                        x-show="nodes.length === 0" 
                        class="flex-1 flex flex-col items-center justify-center py-16 text-center border-2 border-dashed border-gray-200 dark:border-gray-800 rounded-lg"
                    >
                        <div class="w-12 h-12 rounded-full bg-primary-50 dark:bg-primary-950/50 flex items-center justify-center text-primary-500 mb-3">
                            <x-filament::icon icon="heroicon-o-cursor-arrow-rays" class="w-6 h-6" />
                        </div>
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Your Form Canvas is Empty</h4>
                        <p class="text-xs text-gray-400 max-w-xs mt-1">
                            Click or drag elements from the left palette, or pick a starter template above.
                        </p>
                    </div>

                    {{-- Render Top-level Nodes --}}
                    <template x-for="(node, index) in nodes" :key="node.id">
                        <div 
                            draggable="true"
                            @dragstart="onNodeReorderStart($event, node.id, null)"
                            @dragover.prevent
                            @drop.stop.prevent="onNodeReorderDrop($event, node.id, null)"
                            @click="selectNode(node.id)"
                            :class="selectedNodeId === node.id ? 'ring-2 ring-primary-500 border-primary-500' : 'border-gray-200 dark:border-gray-700/80 hover:border-gray-300 dark:hover:border-gray-600'"
                            class="relative p-3.5 rounded-lg border bg-white dark:bg-gray-800/80 shadow-xs cursor-pointer transition group"
                        >
                            {{-- Node Header Bar --}}
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <span class="text-gray-400 cursor-move" title="Drag to reorder">
                                        <x-filament::icon icon="heroicon-m-bars-2" class="w-4 h-4" />
                                    </span>
                                    <span 
                                        class="text-xs font-bold uppercase tracking-wider px-2 py-0.5 rounded-md"
                                        :class="isLayoutType(node.type) ? 'bg-amber-100 dark:bg-amber-950/50 text-amber-700 dark:text-amber-400' : 'bg-blue-100 dark:bg-blue-950/50 text-blue-700 dark:text-blue-400'"
                                        x-text="node.type + (node.subType ? ` (${node.subType})` : '')"
                                    ></span>
                                    <span class="text-xs text-gray-400 font-mono font-medium" x-text="`$${node.name || node.title || ''}`"></span>
                                </div>

                                <div class="flex items-center gap-1 opacity-80 group-hover:opacity-100">
                                    <button 
                                        type="button"
                                        @click.stop="duplicateNode(node.id)"
                                        class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 rounded"
                                        title="Duplicate"
                                    >
                                        <x-filament::icon icon="heroicon-o-document-duplicate" class="w-3.5 h-3.5" />
                                    </button>
                                    <button 
                                        type="button"
                                        @click.stop="deleteNode(node.id)"
                                        class="p-1 text-gray-400 hover:text-red-500 rounded"
                                        title="Delete"
                                    >
                                        <x-filament::icon icon="heroicon-o-trash" class="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            </div>

                            {{-- Preview Body --}}
                            <div class="pointer-events-none">
                                {{-- Section Container --}}
                                <template x-if="node.type === 'Section'">
                                    <div class="p-3 bg-gray-50 dark:bg-gray-900/60 rounded-md border border-gray-200 dark:border-gray-800 pointer-events-auto">
                                        <div class="font-semibold text-sm text-gray-800 dark:text-gray-200" x-text="node.title || 'Section Title'"></div>
                                        <div x-show="node.description" class="text-xs text-gray-400 mb-2" x-text="node.description"></div>

                                        {{-- Nested Children Dropzone --}}
                                        <div 
                                            @dragover.prevent
                                            @drop.stop.prevent="onDropInsideContainer($event, node.id)"
                                            :class="node.columns > 1 ? `grid grid-cols-${node.columns} gap-2` : 'flex flex-col gap-2'"
                                            class="min-h-[60px] p-2 mt-2 bg-white dark:bg-gray-800 rounded border border-dashed border-gray-300 dark:border-gray-700"
                                        >
                                            <div x-show="!node.children || node.children.length === 0" class="col-span-full py-3 text-center text-xs text-gray-400">
                                                Drop fields inside this Section
                                            </div>

                                            <template x-for="child in node.children" :key="child.id">
                                                <div 
                                                    @click.stop="selectNode(child.id)"
                                                    :class="selectedNodeId === child.id ? 'ring-2 ring-primary-500 border-primary-500' : 'border-gray-200 dark:border-gray-700'"
                                                    class="p-2.5 rounded border bg-gray-50 dark:bg-gray-900/90 shadow-2xs relative group/child"
                                                >
                                                    <div class="flex items-center justify-between text-xs mb-1">
                                                        <span class="font-semibold text-gray-700 dark:text-gray-300" x-text="child.label || child.name"></span>
                                                        <button 
                                                            type="button" 
                                                            @click.stop="deleteNode(child.id)"
                                                            class="text-gray-400 hover:text-red-500"
                                                        >
                                                            <x-filament::icon icon="heroicon-o-trash" class="w-3 h-3" />
                                                        </button>
                                                    </div>
                                                    <div class="h-6 bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700 flex items-center px-2 text-[11px] text-gray-400">
                                                        <span x-text="child.placeholder || child.type"></span>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>

                                {{-- Grid Container --}}
                                <template x-if="node.type === 'Grid'">
                                    <div class="p-3 bg-gray-50 dark:bg-gray-900/60 rounded-md border border-gray-200 dark:border-gray-800 pointer-events-auto">
                                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1" x-text="`Grid (${node.columns || 2} Columns)`"></div>
                                        <div 
                                            @dragover.prevent
                                            @drop.stop.prevent="onDropInsideContainer($event, node.id)"
                                            class="grid gap-2 min-h-[60px] p-2 bg-white dark:bg-gray-800 rounded border border-dashed border-gray-300 dark:border-gray-700"
                                            :style="`grid-template-columns: repeat(${node.columns || 2}, minmax(0, 1fr))`"
                                        >
                                            <div x-show="!node.children || node.children.length === 0" class="col-span-full py-3 text-center text-xs text-gray-400">
                                                Drop fields inside this Grid
                                            </div>

                                            <template x-for="child in node.children" :key="child.id">
                                                <div 
                                                    @click.stop="selectNode(child.id)"
                                                    :class="selectedNodeId === child.id ? 'ring-2 ring-primary-500 border-primary-500' : 'border-gray-200 dark:border-gray-700'"
                                                    class="p-2.5 rounded border bg-gray-50 dark:bg-gray-900/90 shadow-2xs relative group/child"
                                                >
                                                    <div class="flex items-center justify-between text-xs mb-1">
                                                        <span class="font-semibold text-gray-700 dark:text-gray-300" x-text="child.label || child.name"></span>
                                                        <button 
                                                            type="button" 
                                                            @click.stop="deleteNode(child.id)"
                                                            class="text-gray-400 hover:text-red-500"
                                                        >
                                                            <x-filament::icon icon="heroicon-o-trash" class="w-3 h-3" />
                                                        </button>
                                                    </div>
                                                    <div class="h-6 bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700 flex items-center px-2 text-[11px] text-gray-400">
                                                        <span x-text="child.placeholder || child.type"></span>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>

                                {{-- Standard Inputs Preview --}}
                                <template x-if="!isLayoutType(node.type)">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                            <span x-text="node.label || node.name || 'Field Label'"></span>
                                            <span x-show="node.required" class="text-red-500">*</span>
                                        </label>

                                        {{-- TextInput preview --}}
                                        <template x-if="node.type === 'TextInput'">
                                            <div class="w-full h-8 px-2.5 text-xs rounded-md bg-gray-50 dark:bg-gray-800/80 border border-gray-200 dark:border-gray-700 flex items-center text-gray-400">
                                                <span x-text="node.placeholder || (node.subType === 'password' ? '••••••••' : 'Enter ' + (node.label || node.name))"></span>
                                            </div>
                                        </template>

                                        {{-- Textarea preview --}}
                                        <template x-if="node.type === 'Textarea'">
                                            <div class="w-full h-14 p-2 text-xs rounded-md bg-gray-50 dark:bg-gray-800/80 border border-gray-200 dark:border-gray-700 text-gray-400">
                                                <span x-text="node.placeholder || 'Enter text here...'"></span>
                                            </div>
                                        </template>

                                        {{-- Select preview --}}
                                        <template x-if="node.type === 'Select'">
                                            <div class="w-full h-8 px-2.5 text-xs rounded-md bg-gray-50 dark:bg-gray-800/80 border border-gray-200 dark:border-gray-700 flex items-center justify-between text-gray-400">
                                                <span x-text="node.placeholder || 'Select an option...'"></span>
                                                <x-filament::icon icon="heroicon-m-chevron-down" class="w-3.5 h-3.5 text-gray-400" />
                                            </div>
                                        </template>

                                        {{-- Toggle preview --}}
                                        <template x-if="node.type === 'Toggle'">
                                            <div class="flex items-center gap-2 mt-1">
                                                <div class="w-8 h-4 bg-primary-600 rounded-full relative">
                                                    <div class="w-3 h-3 bg-white rounded-full absolute right-0.5 top-0.5"></div>
                                                </div>
                                                <span class="text-xs text-gray-500">Enabled</span>
                                            </div>
                                        </template>

                                        {{-- Checkbox preview --}}
                                        <template x-if="node.type === 'Checkbox'">
                                            <div class="flex items-center gap-2 mt-1">
                                                <div class="w-4 h-4 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 flex items-center justify-center text-primary-500">
                                                    <x-filament::icon icon="heroicon-m-check" class="w-3 h-3" />
                                                </div>
                                                <span class="text-xs text-gray-600 dark:text-gray-400" x-text="node.helperText || 'Check this option'"></span>
                                            </div>
                                        </template>

                                        {{-- Radio preview --}}
                                        <template x-if="node.type === 'Radio'">
                                            <div class="flex flex-col gap-1.5 mt-1">
                                                <template x-for="opt in (node.options || [{label: 'Option 1'}, {label: 'Option 2'}])">
                                                    <div class="flex items-center gap-2">
                                                        <div class="w-3.5 h-3.5 rounded-full border border-primary-500 flex items-center justify-center">
                                                            <div class="w-1.5 h-1.5 rounded-full bg-primary-500"></div>
                                                        </div>
                                                        <span class="text-xs text-gray-600 dark:text-gray-400" x-text="opt.label"></span>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>

                                        {{-- DatePicker preview --}}
                                        <template x-if="node.type === 'DatePicker'">
                                            <div class="w-full h-8 px-2.5 text-xs rounded-md bg-gray-50 dark:bg-gray-800/80 border border-gray-200 dark:border-gray-700 flex items-center justify-between text-gray-400">
                                                <span>YYYY-MM-DD</span>
                                                <x-filament::icon icon="heroicon-o-calendar" class="w-3.5 h-3.5 text-gray-400" />
                                            </div>
                                        </template>

                                        <div x-show="node.helperText" class="text-[10px] text-gray-400 mt-1" x-text="node.helperText"></div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Right: Property Inspector (3 columns) --}}
            <div class="lg:col-span-3 bg-white dark:bg-gray-900 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-800 flex flex-col gap-4">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-2">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        Properties Inspector
                    </h3>
                    <template x-if="selectedNode">
                        <span class="text-xs px-2 py-0.5 rounded bg-primary-50 dark:bg-primary-950 text-primary-600 dark:text-primary-400 font-semibold" x-text="selectedNode.type"></span>
                    </template>
                </div>

                {{-- Empty Selection State --}}
                <div x-show="!selectedNode" class="py-12 text-center text-gray-400 text-xs">
                    <x-filament::icon icon="heroicon-o-adjustments-vertical" class="w-8 h-8 mx-auto text-gray-300 dark:text-gray-600 mb-2" />
                    Select a component from the canvas to edit its properties.
                </div>

                {{-- Selected Node Properties --}}
                <div x-show="selectedNode" class="flex flex-col gap-3 text-xs">
                    
                    {{-- Section / Grid specific settings --}}
                    <template x-if="selectedNode && selectedNode.type === 'Section'">
                        <div class="flex flex-col gap-3">
                            <div>
                                <label class="block font-semibold mb-1 text-gray-700 dark:text-gray-300">Section Title</label>
                                <input 
                                    type="text" 
                                    x-model="selectedNode.title" 
                                    class="w-full text-xs rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-2.5 py-1.5 focus:border-primary-500 focus:ring-1 focus:ring-primary-500" 
                                />
                            </div>
                            <div>
                                <label class="block font-semibold mb-1 text-gray-700 dark:text-gray-300">Description</label>
                                <input 
                                    type="text" 
                                    x-model="selectedNode.description" 
                                    class="w-full text-xs rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-2.5 py-1.5 focus:border-primary-500 focus:ring-1 focus:ring-primary-500" 
                                />
                            </div>
                            <div>
                                <label class="block font-semibold mb-1 text-gray-700 dark:text-gray-300">Inner Columns</label>
                                <select 
                                    x-model="selectedNode.columns" 
                                    class="w-full text-xs rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-2.5 py-1.5"
                                >
                                    <option value="1">1 Column</option>
                                    <option value="2">2 Columns</option>
                                    <option value="3">3 Columns</option>
                                    <option value="4">4 Columns</option>
                                </select>
                            </div>
                            <div class="flex items-center gap-2 pt-1">
                                <input type="checkbox" id="sec_collapsible" x-model="selectedNode.collapsible" class="rounded text-primary-600 focus:ring-primary-500" />
                                <label for="sec_collapsible" class="text-gray-700 dark:text-gray-300 cursor-pointer">Collapsible</label>
                            </div>
                        </div>
                    </template>

                    <template x-if="selectedNode && selectedNode.type === 'Grid'">
                        <div class="flex flex-col gap-3">
                            <div>
                                <label class="block font-semibold mb-1 text-gray-700 dark:text-gray-300">Grid Columns</label>
                                <select 
                                    x-model="selectedNode.columns" 
                                    class="w-full text-xs rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-2.5 py-1.5"
                                >
                                    <option value="1">1 Column</option>
                                    <option value="2">2 Columns</option>
                                    <option value="3">3 Columns</option>
                                    <option value="4">4 Columns</option>
                                </select>
                            </div>
                        </div>
                    </template>

                    {{-- Standard Field Properties --}}
                    <template x-if="selectedNode && !isLayoutType(selectedNode.type)">
                        <div class="flex flex-col gap-3">
                            {{-- Field Name --}}
                            <div>
                                <label class="block font-semibold mb-1 text-gray-700 dark:text-gray-300">Field Name (Schema Key)</label>
                                <input 
                                    type="text" 
                                    x-model="selectedNode.name" 
                                    class="w-full text-xs font-mono rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-2.5 py-1.5 focus:border-primary-500 focus:ring-1 focus:ring-primary-500" 
                                />
                            </div>

                            {{-- Label --}}
                            <div>
                                <label class="block font-semibold mb-1 text-gray-700 dark:text-gray-300">Display Label</label>
                                <input 
                                    type="text" 
                                    x-model="selectedNode.label" 
                                    class="w-full text-xs rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-2.5 py-1.5 focus:border-primary-500 focus:ring-1 focus:ring-primary-500" 
                                />
                            </div>

                            {{-- Placeholder --}}
                            <div>
                                <label class="block font-semibold mb-1 text-gray-700 dark:text-gray-300">Placeholder</label>
                                <input 
                                    type="text" 
                                    x-model="selectedNode.placeholder" 
                                    class="w-full text-xs rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-2.5 py-1.5" 
                                />
                            </div>

                            {{-- Helper Text --}}
                            <div>
                                <label class="block font-semibold mb-1 text-gray-700 dark:text-gray-300">Helper Text</label>
                                <input 
                                    type="text" 
                                    x-model="selectedNode.helperText" 
                                    class="w-full text-xs rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-2.5 py-1.5" 
                                />
                            </div>

                            {{-- Subtype (for TextInput) --}}
                            <template x-if="selectedNode.type === 'TextInput'">
                                <div>
                                    <label class="block font-semibold mb-1 text-gray-700 dark:text-gray-300">Input Type</label>
                                    <select 
                                        x-model="selectedNode.subType" 
                                        class="w-full text-xs rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-2.5 py-1.5"
                                    >
                                        <option value="text">Text</option>
                                        <option value="email">Email</option>
                                        <option value="numeric">Numeric</option>
                                        <option value="password">Password</option>
                                        <option value="tel">Telephone</option>
                                        <option value="url">URL</option>
                                    </select>
                                </div>
                            </template>

                            {{-- Column Span --}}
                            <div>
                                <label class="block font-semibold mb-1 text-gray-700 dark:text-gray-300">Column Span</label>
                                <select 
                                    x-model="selectedNode.columnSpan" 
                                    class="w-full text-xs rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-2.5 py-1.5"
                                >
                                    <option value="1">1 Column (Default)</option>
                                    <option value="2">2 Columns</option>
                                    <option value="full">Full Width (columnSpanFull)</option>
                                </select>
                            </div>

                            {{-- Options Manager for Select / Radio --}}
                            <template x-if="selectedNode.type === 'Select' || selectedNode.type === 'Radio'">
                                <div class="border-t border-gray-100 dark:border-gray-800 pt-3">
                                    <div class="flex items-center justify-between mb-2">
                                        <label class="font-semibold text-gray-700 dark:text-gray-300">Options</label>
                                        <button 
                                            type="button" 
                                            @click="addOptionToSelected()" 
                                            class="text-primary-600 dark:text-primary-400 hover:underline font-medium text-[11px]"
                                        >
                                            + Add Option
                                        </button>
                                    </div>

                                    <div class="flex flex-col gap-1.5">
                                        <template x-for="(opt, optIdx) in (selectedNode.options || [])" :key="optIdx">
                                            <div class="flex items-center gap-1">
                                                <input 
                                                    type="text" 
                                                    placeholder="Key" 
                                                    x-model="opt.value" 
                                                    class="w-1/2 text-xs font-mono rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-2 py-1"
                                                />
                                                <input 
                                                    type="text" 
                                                    placeholder="Label" 
                                                    x-model="opt.label" 
                                                    class="w-1/2 text-xs rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-2 py-1"
                                                />
                                                <button 
                                                    type="button" 
                                                    @click="removeOptionFromSelected(optIdx)" 
                                                    class="text-gray-400 hover:text-red-500 p-1"
                                                >
                                                    <x-filament::icon icon="heroicon-o-x-mark" class="w-3.5 h-3.5" />
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            {{-- Validation Toggles --}}
                            <div class="border-t border-gray-100 dark:border-gray-800 pt-3 flex flex-col gap-2">
                                <label class="font-semibold text-gray-700 dark:text-gray-300">Validation & State</label>

                                <div class="flex items-center gap-2">
                                    <input type="checkbox" id="prop_req" x-model="selectedNode.required" class="rounded text-primary-600" />
                                    <label for="prop_req" class="text-gray-700 dark:text-gray-300 cursor-pointer">Required</label>
                                </div>

                                <div class="flex items-center gap-2">
                                    <input type="checkbox" id="prop_dis" x-model="selectedNode.disabled" class="rounded text-primary-600" />
                                    <label for="prop_dis" class="text-gray-700 dark:text-gray-300 cursor-pointer">Disabled</label>
                                </div>

                                <div class="flex items-center gap-2">
                                    <input type="checkbox" id="prop_ro" x-model="selectedNode.readOnly" class="rounded text-primary-600" />
                                    <label for="prop_ro" class="text-gray-700 dark:text-gray-300 cursor-pointer">Read Only</label>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- TAB 2: GENERATED PHP CODE --}}
        <div x-show="activeTab === 'code'" class="flex flex-col gap-4">
            <div class="flex flex-wrap items-center justify-between gap-3 p-3 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800">
                <div class="flex items-center gap-4 text-xs">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" x-model="codeSettings.wrapInMethod" class="rounded text-primary-600 focus:ring-primary-500" />
                        <span class="font-medium text-gray-700 dark:text-gray-300">Wrap in `public static function form(Form $form)`</span>
                    </label>

                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" x-model="codeSettings.useFqcn" class="rounded text-primary-600 focus:ring-primary-500" />
                        <span class="font-medium text-gray-700 dark:text-gray-300">Use FQCN (`Forms\Components\...`)</span>
                    </label>
                </div>

                <button 
                    type="button"
                    @click="copyPhpCode()"
                    class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-primary-600 text-white hover:bg-primary-500 transition shadow-sm"
                >
                    <x-filament::icon icon="heroicon-o-clipboard-document" class="w-3.5 h-3.5" />
                    <span x-text="copied ? 'Copied!' : 'Copy Code'"></span>
                </button>
            </div>

            <div class="relative bg-gray-950 text-gray-100 rounded-xl p-4 font-mono text-xs overflow-x-auto border border-gray-800 shadow-inner">
                <pre><code x-text="generatedCode"></code></pre>
            </div>
        </div>

        {{-- TAB 3: JSON SCHEMA (IMPORT / EXPORT) --}}
        <div x-show="activeTab === 'json'" class="flex flex-col gap-4">
            <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800">
                <div class="text-xs text-gray-500">
                    You can copy this JSON to save your design, or paste previously exported JSON and click "Load Schema".
                </div>

                <div class="flex items-center gap-2">
                    <button 
                        type="button"
                        @click="loadJsonFromInput()"
                        class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200"
                    >
                        Load JSON into Canvas
                    </button>
                </div>
            </div>

            <div class="relative bg-gray-950 text-gray-100 rounded-xl p-4 font-mono text-xs border border-gray-800 shadow-inner">
                <textarea 
                    x-model="jsonSchemaText" 
                    rows="16" 
                    class="w-full bg-transparent border-0 focus:ring-0 text-emerald-400 font-mono text-xs resize-y"
                ></textarea>
            </div>
        </div>
    </div>

    {{-- Alpine.js Logic --}}
    <script>
        function formGenerator() {
            return {
                activeTab: 'canvas',
                selectedNodeId: null,
                copied: false,
                dragOverCanvas: false,
                draggingItem: null,
                reorderDragId: null,

                codeSettings: {
                    wrapInMethod: false,
                    useFqcn: false,
                },

                // Component Palettes
                layoutPalette: [
                    {
                        type: 'Section',
                        label: 'Section',
                        description: 'Group fields with title & card style',
                        iconSvg: '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>'
                    },
                    {
                        type: 'Grid',
                        label: 'Grid (Columns)',
                        description: 'Multi-column layout wrapper',
                        iconSvg: '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>'
                    }
                ],

                fieldPalette: [
                    {
                        type: 'TextInput',
                        subType: 'text',
                        label: 'Text Input',
                        iconSvg: '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" /></svg>'
                    },
                    {
                        type: 'TextInput',
                        subType: 'email',
                        label: 'Email Input',
                        iconSvg: '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" /></svg>'
                    },
                    {
                        type: 'TextInput',
                        subType: 'numeric',
                        label: 'Numeric / Number',
                        iconSvg: '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" /></svg>'
                    },
                    {
                        type: 'TextInput',
                        subType: 'password',
                        label: 'Password',
                        iconSvg: '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>'
                    },
                    {
                        type: 'Textarea',
                        label: 'Textarea',
                        iconSvg: '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" /></svg>'
                    },
                    {
                        type: 'Select',
                        label: 'Select Dropdown',
                        iconSvg: '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" /></svg>'
                    },
                    {
                        type: 'Toggle',
                        label: 'Toggle Switch',
                        iconSvg: '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h8a5 5 0 015 5 5 5 0 01-5 5H8a5 5 0 01-5-5 5 5 0 015-5z" /><circle cx="15" cy="12" r="3" fill="currentColor"/></svg>'
                    },
                    {
                        type: 'Checkbox',
                        label: 'Checkbox',
                        iconSvg: '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'
                    },
                    {
                        type: 'Radio',
                        label: 'Radio Group',
                        iconSvg: '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="12" cy="12" r="9" stroke-width="2"/><circle cx="12" cy="12" r="4" fill="currentColor"/></svg>'
                    },
                    {
                        type: 'DatePicker',
                        label: 'Date Picker',
                        iconSvg: '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>'
                    }
                ],

                // Active Nodes Array (The Form Tree)
                nodes: [],

                init() {
                    // Start with a clean starter template
                    this.loadTemplate('user');
                },

                get selectedNode() {
                    if (!this.selectedNodeId) return null;
                    return this.findNodeRecursive(this.nodes, this.selectedNodeId);
                },

                get generatedCode() {
                    return this.generatePhpCodeClientSide(this.nodes, this.codeSettings);
                },

                get jsonSchemaText() {
                    return JSON.stringify(this.nodes, null, 2);
                },

                set jsonSchemaText(val) {
                    try {
                        this.nodes = JSON.parse(val);
                    } catch (e) {}
                },

                totalComponentsCount() {
                    let count = 0;
                    const countFn = (items) => {
                        for (const it of items) {
                            count++;
                            if (it.children && it.children.length) {
                                countFn(it.children);
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

                // Factory to create a node object
                createNode(type, subType = 'text') {
                    const id = 'node_' + Math.random().toString(36).substr(2, 9);
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
                    const original = this.findNodeRecursive(this.nodes, id);
                    if (!original) return;
                    const clone = JSON.parse(JSON.stringify(original));
                    clone.id = 'node_' + Math.random().toString(36).substr(2, 9);
                    if (clone.name) clone.name = clone.name + '_copy';
                    this.nodes.push(clone);
                    this.selectNode(clone.id);
                },

                clearAll() {
                    if (confirm('Are you sure you want to clear the entire form canvas?')) {
                        this.nodes = [];
                        this.selectedNodeId = null;
                    }
                },

                addOptionToSelected() {
                    if (!this.selectedNode) return;
                    if (!this.selectedNode.options) this.selectedNode.options = [];
                    const idx = this.selectedNode.options.length + 1;
                    this.selectedNode.options.push({
                        value: 'option_' + idx,
                        label: 'Option ' + idx
                    });
                },

                removeOptionFromSelected(idx) {
                    if (!this.selectedNode || !this.selectedNode.options) return;
                    this.selectedNode.options.splice(idx, 1);
                },

                // Drag and drop handlers
                onDragStart(event, type, subType = 'text') {
                    this.draggingItem = { type, subType };
                    event.dataTransfer.setData('text/plain', JSON.stringify(this.draggingItem));
                },

                onDropOnCanvas(event) {
                    this.dragOverCanvas = false;
                    if (this.draggingItem) {
                        this.addComponent(this.draggingItem.type, this.draggingItem.subType);
                        this.draggingItem = null;
                    }
                },

                onDropInsideContainer(event, containerId) {
                    event.stopPropagation();
                    if (!this.draggingItem) return;
                    const container = this.findNodeRecursive(this.nodes, containerId);
                    if (container && this.isLayoutType(container.type)) {
                        if (!container.children) container.children = [];
                        const newNode = this.createNode(this.draggingItem.type, this.draggingItem.subType);
                        container.children.push(newNode);
                        this.selectNode(newNode.id);
                    }
                    this.draggingItem = null;
                },

                onNodeReorderStart(event, nodeId) {
                    this.reorderDragId = nodeId;
                },

                onNodeReorderDrop(event, targetId) {
                    event.stopPropagation();
                    if (!this.reorderDragId || this.reorderDragId === targetId) return;

                    const dragIdx = this.nodes.findIndex(n => n.id === this.reorderDragId);
                    const targetIdx = this.nodes.findIndex(n => n.id === targetId);

                    if (dragIdx > -1 && targetIdx > -1) {
                        const [dragged] = this.nodes.splice(dragIdx, 1);
                        this.nodes.splice(targetIdx, 0, dragged);
                    }
                    this.reorderDragId = null;
                },

                loadJsonFromInput() {
                    try {
                        const parsed = JSON.parse(this.jsonSchemaText);
                        if (Array.isArray(parsed)) {
                            this.nodes = parsed;
                            this.activeTab = 'canvas';
                        }
                    } catch (e) {
                        alert('Invalid JSON Schema format.');
                    }
                },

                copyPhpCode() {
                    navigator.clipboard.writeText(this.generatedCode).then(() => {
                        this.copied = true;
                        setTimeout(() => this.copied = false, 2500);
                    });
                },

                humanize(str) {
                    return str
                        .replace(/^[\s_]+|[\s_]+$/g, '')
                        .replace(/[_\s]+/g, ' ')
                        .replace(/^[a-z]/, function(m) { return m.toUpperCase(); });
                },

                findNodeRecursive(items, id) {
                    for (const item of items) {
                        if (item.id === id) return item;
                        if (item.children && item.children.length) {
                            const found = this.findNodeRecursive(item.children, id);
                            if (found) return found;
                        }
                    }
                    return null;
                },

                removeNodeRecursive(items, id) {
                    return items
                        .filter(item => item.id !== id)
                        .map(item => {
                            if (item.children && item.children.length) {
                                return { ...item, children: this.removeNodeRecursive(item.children, id) };
                            }
                            return item;
                        });
                },

                // Starter Templates
                loadTemplate(name) {
                    if (name === 'user') {
                        this.nodes = [
                            {
                                id: 'sec_1',
                                type: 'Section',
                                title: 'Personal Information',
                                description: 'Basic profile information for the user.',
                                columns: 2,
                                children: [
                                    {
                                        id: 'f_1',
                                        type: 'TextInput',
                                        subType: 'text',
                                        name: 'first_name',
                                        label: 'First Name',
                                        required: true,
                                        columnSpan: '1'
                                    },
                                    {
                                        id: 'f_2',
                                        type: 'TextInput',
                                        subType: 'text',
                                        name: 'last_name',
                                        label: 'Last Name',
                                        required: true,
                                        columnSpan: '1'
                                    },
                                    {
                                        id: 'f_3',
                                        type: 'TextInput',
                                        subType: 'email',
                                        name: 'email',
                                        label: 'Email Address',
                                        required: true,
                                        columnSpan: 'full'
                                    }
                                ]
                            },
                            {
                                id: 'sec_2',
                                type: 'Section',
                                title: 'Role & Status',
                                columns: 2,
                                children: [
                                    {
                                        id: 'f_4',
                                        type: 'Select',
                                        name: 'role',
                                        label: 'User Role',
                                        required: true,
                                        options: [
                                            { value: 'admin', label: 'Administrator' },
                                            { value: 'editor', label: 'Editor' },
                                            { value: 'user', label: 'Standard User' }
                                        ]
                                    },
                                    {
                                        id: 'f_5',
                                        type: 'Toggle',
                                        name: 'is_active',
                                        label: 'Account Active',
                                        default: true
                                    }
                                ]
                            }
                        ];
                    } else if (name === 'contact') {
                        this.nodes = [
                            {
                                id: 'f_c1',
                                type: 'TextInput',
                                name: 'name',
                                label: 'Your Name',
                                required: true
                            },
                            {
                                id: 'f_c2',
                                type: 'TextInput',
                                subType: 'email',
                                name: 'email',
                                label: 'Email',
                                required: true
                            },
                            {
                                id: 'f_c3',
                                type: 'Select',
                                name: 'department',
                                label: 'Department',
                                options: [
                                    { value: 'support', label: 'Customer Support' },
                                    { value: 'sales', label: 'Sales & Billing' }
                                ]
                            },
                            {
                                id: 'f_c4',
                                type: 'Textarea',
                                name: 'message',
                                label: 'Message',
                                rows: 4,
                                required: true,
                                columnSpan: 'full'
                            }
                        ];
                    } else if (name === 'product') {
                        this.nodes = [
                            {
                                id: 'sec_p1',
                                type: 'Section',
                                title: 'Product Details',
                                columns: 2,
                                children: [
                                    {
                                        id: 'p_1',
                                        type: 'TextInput',
                                        name: 'title',
                                        label: 'Product Title',
                                        required: true,
                                        columnSpan: 'full'
                                    },
                                    {
                                        id: 'p_2',
                                        type: 'TextInput',
                                        subType: 'numeric',
                                        name: 'price',
                                        label: 'Price ($)',
                                        required: true
                                    },
                                    {
                                        id: 'p_3',
                                        type: 'TextInput',
                                        subType: 'numeric',
                                        name: 'sku',
                                        label: 'SKU / Barcode'
                                    },
                                    {
                                        id: 'p_4',
                                        type: 'Textarea',
                                        name: 'description',
                                        label: 'Description',
                                        columnSpan: 'full'
                                    }
                                ]
                            }
                        ];
                    }
                    this.selectedNodeId = null;
                },

                // Real-time Client-side PHP Code Generator
                generatePhpCodeClientSide(nodes, settings) {
                    if (!nodes || nodes.length === 0) {
                        return '// No components added yet.\n// Drag and drop components from the palette into the canvas.';
                    }

                    const indentUnit = '    ';
                    const isFqcn = settings.useFqcn;

                    const resolveClass = (type) => isFqcn ? `Forms\\Components\\${type}` : type;

                    const collectTypes = (items, set) => {
                        for (const it of items) {
                            set.add(it.type);
                            if (it.children) collectTypes(it.children, set);
                        }
                    };

                    const renderModifiers = (node, level) => {
                        const mIndent = indentUnit.repeat(level + 1);
                        const lines = [];

                        if (node.label) lines.push(`${mIndent}->label('${node.label}')`);
                        if (node.placeholder) lines.push(`${mIndent}->placeholder('${node.placeholder}')`);
                        if (node.helperText) lines.push(`${mIndent}->helperText('${node.helperText}')`);
                        if (node.required) lines.push(`${mIndent}->required()`);
                        if (node.disabled) lines.push(`${mIndent}->disabled()`);
                        if (node.readOnly) lines.push(`${mIndent}->readOnly()`);

                        if (node.columnSpan === 'full') {
                            lines.push(`${mIndent}->columnSpanFull()`);
                        } else if (node.columnSpan && parseInt(node.columnSpan) > 1) {
                            lines.push(`${mIndent}->columnSpan(${node.columnSpan})`);
                        }

                        return lines;
                    };

                    const renderSingleNode = (node, level) => {
                        const indent = indentUnit.repeat(level);
                        const mIndent = indentUnit.repeat(level + 1);
                        const cls = resolveClass(node.type);

                        if (node.type === 'Section') {
                            const lines = [`${indent}${cls}::make('${node.title || 'Section'}')`];
                            if (node.description) lines.push(`${mIndent}->description('${node.description}')`);
                            if (node.collapsible) lines.push(`${mIndent}->collapsible()`);
                            if (node.columns && parseInt(node.columns) > 1) lines.push(`${mIndent}->columns(${node.columns})`);

                            if (node.children && node.children.length) {
                                const childCode = renderNodeList(node.children, level + 2);
                                lines.push(`${mIndent}->schema([\n${childCode}\n${mIndent}])`);
                            } else {
                                lines.push(`${mIndent}->schema([])`);
                            }
                            return lines.join('\n');
                        }

                        if (node.type === 'Grid') {
                            const lines = [`${indent}${cls}::make(${node.columns || 2})`];
                            if (node.children && node.children.length) {
                                const childCode = renderNodeList(node.children, level + 2);
                                lines.push(`${mIndent}->schema([\n${childCode}\n${mIndent}])`);
                            } else {
                                lines.push(`${mIndent}->schema([])`);
                            }
                            return lines.join('\n');
                        }

                        // Standard inputs
                        const lines = [`${indent}${cls}::make('${node.name || 'field'}')`];
                        lines.push(...renderModifiers(node, level));

                        if (node.type === 'TextInput') {
                            if (node.subType === 'email') lines.push(`${mIndent}->email()`);
                            if (node.subType === 'numeric') lines.push(`${mIndent}->numeric()`);
                            if (node.subType === 'password') {
                                lines.push(`${mIndent}->password()`);
                                lines.push(`${mIndent}->revealable()`);
                            }
                            if (node.subType === 'tel') lines.push(`${mIndent}->tel()`);
                            if (node.subType === 'url') lines.push(`${mIndent}->url()`);
                        }

                        if (node.type === 'Textarea' && node.rows) {
                            lines.push(`${mIndent}->rows(${node.rows})`);
                        }

                        if ((node.type === 'Select' || node.type === 'Radio') && node.options && node.options.length) {
                            const optIndent = indentUnit.repeat(level + 2);
                            const optEntries = node.options.map(o => `${optIndent}'${o.value}' => '${o.label}'`).join(',\n');
                            lines.push(`${mIndent}->options([\n${optEntries},\n${mIndent}])`);
                        }

                        return lines.join('\n');
                    };

                    const renderNodeList = (items, level) => {
                        return items.map(n => renderSingleNode(n, level)).join(',\n\n');
                    };

                    const baseLevel = settings.wrapInMethod ? 2 : 1;
                    const schemaCode = renderNodeList(nodes, baseLevel);

                    if (!settings.wrapInMethod) {
                        let header = '';
                        if (!isFqcn) {
                            const typesSet = new Set();
                            collectTypes(nodes, typesSet);
                            const sorted = Array.from(typesSet).sort();
                            header = sorted.map(t => `use Filament\\Forms\\Components\\${t};`).join('\n') + '\n\n';
                        }
                        return header + '[\n' + schemaCode + '\n]';
                    }

                    // Wrap in full method
                    let methodHeader = "use Filament\\Forms;\nuse Filament\\Forms\\Form;\n";
                    if (!isFqcn) {
                        const typesSet = new Set();
                        collectTypes(nodes, typesSet);
                        const sorted = Array.from(typesSet).sort();
                        methodHeader += sorted.map(t => `use Filament\\Forms\\Components\\${t};`).join('\n') + '\n';
                    }

                    return `${methodHeader}
public static function form(Form \$form): Form
{
    return \$form
        ->schema([
${schemaCode}
        ]);
}`;
                }
            };
        }
    </script>
</x-filament-panels::page>

