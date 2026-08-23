<x-layouts::auth :title="__('Pending approval')">
    <div class="flex flex-col items-center gap-6 text-center">
        <flux:icon name="clock" variant="outline" class="size-12 text-zinc-400" />

        <div class="space-y-2">
            <flux:heading size="lg">{{ __('Your account is pending approval') }}</flux:heading>
            <flux:text>
                {{ __('Your account has been created, but it must be approved by an administrator before you can access the dashboard.') }}
            </flux:text>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <flux:button variant="danger" type="submit" class="w-full" data-test="logout-button">
                {{ __('Log out') }}
            </flux:button>
        </form>
    </div>
</x-layouts::auth>
