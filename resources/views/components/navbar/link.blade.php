@props([
    'href' => '#',
    'label',
    'active' => false,
])

<a
    href="{{ $href }}"
    @if ($active) aria-current="page" @endif
    @click="open = false"
    class="rounded-sm px-1 py-2 text-sm font-medium transition-colors hover:text-amc-orange focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amc-orange focus-visible:ring-offset-2 focus-visible:ring-offset-amc-blue {{ $active ? 'text-amc-orange' : 'text-white/90' }}"
>
    {{ $label }}
</a>
