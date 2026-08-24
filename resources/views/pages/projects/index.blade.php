<x-layouts::app :title="__('Proyectos | AMC Gestión de Riesgos')" :description="__('Conoce los proyectos y resultados prácticos de AMC Gestión de Riesgos.')">
    <x-header />

    <main class=" bg-amc-gray-bg">
        <div class="mx-auto max-w-7xl px-6 py-24 lg:px-8">


            <h1 class="mt-4 text-4xl font-semibold tracking-tight text-amc-blue sm:text-5xl">
                {{ __('Selected work and practical results.') }}
            </h1>
            <p class="mt-6 max-w-2xl text-lg leading-8 text-amc-gray-text">
                {{ __('Explore our portfolio of risk management projects and practical results.') }}
            </p>

            @if ($projects->count() > 0)
                <div class="mt-12 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($projects as $project)
                        <a href="{{ route('projects.show', $project->slug) }}" class="group block">
                            <article
                                class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-zinc-200 transition hover:shadow-md dark:bg-zinc-800 dark:ring-zinc-700">
                                @if ($project->coverImage)
                                    <div class="aspect-video overflow-hidden bg-zinc-100 dark:bg-zinc-700">
                                        <img src="{{ \App\Support\ImageUrl::public($project->coverImage->image_path) }}"
                                            alt="{{ $project->title }}"
                                            class="h-full w-full object-cover transition group-hover:scale-105">
                                    </div>
                                @else
                                    <div class="aspect-video bg-zinc-100 dark:bg-zinc-700"></div>
                                @endif

                                <div class="p-6">
                                    <h2
                                        class="text-xl font-semibold text-amc-blue group-hover:text-amc-orange-text transition dark:text-white">
                                        {{ $project->title }}
                                    </h2>
                                    <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                                        {{ $project->client }}
                                        @if ($project->location)
                                            &middot; {{ $project->location }}
                                        @endif
                                    </p>
                                    @if ($project->excerpt)
                                        <p class="mt-3 text-sm leading-6 text-amc-gray-text line-clamp-3">
                                            {{ $project->excerpt }}
                                        </p>
                                    @endif
                                    <div class="mt-4 flex items-center gap-2 text-xs text-zinc-500 dark:text-zinc-400">
                                        @if ($project->featured)
                                            <flux:badge color="amber" size="sm">{{ __('Featured') }}</flux:badge>
                                        @endif
                                        <time datetime="{{ $project->date?->toIso8601String() }}">
                                            {{ $project->date?->format('M Y') }}
                                        </time>
                                    </div>
                                </div>
                            </article>
                        </a>
                    @endforeach
                </div>

                @if ($projects->hasPages())
                    <div class="mt-12">
                        {{ $projects->links() }}
                    </div>
                @endif
            @else
                <div
                    class="mt-12 rounded-lg border-2 border-dashed border-zinc-300 p-12 text-center dark:border-zinc-600">
                    <flux:text class="text-zinc-500">{{ __('No projects available yet.') }}</flux:text>
                </div>
            @endif
        </div>
    </main>
</x-layouts::app>
