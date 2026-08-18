<x-layouts::app :title="__('Blog')" :description="__('Read AMC perspectives, guides, and practical insights.')">
    <x-header />

    <main class="mx-auto max-w-7xl px-6 py-24 lg:px-8">
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amc-orange">{{ __('Blog') }}</p>
        <h1 class="mt-4 text-4xl font-semibold tracking-tight text-amc-blue sm:text-5xl">
            {{ __('Ideas for moving forward.') }}
        </h1>
        <p class="mt-6 max-w-2xl text-lg leading-8 text-amc-gray-text">
            {{ __('Read our latest perspectives, practical guides, and lessons from the work we do.') }}
        </p>

        @if ($tags->isNotEmpty())
            <div class="mt-8 flex flex-wrap gap-2">
                <a href="{{ route('blog') }}"
                    class="rounded-full px-3 py-1 text-sm font-medium transition {{ $activeTag === null ? 'bg-amc-orange text-white' : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700' }}">
                    {{ __('All') }}
                </a>
                @foreach ($tags as $tag)
                    <a href="{{ route('blog', ['tag' => $tag->slug]) }}"
                        class="rounded-full px-3 py-1 text-sm font-medium transition {{ $activeTag === $tag->slug ? 'bg-amc-orange text-white' : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700' }}">
                        {{ $tag->name }}
                    </a>
                @endforeach
            </div>
        @endif

        @if ($posts->isEmpty())
            <p class="mt-12 text-lg text-zinc-500">{{ __('No posts yet.') }}</p>
        @else
            <div class="mt-12 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($posts as $post)
                    <a href="{{ route('blog.show', $post->slug) }}" class="group block">
                        @if ($post->cover_image_thumb)
                            <img src="{{ \App\Support\ImageUrl::public($post->cover_image_thumb) }}" alt=""
                                class="aspect-3/2 w-full rounded-lg object-cover" loading="lazy">
                        @else
                            <div class="aspect-3/2 w-full rounded-lg bg-zinc-100 dark:bg-zinc-800"></div>
                        @endif
                        <div class="mt-4">
                            @if ($post->tags->isNotEmpty())
                                <div class="mb-2 flex flex-wrap gap-1">
                                    @foreach ($post->tags as $tag)
                                        <span class="text-xs font-medium text-amc-orange">{{ $tag->name }}</span>
                                    @endforeach
                                </div>
                            @endif
                            <h3
                                class="text-lg font-semibold text-amc-blue group-hover:text-amc-orange transition dark:text-white dark:group-hover:text-amc-orange">
                                {{ $post->title }}
                            </h3>
                            @if ($post->excerpt)
                                <p class="mt-1 text-sm text-zinc-500 line-clamp-2">{{ $post->excerpt }}</p>
                            @endif
                            <p class="mt-2 text-xs text-zinc-400">{{ $post->published_at?->format('M d, Y') }}</p>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-12">
                {{ $posts->links() }}
            </div>
        @endif
    </main>
</x-layouts::app>
