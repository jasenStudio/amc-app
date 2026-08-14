<div x-data="navbar({{ Js::from(request()->routeIs('home') ? 'home' : '') }})" @keydown.escape.window="open = false" @navbar-close.window="open = false"
    @resize.window="if (window.innerWidth >= 768) open = false" class="bg-amc-blue text-white shadow-sm">

    <div class="mx-auto flex max-w-7xl items-center justify-between gap-6 px-6 py-4 sm:px-8 lg:px-10">
        <x-navbar.brand />

        <div class="hidden items-center gap-8 md:flex">
            <x-navbar.desktop-links />
            <x-navbar.cta href="{{ route('projects') }}" />
        </div>

        <x-navbar.toggle />
    </div>

    <x-navbar.mobile-panel />
</div>
