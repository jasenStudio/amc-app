@props(['project'])

<article class="group flex h-full flex-col overflow-hidden rounded-xl bg-amc-blue shadow-sm transition hover:shadow-md">
    <a href="{{ route('projects.show', $project->slug) }}"
        class="flex h-full flex-col focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-amc-orange focus-visible:ring-inset">

        @if ($project->coverImage)
            <div class="aspect-4/3 w-full overflow-hidden bg-zinc-100 dark:bg-zinc-700">
                <img src="{{ \App\Support\ImageUrl::public($project->coverImage->image_path) }}"
                    alt="{{ $project->title }}" loading="lazy" decoding="async"
                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
            </div>
        @else
            <div class="aspect-4/3 w-full bg-linear-to-br from-amc-blue to-amc-blue/80"></div>
        @endif

        <div class="flex flex-1 flex-col gap-3 p-6 text-white">
            <h2 class="text-xl font-semibold leading-tight sm:text-2xl">
                {{ $project->title }}
            </h2>

            <p class="text-sm text-white/70">
                {{ $project->client }}
                @if ($project->location)
                    &middot; {{ $project->location }}
                @endif
            </p>

            <p class="mt-1 text-sm leading-6 text-white/80">
                @if ($project->excerpt)
                    {{ $project->excerpt }}
                @else
                    {{ \Illuminate\Support\Str::limit(strip_tags($project->description), 160) }}
                @endif
            </p>

            <div class="mt-auto pt-6">
                <div class="border-t border-white/15"></div>
                <div class="mt-4 flex items-center justify-between text-sm text-white/70">
                    <time datetime="{{ $project->date?->toIso8601String() }}">
                        {{ $project->date?->format('M Y') }}
                    </time>
                    <span
                        class="text-xl leading-none text-amc-orange-text transition-transform group-hover:translate-x-1"
                        aria-hidden="true">&rarr;</span>
                </div>
            </div>
        </div>
    </a>
</article>
