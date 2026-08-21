<x-layouts::app :title="__('AMC Gestión de Riesgos | Seguridad en alturas y líneas de vida')" :description="__(
    'AMC Gestión de Riesgos SAS ofrece instalación de líneas de vida, puntos de anclaje certificados, seguridad en alturas y asesorías SG-SST para empresas.',
)" bodyBg="bg-amc-blue">

    <x-header />

    <main>
        <x-sections.hero />

        <x-sections.metrics />

        <x-sections.about />

        <x-sections.services />

        <section id="projects" aria-labelledby="projects-title" class="bg-amc-gray-bg">
            <div class="mx-auto max-w-7xl px-6 py-24 lg:px-8">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amc-orange-text">{{ __('Projects') }}</p>
                <h2 id="projects-title" class="mt-4 text-3xl font-semibold tracking-tight text-amc-blue sm:text-4xl">
                    {{ __('Selected work and practical results.') }}
                </h2>
                <p class="mt-5 max-w-2xl text-lg leading-8 text-amc-gray-text">
                    {{ __('Explore projects where strategy, design, and delivery came together to create measurable progress.') }}
                </p>
                <a href="{{ route('projects') }}"
                    class="mt-8 inline-flex text-base font-semibold text-amc-orange-text transition hover:text-amc-orange-hover">
                    {{ __('Ver proyectos') }} <span aria-hidden="true">&rarr;</span>
                </a>
            </div>
        </section>

        <x-sections.blog />

        <section id="contact" aria-labelledby="contact-title" class="bg-amc-blue text-white">
            <div
                class="mx-auto flex max-w-7xl flex-col gap-8 px-6 py-24 sm:flex-row sm:items-end sm:justify-between lg:px-8">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amc-orange-text">
                        {{ __('Contact') }}
                    </p>
                    <h2 id="contact-title" class="mt-4 max-w-xl text-3xl font-semibold tracking-tight sm:text-4xl">
                        {{ __('Have a project in mind? Let us talk.') }}</h2>
                </div>
                <a href="mailto:hello@example.com"
                    class="shrink-0 rounded-full bg-amc-orange-text px-6 py-3 text-sm font-semibold text-white transition hover:bg-amc-orange-hover">
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
