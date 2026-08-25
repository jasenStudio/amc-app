<x-layouts::app :title="__('Misión | AMC Gestión de Riesgos')" :description="__(
    'Conoce la misión de AMC Gestión de Riesgos: combinar estrategia, diseño y tecnología para crear resultados que importan.',
)" bodyBg="bg-amc-blue">
    <x-header />

    <main class="bg-amc-gray-bg text-white">
        <section class="mx-auto grid min-h-screen max-w-7xl items-start gap-12 px-6 py-32 lg:grid-cols-2 lg:px-8">
            <div class="max-w-2xl">
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-amc-orange-text">
                    {{ __('mission') }}
                </p>
                <h1 class="mt-6 text-4xl font-semibold tracking-tight text-amc-blue sm:text-5xl">
                    {{ __('mission_title') }}
                </h1>
                <p class="mt-6 max-w-2xl text-lg leading-8 text-amc-blue/80">
                    {{ __('mission_description') }}
                </p>
                <a href="{{ route('home') }}/#about"
                    class="mt-10 inline-flex items-center gap-2 font-semibold text-amc-orange-text transition hover:text-amc-orange-hover">
                    {{ __('Volver a nosotros') }}
                    <span aria-hidden="true">&larr;</span>
                </a>
            </div>
            <figure class="overflow-hidden rounded-xl">
                <img src="{{ asset('assets/images/mision-amc-gestion-riesgo.webp') }}"
                    alt="{{ __('Equipo de AMC Gestión de Riesgos trabajando en alturas con elementos de protección personal') }}"
                    loading="lazy" decoding="async" class="aspect-4/3 w-full object-cover lg:aspect-auto lg:h-full">
            </figure>
        </section>
    </main>
</x-layouts::app>
