@props(['label', 'href' => '#', 'items' => [], 'active' => false, 'variant' => 'desktop', 'section' => null])

@php
    $dropdownId = 'navbar-dropdown-' . \Illuminate\Support\Str::slug($label) . '-' . $variant;
    $buttonId = $dropdownId . '-button';
@endphp

<div x-data="{ dropdownOpen: {{ $active ? 'true' : 'false' }} }" @keydown.escape.stop="dropdownOpen = false" @click.outside="dropdownOpen = false"
    class="relative {{ $variant === 'mobile' ? 'w-full' : '' }}">
    <div class="flex items-center gap-2">
        <a href="{{ $href }}"
            @if ($active && ! $section) aria-current="page" @endif
            @if ($section) x-bind:aria-current="activeSection === '{{ $section }}' || {{ $active ? 'true' : 'false' }} ? 'page' : null" @endif
            @if ($variant === 'mobile') @click="$dispatch('navbar-close')" @endif
            class="flex-1 rounded-sm px-1 py-2 text-sm font-medium transition-colors hover:text-amc-orange focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amc-orange focus-visible:ring-offset-2 focus-visible:ring-offset-amc-blue {{ ! $section && $active ? 'text-amc-orange' : 'text-white/90' }}"
            @if ($section) x-bind:class="{ 'text-amc-orange': activeSection === '{{ $section }}' || {{ $active ? 'true' : 'false' }}, 'text-white/90': activeSection !== '{{ $section }}' && {{ $active ? 'false' : 'true' }} }" @endif>
            {{ $label }}
        </a>
        <button id="{{ $buttonId }}" type="button" @click="dropdownOpen = !dropdownOpen"
            :aria-expanded="dropdownOpen.toString()" aria-haspopup="true" aria-controls="{{ $dropdownId }}"
            aria-label="{{ __('Mostrar opciones de :label', ['label' => $label]) }}"
            class="rounded-sm py-2 text-white/90 transition-colors hover:text-amc-orange focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amc-orange focus-visible:ring-offset-2 focus-visible:ring-offset-amc-blue">
            <svg class="size-4 shrink-0 transition-transform" :class="{ 'rotate-180': dropdownOpen }"
                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                stroke-linejoin="round" aria-hidden="true">
                <path d="m6 9 6 6 6-6" />
            </svg>
        </button>
    </div>

    <div id="{{ $dropdownId }}" x-show="dropdownOpen" x-cloak role="menu" aria-labelledby="{{ $buttonId }}"
        x-transition:enter="transition duration-200 ease-out" x-transition:enter-start="-translate-y-2 opacity-0"
        x-transition:enter-end="translate-y-0 opacity-100" x-transition:leave="transition duration-150 ease-in"
        x-transition:leave-start="translate-y-0 opacity-100" x-transition:leave-end="-translate-y-2 opacity-0"
        class="{{ $variant === 'mobile' ? 'ml-3 border-l border-white/15 py-2 pl-4' : 'absolute left-0 top-full z-20 mt-2 min-w-48 rounded-md border border-white/10 bg-amc-blue p-2 shadow-xl' }}">
        <div class="flex flex-col gap-1">
            @foreach ($items as $item)
                <div @click="dropdownOpen = false{{ $variant === 'mobile' ? '; $dispatch(\'navbar-close\')' : '' }}">
                    <x-navbar.link :href="$item['href']" :label="$item['label']" :active="$item['active'] ?? false" />
                </div>
            @endforeach
        </div>
    </div>
</div>
