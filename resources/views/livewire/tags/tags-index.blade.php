<section class="w-full space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Tags') }}</flux:heading>
            <flux:subheading>{{ __('Manage blog tags.') }}</flux:subheading>
        </div>
    </div>

    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        <flux:input wire:model.live.debounce.300ms="search" :label="__('Search')" type="search"
            placeholder="{{ __('Tag name…') }}" />
    </div>

    <flux:table :paginate="$tags">
        <flux:table.columns>
            <flux:table.column>{{ __('Name') }}</flux:table.column>
            <flux:table.column>{{ __('Slug') }}</flux:table.column>
            <flux:table.column>{{ __('Posts') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($tags as $tag)
                <flux:table.row :key="$tag->id">
                    <flux:table.cell variant="strong">{{ $tag->name }}</flux:table.cell>
                    <flux:table.cell>{{ $tag->slug }}</flux:table.cell>
                    <flux:table.cell>{{ $tag->posts_count ?? 0 }}</flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="3" class="text-center text-zinc-500">
                        {{ __('No tags found.') }}
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</section>
