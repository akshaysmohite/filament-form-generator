<aside class="fg-side border-b border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-950">
    <h3 class="mb-1 text-sm font-semibold text-gray-950 dark:text-gray-100">Form Components</h3>
    <p class="mb-4 text-xs leading-5 text-gray-500">Click or drag a component into the form.</p>

    <div class="fg-tiles">
        <template x-for="item in layoutPalette.concat(fieldPalette)" :key="item.label">
            <div
                draggable="true"
                @dragstart="onDragStart($event, item.type, item.subType || 'text')"
                @click="addComponent(item.type, item.subType || 'text')"
                class="fg-tile flex select-none flex-col items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white px-2 py-4 text-center dark:border-gray-700 dark:bg-gray-900"
            >
                <span class="text-gray-500" x-html="item.iconSvg"></span>
                <span class="text-xs font-medium text-gray-950 dark:text-gray-200" x-text="item.label"></span>
            </div>
        </template>
    </div>
</aside>

<section
    @dragover.prevent="dragOverCanvas = true"
    @dragleave.prevent="dragOverCanvas = false"
    @drop.prevent="onDropOnCanvas($event)"
    :class="dragOverCanvas ? 'bg-primary-50/40 dark:bg-primary-950/20' : 'bg-white dark:bg-gray-900'"
    class="fg-canvas transition"
>
    <div class="mb-4">
        <h3 class="text-base font-semibold text-gray-950 dark:text-gray-100">Form preview</h3>
        <p class="mt-1 text-sm text-gray-500">Drag components from the toolbox. Click a field to edit it on the right.</p>
    </div>

    <div
        x-show="nodes.length === 0"
        class="fg-drop flex flex-col items-center justify-center rounded-xl border border-gray-300 px-6 py-8 text-center"
    >
        <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-400">
            <x-filament::icon icon="heroicon-o-cursor-arrow-rays" class="h-6 w-6" />
        </div>
        <p class="text-sm font-medium text-gray-950 dark:text-gray-200">This form is empty</p>
        <p class="mt-1 max-w-xs text-sm text-gray-500">Add a component from the toolbox on the left.</p>
    </div>

    <div class="flex flex-col gap-3">
        <template x-for="node in nodes" :key="node.id">
            <div
                draggable="true"
                @dragstart="onNodeReorderStart($event, node.id)"
                @dragover.prevent
                @drop.stop.prevent="onNodeReorderDrop($event, node.id)"
            >
                <template x-if="node.type === 'Section' || node.type === 'Grid'">
                    <div
                        @click="selectNode(node.id)"
                        :class="selectedNodeId === node.id ? 'fg-on' : 'border-gray-200 dark:border-gray-700'"
                        class="overflow-hidden rounded-xl border bg-white dark:bg-gray-900"
                    >
                        <div class="flex items-start gap-3 border-b border-gray-100 px-3 py-3 dark:border-gray-800">
                            <span class="fg-grip mt-0.5 shrink-0">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><circle cx="7" cy="4.5" r="1.15"></circle><circle cx="13" cy="4.5" r="1.15"></circle><circle cx="7" cy="10" r="1.15"></circle><circle cx="13" cy="10" r="1.15"></circle><circle cx="7" cy="15.5" r="1.15"></circle><circle cx="13" cy="15.5" r="1.15"></circle></svg>
                            </span>
                            <div class="min-w-0 flex-1">
                                <div class="text-sm font-semibold text-gray-950 dark:text-gray-100" x-text="node.type === 'Grid' ? ((node.columns || 2) + ' columns') : (node.title || 'Section')"></div>
                                <div x-show="node.description" class="mt-0.5 text-xs text-gray-500" x-text="node.description"></div>
                            </div>
                            <div class="fg-field-actions flex shrink-0 items-center gap-0.5">
                                <button type="button" @click.stop="duplicateNode(node.id)" class="rounded-md p-1" title="Duplicate">
                                    <x-filament::icon icon="heroicon-o-document-duplicate" class="h-4 w-4" />
                                </button>
                                <button type="button" @click.stop="deleteNode(node.id)" class="fg-delete-btn rounded-md p-1" title="Delete">
                                    <x-filament::icon icon="heroicon-o-trash" class="h-4 w-4" />
                                </button>
                            </div>
                        </div>

                        <div
                            @dragover.prevent
                            @drop.stop.prevent="onDropInsideContainer($event, node.id)"
                            class="grid gap-3 bg-gray-50/70 p-3 dark:bg-gray-950/40"
                            :style="`grid-template-columns: repeat(${node.columns || (node.type === 'Grid' ? 2 : 1)}, minmax(0, 1fr))`"
                        >
                            <div x-show="!node.children || node.children.length === 0" class="fg-drop col-span-full rounded-xl border border-gray-300 py-8 text-center text-sm text-gray-400">
                                Drop fields here
                            </div>

                            <template x-for="child in node.children" :key="child.id">
                                <div x-data="{ node: child }" class="min-w-0" :style="child.columnSpan === 'full' ? 'grid-column: 1 / -1' : ''">
                                    @include('filament-form-generator::pages.partials.field-row')
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <template x-if="node.type !== 'Section' && node.type !== 'Grid'">
                    <div>
                        @include('filament-form-generator::pages.partials.field-row')
                    </div>
                </template>
            </div>
        </template>
    </div>
</section>
