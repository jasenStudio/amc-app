<x-layouts::app :title="__('Visión | AMC Gestión de Riesgos')" :description="__('Conoce la visión de AMC Gestión de Riesgos: ayudar a las organizaciones a avanzar con confianza mediante estrategia clara y experiencias digitales útiles.')">
    <x-header />

    <main class="mx-auto max-w-7xl px-6 py-24 lg:px-8">
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amc-orange-text">{{ __('Vision') }}</p>
        <h1 class="mt-4 text-4xl font-semibold tracking-tight text-amc-blue sm:text-5xl">
            {{ __('A future built with clarity.') }}
        </h1>
        <p class="mt-6 max-w-2xl text-lg leading-8 text-amc-gray-text">
            {{ __('Our vision is to help organizations make confident progress through clear strategy and useful digital experiences.') }}
        </p>
        <a href="{{ route('home') }}/#about"
            class="mt-8 inline-flex font-semibold text-amc-orange-text hover:text-amc-orange-hover">
            {{ __('Volver a nosotros') }} <span aria-hidden="true">&larr;</span>
        </a>
    </main>
</x-layouts::app>
