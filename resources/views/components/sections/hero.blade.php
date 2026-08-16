<!-- TODO AGREGAR LOS VINCULOS PARA LOS CTA -->
<section id="hero" aria-labelledby="hero-accessible-title" class="relative isolate overflow-hidden bg-amc-blue">
    <picture>
        <source media="(max-width: 767px)" srcset="{{ asset('assets/images/hero-amc-gestion-riesgo.webp') }}">
        <source media="(min-width: 768px)" srcset="{{ asset('assets/images/hero-amc-desktop.webp') }}">
        <img src="{{ asset('assets/images/hero-amc-desktop.webp') }}"
            alt="Técnico de AMC Gestión de Riesgos realizando trabajo en altura con anclaje certificado"
            fetchpriority="high"
            class="absolute inset-0 h-full w-full object-cover object-[70%_20%] md:object-[80%_10%] grayscale-15">
    </picture>

    <div class="hero-gradient absolute inset-0" aria-hidden="true"></div>

    <div class="relative mx-auto flex min-h-screen max-w-7xl items-center px-6 py-24 lg:px-8">
        <div class="max-w-2xl text-white">
            <h1 id="hero-accessible-title" class="sr-only">
                {{ __('Inicio - AMC Gestión de Riesgos') }}
            </h1>
            <p class="mb-6 text-sm font-semibold uppercase tracking-[0.24em] text-amc-orange">
                {{ __('Certificación ONAC / Res. 4272') }}</p>
            <h2 class="text-5xl font-semibold tracking-tight sm:text-6xl lg:text-7xl">
                {{ __('Protegemos vidas mediante') }}
                <span class="text-amc-orange">
                    {{ __('soluciones certificadas.') }}
                </span>
            </h2>
            <p class="mt-6 max-w-xl text-lg leading-8 text-white/80">
                {{ __('La seguridad de su equipo es nuestra mayor responsabilidad.') }}
            </p>
            <div class="mt-10 flex flex-wrap items-center gap-4">
                <a href="#services"
                    class="rounded-md bg-amc-orange px-6 py-3 text-sm font-semibold text-white transition hover:bg-amc-orange-hover focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-amc-blue">
                    {{ __('Solicitar asesoría') }}
                </a>
                <a href="#contact"
                    class="rounded-md border border-white/50 px-6 py-3 text-sm font-semibold text-white transition hover:border-white hover:bg-white/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-amc-blue">
                    {{ __('Ver portafolio') }}
                </a>
            </div>
        </div>
    </div>
</section>
