<section class="w-full space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Tags') }}</flux:heading>
            <flux:subheading>{{ __('manage_tags') }}</flux:subheading>
        </div>

        <div>
            <flux:button variant="primary" :href="route('dashboard.tags.create')" wire:navigate icon="plus"
                data-test="new-tag">
                {{ __('New Tag') }}
            </flux:button>
        </div>
    </div>

    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        <flux:input wire:model.live.debounce.300ms="search" :label="__('Search')" type="search"
            placeholder="{{ __('Search tags...') }}" />
    </div>

    <flux:table :paginate="$tags">
        <flux:table.columns>
            <flux:table.column>{{ __('Name') }}</flux:table.column>
            <flux:table.column>{{ __('Slug') }}</flux:table.column>
            <flux:table.column>{{ __('Posts') }}</flux:table.column>
            <flux:table.column align="end">{{ __('Actions') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($tags as $tag)
                <flux:table.row :key="$tag->id">
                    <flux:table.cell variant="strong">{{ $tag->name }}</flux:table.cell>
                    <flux:table.cell>{{ $tag->slug }}</flux:table.cell>
                    <flux:table.cell>{{ $tag->posts_count ?? 0 }}</flux:table.cell>

                    <flux:table.cell align="end">
                        <div class="flex justify-end gap-2">
                            <flux:button icon="pencil" :href="route('dashboard.tags.edit', $tag)" wire:navigate
                                variant="ghost" size="sm">
                                {{ __('Edit') }}
                            </flux:button>

                            <flux:button icon="trash" variant="ghost" size="sm"
                                wire:click="$set('confirmingDeletion', {{ $tag->id }})">
                                {{ __('Delete') }}
                            </flux:button>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="4" class="text-center text-zinc-500">
                        {{ __('No tags found.') }}
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <flux:modal name="confirm-tag-deletion" wire:model="confirmingDeletion" focusable class="max-w-lg">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Delete this tag?') }}</flux:heading>
                <flux:subheading>
                    {{ __('The tag will be removed from the system. Posts using this tag will not be affected.') }}
                </flux:subheading>
            </div>

            <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                <flux:modal.close>
                    <flux:button variant="filled">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>

                <flux:button variant="danger" wire:click="delete({{ (int) $confirmingDeletion }})"
                    data-test="confirm-delete-tag">
                    {{ __('Delete tag') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</section>
