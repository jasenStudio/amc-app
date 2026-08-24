<section class="w-full space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('services') }}</flux:heading>
            <flux:subheading>{{ __('manage_services_offered') }}</flux:subheading>
        </div>
        <flux:button variant="primary" :href="route('dashboard.services.create')" wire:navigate icon="plus" data-test="new-service">
            {{ __('new_service') }}
        </flux:button>
    </div>

    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        <flux:input wire:model.live.debounce.300ms="search" :label="__('search')" type="search"
            placeholder="{{ __('search_services_placeholder') }}" />

        <flux:select wire:model.live="status" :label="__('status')">
            <flux:select.option value="">{{ __('all_statuses') }}</flux:select.option>
            @foreach ($statuses as $s)
                <flux:select.option :value="$s->value">{{ $s->label() }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:select wire:model.live="featured" :label="__('featured')">
            <flux:select.option value="">{{ __('any') }}</flux:select.option>
            <flux:select.option value="yes">{{ __('featured_only') }}</flux:select.option>
            <flux:select.option value="no">{{ __('not_featured') }}</flux:select.option>
        </flux:select>
    </div>

    <flux:table :paginate="$services">
        <flux:table.columns>
            <flux:table.column>{{ __('cover') }}</flux:table.column>
            <flux:table.column>{{ __('title') }}</flux:table.column>
            <flux:table.column>{{ __('status') }}</flux:table.column>
            <flux:table.column align="center">{{ __('featured') }}</flux:table.column>
            <flux:table.column align="end">{{ __('order') }}</flux:table.column>
            <flux:table.column align="end">{{ __('actions') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($services as $service)
                <flux:table.row :key="$service->id">
                    <flux:table.cell>
                        @if ($service->coverImage)
                            <img src="{{ \App\Support\ImageUrl::public($service->coverImage->image_path) }}" alt=""
                                class="size-10 rounded object-cover">
                        @else
                            <div class="size-10 rounded bg-zinc-100 dark:bg-zinc-700"></div>
                        @endif
                    </flux:table.cell>

                    <flux:table.cell variant="strong">
                        <div class="max-w-xs truncate">{{ $service->title }}</div>
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:badge :color="$service->status->value === 'active' ? 'green' : 'zinc'" size="sm">
                            {{ $service->status->label() }}
                        </flux:badge>
                    </flux:table.cell>

                    <flux:table.cell align="center">
                        @if ($service->featured)
                            <flux:icon name="star" class="size-4 text-amber-500 inline" />
                        @else
                            <flux:icon name="star" class="size-4 text-zinc-300 inline" variant="outline" />
                        @endif
                    </flux:table.cell>

                    <flux:table.cell align="end">{{ $service->order }}</flux:table.cell>

                    <flux:table.cell align="end">
                        <div class="flex justify-end gap-2">
                            @if ($this->canUpdate($service))
                                <flux:button size="sm" :href="route('dashboard.services.edit', $service)" wire:navigate
                                    variant="ghost" icon="pencil" data-test="edit-service-{{ $service->id }}">
                                    {{ __('edit') }}
                                </flux:button>
                            @endif
                            @if ($this->canDelete($service))
                                <flux:button size="sm" variant="ghost" icon="trash"
                                    data-test="delete-service-{{ $service->id }}"
                                    wire:click="$set('confirmingDeletion', {{ $service->id }})">
                                    {{ __('delete') }}
                                </flux:button>
                            @endif
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="6" class="text-center text-zinc-500">
                        {{ __('no_services_found') }}
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <flux:modal name="confirm-service-deletion" wire:model="confirmingDeletion" focusable class="max-w-lg">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('delete_this_service') }}</flux:heading>
                <flux:subheading>
                    {{ __('delete_service_warning') }}
                </flux:subheading>
            </div>

            <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                <flux:modal.close>
                    <flux:button variant="filled">{{ __('cancel') }}</flux:button>
                </flux:modal.close>

                <flux:button variant="danger" wire:click="delete({{ (int) $confirmingDeletion }})"
                    data-test="confirm-delete-service">
                    {{ __('delete_service') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</section>
