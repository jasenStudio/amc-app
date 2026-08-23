<section class="w-full space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Projects') }}</flux:heading>
            <flux:subheading>{{ __('Manage projects and clients.') }}</flux:subheading>
        </div>
        <flux:button variant="primary" :href="route('dashboard.projects.create')" wire:navigate icon="plus" data-test="new-project">
            {{ __('New project') }}
        </flux:button>
    </div>

    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        <flux:input wire:model.live.debounce.300ms="search" :label="__('Search')" type="search"
            placeholder="{{ __('Title, client…') }}" />

        <flux:select wire:model.live="status" :label="__('Status')">
            <flux:select.option value="">{{ __('All statuses') }}</flux:select.option>
            @foreach ($statuses as $s)
                <flux:select.option :value="$s->value">{{ $s->label() }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:select wire:model.live="featured" :label="__('Featured')">
            <flux:select.option value="">{{ __('Any') }}</flux:select.option>
            <flux:select.option value="yes">{{ __('Featured only') }}</flux:select.option>
            <flux:select.option value="no">{{ __('Not featured') }}</flux:select.option>
        </flux:select>

        <flux:input wire:model.live.debounce.300ms="location" :label="__('Location')" type="search"
            placeholder="{{ __('City…') }}" />
    </div>

    <flux:table :paginate="$projects">
        <flux:table.columns>
            <flux:table.column>{{ __('Cover') }}</flux:table.column>
            <flux:table.column>{{ __('Title') }}</flux:table.column>
            <flux:table.column>{{ __('Client') }}</flux:table.column>
            <flux:table.column>{{ __('Status') }}</flux:table.column>
            <flux:table.column align="center">{{ __('Featured') }}</flux:table.column>
            <flux:table.column>{{ __('Date') }}</flux:table.column>
            <flux:table.column align="end">{{ __('Actions') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($projects as $project)
                <flux:table.row :key="$project->id">
                    <flux:table.cell>
                        @if ($project->coverImage)
                            <img src="{{ \App\Support\ImageUrl::public($project->coverImage->image_path) }}" alt=""
                                class="size-10 rounded object-cover">
                        @else
                            <div class="size-10 rounded bg-zinc-100 dark:bg-zinc-700"></div>
                        @endif
                    </flux:table.cell>

                    <flux:table.cell variant="strong">
                        <div class="max-w-xs truncate">{{ $project->title }}</div>
                    </flux:table.cell>

                    <flux:table.cell>
                        <div class="max-w-xs truncate">{{ $project->client }}</div>
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:badge :color="$project->status->value === 'active' ? 'green' : 'zinc'" size="sm">
                            {{ $project->status->label() }}
                        </flux:badge>
                    </flux:table.cell>

                    <flux:table.cell align="center">
                        @if ($project->featured)
                            <flux:icon name="star" class="size-4 text-amber-500 inline" />
                        @else
                            <flux:icon name="star" class="size-4 text-zinc-300 inline" variant="outline" />
                        @endif
                    </flux:table.cell>

                    <flux:table.cell>
                        {{ $project->date?->format('M d, Y') }}
                    </flux:table.cell>

                    <flux:table.cell align="end">
                        <div class="flex justify-end gap-2">
                            @if ($this->canUpdate($project))
                                <flux:button size="sm" :href="route('dashboard.projects.edit', $project)" wire:navigate
                                    variant="ghost" icon="pencil" data-test="edit-project-{{ $project->id }}">
                                    {{ __('Edit') }}
                                </flux:button>
                            @endif
                            @if ($this->canDelete($project))
                                <flux:button size="sm" variant="ghost" icon="trash"
                                    data-test="delete-project-{{ $project->id }}"
                                    wire:click="$set('confirmingDeletion', {{ $project->id }})">
                                    {{ __('Delete') }}
                                </flux:button>
                            @endif
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="7" class="text-center text-zinc-500">
                        {{ __('No projects found.') }}
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <flux:modal name="confirm-project-deletion" wire:model="confirmingDeletion" focusable class="max-w-lg">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Delete this project?') }}</flux:heading>
                <flux:subheading>
                    {{ __('The project will be hidden from the public site and can be restored later by an admin.') }}
                </flux:subheading>
            </div>

            <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                <flux:modal.close>
                    <flux:button variant="filled">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>

                <flux:button variant="danger" wire:click="delete({{ (int) $confirmingDeletion }})"
                    data-test="confirm-delete-project">
                    {{ __('Delete project') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</section>
