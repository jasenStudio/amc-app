<x-layouts::app :title="__('Misión | AMC Gestión de Riesgos')" :description="__('Conoce la misión de AMC Gestión de Riesgos: combinar estrategia, diseño y tecnología para crear resultados que importan.')">
    <x-header />

    <main class="mx-auto max-w-7xl px-6 py-24 lg:px-8">
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amc-orange-text">{{ __('Mission') }}</p>
        <h1 class="mt-4 text-4xl font-semibold tracking-tight text-amc-blue sm:text-5xl">
            {{ __('Turning complex challenges into progress.') }}
        </h1>
        <p class="mt-6 max-w-2xl text-lg leading-8 text-amc-gray-text">
            {{ __('Our mission is to combine strategy, design, and technology to create outcomes that matter.') }}
        </p>
        <a href="{{ route('home') }}/#about"
            class="mt-8 inline-flex font-semibold text-amc-orange-text hover:text-amc-orange-hover">
            {{ __('Volver a nosotros') }} <span aria-hidden="true">&larr;</span>
        </a>
    </main>
</x-layouts::app>
