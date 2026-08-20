<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head-public')
    @commenterStyles
    @livewireStyles
</head>

<body class="min-h-screen font-sans text-amc-blue antialiased bg-amc-blue">
    {{ $slot }}

    @stack('scripts')
    @commenterScripts
    @livewireScripts
</body>

</html>
