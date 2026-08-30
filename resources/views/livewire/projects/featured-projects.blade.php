<div>
    @if ($projects->count() > 0)
        <div class="mt-12 grid gap-x-12 gap-y-10 md:grid-cols-2">
            @foreach ($projects as $project)
                <a href="{{ route('projects.show', $project->slug) }}" class="group block focus-visible:outline-none">
                    <hr class="mb-6 border-0 border-t border-zinc-300 transition group-hover:border-amc-orange-text">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amc-blue">
                        {{ $project->client }}
                        @if ($project->location)
                            &mdash; {{ $project->location }}
                        @endif
                    </p>
                    <h3 class="mt-4 text-2xl font-medium  leading-snug text-amc-blue sm:text-3xl">
                        {{ $project->title }}
                    </h3>
                    @if ($project->excerpt)
                        <p class="mt-3 text-base leading-7 text-amc-gray-text">
                            {{ $project->excerpt }}
                        </p>
                    @else
                        <p class="mt-3 text-base leading-7 text-amc-gray-text">
                            {{ \Illuminate\Support\Str::limit(strip_tags($project->description), 180) }}
                        </p>
                    @endif
                </a>
            @endforeach
        </div>
    @endif
</div>
