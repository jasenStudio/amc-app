<x-layouts::app :title="__('Página no encontrada | AMC Gestión de Riesgos')" :description="__('La página que buscas no existe o fue movida. Vuelve al inicio o explora nuestros servicios certificados en seguridad en alturas y líneas de vida.')" ogType="website">
    <x-header />

    <main id="main-content" class="bg-amc-gray-bg">
        <section class="mx-auto flex min-h-[70vh] max-w-7xl flex-col items-center justify-center px-6 py-24 text-center lg:px-8">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amc-orange-text">404</p>
            <h1 class="mt-4 text-4xl font-bold tracking-tight text-amc-blue sm:text-5xl">
                {{ __('Página no encontrada') }}
            </h1>
            <p class="mt-6 max-w-2xl text-lg leading-8 text-amc-gray-text">
                {{ __('La página que buscas no existe, fue movida o el enlace es incorrecto. Sigue explorando nuestros servicios certificados o vuelve al inicio.') }}
            </p>
            <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
                <a href="{{ route('home') }}"
                    class="rounded-md bg-amc-orange-text px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-amc-orange-hover focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amc-orange focus-visible:ring-offset-2">
                    {{ __('Volver al inicio') }}
                </a>
                @if ($hasFeaturedServices)
                <a href="{{ route('services') }}"
                    class="rounded-md border border-amc-blue/20 bg-white px-6 py-3 text-sm font-semibold text-amc-blue transition hover:border-amc-blue/30 hover:bg-amc-gray-bg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amc-orange focus-visible:ring-offset-2">
                    {{ __('Ver servicios') }}
                </a>
                @endif
                @if ($hasFeaturedPosts)
                <a href="{{ route('blog') }}"
                    class="rounded-md border border-amc-blue/20 bg-white px-6 py-3 text-sm font-semibold text-amc-blue transition hover:border-amc-blue/30 hover:bg-amc-gray-bg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amc-orange focus-visible:ring-offset-2">
                    {{ __('Ir al blog') }}
                </a>
                @endif
                <a href="{{ route('home') }}#contact"
                    class="text-sm font-semibold text-amc-orange-text transition hover:text-amc-orange-hover focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amc-orange focus-visible:ring-offset-2">
                    {{ __('Contáctanos') }} <span aria-hidden="true">&rarr;</span>
                </a>
            </div>
        </section>
    </main>

    <x-footer />
</x-layouts::app>
