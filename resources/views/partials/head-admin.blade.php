<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
    window.__t = <?php echo json_encode([
        'url' => __('URL'),
        'imageUploadFailed' => __('Image upload failed.'),
        'youtubeUrl' => __('actions.youtube_url'),
        'youtubeUrlInvalid' => __('actions.youtube_url_invalid'),
        'selectImageFirst' => __('actions.select_image_first'),
        'imageAlt' => __('actions.image_alt'),
    ]); ?>
</script>

<title>
    {{ filled($title ?? null) ? $title . ' - ' . config('app.name', 'amc Gestion del riesgo') : config('app.name', 'amc Gestion del riesgo') }}
</title>

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
@fonts

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
