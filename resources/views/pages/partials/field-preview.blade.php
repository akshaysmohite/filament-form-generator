<div class="min-w-0 flex-1">
    <template x-if="node.type !== 'Checkbox' && node.type !== 'Toggle'">
        <div class="mb-2 flex items-center gap-1 text-sm font-medium text-gray-950 dark:text-gray-100">
            <span x-text="node.label || node.name || 'Field'"></span>
            <span x-show="node.required" class="text-danger-600">*</span>
        </div>
    </template>

    <template x-if="node.type === 'TextInput' || !node.type">
        <div class="flex h-10 items-center rounded-lg border border-gray-200 bg-white px-3 text-sm text-gray-400 dark:border-gray-700 dark:bg-gray-900">
            <span x-text="node.placeholder || (node.subType === 'password' ? '••••••••' : (node.subType === 'email' ? 'name@example.com' : 'Enter ' + (node.label || 'text')))"></span>
        </div>
    </template>

    <template x-if="node.type === 'Textarea'">
        <div class="fg-area flex items-start rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-400 dark:border-gray-700 dark:bg-gray-900">
            <span x-text="node.placeholder || 'Enter text here...'"></span>
        </div>
    </template>

    <template x-if="node.type === 'Select'">
        <div class="flex h-10 items-center justify-between rounded-lg border border-gray-200 bg-white px-3 text-sm text-gray-400 dark:border-gray-700 dark:bg-gray-900">
            <span x-text="node.placeholder || ((node.options && node.options[0]) ? node.options[0].label : 'Select an option')"></span>
            <svg class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
        </div>
    </template>

    <template x-if="node.type === 'DatePicker'">
        <div class="flex h-10 items-center justify-between rounded-lg border border-gray-200 bg-white px-3 text-sm text-gray-400 dark:border-gray-700 dark:bg-gray-900">
            <span x-text="node.placeholder || 'Select a date'"></span>
            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
    </template>

    <template x-if="node.type === 'Toggle'">
        <div class="flex items-center justify-between gap-3">
            <span class="text-sm font-medium text-gray-950 dark:text-gray-100">
                <span x-text="node.label || node.name || 'Toggle'"></span>
                <span x-show="node.required" class="text-danger-600">*</span>
            </span>
            <span class="relative inline-flex h-6 w-11 shrink-0 rounded-full bg-primary-600">
                <span class="fg-switch-knob"></span>
            </span>
        </div>
    </template>

    <template x-if="node.type === 'Checkbox'">
        <div>
            <div class="mb-2 text-sm font-medium text-gray-950 dark:text-gray-100">
                <span x-text="node.label || node.name || 'Checkbox'"></span>
                <span x-show="node.required" class="text-danger-600">*</span>
            </div>
            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                <span class="flex h-4 w-4 items-center justify-center rounded border border-gray-300 bg-white text-primary-600 dark:border-gray-600 dark:bg-gray-900">
                    <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.7 5.3a1 1 0 010 1.4l-7.2 7.2a1 1 0 01-1.4 0L3.3 9.1a1 1 0 011.4-1.4l3.1 3.1 6.5-6.5a1 1 0 011.4 0z" clip-rule="evenodd"/></svg>
                </span>
                <span x-text="node.helperText || 'Checked'"></span>
            </div>
        </div>
    </template>

    <template x-if="node.type === 'Radio'">
        <div class="flex flex-col gap-2">
            <template x-for="(opt, optIndex) in (node.options || [{label: 'Option 1'}, {label: 'Option 2'}])" :key="optIndex">
                <div class="flex items-center gap-2 text-sm text-gray-950 dark:text-gray-200">
                    <span class="flex h-4 w-4 items-center justify-center rounded-full border border-gray-300 bg-white dark:border-gray-600 dark:bg-gray-900">
                        <span x-show="optIndex === 0" class="h-2 w-2 rounded-full bg-primary-600"></span>
                    </span>
                    <span x-text="opt.label"></span>
                </div>
            </template>
        </div>
    </template>

    <p x-show="node.helperText && node.type !== 'Checkbox'" class="mt-1.5 text-xs text-gray-400" x-text="node.helperText"></p>
</div>
