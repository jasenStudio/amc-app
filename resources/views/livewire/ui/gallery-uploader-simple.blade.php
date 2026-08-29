<div class="relative space-y-2">
    <label class="block">
        <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
            {{ __('Upload images') }}
        </span>
        <input type="file" wire:model="uploads" accept="image/png,image/jpeg,image/webp" multiple
            wire:loading.attr="disabled" wire:target="uploads"
            class="mt-1 block w-full text-sm text-zinc-500 dark:text-zinc-400
                   file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0
                   file:text-sm file:font-semibold file:bg-zinc-100 file:text-zinc-700
                   hover:file:bg-zinc-200 dark:file:bg-zinc-700 dark:file:text-zinc-200
                   dark:hover:file:bg-zinc-600 file:cursor-pointer cursor-pointer" />
    </label>

    @php
        $uploadErrors = collect($errors->get('uploads'))
            ->merge(collect($errors->get('uploads.*'))->flatten())
            ->filter()
            ->unique()
            ->values();
    @endphp

    @if ($uploadErrors->isNotEmpty())
        <div class="space-y-1">
            @foreach ($uploadErrors as $message)
                <p class="text-xs text-red-500">{{ $message }}</p>
            @endforeach
        </div>
    @endif

    <div wire:loading.class.remove="hidden" wire:target="uploads"
        class="hidden absolute inset-0 bg-white/70 dark:bg-zinc-900/70 rounded-md z-10 flex flex-col items-center justify-center gap-2">
        <flux:icon name="arrow-path" class="animate-spin text-zinc-500 dark:text-zinc-400 size-8" />
        <span class="text-sm font-medium text-zinc-600 dark:text-zinc-300">{{ __('Processing images...') }}</span>
    </div>
</div>
