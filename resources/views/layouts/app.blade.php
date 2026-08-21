<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head-public')
    @livewireStyles
    @commenterStyles
</head>

<body class="min-h-screen font-sans text-amc-blue antialiased {{ $bodyBg ?? 'bg-white' }}">
    {{ $slot }}

    @stack('scripts')
    @livewireScripts
    @commenterScripts
</body>

</html>
