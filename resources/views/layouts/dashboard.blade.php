<x-layouts::dashboard.sidebar :title="$title ?? null">
    <flux:main>
        {{ $slot }}
    </flux:main>
</x-layouts::dashboard.sidebar>
