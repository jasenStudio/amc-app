@props([
    'href' => '#',
    'label',
    'active' => false,
    'section' => null,
])

<a href="{{ $href }}" {{ $attributes }} @if ($active && !$section) aria-current="page" @endif
    @if ($section) x-bind:aria-current="activeSection === '{{ $section }}' ? 'page' : null" @endif
    @click="open = false"
    class="rounded-sm px-1 py-2 text-sm font-medium transition-colors hover:text-amc-orange focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amc-orange focus-visible:ring-offset-2 focus-visible:ring-offset-amc-blue {{ !$section && $active ? 'text-amc-orange' : 'text-white/90' }}"
    @if ($section) x-bind:class="{ 'text-amc-orange': activeSection === '{{ $section }}', 'text-white/90': activeSection !== '{{ $section }}' }" @endif>
    {{ $label }}
</a>
