@if ($errors->any())
    <flux:callout variant="danger" icon="exclamation-triangle" :heading="__('review_form_errors')">
        <ul class="list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </flux:callout>
@endif