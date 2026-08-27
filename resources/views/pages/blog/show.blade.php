<x-layouts::app :title="$post->seoTitle()" :description="$post->seoDescription()" :ogImage="\App\Support\ImageUrl::public($post->seoImage())" ogType="article" :publishedTime="$post->published_at?->toIso8601String()">
    <x-header />

    <main id="main-content" class="bg-amc-gray-bg">
        <div class="mx-auto max-w-4xl px-6 py-10 lg:px-8">
            <x-breadcrumbs :items="[
                ['label' => __('Inicio'), 'href' => route('home')],
                ['label' => __('Blog'), 'href' => route('blog')],
                ['label' => $post->title],
            ]" />

            @if ($post->tags->isNotEmpty())
                <div class="mb-4 flex flex-wrap gap-2">
                    @foreach ($post->tags as $tag)
                        <a href="{{ route('blog', ['tag' => $tag->slug]) }}"
                            class="rounded-full bg-amc-orange/10 px-3 py-1 text-xs font-medium text-amc-orange-text hover:bg-amc-orange/20 transition">
                            {{ $tag->name }}
                        </a>
                    @endforeach
                </div>
            @endif

            <h1 class="text-4xl font-semibold tracking-tight text-amc-blue sm:text-5xl">
                {{ $post->title }}
            </h1>

            <div class="mt-4 flex items-center gap-3 text-sm text-zinc-500">
                @if ($post->author)
                    <span>{{ $post->author->name }}</span>
                    <span>&middot;</span>
                @endif

                <time datetime="{{ $post->published_at?->toIso8601String() }}">
                    {{ $post->published_at?->format('M d, Y') }}
                </time>
            </div>

            @if ($post->excerpt)
                <p class="mt-6 text-lg leading-8 text-zinc-400 ">
                    {{ $post->excerpt }}
                </p>
            @endif

            @if ($post->cover_image_url)
                <div class="mt-8 aspect-video w-full overflow-hidden rounded-xl bg-zinc-100">
                    <img src="{{ $post->cover_image_url }}" alt="{{ $post->title }}"
                        class="h-full w-full object-cover" loading="lazy">
                </div>
            @endif

            <article
                class="prose article-prose-content dark:prose-invert prose-amc mt-5 mb-5 max-w-none text-amc-blue/80">
                {!! $post->body !!}
            </article>

            <x-commenter:: :model="$post" />
        </div>

    </main>

    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "BlogPosting",
        "headline": {{ json_encode($post->title) }},
        "description": {{ json_encode($post->seoDescription() ?: \Illuminate\Support\Str::limit(strip_tags($post->excerpt ?? ''), 160)) }},
        "datePublished": {{ json_encode($post->published_at?->toIso8601String()) }},
        "dateModified": {{ json_encode($post->updated_at?->toIso8601String()) }},
        "author": {
            "@@type": "Organization",
            "name": "AMC Gestión de Riesgos SAS"
        },
        "publisher": {
            "@@type": "Organization",
            "name": "AMC Gestión de Riesgos SAS",
            "logo": {
                "@@type": "ImageObject",
                "url": {{ json_encode(asset('assets/images/logo.webp')) }}
            }
        },
        "mainEntityOfPage": {
            "@@type": "WebPage",
            "@id": {{ json_encode(route('blog.show', $post->slug)) }}
        }@if($post->seoImage()), "image": {{ json_encode(\App\Support\ImageUrl::public($post->seoImage())) }}@endif
    }
    </script>

    <x-footer />
</x-layouts::app>
