<section class="space-y-6 flex flex-col justify-center items-center max-w-lg mx-auto mt-10 lg:mt-20">
    <div class="self-start">
        <flux:heading size="xl">{{ $this->user ? __('Edit user') : __('New user') }}</flux:heading>
        <flux:subheading>
            {{ $this->user ? __('Update user information and role.') : __('Create a new platform user.') }}
        </flux:subheading>
    </div>

    <form wire:submit="save" class="space-y-6 w-full">

        <flux:input wire:model="name" :label="__('Name')" required autofocus data-test="user-name" />

        <flux:input wire:model="email" :label="__('Email')" type="email" required data-test="user-email" />

        <flux:select wire:model="role" :label="__('Role')" :disabled="$isSelf" data-test="user-role">
            @foreach ($availableRoles as $r)
                <flux:select.option :value="$r->value">{{ $r->label() }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:input wire:model="password" :label="__('Password')" type="password" :required="! $this->user"
            :placeholder="$this->user ? __('Leave blank to keep current password') : ''" data-test="user-password" />

        <div class="flex items-center justify-end gap-3">
            <flux:button :href="route('dashboard.users.index')" wire:navigate variant="filled">
                {{ __('Cancel') }}
            </flux:button>
            <flux:button variant="primary" type="submit" data-test="save-user">
                {{ __('Save user') }}
            </flux:button>
        </div>
    </form>
</section>
