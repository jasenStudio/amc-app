<footer class="bg-amc-blue text-white">
    <div class="mx-auto max-w-7xl px-6 lg:px-8 py-12 grid grid-cols-1 gap-10 md:grid-cols-2 lg:grid-cols-4">

        <div class="flex flex-col gap-4">
            <span class="self-start">
                <x-navbar.brand />
            </span>
            <div class="text-sm text-white/80 space-y-1">
                <p>AMC Gestión de Riesgos SAS</p>
                <p><a href="tel:+573147874006"
                        class="transition hover:text-amc-orange focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amc-orange focus-visible:ring-offset-2 focus-visible:ring-offset-amc-blue">+573147874006</a>
                </p>
                <p><a href="mailto:gerencia@amcgestiondelriesgo.com.co"
                        class="transition hover:text-amc-orange focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amc-orange focus-visible:ring-offset-2 focus-visible:ring-offset-amc-blue">gerencia@amcgestiondelriesgo.com.co</a>
                </p>
            </div>
        </div>

        <div x-data="{ open: false }">
            <button type="button" @click="open = !open"
                class="flex w-full items-center justify-between gap-2 md:pointer-events-none md:cursor-default">
                <h3 class="text-amc-orange font-semibold text-sm uppercase tracking-wide mb-4">{{ __('Navegación') }}
                </h3>
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="mb-4 size-4 shrink-0 text-amc-orange transition-transform md:hidden"
                    :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <ul class="space-y-2 text-sm md:block" :class="{ 'hidden': !open }">
                <li><a href="{{ route('home') }}"
                        class="text-white/80 transition hover:text-amc-orange focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amc-orange focus-visible:ring-offset-2 focus-visible:ring-offset-amc-blue">{{ __('Inicio') }}</a>
                </li>
                <li><a href="{{ route('home') }}#about"
                        class="text-white/80 transition hover:text-amc-orange focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amc-orange focus-visible:ring-offset-2 focus-visible:ring-offset-amc-blue">{{ __('Nosotros') }}</a>
                </li>
                <li><a href="{{ route('home') }}#services"
                        class="text-white/80 transition hover:text-amc-orange focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amc-orange focus-visible:ring-offset-2 focus-visible:ring-offset-amc-blue">{{ __('Servicios') }}</a>
                </li>
                <li><a href="{{ route('projects') }}"
                        class="text-white/80 transition hover:text-amc-orange focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amc-orange focus-visible:ring-offset-2 focus-visible:ring-offset-amc-blue">{{ __('Proyectos') }}</a>
                </li>
                <li><a href="{{ route('blog') }}"
                        class="text-white/80 transition hover:text-amc-orange focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amc-orange focus-visible:ring-offset-2 focus-visible:ring-offset-amc-blue">{{ __('Blog') }}</a>
                </li>
                <li><a href="{{ route('home') }}#contact"
                        class="text-white/80 transition hover:text-amc-orange focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amc-orange focus-visible:ring-offset-2 focus-visible:ring-offset-amc-blue">{{ __('Contacto') }}</a>
                </li>
            </ul>
        </div>

        <div>
            <h3 class="text-amc-orange font-semibold text-sm uppercase tracking-wide mb-4">{{ __('Portafolio') }}</h3>
            <ul class="space-y-2 text-sm">
                <li>
                    <a href="{{ url('/portafolio') }}" target="_blank" rel="noopener noreferrer"
                        class="text-white/80 transition hover:text-amc-orange focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amc-orange focus-visible:ring-offset-2 focus-visible:ring-offset-amc-blue">
                        {{ __('Portafolio y Asesoría') }}
                    </a>
                </li>
            </ul>
        </div>

        <div>
            <h3 class="text-amc-orange font-semibold text-sm uppercase tracking-wide mb-4">{{ __('Síguenos') }}</h3>
            <div class="flex items-center gap-3">
                <a href="https://youtube.com/@amcgestiondelriesgo?si=CjEudva4Ce8gOzar" target="_blank"
                    aria-label="{{ __('YouTube') }}"
                    class="text-white/80 transition hover:text-amc-orange focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amc-orange focus-visible:ring-offset-2 focus-visible:ring-offset-amc-blue">
                    <x-icon-youtube class="size-5" />
                </a>
            </div>
        </div>
    </div>

    <div class="border-t border-white/15">
        <div
            class="mx-auto max-w-7xl px-6 lg:px-8 py-5 flex flex-col gap-3 text-sm text-white/60 sm:flex-row sm:items-center sm:justify-between">
            <p>&copy; {{ date('Y') }} AMC Gestión de Riesgos SAS. {{ __('Todos los derechos reservados.') }}</p>
            <a href="{{ route('privacy.policy') }}"
                class="text-white/60 underline-offset-4 transition hover:text-amc-orange hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amc-orange focus-visible:ring-offset-2 focus-visible:ring-offset-amc-blue">
                {{ __('Política de Tratamiento de Datos Personales') }}
            </a>
        </div>
    </div>
</footer>
