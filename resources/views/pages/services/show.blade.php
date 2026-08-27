<x-layouts::app :title="$service->seoTitle() . ' | AMC Gestión de Riesgos'" :description="$service->seoDescription()" :ogImage="\App\Support\ImageUrl::public($service->seoImage())" ogType="article">

    <x-header />

    <main id="main-content" class="bg-amc-gray-bg">
        <section aria-labelledby="service-title" class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
            <x-breadcrumbs :items="[
                ['label' => __('Inicio'), 'href' => route('home')],
                ['label' => __('Servicios'), 'href' => route('services')],
                ['label' => $service->title],
            ]" />

            <div class="mt-10 grid items-center gap-10 lg:grid-cols-2">
                @if ($service->coverImage)
                    <figure class="overflow-hidden rounded-xl bg-amc-blue">
                        <img src="{{ \App\Support\ImageUrl::public($service->coverImage->image_path) }}"
                            alt="{{ $service->title }}" width="768" height="512"
                            class="aspect-[4/3] w-full object-cover" fetchpriority="high">
                    </figure>
                @else
                    <figure class="overflow-hidden rounded-xl bg-amc-blue">
                        <div class="aspect-[4/3] w-full bg-gradient-to-br from-amc-blue to-amc-blue/80"></div>
                    </figure>
                @endif

                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amc-orange-text">
                        {{ __('Servicio') }}</p>
                    <h1 id="service-title" class="mt-4 text-4xl font-semibold tracking-tight text-amc-blue sm:text-5xl">
                        {{ $service->title }}
                    </h1>

                    <article
                        class="prose article-prose-content dark:prose-invert prose-amc mt-6 max-w-none text-amc-blue/80">
                        {!! nl2br(e($service->description)) !!}
                    </article>

                    <a href="https://wa.me/573147874006?text={{ urlencode("Hola, quiero más información del servicio {$service->title}") }}"
                        target="_blank" rel="noopener noreferrer"
                        class="mt-8 inline-flex rounded-md bg-amc-orange-text px-6 py-3 text-sm font-semibold text-white transition hover:bg-amc-orange-hover focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amc-orange focus-visible:ring-offset-2">
                        {{ __('Solicitar asesoría') }}
                    </a>
                </div>
            </div>

            @php
                $galleryImages = $service->images->where('is_cover', false);
            @endphp
            @if ($galleryImages->isNotEmpty())
                <div class="mt-12">
                    <h2 class="text-2xl font-semibold text-amc-blue">{{ __('Galeria de servicios') }}</h2>

                    <div id="pswp-gallery" class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3">
                        @foreach ($galleryImages as $image)
                            <a href="{{ \App\Support\ImageUrl::public($image->image_path) }}"
                                class="group block aspect-square overflow-hidden rounded-lg bg-zinc-100 dark:bg-zinc-700"
                                data-pswp-width="{{ $image->width }}" data-pswp-height="{{ $image->height }}"
                                target="_blank" rel="noopener">
                                <img src="{{ \App\Support\ImageUrl::public($image->image_path) }}"
                                    alt="{{ $service->title }}" loading="lazy"
                                    class="aspect-square w-full object-cover transition-transform duration-300 group-hover:scale-105">
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if (isset($relatedServices) && $relatedServices->isNotEmpty())
                <div class="mt-16 border-t border-slate-200 pt-12">
                    <h2 class="text-2xl font-semibold text-amc-blue">{{ __('Servicios relacionados') }}</h2>
                    <p class="mt-2 text-sm text-amc-gray-text">
                        {{ __('Explora otras soluciones certificadas de AMC.') }}</p>
                    <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($relatedServices as $related)
                            <x-services.card :service="$related" />
                        @endforeach
                    </div>
                    <div class="mt-8 text-center">
                        <a href="{{ route('services') }}"
                            class="inline-flex items-center gap-2 text-sm font-semibold text-amc-orange-text hover:text-amc-orange-hover transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amc-orange focus-visible:ring-offset-2">
                            {{ __('Ver todos los servicios') }} <span aria-hidden="true">&rarr;</span>
                        </a>
                    </div>
                </div>
            @endif
        </section>
    </main>

    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "Service",
        "name": {{ json_encode($service->title) }},
        "description": {{ json_encode(\Illuminate\Support\Str::limit(strip_tags($service->description), 160)) }},
        "provider": {
            "@@type": "Organization",
            "name": "AMC Gestión de Riesgos SAS",
            "telephone": "+573147874006",
            "email": "gerencia@amcgestiondelriesgo.com.co",
            "url": {{ json_encode(config('app.url')) }}
        },
        "areaServed": {
            "@@type": "Country",
            "name": "Colombia"
        },
        "url": {{ json_encode(route('services.show', $service->slug)) }}@if($service->seoImage()), "image": {{ json_encode(\App\Support\ImageUrl::public($service->seoImage())) }}@endif
    }
    </script>

    @once
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/photoswipe@5.4.4/dist/photoswipe.min.css">
        <script type="module">
            import PhotoSwipeLightbox from 'https://cdn.jsdelivr.net/npm/photoswipe@5.4.4/dist/photoswipe-lightbox.esm.min.js';
            const lightbox = new PhotoSwipeLightbox({
                gallery: '#pswp-gallery',
                children: 'a',
                pswpModule: () => import('https://cdn.jsdelivr.net/npm/photoswipe@5.4.4/dist/photoswipe.esm.min.js'),
                bgOpacity: 0.92,
                showHideAnimationType: 'fade',
            });
            lightbox.init();
        </script>
    @endonce

    <x-footer />
</x-layouts::app>
