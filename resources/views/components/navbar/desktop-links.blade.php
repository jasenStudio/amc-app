<nav aria-label="{{ __('Navegación principal') }}" class="flex items-center gap-7 lg:gap-9">
    @if (request()->routeIs('home'))
        <x-navbar.link href="#hero" :label="__('Inicio')" section="hero" data-theme="dark" />
    @else
        <x-navbar.link :href="route('home')" :label="__('Inicio')" data-theme="dark" />
    @endif
    <x-navbar.dropdown href="{{ route('home') }}#about" :label="__('Nosotros')" :active="request()->routeIs('about', 'about.*')" section="about"
        data-theme="light" :items="[
            ['label' => __('Visión'), 'href' => route('about.vision'), 'active' => request()->routeIs('about.vision')],
            [
                'label' => __('Misión'),
                'href' => route('about.mission'),
                'active' => request()->routeIs('about.mission'),
            ],
        ]" />
    <x-navbar.link href="{{ route('home') }}#services" :label="__('Servicios')" section="services" data-theme="light" />
    <x-navbar.link href="{{ route('home') }}#projects" :label="__('Proyectos')" section="projects" data-theme="light" />
    <x-navbar.link href="{{ route('home') }}#blog" :label="__('Blog')" section="blog" data-theme="light" />
    <x-navbar.link href="{{ route('home') }}#contact" :label="__('Contacto')" section="contact" />
</nav>
