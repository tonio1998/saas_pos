<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#059669">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="apple-touch-icon" href="{{ asset('images/ic_launcher.png') }}">
    <link rel="icon" href="{{ asset('images/ic_launcher.png') }}">
    <title>@hasSection('title')@yield('title') &mdash; LikhaPOS SuperAdmin @else LikhaPOS SuperAdmin Console @endif</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('theme')
    @stack('styles')
</head>
<body class="bg-light">

<!-- Mobile Sidebar Backdrop Overlay -->
<div id="sidebarBackdrop" class="sidebar-overlay"></div>

<div class="likha-app-container">
    <!-- Responsive SuperAdmin Sidebar Drawer -->
    <aside id="mainSidebar" class="likha-sidebar">
        <x-main.sidebar />
    </aside>

    <!-- Main Content Area (Topbar + Page Body) -->
    <div class="likha-main-content">
        <x-main.topbar />

        <main class="likha-page-body">
            @yield('content')
        </main>
    </div>
</div>

<x-alerts />
<x-ios-confirm />
@include('components.footer')
@include('components.support.support-hub')
@stack('scripts')

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const sidebar = document.getElementById('mainSidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        const toggleBtn = document.getElementById('sidebarToggle');
        const closeBtn = document.getElementById('sidebarCloseBtn');

        function openSidebar() {
            if (sidebar) sidebar.classList.add('show');
            if (backdrop) backdrop.classList.add('show');
        }

        function closeSidebar() {
            if (sidebar) sidebar.classList.remove('show');
            if (backdrop) backdrop.classList.remove('show');
        }

        if (toggleBtn) toggleBtn.addEventListener('click', openSidebar);
        if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
        if (backdrop) backdrop.addEventListener('click', closeSidebar);
    });
</script>

</body>
</html>
