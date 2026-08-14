<x-layouts::app :title="__('Services')" :description="__('Discover AMC services for clear, practical progress.')">
    <x-header />

    <main class="mx-auto max-w-7xl px-6 py-24 lg:px-8">
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amc-orange">{{ __('Services') }}</p>
        <h1 class="mt-4 text-4xl font-semibold tracking-tight text-amc-blue sm:text-5xl">
            {{ __('Services designed around progress.') }}
        </h1>
        <p class="mt-6 max-w-2xl text-lg leading-8 text-amc-gray-text">
            {{ __('This services page is ready for the complete service catalogue.') }}
        </p>
        <a href="{{ route('home') }}#services" class="mt-8 inline-flex font-semibold text-amc-orange hover:text-amc-orange-hover">
            {{ __('Volver a servicios') }} <span aria-hidden="true">&larr;</span>
        </a>
    </main>
</x-layouts::app>
