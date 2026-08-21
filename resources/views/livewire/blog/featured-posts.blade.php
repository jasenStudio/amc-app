<div class="mt-10">


    @if ($posts->isNotEmpty())
        <div class="grid md:grid-cols-3">
            @foreach ($posts as $post)
                <a href="{{ route('blog.show', $post->slug) }}"
                    class="group bg-background p-8 transition-colors
                hover:bg-amc-orange-hover/5 md:nth-[3n+2]:border-x md:nth-[3n+2]:border-zinc-200 ">
                    <p class="mb-6 font-mono text-[10px] uppercase tracking-widest text-muted-foreground">
                        {{ $post->published_at->translatedFormat('d \d\e F \d\e Y') }}</p>
                    <h3 class="text-lg font-semibold leading-snug text-amc-blue group-hover:text-amc-orange transition ">
                        {{ $post->title }}
                    </h3>
                </a>
            @endforeach
        </div>
    @endif
</div>
