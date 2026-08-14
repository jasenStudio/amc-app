<div id="mobile-navigation" x-show="open" x-cloak role="dialog" aria-modal="true" aria-labelledby="mobile-navigation-title"
    @click.stop x-transition:enter="transition duration-300 ease-out" x-transition:enter-start="-translate-x-full"
    x-transition:enter-end="translate-x-0" x-transition:leave="transition duration-200 ease-in"
    x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
    class="fixed inset-y-0 left-0 z-50 flex w-[min(20rem,85vw)] flex-col bg-amc-blue shadow-2xl md:hidden">
    <div class="flex items-center justify-between border-b border-white/10 px-6 py-5">

        <x-navbar.brand />
        <button type="button" @click="open = false" aria-label="{{ __('Cerrar menú') }}"
            class="rounded-md p-2 text-white transition-colors hover:text-amc-orange focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amc-orange focus-visible:ring-offset-2 focus-visible:ring-offset-amc-blue">
            <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18" />
            </svg>
        </button>
    </div>

    <nav aria-label="{{ __('Navegación móvil') }}" class="flex flex-col gap-2 px-6 py-6">
        <x-navbar.link href="{{ route('home') }}" :label="__('Inicio')" :active="request()->routeIs('home')" />
        <x-navbar.link :label="__('Servicios')" />
        <x-navbar.link :label="__('Nosotros')" />
        <x-navbar.link :label="__('Contacto')" />
        <x-navbar.cta />
    </nav>
</div>

<div x-show="open" x-cloak x-transition:enter="transition-opacity duration-300 ease-out"
    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity duration-200 ease-in" x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0" @click="open = false" aria-hidden="true"
    class="fixed inset-0 z-40 bg-black/55 md:hidden"></div>
