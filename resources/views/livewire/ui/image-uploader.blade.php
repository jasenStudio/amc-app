<div class="space-y-3">
    <flux:label>{{ __('Cover image') }}</flux:label>

    @if ($existingThumbUrl && !$upload)
        <div class="relative group">
            <img src="{{ $existingThumbUrl }}" alt="" class="w-full rounded-md object-cover aspect-video"
                data-test="current-cover">
            <div
                class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-colors rounded-md flex items-center justify-center opacity-0 group-hover:opacity-100">
                <flux:button type="button" variant="ghost" icon="trash" wire:click="removeImage"
                    data-test="remove-cover" class="text-white" wire:loading.attr="disabled">
                    {{ __('Remove cover') }}
                </flux:button>
            </div>
        </div>
    @endif

    <div class="space-y-2">
        <label class="block">
            <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('Upload new') }}</span>
            <input type="file" wire:model="upload" accept="image/png,image/jpeg,image/webp"
                wire:loading.attr="disabled" wire:target="upload"
                class="mt-1 block w-full text-sm text-zinc-500 dark:text-zinc-400
                       file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0
                       file:text-sm file:font-semibold file:bg-zinc-100 file:text-zinc-700
                       hover:file:bg-zinc-200 dark:file:bg-zinc-700 dark:file:text-zinc-200
                       dark:hover:file:bg-zinc-600 file:cursor-pointer cursor-pointer"
                data-test="cover-upload" />
        </label>

        @error('upload')
            <flux:error name="upload" />
        @enderror

        @if ($upload)
            <div class="relative rounded-md overflow-hidden">
                <img src="{{ $upload->temporaryUrl() }}" alt="{{ __('Preview') }}"
                    class="w-full object-cover aspect-video" data-test="cover-preview">

                <div wire:loading.class.remove="hidden" wire:target="upload"
                    class="hidden absolute inset-0 bg-black/50 rounded-md flex flex-col items-center justify-center gap-2">
                    <flux:icon name="arrow-path" class="animate-spin text-white size-8" />
                    <span class="text-sm font-medium text-white">{{ __('Uploading...') }}</span>
                </div>
            </div>
        @endif
    </div>
</div>
