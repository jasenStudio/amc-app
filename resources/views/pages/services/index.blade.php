<x-layouts::app :title="__('Servicios | AMC Gestión de Riesgos')" :description="__('Catálogo de servicios certificados de AMC Gestión de Riesgos para proteger personas, operaciones y activos.')">
    <x-header />

    <main>
        <section aria-labelledby="services-page-title" class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amc-orange-text">{{ __('Servicios') }}</p>
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

                @if ($services->count() > 0)
                    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($services as $service)
                            <x-services.card :service="$service" />
                        @endforeach
                    </div>

                    @if ($services->hasPages())
                        <div class="mt-12">
                            {{ $services->links() }}
                        </div>
                    @endif
                @else
                    <div class="rounded-lg border-2 border-dashed border-zinc-300 p-12 text-center">
                        <p class="text-sm text-zinc-500">{{ __('No services available yet.') }}</p>
                    </div>
                @endif
            </div>
        </section>
    </main>
</x-layouts::app>
