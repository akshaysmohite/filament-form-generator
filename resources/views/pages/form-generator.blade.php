<x-filament-panels::page>
    <div
        wire:ignore
        x-load
        x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('form-generator', 'akshay/filament-form-generator') }}"
        x-data="formGenerator()"
        class="flex flex-col gap-4 font-sans text-gray-950 dark:text-gray-100"
    >
        <style>
            .fg-builder { display: grid; grid-template-columns: 1fr; }
            @media (min-width: 1180px) {
                .fg-builder { grid-template-columns: 248px minmax(0, 1fr) 292px; }
                .fg-side { border-right: 1px solid #e5e7eb; border-bottom: 0; }
                .fg-inspector { border-left: 1px solid #e5e7eb; border-top: 0; }
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
                    x-model="jsonDraft" 
                    rows="16" 
                    class="w-full bg-transparent border-0 focus:ring-0 text-emerald-400 font-mono text-xs resize-y"
                ></textarea>
            </div>
        </div>
    </div>

</x-filament-panels::page>

