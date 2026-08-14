<x-layouts::app :title="__('Home')" :description="__('AMC creates clear digital experiences for ambitious teams.')">

    <x-header />

    <main>
        <section class="relative isolate overflow-hidden bg-amc-blue">
            <div
                class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1497366811353-6870744d04b2?auto=format&fit=crop&w=2200&q=80')] bg-cover bg-center grayscale-hover">
            </div>
            <div class="hero-gradient absolute inset-0"></div>
            <div class="relative mx-auto flex min-h-screen max-w-7xl items-center px-6 py-24 lg:px-8">
                <div class="max-w-2xl text-white">
                    <p class="mb-6 text-sm font-semibold uppercase tracking-[0.24em] text-amc-orange">
                        {{ __('Welcome to AMC') }}</p>
                    <h1 class="text-5xl font-semibold tracking-tight sm:text-6xl lg:text-7xl">
                        {{ __('Ideas that move your business forward.') }}
                    </h1>
                    <p class="mt-6 max-w-xl text-lg leading-8 text-white/80">
                        {{ __('We combine strategy, design, and technology to build digital experiences that create lasting impact.') }}
                    </p>
                    <div class="mt-10 flex flex-wrap items-center gap-4">
                        <a href="#services"
                            class="rounded-full bg-amc-orange px-6 py-3 text-sm font-semibold text-white transition hover:bg-amc-orange-hover">
                            {{ __('Explore services') }}
                        </a>
                        <a href="#contact"
                            class="rounded-full border border-white/50 px-6 py-3 text-sm font-semibold text-white transition hover:border-white hover:bg-white/10">
                            {{ __('Start a conversation') }}
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
