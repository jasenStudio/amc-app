@props(['allTags'])

<flux:fieldset :legend="__('Tags')">
    <div class="space-y-3">
        <div class="flex flex-wrap gap-2">
            @forelse ($allTags as $tag)
                <label
                    class="inline-flex items-center gap-2 rounded-md border border-zinc-200 bg-white px-2 py-1 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                    <input type="checkbox" value="{{ $tag->id }}" wire:model="tag_ids"
                        class="rounded border-zinc-300 text-amc-blue focus:ring-amc-blue"
                        data-test="tag-{{ $tag->id }}" />
                    <span>{{ $tag->name }}</span>
                </label>
            @empty
                <p class="text-sm text-zinc-500">{{ __('No tags yet. Add the first one below.') }}</p>
            @endforelse
        </div>

        <div class="flex items-end gap-2">
            <flux:input wire:model="new_tag_name" :label="__('New tag')"
                :placeholder="__('Tag name')" class="flex-1" data-test="new-tag-name" />
        </div>
    </div>
</flux:fieldset>
