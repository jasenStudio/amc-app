<section class="space-y-6 flex flex-col justify-center items-center max-w-lg mx-auto mt-10 lg:mt-20">
    <div class="self-start">
        <flux:heading size="xl">{{ $this->user ? __('Edit user') : __('New user') }}</flux:heading>
        <flux:subheading>
            {{ $this->user ? __('Update user information and role.') : __('Create a new platform user.') }}
        </flux:subheading>
    </div>

    <form wire:submit="save" class="space-y-6 w-full">
        <x-dashboard.form-error-summary />

        <flux:input wire:model.live.blur="name" :label="__('Name')" autofocus :label:badge="__('required_field')"
            data-test="user-name" />

        <flux:input wire:model.live.blur="email" :label="__('Email')" type="email" :label:badge="__('required_field')"
            data-test="user-email" />

        <flux:select wire:model="role" :label="__('Role')" :label:badge="__('required_field')" :disabled="$isSelf"
            data-test="user-role">
            @foreach ($availableRoles as $r)
                <flux:select.option :value="$r->value">{{ $r->label() }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:input wire:model.live.blur="password" :label="__('Password')" type="password"
            :label:badge="$this->user ? null : __('required_field')"
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
