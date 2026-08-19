<div>
    @if ($posts->isNotEmpty())
        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($posts as $post)
                <a href="{{ route('blog.show', $post->slug) }}" class="group block">
                    @if ($post->cover_image_thumb_url)
                        <img src="{{ $post->cover_image_thumb_url }}" alt="" class="aspect-[3/2] w-full rounded-lg object-cover" loading="lazy">
                    @else
                        <div class="aspect-[3/2] w-full rounded-lg bg-zinc-100 dark:bg-zinc-800"></div>
                    @endif
                    <h3 class="mt-3 text-base font-semibold text-amc-blue group-hover:text-amc-orange transition dark:text-white">
                        {{ $post->title }}
                    </h3>
                    @if ($post->excerpt)
                        <p class="mt-1 text-sm text-zinc-500 line-clamp-2">{{ $post->excerpt }}</p>
                    @endif
                </a>
            @endforeach
        </div>
    @endif
</div>