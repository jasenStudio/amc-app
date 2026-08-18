<a href="{{ route('home') }}" aria-label="{{ __('AMC, ir al inicio') }}"
    class="flex justify-center items-center shrink-0 rounded-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amc-orange focus-visible:ring-offset-2 focus-visible:ring-offset-amc-blue">
    <img src="{{ asset('assets/images/logo.webp') }}" class="w-10 sm:w-8 md:w-10 lg:w-12 me-3"
        alt="Logo AMC Gestion del riesgo" />
    <div>
        <span
            class="block text-xl md:text-2xl lg:text-3xl font-bold leading-none tracking-[-0.08em] text-white sm:text-4xl">
            <span class="text-amc-orange">A</span>MC
        </span>
        <span
            class="mt-1 block text-[0.48rem] md:text-[0.50rem] lg:text-[0.52rem] font-bold uppercase leading-none tracking-[0.16em] text-white/75 sm:text-[0.6rem]">
            {{ __('Gestión de riesgos SAS') }}
        </span>
    </div>
</a>
