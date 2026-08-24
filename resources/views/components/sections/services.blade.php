@props([
    'title' => __('Servicios Principales'),
    'viewAllLabel' => __('Ver todos'),
])

@php
    $services = \Illuminate\Support\Facades\Schema::hasTable('services')
        ? \App\Models\Service::query()
            ->active()
            ->ordered()
            ->with(['coverImage'])
            ->limit(6)
            ->get()
        : collect();
@endphp

<section id="services" aria-labelledby="services-title" class="bg-amc-gray-bg">

    <div x-data="fadeInOnScroll" class="fade-in-scroll">
        <div class="mx-auto max-w-7xl px-6 py-12 lg:px-8">
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

            @if ($services->count() > 0)
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($services as $service)
                        <x-services.card :service="$service" />
                    @endforeach
                </div>
            @else
                <div class="rounded-lg border-2 border-dashed border-zinc-300 p-8 text-center">
                    <p class="text-sm text-zinc-500">{{ __('No services available yet.') }}</p>
                </div>
            @endif
        </div>
    </div>
</section>
