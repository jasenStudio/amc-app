<x-layouts::app :title="__('Líneas de Vida Certificadas y Seguridad en Alturas | AMC Gestión de Riesgos')" :description="__(
    'AMC Gestión de Riesgos SAS ofrece instalación de líneas de vida, puntos de anclaje certificados, seguridad en alturas y asesorías SG-SST para empresas.',
)">

    <x-header />

    <main>
        <x-sections.hero />

        <x-sections.metrics />

        <x-sections.about />

        <section id="services" aria-labelledby="services-title" class="mx-auto max-w-7xl px-6 py-24 lg:px-8">
            <div class="max-w-2xl">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amc-orange">{{ __('What we do') }}</p>
                <h2 id="services-title" class="mt-4 text-3xl font-semibold tracking-tight text-amc-blue sm:text-4xl">
                    {{ __('Services designed around progress.') }}</h2>
            </div>

            <div class="mt-12 grid gap-6 md:grid-cols-3">
                @foreach ([['icon' => 'lightbulb', 'title' => __('Strategy'), 'description' => __('Find the right direction, clarify priorities, and create a plan your team can act on.')], ['icon' => 'pencil-ruler', 'title' => __('Design'), 'description' => __('Shape intuitive experiences that make your brand useful, distinctive, and easy to trust.')], ['icon' => 'rocket', 'title' => __('Delivery'), 'description' => __('Move from idea to launch with thoughtful technology and a pragmatic delivery process.')]] as $service)
                    <article class="group relative min-h-80 overflow-hidden rounded-2xl bg-amc-blue p-8 text-white">
                        <div
                            class="absolute inset-0 opacity-0 transition duration-300 group-hover:opacity-100 service-card-gradient">
                        </div>
                        <div class="relative flex h-full flex-col">
                            <x-icon :name="$service['icon']" class="size-8 text-amc-orange" aria-hidden="true" />
                            <h3 class="mt-auto text-2xl font-semibold">{{ $service['title'] }}</h3>
                            <p class="mt-3 leading-7 text-white/70">{{ $service['description'] }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
            <a href="{{ route('services') }}"
                class="mt-10 inline-flex text-base font-semibold text-amc-orange transition hover:text-amc-orange-hover">
                {{ __('Ver todos los servicios') }} <span aria-hidden="true">&rarr;</span>
            </a>
        </section>

        <section id="projects" aria-labelledby="projects-title" class="bg-amc-gray-bg">
            <div class="mx-auto max-w-7xl px-6 py-24 lg:px-8">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amc-orange">{{ __('Projects') }}</p>
                <h2 id="projects-title" class="mt-4 text-3xl font-semibold tracking-tight text-amc-blue sm:text-4xl">
                    {{ __('Selected work and practical results.') }}
                </h2>
                <p class="mt-5 max-w-2xl text-lg leading-8 text-amc-gray-text">
                    {{ __('Explore projects where strategy, design, and delivery came together to create measurable progress.') }}
                </p>
                <a href="{{ route('projects') }}"
                    class="mt-8 inline-flex text-base font-semibold text-amc-orange transition hover:text-amc-orange-hover">
                    {{ __('Ver proyectos') }} <span aria-hidden="true">&rarr;</span>
                </a>
            </div>
        </section>

        <section id="blog" aria-labelledby="blog-title" class="mx-auto max-w-7xl px-6 py-24 lg:px-8">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amc-orange">{{ __('Blog') }}</p>
            <h2 id="blog-title" class="mt-4 text-3xl font-semibold tracking-tight text-amc-blue sm:text-4xl">
                {{ __('Ideas for moving forward.') }}
            </h2>
            <p class="mt-5 max-w-2xl text-lg leading-8 text-amc-gray-text">
                {{ __('Read our latest perspectives, practical guides, and lessons from the work we do.') }}
            </p>
            <a href="{{ route('blog') }}"
                class="mt-8 inline-flex text-base font-semibold text-amc-orange transition hover:text-amc-orange-hover">
                {{ __('Ir al blog') }} <span aria-hidden="true">&rarr;</span>
            </a>
        </section>

        <section id="contact" aria-labelledby="contact-title" class="bg-amc-blue text-white">
            <div
                class="mx-auto flex max-w-7xl flex-col gap-8 px-6 py-24 sm:flex-row sm:items-end sm:justify-between lg:px-8">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amc-orange">{{ __('Contact') }}
                    </p>
                    <h2 id="contact-title" class="mt-4 max-w-xl text-3xl font-semibold tracking-tight sm:text-4xl">
                        {{ __('Have a project in mind? Let us talk.') }}</h2>
                </div>
                <a href="mailto:hello@example.com"
                    class="shrink-0 rounded-full bg-amc-orange px-6 py-3 text-sm font-semibold text-white transition hover:bg-amc-orange-hover">
                    hello@example.com
                </a>
            </div>
        </section>
    </main>

    <footer class="border-t border-amc-gray-bg bg-white">
        <div
            class="mx-auto flex max-w-7xl flex-col gap-5 px-6 py-8 text-sm text-amc-gray-text sm:flex-row sm:items-center sm:justify-between lg:px-8">
            <span class="font-semibold text-amc-blue">AMC</span>
            <div class="flex items-center gap-5 text-lg">
                <a href="https://www.linkedin.com" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn"
                    class="transition hover:text-amc-orange"><x-icon-linkedin class="size-5" /></a>
                <a href="https://github.com" target="_blank" rel="noopener noreferrer" aria-label="GitHub"
                    class="transition hover:text-amc-orange"><x-icon-github class="size-5" /></a>
                <a href="mailto:hello@example.com" aria-label="Email" class="transition hover:text-amc-orange"><x-icon
                        name="mail" class="size-5" /></a>
            </div>
            <span>&copy; {{ date('Y') }} AMC. {{ __('All rights reserved.') }}</span>
        </div>
    </footer>
</x-layouts::app>
