@php
    $tenant = auth()->check() ? auth()->user()->tenant : null;
    if (!$tenant) {
        $tenant = \App\Models\POS\POSTenant::first();
    }
    $hasLogo = $tenant && $tenant->logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($tenant->logo);
    $appStoreConfig = [
        'business_name'   => $tenant?->business_name ?? 'MINIMART POS STORE',
        'business_code'   => $tenant?->business_code ?? 'MINI-001',
        'owner_name'      => $tenant?->owner_name ?? '',
        'phone'           => $tenant?->phone ?? '',
        'address'         => $tenant?->address ?? '',
        'tin'             => $tenant?->tin ?? '',
        'header_text'     => $tenant?->header_text ?? '',
        'footer_text'     => $tenant?->footer_text ?? "THANK YOU FOR YOUR PURCHASE!\nPLEASE COME AGAIN",
        'logo'            => $hasLogo ? \Illuminate\Support\Facades\Storage::url($tenant->logo) : null,
        'currency_symbol' => $tenant?->currency_symbol ?? '₱',
        'cashier_name'    => auth()->user()?->name ?? auth()->user()?->username ?? 'Cashier',
    ];
@endphp
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
    <title>@yield('title', 'LikhaPOS Minimart') - Cloud POS & CRM</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script>
        window.POS_STORE_CONFIG = {!! json_encode($appStoreConfig) !!};
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('theme')
    @stack('styles')
</head>
<body class="bg-light">

<!-- Mobile Sidebar Backdrop Overlay -->
<div id="sidebarBackdrop" class="sidebar-overlay"></div>

<div class="likha-app-container">
    <!-- Responsive Light Sidebar Drawer -->
    <aside id="mainSidebar" class="likha-sidebar">
        <x-pos.sidebar />
    </aside>

    <!-- Main Content Area (Topbar + Page Body) -->
    <div class="likha-main-content">
        <x-pos.topbar />

        <main class="likha-page-body">
            @yield('content')
        </main>
    </div>
</div>

<!-- Mobile Quick Action Bottom Navigation Bar (Visible < 768px) -->
<nav class="likha-mobile-nav d-md-none">
    <div class="d-flex align-items-center justify-content-around h-100">
        <a href="{{ route('dashboard.index') }}" class="mobile-nav-item {{ request()->routeIs('dashboard.*') ? 'active' : '' }}">
            <i class="bi bi-house-door-fill"></i>
            <span>Home</span>
        </a>
        <a href="{{ route('terminal.index') }}" class="mobile-nav-item {{ request()->routeIs('terminal.*') ? 'active' : '' }}">
            <i class="bi bi-calculator-fill"></i>
            <span>POS</span>
        </a>
        <a href="{{ route('products.index') }}" class="mobile-nav-item {{ request()->routeIs('products.*') ? 'active' : '' }}">
            <i class="bi bi-box-seam-fill"></i>
            <span>Products</span>
        </a>
        <a href="{{ route('customers.index') }}" class="mobile-nav-item {{ request()->routeIs('customers.*') ? 'active' : '' }}">
            <i class="bi bi-book-half"></i>
            <span>Suki CRM</span>
        </a>
        <button type="button" id="mobileMenuBtn" class="mobile-nav-item border-0 bg-transparent">
            <i class="bi bi-grid-fill"></i>
            <span>Menu</span>
        </button>
    </div>
</nav>

<x-alerts />
<x-ios-confirm />
@include('components.footer')
@stack('scripts')

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const sidebar = document.getElementById('mainSidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        const toggleBtn = document.getElementById('sidebarToggle');
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
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
        if (mobileMenuBtn) mobileMenuBtn.addEventListener('click', openSidebar);
        if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
        if (backdrop) backdrop.addEventListener('click', closeSidebar);
    });
</script>

<div class="modal fade" id="actionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="modal-content action-modal border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-bottom py-3 px-4 bg-white d-flex align-items-center justify-content-between">
                <h5 class="modal-title fw-bold text-dark font-mono fs-6 mb-0" id="actionModalTitle"></h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3.5 bg-light">
                <div class="d-grid gap-2.5" id="actionModalBody"></div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
