<x-layouts::app :title="__('Servicios | AMC Gestión de Riesgos')" :description="__('Conoce el catálogo de servicios de AMC Gestión de Riesgos.')">
    <x-header />

    <main>
        <section aria-labelledby="services-page-title" class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amc-orange">{{ __('Servicios') }}</p>
            <h1 id="services-page-title" class="mt-4 max-w-3xl text-4xl font-semibold tracking-tight text-amc-blue sm:text-5xl">
                {{ __('Soluciones para trabajar con más seguridad.') }}
            </h1>
            <p class="mt-6 max-w-2xl text-lg leading-8 text-amc-gray-text">
                {{ __('Conoce nuestro portafolio de servicios certificados para proteger personas, operaciones y activos.') }}
            </p>
        </section>

        <section aria-labelledby="services-catalog-title" class="bg-amc-gray-bg">
            <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
                <h2 id="services-catalog-title" class="sr-only">{{ __('Catálogo de servicios') }}</h2>
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($services as $service)
                        <x-services.card :service="$service" />
                    @endforeach
                </div>
            </div>
        </section>
    </main>
</x-layouts::app>
