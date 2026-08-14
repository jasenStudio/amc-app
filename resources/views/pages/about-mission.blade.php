<x-layouts::app :title="__('Mission')" :description="__('Discover the AMC mission.')">
    <x-header />

    <main class="mx-auto max-w-7xl px-6 py-24 lg:px-8">
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amc-orange">{{ __('Mission') }}</p>
        <h1 class="mt-4 text-4xl font-semibold tracking-tight text-amc-blue sm:text-5xl">
            {{ __('Turning complex challenges into progress.') }}
        </h1>
        <p class="mt-6 max-w-2xl text-lg leading-8 text-amc-gray-text">
            {{ __('Our mission is to combine strategy, design, and technology to create outcomes that matter.') }}
        </p>
        <a href="{{ route('about') }}" class="mt-8 inline-flex font-semibold text-amc-orange hover:text-amc-orange-hover">
            {{ __('Volver a nosotros') }} <span aria-hidden="true">&larr;</span>
        </a>
    </main>
</x-layouts::app>
