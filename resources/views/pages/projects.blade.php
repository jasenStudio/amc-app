<x-layouts::app :title="__('Projects')" :description="__('Explore AMC projects and practical results.')">
    <x-header />

    <main class="mx-auto max-w-7xl px-6 py-24 lg:px-8">
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amc-orange-text">{{ __('Projects') }}</p>
        <h1 class="mt-4 text-4xl font-semibold tracking-tight text-amc-blue sm:text-5xl">
            {{ __('Selected work and practical results.') }}
        </h1>
        <p class="mt-6 max-w-2xl text-lg leading-8 text-amc-gray-text">
            {{ __('This projects page is ready for the complete portfolio.') }}
        </p>
        <a href="{{ route('home') }}#projects" class="mt-8 inline-flex font-semibold text-amc-orange-text hover:text-amc-orange-hover">
            {{ __('Volver a proyectos') }} <span aria-hidden="true">&larr;</span>
        </a>
    </main>
</x-layouts::app>
