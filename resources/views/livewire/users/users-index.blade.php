<section class="w-full space-y-6">
    <div>
        <flux:heading size="xl">{{ __('Users') }}</flux:heading>
        <flux:subheading>{{ __('Manage platform users.') }}</flux:subheading>
    </div>

    <flux:card>
        <div class="space-y-4">
            <flux:heading size="lg">{{ __('Coming soon') }}</flux:heading>
            <flux:text>{{ __('User management is under development.') }}</flux:text>
        </div>
    </flux:card>

    <flux:table :paginate="$users">
        <flux:table.columns>
            <flux:table.column>{{ __('Name') }}</flux:table.column>
            <flux:table.column>{{ __('Email') }}</flux:table.column>
            <flux:table.column>{{ __('Role') }}</flux:table.column>
            <flux:table.column>{{ __('Created') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($users as $user)
                <flux:table.row :key="$user->id">
                    <flux:table.cell variant="strong">{{ $user->name }}</flux:table.cell>
                    <flux:table.cell>{{ $user->email }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge :color="$user->role === 'admin' ? 'green' : 'zinc'" size="sm">
                            {{ ucfirst($user->role ?? 'user') }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>{{ $user->created_at->format('Y-m-d') }}</flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="4" class="text-center text-zinc-500">
                        {{ __('No users found.') }}
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</section>
