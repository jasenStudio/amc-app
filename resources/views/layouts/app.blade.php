<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head-public')
    @commenterStyles
</head>

<body class="min-h-screen bg-white font-sans text-amc-blue antialiased">
    {{ $slot }}

    @stack('scripts')
    @commenterScripts
</body>

</html>
