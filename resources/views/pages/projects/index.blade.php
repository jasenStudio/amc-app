<x-layouts::app :title="__('Proyectos | AMC Gestión de Riesgos')" :description="__('Conoce los proyectos y resultados prácticos de AMC Gestión de Riesgos.')">
    <x-header />

    <main id="main-content" class="bg-amc-gray-bg">
        <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amc-orange-text">
                {{ __('projects_page_kicker') }}
            </p>
            <h1 class="mt-4 max-w-3xl text-4xl font-semibold tracking-tight text-amc-blue sm:text-5xl">
                {{ __('projects_page_title') }}
            </h1>
            <p class="mt-6 max-w-2xl text-lg leading-8 text-amc-gray-text">
                {{ __('projects_page_subtitle') }}
            </p>

            @if ($projects->count() > 0)
                <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($projects as $project)
                        <x-projects.card :project="$project" />
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

    <x-footer />
</x-layouts::app>
