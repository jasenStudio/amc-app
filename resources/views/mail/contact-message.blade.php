<p>Nuevo mensaje de contacto:</p>

<p><strong>Nombre:</strong> {{ $contactMessage->name }}</p>
<p><strong>Empresa:</strong> {{ $contactMessage->company ?? 'N/A' }}</p>
<p><strong>Servicio:</strong> {{ $contactMessage->service?->title ?? 'N/A' }}</p>

<p><strong>Mensaje:</strong></p>
<p>{{ $contactMessage->message }}</p>
