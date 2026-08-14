<x-layouts::app :title="__('Blog')" :description="__('Read AMC perspectives, guides, and practical insights.')">
    <x-header />

    <main class="mx-auto max-w-7xl px-6 py-24 lg:px-8">
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amc-orange">{{ __('Blog') }}</p>
        <h1 class="mt-4 text-4xl font-semibold tracking-tight text-amc-blue sm:text-5xl">
            {{ __('Ideas for moving forward.') }}
        </h1>
        <p class="mt-6 max-w-2xl text-lg leading-8 text-amc-gray-text">
            {{ __('This blog page is ready for the latest articles and insights.') }}
        </p>
        <a href="{{ route('home') }}#blog" class="mt-8 inline-flex font-semibold text-amc-orange hover:text-amc-orange-hover">
            {{ __('Volver al blog') }} <span aria-hidden="true">&larr;</span>
        </a>
    </main>
</x-layouts::app>
