<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head-public')
    @livewireStyles
    @commenterStyles
</head>

<body class="min-h-screen font-sans text-amc-blue antialiased {{ $bodyBg ?? 'bg-white' }}">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-[100] focus:rounded-md focus:bg-amc-blue focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-white focus:shadow-lg">{{ __('Saltar al contenido') }}</a>
    {{ $slot }}

    @stack('scripts')
    @livewireScripts
    @commenterScripts
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
</body>

</html>
