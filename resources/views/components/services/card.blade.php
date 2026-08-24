@props(['service'])

<article class="group relative h-80 overflow-hidden rounded-xl bg-amc-blue">
    <a href="{{ route('services.show', $service->slug) }}"
        class="absolute inset-0 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-amc-orange focus-visible:ring-inset">
        @if ($service->coverImage)
            <img src="{{ \App\Support\ImageUrl::public($service->coverImage->image_path) }}"
                alt="{{ $service->title }}" loading="lazy" decoding="async"
                class="absolute inset-0 h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
        @else
            <div class="absolute inset-0 h-full w-full bg-gradient-to-br from-amc-blue to-amc-blue/80"></div>
        @endif
        <div class="service-card-gradient absolute inset-0"></div>
        <div class="absolute inset-x-6 bottom-6 flex items-end gap-2 text-white">
            <div class="min-w-0 flex-1">
                <h2 class="wrap-break-word text-xl font-bold leading-tight sm:text-2xl">{{ $service->title }}</h2>
                @if ($service->excerpt)
                    <p class="mt-3 max-w-prose text-sm leading-6 text-white/85">{{ $service->excerpt }}</p>
                @else
                    <p class="mt-3 max-w-prose text-sm leading-6 text-white/85">{{ \Illuminate\Support\Str::limit(strip_tags($service->description), 120) }}</p>
                @endif
            </div>
            <span
                class="shrink-0 text-5xl font-bold leading-none text-amc-orange transition-transform group-hover:translate-x-2"
                aria-hidden="true">&rarr;</span>
        </div>
    </a>
</article>
