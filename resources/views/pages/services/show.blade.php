<x-layouts::app :title="$service['title'] . ' | AMC Gestión de Riesgos'" :description="$service['description']">
    <x-header />

    <main>
        <section aria-labelledby="service-title" class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
                <a href="{{ route('services') }}"
                    class="inline-flex items-center gap-2 text-sm font-semibold text-amc-orange-text transition hover:text-amc-orange-hover focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amc-orange focus-visible:ring-offset-2">
                <span aria-hidden="true">&larr;</span>
                {{ __('Volver a servicios') }}
            </a>

            <div class="mt-10 grid items-center gap-10 lg:grid-cols-2">
                <figure class="overflow-hidden rounded-xl bg-amc-blue">
                    <img src="{{ $service['image'] }}" alt="{{ $service['imageAlt'] }}" width="768" height="512"
                        class="aspect-[4/3] w-full object-cover" fetchpriority="high">
                </figure>
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amc-orange-text">{{ __('Servicio') }}</p>
                    <h1 id="service-title" class="mt-4 text-4xl font-semibold tracking-tight text-amc-blue sm:text-5xl">
                        {{ $service['title'] }}
                    </h1>
                    <p class="mt-6 text-lg leading-8 text-amc-gray-text">{{ $service['description'] }}</p>
                    <a href="{{ route('home') }}#contact"
                        class="mt-8 inline-flex rounded-md bg-amc-orange-text px-6 py-3 text-sm font-semibold text-white transition hover:bg-amc-orange-hover focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amc-orange focus-visible:ring-offset-2">
                        {{ __('Solicitar asesoría') }}
                    </a>
                </div>
            </div>
        </section>
    </main>
</x-layouts::app>
