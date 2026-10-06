@php
    $tenant = auth()->check() ? auth()->user()->tenant : null;
    if (!$tenant) {
        $tenant = \App\Models\POS\POSTenant::first();
    }
    $isSuperAdmin = auth()->check() && (auth()->user()->hasRole('SA') || auth()->user()->is_super_admin);
    $isSuspended = $tenant && $tenant->isSuspended();
    $hasLogo = $tenant && $tenant->logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($tenant->logo);
    $hasSquareLogo = $tenant && $tenant->logo_square && \Illuminate\Support\Facades\Storage::disk('public')->exists($tenant->logo_square);
    $posStoreConfig = [
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
        window.POS_STORE_CONFIG = {!! json_encode($posStoreConfig) !!};
        @if($isSuspended && !$isSuperAdmin)
        window.TENANT_SUSPENDED = true;
        @endif
    </script>
    @vite([
        'resources/css/app.css',
        'resources/js/app.js',
        'resources/js/pages/terminal.js'
    ])
    @include('theme')
    <style>
        .pos-app-body.tenant-suspended .pos-main-wrapper {
            filter: blur(5px);
            pointer-events: none !important;
            user-select: none !important;
        }
        .suspended-glass-card {
            background: rgba(15, 23, 42, 0.94);
            border: 1px solid rgba(239, 68, 68, 0.35);
            border-radius: 28px;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.75);
            color: #ffffff;
            position: relative;
            z-index: 1000000;
        }
    </style>
    @stack('styles')
</head>
<body class="pos-app-body {{ $isSuspended && !$isSuperAdmin ? 'tenant-suspended' : '' }}" style="margin:0;padding:0;height:100vh;overflow:hidden;background:#f8fafc;">

@if($isSuspended && $isSuperAdmin)
    <div class="bg-dark text-warning px-3 py-1.5 border-bottom border-warning d-flex align-items-center justify-content-between" style="z-index: 10000; position: relative;">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-warning text-dark fw-bold">SUPERADMIN INSPECTION</span>
            <span class="small text-light">This store (<strong>{{ $tenant->business_name }}</strong>) is currently <strong>SUSPENDED / LOCKED</strong>. You are previewing in SuperAdmin bypass mode.</span>
        </div>
        <a href="{{ route('sa.subscriptions.monitoring') }}" class="btn btn-xs btn-outline-warning rounded-pill px-3 py-0.5 fw-bold">
            Manage in SA Monitoring &rarr;
        </a>
    </div>
@endif

<main class="pos-main-wrapper" style="height:100vh;display:flex;flex-direction:column;overflow:hidden;">
    @yield('content')
</main>

@if($isSuspended && !$isSuperAdmin)
    <!-- Suspended / Locked Frosted Glass Overlay (Viewing Only, Zero Interactive Control) -->
    <div id="posSuspendedBackdrop" class="pos-suspended-backdrop" style="position: fixed; inset: 0; z-index: 999999; background: rgba(15, 23, 42, 0.72); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); display: flex; align-items: center; justify-content: center; padding: 24px;">
        <div class="suspended-glass-card text-center p-4 p-md-5" style="max-width: 550px; width: 100%;">
            <div class="mb-3 position-relative d-inline-block">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 82px; height: 82px; background: rgba(239, 68, 68, 0.15); border: 2px solid rgba(239, 68, 68, 0.4); box-shadow: 0 0 35px rgba(239, 68, 68, 0.35);">
                    <i class="bi bi-shield-lock-fill text-danger" style="font-size: 2.4rem;"></i>
                </div>
            </div>

            <div class="mb-2">
                <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-3 py-1.5 fw-bold text-uppercase" style="letter-spacing: 0.8px; font-size: 0.75rem;">
                    <i class="bi bi-exclamation-octagon-fill me-1"></i> Account Suspended &bull; View-Only Mode
                </span>
            </div>

            <h3 class="fw-extrabold text-white mb-1">POS Terminal Locked</h3>
            <p class="text-secondary small mb-3">
                Store Account: <strong class="text-light">{{ $tenant->business_name }}</strong>
            </p>

            <p class="small text-slate-300 mb-4 px-2" style="color: #cbd5e1; line-height: 1.6;">
                This cashier terminal has been placed on administrative hold due to subscription status or account suspension.
                Active cashiering, barcode scanning, and sales checkouts are locked.
                <br>
                <span style="color: #94a3b8 !important;">
                    Your store data, catalog, and past transactions remain preserved in view-only mode.
                </span>
            </p>

            <!-- 24/7 Customer Support Hotline Box -->
            <div class="p-3 mb-4 rounded-3 text-start" style="background: rgba(15, 23, 42, 0.85); border: 1px dashed rgba(245, 158, 11, 0.5);">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2.5">
                        <div class="rounded-circle bg-warning text-dark p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                            <i class="bi bi-headset fs-5"></i>
                        </div>
                        <div>
                            <div class="text-uppercase text-secondary fw-bold" style="font-size: 0.68rem; letter-spacing: 0.5px;">24/7 Priority Support Hotline</div>
                            <div class="text-warning font-mono fw-bold fs-6">0912 894 1731</div>
                        </div>
                    </div>
                    <a href="tel:09128941731" class="btn btn-warning btn-sm rounded-pill px-3 py-1.5 fw-bold shadow-sm d-flex align-items-center gap-1.5">
                        <i class="bi bi-telephone-fill"></i> Call Now
                    </a>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-grid gap-2">
                <a href="{{ route('subscription.checkout') }}" class="btn btn-success text-white rounded-xl py-2.5 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2" style="background: #059669; border-color: #059669;">
                    <i class="bi bi-credit-card-2-front-fill fs-5"></i> Renew Subscription / Settle Now
                </a>

                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-light rounded-xl py-2 flex-grow-1 fw-bold small d-flex align-items-center justify-content-center gap-1.5" data-bs-toggle="modal" data-bs-target="#supportHubModal">
                        <i class="bi bi-chat-dots-fill"></i> 24/7 Support Hub
                    </button>
                    <a href="{{ route('dashboard.index') }}" class="btn btn-outline-secondary rounded-xl py-2 flex-grow-1 fw-bold small d-flex align-items-center justify-content-center gap-1.5 text-light border-secondary">
                        <i class="bi bi-speedometer2"></i> View Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Intercept and cancel all terminal keyboard shortcuts and barcode bursts
        window.addEventListener('keydown', function(e) {
            e.stopPropagation();
            if (['F1', 'F2', 'F3', 'F4', 'F8', 'F9', 'F12', 'Enter', 'Escape'].includes(e.key)) {
                e.preventDefault();
            }
        }, true);
    </script>
@endif

<x-alerts />
<x-ios-confirm />
@include('components.support.support-hub')
@stack('scripts')
</body>
</html>
