<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>
    {{ filled($title ?? null) ? $title . ' - ' . config('app.name', 'AMC Gestión de Riesgos | Seguridad en alturas y líneas de vida') : config('app.name', 'AMC Gestión de Riesgos | Seguridad en alturas y líneas de vida') }}
</title>

@php
    $seoTitle = $title ?? config('app.name', 'AMC Gestión de Riesgos | Seguridad en alturas y líneas de vida');
    $seoDescription = $description ?? 'AMC Gestión de Riesgos SAS ofrece instalación de líneas de vida, puntos de anclaje certificados, seguridad en alturas y asesorías SG-SST para empresas.';
    $canonicalUrl = url()->current();
    $ogImage = $ogImage ?? null;
    $ogType = $ogType ?? 'website';
@endphp
<meta name="description" content="{{ $seoDescription }}" />
<link rel="canonical" href="{{ $canonicalUrl }}" />
<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

<meta property="og:site_name" content="AMC Gestión de Riesgos SAS" />
<meta property="og:title" content="{{ $seoTitle }}" />
<meta property="og:description" content="{{ $seoDescription }}" />
<meta property="og:url" content="{{ $canonicalUrl }}" />
<meta property="og:type" content="{{ $ogType }}" />
@if ($ogType === 'article' && isset($publishedTime))
    <meta property="article:published_time" content="{{ $publishedTime }}" />
@endif
@if ($ogImage)
    <meta property="og:image" content="{{ $ogImage }}" />
@else
    <meta property="og:image" content="{{ asset('assets/images/logo.webp') }}" />
@endif
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="{{ $seoTitle }}" />
<meta name="twitter:description" content="{{ $seoDescription }}" />
@if ($ogImage)
    <meta name="twitter:image" content="{{ $ogImage }}" />
@endif

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "Organization",
    "name": "AMC Gestión de Riesgos SAS",
    "telephone": "+573147874006",
    "email": "gerencia@amcgestiondelriesgo.com.co",
    "address": {
        "@@type": "PostalAddress",
        "addressCountry": "CO"
    },
    "url": "{{ config('app.url') }}"
}
</script>

@vite(['resources/css/public.css', 'resources/js/public.js'])
