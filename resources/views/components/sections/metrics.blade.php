@props([
    'items' => [
        ['icon' => 'calendar-clock', 'value' => '10+', 'label' => __('Años')],
        ['icon' => 'building-2', 'value' => '500+', 'label' => __('Proyectos')],
        ['icon' => 'graduation-cap', 'value' => '5000+', 'label' => __('Capacitados')],
        ['icon' => 'shield-check', 'value' => '100%', 'label' => __('Cumplimiento legal')],
    ],
])

<section id="metrics" aria-labelledby="metrics-title" class="bg-amc-gray-bg">
    <div class="mx-auto max-w-7xl px-6 py-14 lg:px-8">
        <div class="grid gap-x-6 grid-cols-2 md:grid-cols-4">
            @foreach ($items as $item)
                <article class="flex flex-col items-center p-6 text-center">
                    <x-icon :name="$item['icon']" class="size-14 text-amc-blue" aria-hidden="true" />
                    <p class="mt-4 text-4xl font-bold text-amc-blue">{{ $item['value'] }}</p>
                    <p class="mt-2 text-sm tracking-wide text-amc-gray-text uppercase">{{ $item['label'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>
