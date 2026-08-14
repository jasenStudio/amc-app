<x-layouts::app :title="__('About us')" :description="__('Learn about AMC vision and mission.')">
    <x-header />

    <main class="mx-auto max-w-7xl px-6 py-24 lg:px-8">
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amc-orange">{{ __('About AMC') }}</p>
        <h1 class="mt-4 text-4xl font-semibold tracking-tight text-amc-blue sm:text-5xl">
            {{ __('A clear foundation for meaningful work.') }}
        </h1>
        <div class="mt-12 grid gap-6 md:grid-cols-2">
            <a href="{{ route('about.vision') }}" class="rounded-2xl bg-amc-blue p-8 text-white transition hover:bg-amc-blue-dark">
                <h2 class="text-2xl font-semibold">{{ __('Vision') }}</h2>
                <p class="mt-3 text-white/75">{{ __('Discover the direction that guides AMC.') }}</p>
            </a>
            <a href="{{ route('about.mission') }}" class="rounded-2xl bg-amc-blue p-8 text-white transition hover:bg-amc-blue-dark">
                <h2 class="text-2xl font-semibold">{{ __('Mission') }}</h2>
                <p class="mt-3 text-white/75">{{ __('Learn how we turn challenges into useful outcomes.') }}</p>
            </a>
        </div>
    </main>
</x-layouts::app>
