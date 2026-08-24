<x-layouts::app :title="$service->seoTitle() . ' | AMC Gestión de Riesgos'" :description="$service->seoDescription()">
    @if ($service->seoImage())
        <meta property="og:image" content="{{ \App\Support\ImageUrl::public($service->seoImage()) }}">
    @endif
    <meta property="og:type" content="article">

    <x-header />

    <main>
        <section aria-labelledby="service-title" class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
            <a href="{{ route('services') }}"
                class="inline-flex items-center gap-2 text-sm font-semibold text-amc-orange-text transition hover:text-amc-orange-hover focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amc-orange focus-visible:ring-offset-2">
                <span aria-hidden="true">&larr;</span>
                {{ __('Volver a servicios') }}
            </a>

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
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amc-orange-text">{{ __('Servicio') }}</p>
                    <h1 id="service-title" class="mt-4 text-4xl font-semibold tracking-tight text-amc-blue sm:text-5xl">
                        {{ $service->title }}
                    </h1>

                    @if ($service->excerpt)
                        <p class="mt-4 text-lg leading-8 text-amc-gray-text">{{ $service->excerpt }}</p>
                    @endif

                    <article class="prose article-prose-content dark:prose-invert prose-amc mt-6 max-w-none text-amc-blue/80">
                        {!! nl2br(e($service->description)) !!}
                    </article>

                    <a href="{{ route('home') }}#contact"
                        class="mt-8 inline-flex rounded-md bg-amc-orange-text px-6 py-3 text-sm font-semibold text-white transition hover:bg-amc-orange-hover focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amc-orange focus-visible:ring-offset-2">
                        {{ __('Solicitar asesoría') }}
                    </a>
                </div>
            </div>

            @if ($service->images->count() > 1)
                <div class="mt-12">
                    <h2 class="text-2xl font-semibold text-amc-blue">{{ __('Gallery') }}</h2>
                    <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3">
                        @foreach ($service->images as $image)
                            @if (! $image->is_cover)
                                <div class="overflow-hidden rounded-lg bg-zinc-100 dark:bg-zinc-700">
                                    <img src="{{ \App\Support\ImageUrl::public($image->image_path) }}"
                                        alt="{{ $service->title }}" class="aspect-square w-full object-cover" loading="lazy">
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        </section>
    </main>
</x-layouts::app>
