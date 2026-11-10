<div
    @click.stop="selectNode(node.id)"
    :class="selectedNodeId === node.id ? 'fg-on' : 'border-gray-200 dark:border-gray-700'"
    class="fg-field-row group flex items-start gap-3 rounded-xl border bg-white p-3 dark:bg-gray-900"
>
    <span class="fg-grip mt-0.5 shrink-0" title="Drag to reorder">
        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <circle cx="7" cy="4.5" r="1.15"></circle>
            <circle cx="13" cy="4.5" r="1.15"></circle>
            <circle cx="7" cy="10" r="1.15"></circle>
            <circle cx="13" cy="10" r="1.15"></circle>
            <circle cx="7" cy="15.5" r="1.15"></circle>
            <circle cx="13" cy="15.5" r="1.15"></circle>
        </svg>
    </span>

    @include('filament-form-generator::pages.partials.field-preview')

    <div class="fg-field-actions flex shrink-0 items-center gap-0.5">
        <button type="button" @click.stop="duplicateNode(node.id)" class="rounded-md p-1 text-gray-500" title="Duplicate">
            <x-filament::icon icon="heroicon-o-document-duplicate" class="h-4 w-4" />
        </button>
        <button type="button" @click.stop="deleteNode(node.id)" class="fg-delete-btn rounded-md p-1" title="Delete">
            <x-filament::icon icon="heroicon-o-trash" class="h-4 w-4" />
        </button>
    </div>
</div>
