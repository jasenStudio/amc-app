<x-layouts::app :title="$project->seoTitle() . ' | AMC Gestión de Riesgos'" :description="$project->seoDescription()" :ogImage="\App\Support\ImageUrl::public($project->seoImage())" ogType="article">

    <x-header />

    <main id="main-content" class="bg-amc-gray-bg">

        {{-- Hero --}}
        <section class="relative isolate flex min-h-screen items-end overflow-hidden bg-amc-blue text-white">

            @if ($project->coverImage)
                <img src="{{ \App\Support\ImageUrl::public($project->coverImage->image_path) }}"
                    alt="{{ $project->title }}" fetchpriority="high" class="absolute inset-0 h-full w-full object-cover">
            @endif

            {{-- Gradiente superior sutil --}}
            <div class="absolute inset-x-0 top-0 h-40 bg-linear-to-b from-amc-blue/40 to-transparent"
                aria-hidden="true"></div>

            {{-- Gradiente inferior fuerte --}}
            <div class="absolute inset-x-0 bottom-0 h-[70%] bg-linear-to-t from-amc-blue via-amc-blue/80 to-transparent"
                aria-hidden="true"></div>

            <a href="{{ route('projects') }}"
                class="absolute left-6 top-8 z-10 inline-flex items-center gap-2 rounded-full bg-black/40 px-4 py-2 text-sm font-medium text-white shadow-md ring-1 ring-white/15 backdrop-blur-md transition hover:bg-black/60 lg:left-8 lg:top-10">
                <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                    aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                {{ __('back_to_projects') }}
            </a>
            {{-- pb-24/32 en vez de pb-16/20: separa el contenido del borde inferior real, sin depender de min-h --}}
            <div class="relative mx-auto w-full max-w-352 px-6 pb-24 pt-28 lg:px-8 lg:pb-32">



                {{-- Hero content --}}
                <div class="flex flex-col gap-10 lg:flex-row lg:items-end lg:justify-between">

                    {{-- Title --}}
                    <div class="max-w-3xl">

                        <span
                            class="inline-flex items-center rounded-md bg-amc-orange px-3 py-1 text-xs font-semibold uppercase tracking-wide text-white">
                            {{ __('case_study') }}
                        </span>

                        <h1 class="mt-6 text-3xl font-semibold leading-tight tracking-tight sm:text-4xl lg:text-5xl">
                            {{ $project->title }}
                        </h1>



                    </div>

                    {{-- Project information --}}
                    {{-- max-w-sm aplica desde sm, así que en tablet (768px) ya no se estira --}}
                    <aside
                        class="w-full max-w-sm rounded-xl border-l-4 border-amc-orange bg-black/40 p-5 text-sm text-white shadow-xl ring-1 ring-white/10 backdrop-blur-sm lg:max-w-[18rem] lg:shrink-0">

                        @if ($project->location)
                            <div class="flex items-center gap-3">
                                <svg class="size-4 shrink-0 text-amc-orange-text" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                </svg>

                                <span>{{ $project->location }}</span>
                            </div>
                        @endif

                        @if ($project->date)
                            <div class="mt-3 flex items-center gap-3">
                                <svg class="size-4 shrink-0 text-amc-orange-text" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0V11.25h18v7.5" />
                                </svg>

                                <span>{{ $project->date->format('M d, Y') }}</span>
                            </div>
                        @endif

                        @if ($project->client)
                            <div class="mt-3 flex items-center gap-3">
                                <svg class="size-4 shrink-0 text-amc-orange-text" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3.75 21h16.5A2.25 2.25 0 0 0 22.5 18.75V5.25A2.25 2.25 0 0 0 20.25 3H3.75A2.25 2.25 0 0 0 1.5 5.25v13.5A2.25 2.25 0 0 0 3.75 21ZM12 7.5h.008v.008H12V7.5Z" />
                                </svg>

                                <span>{{ $project->client }}</span>
                            </div>
                        @endif

                    </aside>

                </div>
            </div>
        </section>

        {{-- Content --}}
        <section class="bg-amc-gray-bg px-6 py-16 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <x-breadcrumbs :items="[
                    ['label' => __('Inicio'), 'href' => route('home')],
                    ['label' => __('Proyectos'), 'href' => route('projects')],
                    ['label' => $project->title],
                ]" />
                <div class="grid grid-cols-1 gap-6 {{ $project->video_url ? 'lg:grid-cols-2' : '' }}">

                    {{-- Descripción --}}
                    <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-black/5">
                        <div class="mb-4 flex items-center gap-2">
                            <span
                                class="flex size-8 items-center justify-center rounded-full bg-amc-blue/10 text-amc-blue">
                                <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                </svg>
                            </span>
                            <h2 class="text-base font-semibold text-amc-blue">{{ __('description') }}</h2>
                        </div>

                        <div class="prose prose-sm max-w-none text-gray-600">
                            {!! $project->description !!}
                        </div>
                    </div>

                    {{-- Video: solo si existe --}}
                    @if ($project->video_url && $videoEmbedUrl)
                        <div class="overflow-hidden rounded-xl bg-black shadow-sm ring-1 ring-black/5">
                            <div class="relative aspect-video w-full">
                                <iframe src="{{ $videoEmbedUrl }}" class="absolute inset-0 h-full w-full"
                                    allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen
                                    title="{{ $project->title }}"></iframe>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </section>

        {{-- Gallery --}}
        @php
            $galleryImages = $project->images->where('is_cover', false);
        @endphp
        @if ($galleryImages->isNotEmpty())
            <section class="bg-white px-6 py-16 lg:px-8">
                <div class="mx-auto max-w-7xl">
                    <h2 class="mb-6 text-lg font-semibold text-amc-blue">{{ __('project_documentation') }}</h2>

                    <div id="pswp-gallery" class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
                        @foreach ($galleryImages as $image)
                            <a href="{{ \App\Support\ImageUrl::public($image->image_path) }}"
                                class="group block aspect-square overflow-hidden rounded-lg bg-zinc-100"
                                data-pswp-width="{{ $image->width }}" data-pswp-height="{{ $image->height }}"
                                target="_blank" rel="noopener">
                                <img src="{{ \App\Support\ImageUrl::public($image->image_path) }}"
                                    alt="{{ $project->title }}" loading="lazy" decoding="async"
                                    class="aspect-square w-full object-cover transition-transform duration-300 group-hover:scale-105">
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    </main>

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
