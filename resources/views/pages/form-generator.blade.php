<x-filament-panels::page>
    <div wire:ignore>
    <div
        x-load
        x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('form-generator', 'akshay/filament-form-generator') }}"
        x-data="formGenerator()"
        x-ref="workspace"
        x-bind:class="{ 'fg-immersive': immersive }"
        @keydown.escape.window="if (immersive) { immersive = false }"
        class="fg-workspace flex flex-col gap-4 font-sans text-gray-950 dark:text-gray-100"
    >
        <style>
            .fg-builder { display: grid; grid-template-columns: 1fr; }
            @media (min-width: 1180px) {
                .fg-builder { grid-template-columns: 248px minmax(0, 1fr) 292px; }
                .fg-side { border-right: 1px solid #e5e7eb; border-bottom: 0; }
                .fg-inspector { border-left: 1px solid #e5e7eb; border-top: 0; }
            }
            .fg-workspace:fullscreen,
            .fg-workspace:-webkit-full-screen {
                box-sizing: border-box;
                width: 100%;
                height: 100%;
                padding: 1rem 1.25rem 1.25rem;
                overflow: auto;
                background-color: #f3f4f6;
            }
            .dark .fg-workspace:fullscreen,
            .dark .fg-workspace:-webkit-full-screen {
                background-color: #030712;
            }
            .fg-workspace:fullscreen .fg-builder,
            .fg-workspace:-webkit-full-screen .fg-builder {
                min-height: calc(100vh - 7.5rem);
            }
            .fg-workspace:fullscreen .fg-canvas,
            .fg-workspace:-webkit-full-screen .fg-canvas,
            .fg-immersive .fg-canvas {
                min-height: calc(100vh - 10rem);
            }
            @media (min-width: 1180px) {
                .fg-workspace:fullscreen .fg-builder,
                .fg-workspace:-webkit-full-screen .fg-builder,
                .fg-immersive .fg-builder {
                    grid-template-columns: 272px minmax(0, 1fr) 320px;
                }
            }
            .fg-immersive {
                position: fixed;
                inset: 0;
                z-index: 120;
                padding: 1rem 1.25rem 1.25rem;
                overflow: auto;
                background-color: #f3f4f6;
            }
            .dark .fg-immersive {
                background-color: #030712;
            }
            .fg-canvas { min-height: 36rem; padding: 1.25rem 1.5rem; }
            .fg-tiles { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; }
            .fg-tile { cursor: grab; }
            .fg-tile:hover { border-color: #f59e0b; }
            .fg-grip { cursor: grab; color: #d1d5db; }
            .fg-on { border-color: #f59e0b !important; box-shadow: 0 0 0 1px #f59e0b; }
            .fg-drop { border-style: dashed; }
            .fg-area { min-height: 4.75rem; }
            .fg-switch-knob { position: absolute; top: 2px; right: 2px; height: 1.25rem; width: 1.25rem; border-radius: 9999px; background: #fff; }
            .fg-code-panel {
                min-height: 12rem;
                padding: 1rem;
                border-radius: 0.75rem;
                border: 1px solid #374151;
                background-color: #111827;
                overflow-x: auto;
            }
            .fg-code-panel pre {
                margin: 0;
                font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace;
                font-size: 0.75rem;
                line-height: 1.6;
                white-space: pre-wrap;
                word-break: break-word;
            }
            .fg-code-panel code,
            .fg-code-panel pre {
                color: #a7f3d0;
            }
            .fg-code-panel .fg-hint {
                color: #9ca3af;
                font-size: 0.75rem;
            }
            .fg-json-panel {
                padding: 1rem;
                border-radius: 0.75rem;
                border: 1px solid #374151;
                background-color: #111827;
            }
            .fg-json-panel textarea {
                width: 100%;
                min-height: 16rem;
                border: 0;
                background: transparent;
                color: #a7f3d0;
                font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace;
                font-size: 0.75rem;
                line-height: 1.6;
                resize: vertical;
            }
            .fg-json-panel textarea:focus {
                outline: none;
                box-shadow: none;
            }
            .fg-field-row {
                align-items: flex-start;
            }
            .fg-field-actions button {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border: 0;
                background: transparent;
                cursor: pointer;
                color: #6b7280;
            }
            .fg-field-actions button:hover {
                background-color: #f3f4f6;
                color: #374151;
            }
            .fg-delete-btn {
                color: #ef4444 !important;
            }
            .fg-delete-btn:hover {
                background-color: #fef2f2 !important;
                color: #dc2626 !important;
            }
        </style>
        {{-- Top Toolbar --}}
        <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-gray-200 bg-white p-3 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="inline-flex items-center gap-1 rounded-lg bg-gray-100 p-1 dark:bg-gray-800">
                <button 
                    type="button"
                    @click="activeTab = 'canvas'"
                    :class="activeTab === 'canvas' ? 'bg-white text-gray-900 shadow-sm dark:bg-gray-900 dark:text-white' : 'text-gray-500 hover:text-gray-800 dark:text-gray-400'"
                    class="flex items-center gap-2 rounded-md px-3 py-1.5 text-sm font-medium transition"
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
                    @click="showCodeTab()"
                    :class="activeTab === 'code' ? 'bg-white text-gray-900 shadow-sm dark:bg-gray-900 dark:text-white' : 'text-gray-500 hover:text-gray-800 dark:text-gray-400'"
                    class="flex items-center gap-2 rounded-md px-3 py-1.5 text-sm font-medium transition"
                >
                    <x-filament::icon icon="heroicon-o-code-bracket" class="w-4 h-4" />
                    <span>Generated PHP Code</span>
                </button>

                <button 
                    type="button"
                    @click="showJsonTab()"
                    :class="activeTab === 'json' ? 'bg-white text-gray-900 shadow-sm dark:bg-gray-900 dark:text-white' : 'text-gray-500 hover:text-gray-800 dark:text-gray-400'"
                    class="flex items-center gap-2 rounded-md px-3 py-1.5 text-sm font-medium transition"
                >
                    <x-filament::icon icon="heroicon-o-document-text" class="w-4 h-4" />
                    <span>JSON Schema</span>
                </button>
            </div>

            <div class="flex items-center gap-2">
                <button
                    type="button"
                    @click="toggleFullscreen()"
                    class="flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                    :title="isFullscreen ? 'Exit full screen (Esc)' : 'Enter full screen'"
                >
                    <span x-show="!isFullscreen" class="inline-flex">
                        <x-filament::icon icon="heroicon-o-arrows-pointing-out" class="h-4 w-4" />
                    </span>
                    <span x-show="isFullscreen" class="inline-flex" x-cloak>
                        <x-filament::icon icon="heroicon-o-arrows-pointing-in" class="h-4 w-4" />
                    </span>
                    <span x-text="isFullscreen ? 'Exit full screen' : 'Full screen'"></span>
                </button>

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
        <div x-show="activeTab === 'canvas'" class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="fg-builder">
                @include('filament-form-generator::pages.partials.builder-columns')

                {{-- Right: Property Inspector (3 columns) --}}
            <div class="fg-inspector flex flex-col gap-4 border-t border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-950">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-2">
                    <h3 class="text-sm font-semibold text-gray-950 dark:text-gray-100">
                        Field settings
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
                <div x-show="selectedNode" :key="selectedNodeId" class="flex flex-col gap-3 text-xs">
                    
                    {{-- Section / Grid specific settings --}}
                    <template x-if="selectedNode && selectedNode.type === 'Section'">
                        <div class="flex flex-col gap-3">
                            <div>
                                <label class="block font-semibold mb-1 text-gray-700 dark:text-gray-300">Section Title</label>
                                <input 
                                    type="text" 
                                    x-model="selectedNode.title" 
                                    class="w-full text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-3 py-2 focus:border-primary-500 focus:ring-1 focus:ring-primary-500" 
                                />
                            </div>
                            <div>
                                <label class="block font-semibold mb-1 text-gray-700 dark:text-gray-300">Description</label>
                                <input 
                                    type="text" 
                                    x-model="selectedNode.description" 
                                    class="w-full text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-3 py-2 focus:border-primary-500 focus:ring-1 focus:ring-primary-500" 
                                />
                            </div>
                            <div>
                                <label class="block font-semibold mb-1 text-gray-700 dark:text-gray-300">Inner Columns</label>
                                <select 
                                    x-model="selectedNode.columns" 
                                    class="w-full text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-3 py-2"
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
                                    class="w-full text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-3 py-2"
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
                                    class="w-full text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-3 py-2 focus:border-primary-500 focus:ring-1 focus:ring-primary-500" 
                                />
                            </div>

                            {{-- Placeholder --}}
                            <div>
                                <label class="block font-semibold mb-1 text-gray-700 dark:text-gray-300">Placeholder</label>
                                <input 
                                    type="text" 
                                    x-model="selectedNode.placeholder" 
                                    class="w-full text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-3 py-2" 
                                />
                            </div>

                            {{-- Helper Text --}}
                            <div>
                                <label class="block font-semibold mb-1 text-gray-700 dark:text-gray-300">Helper Text</label>
                                <input 
                                    type="text" 
                                    x-model="selectedNode.helperText" 
                                    class="w-full text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-3 py-2" 
                                />
                            </div>

                            {{-- Subtype (for TextInput) --}}
                            <template x-if="selectedNode.type === 'TextInput'">
                                <div>
                                    <label class="block font-semibold mb-1 text-gray-700 dark:text-gray-300">Input Type</label>
                                    <select 
                                        x-model="selectedNode.subType" 
                                        class="w-full text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-3 py-2"
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
                                    class="w-full text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-3 py-2"
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

            <div class="fg-code-panel">
                <p x-show="codeLoading" class="fg-hint">Generating PHP code…</p>
                <p x-show="!codeLoading && !generatedCode" class="fg-hint">
                    Add fields on the Visual Canvas tab, then open this tab again.
                </p>
                <pre x-show="!codeLoading && generatedCode"><code x-text="generatedCode"></code></pre>
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

            <div class="fg-json-panel">
                <textarea x-model="jsonDraft" rows="16"></textarea>
            </div>
        </div>
    </div>
    </div>

</x-filament-panels::page>

