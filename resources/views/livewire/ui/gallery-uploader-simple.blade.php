<div class="space-y-2">
    <label class="block">
        <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
            {{ __('Upload images') }}
        </span>
        <input type="file" wire:model="uploads" accept="image/png,image/jpeg,image/webp"
            multiple
            class="mt-1 block w-full text-sm text-zinc-500 dark:text-zinc-400
                   file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0
                   file:text-sm file:font-semibold file:bg-zinc-100 file:text-zinc-700
                   hover:file:bg-zinc-200 dark:file:bg-zinc-700 dark:file:text-zinc-200
                   dark:hover:file:bg-zinc-600 file:cursor-pointer cursor-pointer"
            {{ $loading ? 'disabled' : '' }} />
    </label>

    @error('uploads')
        <p class="text-xs text-red-500">{{ $message }}</p>
    @enderror

    @if ($loading)
        <div class="flex items-center gap-2 text-sm text-zinc-500">
            <flux:icon name="arrow-path" class="animate-spin" />
            <span>{{ __('Processing images...') }}</span>
        </div>
    @endif
</div>
