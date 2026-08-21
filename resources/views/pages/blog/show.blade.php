<x-layouts::app :title="$post->seoTitle()" :description="$post->seoDescription()">
    @if ($post->seoImage())
        <meta property="og:image" content="{{ \App\Support\ImageUrl::public($post->seoImage()) }}">
    @endif
    <meta property="og:type" content="article">
    <meta property="article:published_time" content="{{ $post->published_at?->toIso8601String() }}">
    <x-header />

    <main class="bg-amc-gray-bg">
        <div class="mx-auto max-w-4xl px-6 py-10 lg:px-8">
            <a href="{{ route('blog') }}"
                class="mb-8 inline-flex text-sm font-medium text-amc-orange-text hover:text-amc-orange-hover transition">
                &larr; {{ __('Back to blog') }}
            </a>

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
</x-layouts::app>
