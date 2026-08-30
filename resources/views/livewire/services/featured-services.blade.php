<div>
    @if ($services->count() > 0)
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($services as $service)
                <x-services.card :service="$service" />
            @endforeach
        </div>
    @endif
</div>