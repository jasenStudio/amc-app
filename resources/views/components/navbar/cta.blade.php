@props([
    'href' => '#',
])

<a href="{{ $href }}" {{ $attributes }} @click="open = false"
    class="rounded-md bg-amc-orange-text px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-amc-orange-hover focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amc-orange focus-visible:ring-offset-2 focus-visible:ring-offset-amc-blue">
    {{ __('COTIZAR AHORA') }}
</a>
