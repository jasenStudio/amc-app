<button
    type="button"
    class="rounded-md p-2 text-white transition-colors hover:text-amc-orange focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amc-orange focus-visible:ring-offset-2 focus-visible:ring-offset-amc-blue md:hidden"
    @click="open = !open"
    :aria-expanded="open.toString()"
    aria-controls="mobile-navigation"
    :aria-label="open ? '{{ __('Cerrar menú') }}' : '{{ __('Abrir menú') }}'"
>
    <span class="sr-only" x-text="open ? '{{ __('Cerrar menú') }}' : '{{ __('Abrir menú') }}'"></span>
    <svg x-show="!open" x-cloak class="size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
    </svg>
    <svg x-show="open" x-cloak class="size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18" />
    </svg>
</button>
