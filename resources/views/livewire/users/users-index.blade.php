<section class="w-full space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Users') }}</flux:heading>
            <flux:subheading>{{ __('Manage platform users.') }}</flux:subheading>
        </div>
        @can('create', App\Models\User::class)
            <flux:button variant="primary" :href="route('dashboard.users.create')" wire:navigate icon="plus" data-test="new-user">
                {{ __('New user') }}
            </flux:button>
        @endcan
    </div>

    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        <flux:input wire:model.live.debounce.300ms="search" :label="__('Search')" type="search"
            placeholder="{{ __('Name or email…') }}" />

        <flux:select wire:model.live="role" :label="__('Role')">
            <flux:select.option value="">{{ __('All roles') }}</flux:select.option>
            @foreach ($roles as $r)
                <flux:select.option :value="$r->value">{{ $r->label() }}</flux:select.option>
            @endforeach
        </flux:select>
    </div>

    <flux:table :paginate="$users">
        <flux:table.columns>
            <flux:table.column>{{ __('Name') }}</flux:table.column>
            <flux:table.column>{{ __('Email') }}</flux:table.column>
            <flux:table.column>{{ __('Role') }}</flux:table.column>
            <flux:table.column>{{ __('Created') }}</flux:table.column>
            <flux:table.column align="end">{{ __('Actions') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($users as $user)
                <flux:table.row :key="$user->id">
                    <flux:table.cell variant="strong">{{ $user->name }}</flux:table.cell>
                    <flux:table.cell>{{ $user->email }}</flux:table.cell>
                    <flux:table.cell>
                        @php
                            $color = match ($user->role) {
                                App\Enums\UserRole::SuperAdmin => 'red',
                                App\Enums\UserRole::Admin => 'green',
                                App\Enums\UserRole::Editor => 'blue',
                                default => 'zinc',
                            };
                        @endphp
                        <flux:badge :color="$color" size="sm">
                            {{ $user->role->label() }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>{{ $user->created_at->format('Y-m-d') }}</flux:table.cell>
                    <flux:table.cell align="end">
                        <div class="flex justify-end gap-2">
                            @if ($this->canUpdate($user))
                                <flux:button size="sm" :href="route('dashboard.users.edit', $user)" wire:navigate
                                    variant="ghost" icon="pencil" data-test="edit-user-{{ $user->id }}">
                                    {{ __('Edit') }}
                                </flux:button>
                            @endif
                            @if ($this->canDelete($user))
                                <flux:button size="sm" variant="ghost" icon="trash"
                                    data-test="delete-user-{{ $user->id }}"
                                    wire:click="$set('confirmingDeletion', {{ $user->id }})">
                                    {{ __('Delete') }}
                                </flux:button>
                            @endif
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="5" class="text-center text-zinc-500">
                        {{ __('No users found.') }}
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <flux:modal name="confirm-user-deletion" wire:model="confirmingDeletion" focusable class="max-w-lg">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Delete this user?') }}</flux:heading>
                <flux:subheading>
                    {{ __('The user will be permanently removed from the platform.') }}
                </flux:subheading>
            </div>

            <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                <flux:modal.close>
                    <flux:button variant="filled">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>

                <flux:button variant="danger" wire:click="delete({{ (int) $confirmingDeletion }})"
                    data-test="confirm-delete-user">
                    {{ __('Delete user') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</section>
