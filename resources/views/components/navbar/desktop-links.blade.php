<nav aria-label="{{ __('Navegación principal') }}" class="flex items-center gap-7 lg:gap-9">
    <x-navbar.link
        href="{{ route('home') }}"
        :label="__('Inicio')"
        :active="request()->routeIs('home')"
    />
    <x-navbar.link :label="__('Servicios')" />
    <x-navbar.link :label="__('Nosotros')" />
    <x-navbar.link :label="__('Contacto')" />
</nav>
