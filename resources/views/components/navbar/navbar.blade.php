<div x-data="navbar({{ Js::from(request()->routeIs('home') ? 'home' : '') }})" @keydown.escape.window="open = false" @navbar-close.window="open = false"
    @resize.window="if (window.innerWidth >= 1024) open = false" class="bg-amc-blue text-white shadow-sm">

    <div class="mx-auto flex max-w-7xl items-center justify-between gap-6 px-6 py-4 sm:px-8 lg:px-10">
        <x-navbar.brand />

        <div class="hidden items-center gap-8 lg:flex">
            <x-navbar.desktop-links />
            <x-navbar.cta href="https://wa.me/573147874006?text={{ urlencode('Hola, quiero más información.') }}"
                target="_blank" rel="noopener noreferrer" />
        </div>

        <x-navbar.toggle />
    </div>

    <x-navbar.mobile-panel />
</div>
