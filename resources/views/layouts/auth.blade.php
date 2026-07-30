<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'RetailPOS')</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    @stack('styles')

</head>

<body class="auth-body">

<!-- Background -->
<div class="auth-background">

    <div class="gradient gradient-purple"></div>

    <div class="gradient gradient-green"></div>

    <div class="gradient gradient-yellow"></div>

    <div class="grid-overlay"></div>

</div>

<!-- Floating Elements -->
<div class="floating-shapes">

    <span class="shape shape-1"></span>

    <span class="shape shape-2"></span>

    <span class="shape shape-3"></span>

</div>

<main class="auth-wrapper">

    @yield('content')

</main>

@stack('scripts')

</body>

</html>
