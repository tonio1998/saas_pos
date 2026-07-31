<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#2563eb">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="apple-touch-icon" href="{{ asset('images/ic_launcher.png') }}">
    <link rel="icon" href="{{ asset('images/ic_launcher.png') }}">
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
