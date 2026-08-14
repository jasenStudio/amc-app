<x-layouts::app :title="__('Home')" :description="__('AMC creates clear digital experiences for ambitious teams.')">

    <x-header />

    <main>
        <section class="relative isolate overflow-hidden bg-amc-blue" id="hero">
            <picture>
                <!-- 1. Imagen para pantallas pequeñas (Móviles) hasta 767px -->
                <source media="(max-width: 767px)" srcset="{{ asset('assets/images/hero-amc-gestion-riesgo.webp') }}">

                <!-- 2. Imagen para pantallas medianas/grandes (Desktop) desde 768px -->
                <source media="(min-width: 768px)" srcset="{{ asset('assets/images/hero-acm-desktop.webp') }}">

                <!-- 3. Etiqueta img de respaldo (aplica los estilos CSS y atributos de prioridad) -->
                <img src="{{ asset('assets/images/hero-acm-desktop.webp') }}"
                    alt="Técnico de AMC Gestión de Riesgos realizando trabajo en altura con anclaje certificado"
                    fetchpriority="high"
                    class="absolute inset-0 h-full w-full object-cover object-[70%_20%] md:object-[80%_10%] grayscale-15">
            </picture>

            <img src="{{ asset('assets/images/hero-acm-desktop.webp') }}"
                alt="Técnico de AMC Gestión de Riesgos realizando trabajo en altura con anclaje certificado"
                fetchpriority="high"
                class="absolute inset-0 h-full w-full object-cover object-[70%_10%] md:object-[80%_10%] 
                grayscale-15 
                ">

            <div class="hero-gradient absolute inset-0"></div>
            <div class="relative mx-auto flex min-h-screen max-w-7xl items-center px-6 py-24 lg:px-8">
                <div class="max-w-2xl text-white">
                    <p class="mb-6 text-sm font-semibold uppercase tracking-[0.24em] text-amc-orange">
                        {{ __('Certificación ONAC / Res. 4272') }}</p>
                    <h1 class="text-5xl font-semibold tracking-tight sm:text-6xl lg:text-7xl">
                        {{ __('Protegemos vidas mediante') }}
                        <span class="text-amc-orange">
                            {{ __('soluciones certificadas.') }}
                        </span>
                    </h1>
                    <p class="mt-6 max-w-xl text-lg leading-8 text-white/80">
                        {{ __('La seguridad de su equipo es nuestra mayor responsabilidad.') }}
                    </p>
                    <div class="mt-10 flex flex-wrap items-center gap-4">
                        <a href="#services"
                            class="rounded-md bg-amc-orange px-6 py-3 text-sm font-semibold text-white transition hover:bg-amc-orange-hover">
                            {{ __('Solicitar acesoría') }}
                        </a>
                        <a href="#contact"
                            class="rounded-md border border-white/50 px-6 py-3 text-sm font-semibold text-white transition hover:border-white hover:bg-white/10">
                            {{ __('Ver portafolio') }}
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <section id="about" class="bg-amc-gray-bg">
            <div class="mx-auto grid max-w-7xl gap-12 px-6 py-24 lg:grid-cols-2 lg:items-center lg:px-8">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amc-orange">{{ __('About AMC') }}
                    </p>
                    <h2 class="mt-4 max-w-xl text-3xl font-semibold tracking-tight text-amc-blue sm:text-4xl">
                        {{ __('A clear foundation for meaningful work.') }}</h2>
                </div>
                <div class="space-y-5 text-lg leading-8 text-amc-gray-text">
                    <p>{{ __('We help organizations turn complex challenges into focused, useful, and memorable digital products.') }}
                    </p>
                    <p>{{ __('Our approach is collaborative, practical, and built around outcomes that matter to your team and your audience.') }}
                    </p>
                    <a href="{{ route('about') }}"
                        class="inline-flex text-base font-semibold text-amc-orange transition hover:text-amc-orange-hover">
                        {{ __('Conoce más sobre nosotros') }} <span aria-hidden="true">&rarr;</span>
                    </a>
                </div>
            </div>
        </section>

        <section id="services" class="mx-auto max-w-7xl px-6 py-24 lg:px-8">
            <div class="max-w-2xl">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amc-orange">{{ __('What we do') }}</p>
                <h2 class="mt-4 text-3xl font-semibold tracking-tight text-amc-blue sm:text-4xl">
                    {{ __('Services designed around progress.') }}</h2>
            </div>

            <div class="mt-12 grid gap-6 md:grid-cols-3">
                @foreach ([['icon' => 'fa-lightbulb', 'title' => __('Strategy'), 'description' => __('Find the right direction, clarify priorities, and create a plan your team can act on.')], ['icon' => 'fa-pen-ruler', 'title' => __('Design'), 'description' => __('Shape intuitive experiences that make your brand useful, distinctive, and easy to trust.')], ['icon' => 'fa-rocket', 'title' => __('Delivery'), 'description' => __('Move from idea to launch with thoughtful technology and a pragmatic delivery process.')]] as $service)
                    <article class="group relative min-h-80 overflow-hidden rounded-2xl bg-amc-blue p-8 text-white">
                        <div
                            class="absolute inset-0 opacity-0 transition duration-300 group-hover:opacity-100 service-card-gradient">
                        </div>
                        <div class="relative flex h-full flex-col">
                            <i class="fa-solid {{ $service['icon'] }} text-3xl text-amc-orange" aria-hidden="true"></i>
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

        <section id="projects" class="bg-amc-gray-bg">
            <div class="mx-auto max-w-7xl px-6 py-24 lg:px-8">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amc-orange">{{ __('Projects') }}</p>
                <h2 class="mt-4 text-3xl font-semibold tracking-tight text-amc-blue sm:text-4xl">
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

        <section id="blog" class="mx-auto max-w-7xl px-6 py-24 lg:px-8">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amc-orange">{{ __('Blog') }}</p>
            <h2 class="mt-4 text-3xl font-semibold tracking-tight text-amc-blue sm:text-4xl">
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

        <section id="contact" class="bg-amc-blue text-white">
            <div
                class="mx-auto flex max-w-7xl flex-col gap-8 px-6 py-24 sm:flex-row sm:items-end sm:justify-between lg:px-8">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amc-orange">{{ __('Contact') }}
                    </p>
                    <h2 class="mt-4 max-w-xl text-3xl font-semibold tracking-tight sm:text-4xl">
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
                    class="transition hover:text-amc-orange"><i class="fa-brands fa-linkedin-in"></i></a>
                <a href="https://github.com" target="_blank" rel="noopener noreferrer" aria-label="GitHub"
                    class="transition hover:text-amc-orange"><i class="fa-brands fa-github"></i></a>
                <a href="mailto:hello@example.com" aria-label="Email" class="transition hover:text-amc-orange"><i
                        class="fa-solid fa-envelope"></i></a>
            </div>
            <span>&copy; {{ date('Y') }} AMC. {{ __('All rights reserved.') }}</span>
        </div>
    </footer>
</x-layouts::app>
