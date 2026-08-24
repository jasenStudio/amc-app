<x-layouts::app :title="$project->seoTitle() . ' | AMC Gestión de Riesgos'" :description="$project->seoDescription()">
    @if ($project->seoImage())
        <meta property="og:image" content="{{ \App\Support\ImageUrl::public($project->seoImage()) }}">
    @endif
    <meta property="og:type" content="article">

    <x-header />

    <main class="bg-amc-gray-bg">
        <div class="mx-auto max-w-6xl px-6 py-10 lg:px-8">
            <a href="{{ route('projects') }}"
                class="mb-8 inline-flex text-sm font-medium text-amc-orange-text hover:text-amc-orange-hover transition">
                &larr; {{ __('Back to projects') }}
            </a>

            <div class="max-w-3xl mb-4 flex flex-wrap items-center gap-3 text-sm text-zinc-500">
                <span class="font-medium text-amc-blue">{{ $project->client }}</span>
                @if ($project->location)
                    <span>&middot;</span>
                    <span>{{ $project->location }}</span>
                @endif
                <span>&middot;</span>
                <time datetime="{{ $project->date?->toIso8601String() }}">
                    {{ $project->date?->format('M d, Y') }}
                </time>
                @if ($project->featured)
                    <flux:badge color="amber" size="sm">{{ __('Featured') }}</flux:badge>
                @endif
            </div>

            <h1 class="text-4xl font-semibold tracking-tight text-amc-blue sm:text-5xl">
                {{ $project->title }}
            </h1>

            @if ($project->excerpt)
                <p class="mt-6 text-lg leading-8 text-zinc-400">
                    {{ $project->excerpt }}
                </p>
            @endif

            @if ($project->coverImage)
                <div class="mt-8 aspect-video w-full overflow-hidden rounded-xl bg-zinc-100">
                    <img src="{{ \App\Support\ImageUrl::public($project->coverImage->image_path) }}"
                        alt="{{ $project->title }}" class="h-full w-full object-cover" loading="lazy">
                </div>
            @endif


            {{-- Project content --}}
            <div class="mx-auto mt-12 max-w-3xl">
                <article
                    class="prose article-prose-content dark:prose-invert prose-amc mt-8 mb-8 max-w-none text-amc-blue/80">
                    {!! nl2br(e($project->description)) !!}
                </article>

                <!-- galeria-->
                @if ($project->images->count() > 1)
                    <div class="mt-12">
                        <h2 class="text-2xl font-semibold text-amc-blue">{{ __('Gallery') }}</h2>
                        <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3">
                            @foreach ($project->images as $image)
                                @if (!$image->is_cover)
                                    <div class="overflow-hidden rounded-lg bg-zinc-100 dark:bg-zinc-700">
                                        <img src="{{ \App\Support\ImageUrl::public($image->image_path) }}"
                                            alt="{{ $project->title }}" class="aspect-square w-full object-cover"
                                            loading="lazy">
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- video -->
                @if ($project->video_url && $videoEmbedUrl)
                    <div class="mt-8" x-data="{ playing: false }">
                        <div class="relative aspect-video overflow-hidden rounded-xl bg-zinc-900">
                            <template x-if="!playing">
                                <button @click="playing = true"
                                    class="absolute inset-0 flex h-full w-full items-center justify-center"
                                    aria-label="{{ __('Play video') }}">
                                    @if ($videoThumbnail)
                                        <img src="{{ $videoThumbnail }}" alt=""
                                            class="absolute inset-0 h-full w-full object-cover">
                                    @endif
                                    <div
                                        class="relative z-10 flex size-16 items-center justify-center rounded-full bg-amc-orange-text/90 text-white shadow-lg transition hover:bg-amc-orange-hover hover:scale-110">
                                        <svg class="size-8 ml-1" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z" />
                                        </svg>
                                    </div>
                                    @if (!$videoThumbnail)
                                        <div class="absolute inset-0 bg-gradient-to-br from-amc-blue/80 to-amc-blue/60">
                                        </div>
                                    @endif
                                </button>
                            </template>
                            <template x-if="playing">
                                <iframe src="{{ $videoEmbedUrl }}?autoplay=1" class="h-full w-full"
                                    allow="autoplay; encrypted-media" allowfullscreen></iframe>
                            </template>
                        </div>
                    </div>
                @endif



            </div>

        </div>
    </main>
</x-layouts::app>
