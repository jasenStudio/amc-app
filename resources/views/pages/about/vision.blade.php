<x-layouts::app :title="__('Visión | AMC Gestión de Riesgos')" :description="__(
    'Conoce la visión de AMC Gestión de Riesgos: ayudar a las organizaciones a avanzar con confianza mediante estrategia clara y experiencias digitales útiles.',
)" bodyBg="bg-amc-blue">
    <x-header />

    <main class="bg-amc-blue text-white">
        <section class="mx-auto grid min-h-screen max-w-7xl items-center gap-12 px-6 py-32 lg:grid-cols-2 lg:px-8">
            <div class="max-w-2xl">
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-amc-orange-text">
                    {{ __('Vision') }}
                </p>
                <h1 class="mt-6 text-4xl font-semibold tracking-tight text-white sm:text-5xl">
                    {{ __('vision_title') }}
                </h1>
                <p class="mt-6 max-w-2xl text-lg leading-8 text-white/80">
                    {{ __('vision_description') }}
                </p>
                <a href="{{ route('home') }}/#about"
                    class="mt-10 inline-flex items-center gap-2 font-semibold text-amc-orange-text transition hover:text-amc-orange-hover">
                    {{ __('Volver a nosotros') }}
                    <span aria-hidden="true">&larr;</span>
                </a>
            </div>
            <figure class="overflow-hidden rounded-xl">
                <img src="{{ asset('assets/images/vision-amc-gestion-riesgo.webp') }}"
                    alt="{{ __('Equipo de AMC Gestión de Riesgos trabajando en alturas con elementos de protección personal') }}"
                    loading="lazy" decoding="async"
                    class="aspect-4/3 w-full object-cover lg:aspect-auto lg:h-full">
            </figure>
        </section>
    </main>
</x-layouts::app>
