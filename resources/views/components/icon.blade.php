@props(['name'])

@php
    $path = resource_path("svg/{$name}.svg");
    abort_unless(file_exists($path), 404, "Icon [{$name}] not found.");
    $svg = file_get_contents($path);
    $svg = preg_replace('/\s*class="[^"]*"/', '', $svg, 1);
@endphp

<span class="icon-metric">

    {!! $svg !!}
</span>
