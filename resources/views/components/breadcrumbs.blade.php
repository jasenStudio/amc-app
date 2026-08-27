@props(['items' => []])

<nav aria-label="{{ __('Breadcrumb') }}" class="mb-6">
    <ol class="flex flex-wrap items-center gap-1.5 text-sm text-amc-gray-text">
        @foreach ($items as $item)
            <li class="flex items-center gap-1.5">
                @if (!$loop->first)
                    <span aria-hidden="true" class="text-amc-gray-text/50">/</span>
                @endif
                @if (!empty($item['href']) && !$loop->last)
                    <a href="{{ $item['href'] }}" class="transition hover:text-amc-orange-text focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amc-orange focus-visible:ring-offset-2">{{ $item['label'] }}</a>
                @else
                    <span @if ($loop->last) aria-current="page" class="font-medium text-amc-blue" @endif>{{ $item['label'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
