<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js',
        'resources/js/pages/terminal.js'
    ])
    @include('theme')
    @stack('styles')
</head>
<body>
<div id="sidebarOverlay" class="sidebar-overlay"></div>

<main class="pag">
    @yield('content')
</main>

<x-alerts />
<x-ios-confirm />
@include('components.footer')
@stack('scripts')
</body>
</html>
