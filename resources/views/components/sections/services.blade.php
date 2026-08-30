@props([
    'title' => __('Servicios Principales'),
    'viewAllLabel' => __('Ver todos'),
])

@if ($hasFeaturedServices)
    <section id="services" aria-labelledby="services-title" class="bg-amc-gray-bg">

        <div x-data="fadeInOnScroll" class="fade-in-scroll">
            <div class="mx-auto max-w-7xl px-6 py-28 lg:px-8">
                <div class="mb-8 flex items-center justify-between gap-4">
                    <h2 id="services-title" class="text-3xl font-bold text-amc-blue sm:text-4xl">
                        {{ $title }}
                    </h2>
                    <a href="{{ route('services') }}"
                        class="inline-flex shrink-0 items-center gap-2 text-sm font-semibold uppercase tracking-[0.2em] text-amc-orange-text underline underline-offset-4 transition hover:text-amc-orange-hover focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amc-orange focus-visible:ring-offset-2">
                        {{ $viewAllLabel }}
                        <span aria-hidden="true">&rarr;</span>
                    </a>
                </div>

                <livewire:services.featured-services />
            </div>
        </div>
    </section>
@endif
