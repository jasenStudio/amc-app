@props([
    'title' => __('projects_hero_title'),
    'subtitle' => __('projects_hero_subtitle'),
    'viewAllLabel' => __('projects_view_all'),
])

@if ($hasFeaturedProjects)
    <section id="projects" aria-labelledby="projects-section-title" class="bg-amc-gray-bg">
        <div class="mx-auto max-w-7xl px-6 py-28 lg:px-8">
            <h2 id="projects-section-title"
                class="max-w-3xl text-3xl font-medium  leading-[1.1] tracking-tight text-amc-blue sm:text-5xl lg:text-5xl">
                {{ $title }}
            </h2>

            <livewire:projects.featured-projects />

            <a href="{{ route('projects') }}" data-id="link-to-projects"
                class="mt-12 inline-flex text-base font-semibold text-amc-orange-text transition hover:text-amc-orange-hover">
                {{ $viewAllLabel }} <span aria-hidden="true">&rarr;</span>
            </a>
        </div>
    </section>
@endif
