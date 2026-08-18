<div class="space-y-3">
    <flux:label>{{ __('Gallery images') }}</flux:label>

    @if (!empty($images))
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
            @foreach ($images as $index => $image)
                <div class="relative group aspect-square">
                    <img src="{{ $image['url'] }}" alt="{{ $image['alt'] ?? '' }}"
                        class="w-full h-full object-cover rounded-md">
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-colors rounded-md flex items-center justify-center opacity-0 group-hover:opacity-100">
                        <flux:button type="button" variant="ghost" icon="trash"
                            wire:click="removeImage({{ $index }})"
                            class="text-white"
                            wire:loading.attr="disabled">
                            {{ __('Remove') }}
                        </flux:button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="space-y-2">
        <label class="block">
            <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                {{ __('Upload images') }}
                @if ($maxFiles > 0)
                    <span class="text-xs text-zinc-500">({{ count($images) }}/{{ $maxFiles }})</span>
                @endif
            </span>
            <input type="file" wire:model="uploads" accept="image/png,image/jpeg,image/webp"
                wire:change="uploadImages"
                multiple
                class="mt-1 block w-full text-sm text-zinc-500 dark:text-zinc-400
                       file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0
                       file:text-sm file:font-semibold file:bg-zinc-100 file:text-zinc-700
                       hover:file:bg-zinc-200 dark:file:bg-zinc-700 dark:file:text-zinc-200
                       dark:hover:file:bg-zinc-600 file:cursor-pointer cursor-pointer"
                {{ $loading || count($images) >= $maxFiles ? 'disabled' : '' }} />
        </label>

        @error('uploads')
            <p class="text-xs text-red-500">{{ $message }}</p>
        @enderror

        @if (!empty($uploads))
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                @foreach ($uploads as $upload)
                    <div class="aspect-square">
                        <img src="{{ $upload->temporaryUrl() }}" alt="{{ __('Preview') }}"
                            class="w-full h-full object-cover rounded-md">
                    </div>
                @endforeach
            </div>
        @endif

        @if ($loading)
            <div class="flex items-center gap-2 text-sm text-zinc-500">
                <flux:icon name="arrow-path" class="animate-spin" />
                <span>{{ __('Processing images...') }}</span>
            </div>
        @endif
    </div>
</div>
