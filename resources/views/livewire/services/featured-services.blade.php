@if ($services->count() > 0)
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
        @foreach ($services as $service)
            <x-services.card :service="$service" />
        @endforeach
    </div>
@else
    <div class="rounded-lg border-2 border-dashed border-zinc-300 p-8 text-center">
        <p class="text-sm text-zinc-500">{{ __('No services available yet.') }}</p>
    </div>
@endif
