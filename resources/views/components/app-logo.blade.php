@props([
    'sidebar' => false,
])

@if ($sidebar)
    <flux:sidebar.brand name="AMC Admin" {{ $attributes }}>
        <x-slot name="logo" class="flex items-center justify-center bg-transparent">

            <img src="{{ asset('assets/images/logo.webp') }}" class="w-8 h-8 object-contain"
                alt="Logo AMC Gestion del riesgo">
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand :name="config('app.name', 'AMC')" {{ $attributes }}>
        <x-slot name="logo"
            class="flex aspect-square size-8 items-center justify-center rounded-md bg-accent-content text-accent-foreground">
            <x-app-logo-icon class="size-5 fill-current text-white dark:text-black" />
        </x-slot>
    </flux:brand>
@endif
