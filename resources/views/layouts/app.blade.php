@php
    $tenant = auth()->check() ? auth()->user()->tenant : null;
    if (!$tenant) {
        $tenant = \App\Models\POS\POSTenant::first();
    }
    $isSuperAdmin = auth()->check() && (auth()->user()->hasRole('SA') || auth()->user()->is_super_admin);
    $isSuspended = $tenant && $tenant->isSuspended();
    $hasLogo = $tenant && $tenant->logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($tenant->logo);
    $hasSquareLogo = $tenant && $tenant->logo_square && \Illuminate\Support\Facades\Storage::disk('public')->exists($tenant->logo_square);
    $appStoreConfig = [
        'business_name'   => $tenant?->business_name ?? 'MINIMART POS STORE',
        'business_code'   => $tenant?->business_code ?? 'MINI-001',
        'owner_name'      => $tenant?->owner_name ?? '',
        'phone'           => $tenant?->phone ?? '',
        'address'         => $tenant?->address ?? '',
        'tin'             => $tenant?->tin ?? '',
        'header_text'     => $tenant?->header_text ?? '',
        'footer_text'     => $tenant?->footer_text ?? "THANK YOU FOR YOUR PURCHASE!\nPLEASE COME AGAIN",
        'logo'            => $hasLogo ? \Illuminate\Support\Facades\Storage::url($tenant->logo) : ($hasSquareLogo ? \Illuminate\Support\Facades\Storage::url($tenant->logo_square) : null),
        'logo_square'     => $hasSquareLogo ? \Illuminate\Support\Facades\Storage::url($tenant->logo_square) : ($hasLogo ? \Illuminate\Support\Facades\Storage::url($tenant->logo) : null),
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
    <title>@hasSection('title')@yield('title') &mdash; {{ $appStoreConfig['business_name'] }}@else{{ $appStoreConfig['business_name'] }}@endif</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script>
        window.POS_STORE_CONFIG = {!! json_encode($appStoreConfig) !!};
        @if($isSuspended && !$isSuperAdmin)
        window.TENANT_SUSPENDED = true;
        @endif
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('theme')
    @stack('styles')
</head>
<body class="bg-light {{ $isSuspended && !$isSuperAdmin ? 'store-view-only' : '' }}">

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

        @if($isSuspended && !$isSuperAdmin)
            <!-- Suspended View-Only Sticky Banner -->
            <div class="px-4 py-3 text-white shadow-sm d-flex align-items-center justify-content-between flex-wrap gap-3 sticky-top" style="background: linear-gradient(135deg, #991b1b 0%, #b91c1c 50%, #dc2626 100%); z-index: 1020;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-white text-danger p-2 d-flex align-items-center justify-content-center shadow-xs flex-shrink-0" style="width: 38px; height: 38px;">
                        <i class="bi bi-shield-lock-fill fs-5"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-white text-danger fw-extrabold text-uppercase px-2 py-0.5" style="letter-spacing: 0.5px; font-size: 0.7rem;">STORE SUSPENDED</span>
                            <span class="fw-bold small text-white">View-Only Mode Active</span>
                        </div>
                        <div class="extra-small text-white-50 mt-0.5">
                            Cashier terminal selling and data modifications are disabled. To reactivate your store, renew your subscription or contact 24/7 customer support.
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <a href="tel:09128941731" class="btn btn-sm btn-white text-danger fw-bold rounded-pill px-3 py-1.5 shadow-xs bg-white text-danger border-0">
                        <i class="bi bi-telephone-fill me-1"></i> Support: 0912 894 1731
                    </a>
                    <a href="{{ route('subscription.checkout') }}" class="btn btn-sm btn-warning text-dark fw-bold rounded-pill px-3 py-1.5 shadow-xs border-0">
                        <i class="bi bi-credit-card-2-front-fill me-1"></i> Renew Subscription
                    </a>
                    <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-3 py-1.5" data-bs-toggle="modal" data-bs-target="#supportHubModal">
                        <i class="bi bi-headset me-1"></i> 24/7 Support Hub
                    </button>
                </div>
            </div>
        @elseif($isSuspended && $isSuperAdmin)
            <!-- SuperAdmin Inspection Mode Banner -->
            <div class="bg-dark text-warning px-4 py-2 border-bottom border-warning d-flex align-items-center justify-content-between flex-wrap gap-2 sticky-top" style="z-index: 1020;">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-warning text-dark fw-bold">SUPERADMIN INSPECTION</span>
                    <span class="small text-light">This store (<strong>{{ $tenant->business_name }}</strong>) is currently <strong>SUSPENDED / LOCKED</strong>. You are inspecting in SuperAdmin bypass mode.</span>
                </div>
                <a href="{{ route('sa.subscriptions.monitoring') }}" class="btn btn-sm btn-outline-warning rounded-pill px-3 py-1 fw-bold">
                    Manage in SA Monitoring &rarr;
                </a>
            </div>
        @endif

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
@include('components.support.support-hub')
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

@if($isSuspended && !$isSuperAdmin)
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Intercept any mutative form submissions when store is suspended (View-Only mode)
        document.querySelectorAll('form').forEach(form => {
            const action = (form.getAttribute('action') || '').toLowerCase();
            // Whitelist navigation/read/auth/support routes
            if (action.includes('logout') || action.includes('support') || action.includes('subscription') || action.includes('payment')) {
                return;
            }
            form.addEventListener('submit', function(e) {
                const methodInput = form.querySelector('input[name="_method"]');
                const method = methodInput ? methodInput.value.toUpperCase() : (form.getAttribute('method') || 'GET').toUpperCase();
                if (['POST', 'PUT', 'PATCH', 'DELETE'].includes(method)) {
                    e.preventDefault();
                    if (window.Swal) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'View-Only Mode',
                            text: 'This store account is currently suspended. Data modifications are locked. Please contact 24/7 Support at 0912 894 1731 or renew your subscription.',
                            confirmButtonColor: '#d33',
                            confirmButtonText: 'Understood'
                        });
                    } else {
                        alert('This store account is currently suspended (View-Only Mode). Modifying or saving records is disabled. Please contact 24/7 Customer Support at 0912 894 1731 or renew your subscription.');
                    }
                    return false;
                }
            });
        });
    });
</script>
@endif
</body>
</html>
