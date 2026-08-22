<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#059669">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="apple-touch-icon" href="{{ asset('images/ic_launcher.png') }}">
    <link rel="icon" href="{{ asset('images/ic_launcher.png') }}">
    <title>@yield('title')</title>
    <script>
        (() => {
            const theme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    @vite([
        'resources/css/app.css',
        'resources/js/app.js',
        'resources/js/pages/terminal.js'
    ])
    @include('theme')
    @stack('styles')
</head>
<body class="pos-app-body" style="margin:0;padding:0;height:100vh;overflow:hidden;background:#f8fafc;">

<main class="pos-main-wrapper" style="height:100vh;display:flex;flex-direction:column;overflow:hidden;">
    @yield('content')
</main>

<x-alerts />
<x-ios-confirm />
@stack('scripts')
</body>
</html>
