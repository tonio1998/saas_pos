{{-- ═══════════════════════════════════════════════════════
     LikhaPOS — Modern SaaS CRM Topbar (Dynamic Theme Driven)
     ════════════════════════════════════════════════════════ --}}

@php
    $tenantId = auth()->check() && auth()->user()->tenant_id ? auth()->user()->tenant_id : session('tenant_id');
    $currentTenant = $tenantId ? \App\Models\POS\POSTenant::find($tenantId) : null;
    if (!$currentTenant) {
        $currentTenant = \App\Models\POS\POSTenant::first();
    }
    $isIncomplete = $currentTenant && (empty($currentTenant->phone) || empty($currentTenant->address));
    $userName    = auth()->user()->name ?? 'Store Owner';
    $userEmail   = auth()->user()->email ?? '';
    $userRole    = auth()->check() ? auth()->user()->getRoleNames()->implode(', ') : 'Admin';
    $userInitial = strtoupper(substr($userName, 0, 1));
    $storeName   = session('tenant_name', $currentTenant?->business_name ?? 'LikhaPOS Store');
    $hasTenantLogo = $currentTenant && $currentTenant->logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($currentTenant->logo);
    $tenantLogoUrl = $hasTenantLogo ? \Illuminate\Support\Facades\Storage::url($currentTenant->logo) : null;
    
    // Main branch info
    $mainBranch = $currentTenant?->branches()->where('is_main_branch', true)->first();
    $branchName = $mainBranch?->branch_name ?? 'Main Branch';
@endphp

{{-- ── Topbar ─────────────────────────────────────────── --}}
<header class="crm-topbar" id="crmTopbar">
    <div class="crm-topbar-inner">

        {{-- LEFT: Hamburger + Page Title & Live Status --}}
        <div class="crm-topbar-left">
            <button id="sidebarToggle" class="crm-icon-btn d-lg-none" type="button" aria-label="Toggle Navigation">
                <i class="bi bi-list fs-5"></i>
            </button>
            <div class="crm-page-title">
                <div class="d-flex align-items-center gap-2">
                    <span class="crm-title-text">@yield('title', 'Dashboard')</span>
                    <span class="crm-online-pill">
                        <span class="crm-pulse"></span>Live
                    </span>
                </div>
                <span class="crm-subtitle d-none d-sm-block">@yield('shortText', 'Cloud POS & Minimart CRM')</span>
            </div>
        </div>

        {{-- CENTER: Store Identity Capsule --}}
        <div class="crm-topbar-center d-none d-md-flex">
            <div class="crm-store-pill">
                @if($tenantLogoUrl)
                    <div class="crm-store-icon bg-white p-0.5 overflow-hidden">
                        <img src="{{ $tenantLogoUrl }}" alt="Logo" style="width:100%;height:100%;object-fit:contain;">
                    </div>
                @else
                    <div class="crm-store-icon">
                        <i class="bi bi-shop-window"></i>
                    </div>
                @endif
                <div class="crm-store-info">
                    <span class="crm-store-name">{{ $storeName }}</span>
                    <span class="crm-store-status">
                        <span class="crm-dot-green"></span> {{ $branchName }}
                    </span>
                </div>
            </div>
        </div>

        {{-- RIGHT: Actions + POS Launcher + Profile --}}
        <div class="crm-topbar-right">

            {{-- Notification Bell --}}
            <button class="crm-icon-btn position-relative" title="Notifications">
                <i class="bi bi-bell"></i>
                <span class="crm-notif-badge">3</span>
            </button>

            {{-- Quick Add Dropdown --}}
            <div class="dropdown d-none d-sm-block">
                <button class="crm-icon-btn" data-bs-toggle="dropdown" title="Quick Actions">
                    <i class="bi bi-plus-lg"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end crm-dropdown shadow-lg border-0 rounded-4 py-2 mt-2" style="min-width:220px;">
                    <li class="dropdown-header small text-muted fw-bold px-3 pb-2 text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.5px;">Quick Actions</li>
                    <li>
                        <a class="dropdown-item crm-drop-item" href="{{ route('products.create') }}">
                            <span class="crm-drop-icon bg-primary-subtle text-primary"><i class="bi bi-box-seam"></i></span>
                            <div>
                                <div class="fw-semibold">New Product</div>
                                <div class="small text-muted" style="font-size: 0.72rem;">Add item to catalog</div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item crm-drop-item" href="{{ route('customers.create') }}">
                            <span class="crm-drop-icon bg-success-subtle text-success"><i class="bi bi-person-plus"></i></span>
                            <div>
                                <div class="fw-semibold">New Customer</div>
                                <div class="small text-muted" style="font-size: 0.72rem;">Register suki ledger</div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item crm-drop-item" href="{{ route('cashiering.cash-transactions.create') }}">
                            <span class="crm-drop-icon bg-warning-subtle text-warning"><i class="bi bi-cash-coin"></i></span>
                            <div>
                                <div class="fw-semibold">Cash In / Cash Out</div>
                                <div class="small text-muted" style="font-size: 0.72rem;">Drawer reconciliation</div>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>

            {{-- POS Terminal Button --}}
            <a href="{{ route('terminal.index') }}" class="crm-pos-btn d-none d-sm-flex">
                <i class="bi bi-calculator-fill"></i>
                <span>POS Terminal</span>
            </a>

            <div class="crm-divider d-none d-md-block"></div>

            {{-- User Profile Dropdown --}}
            <div class="dropdown">
                <button class="crm-user-btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="crm-avatar">{{ $userInitial }}</div>
                    <div class="crm-user-info d-none d-md-block">
                        <span class="crm-user-name">{{ $userName }}</span>
                        <span class="crm-user-role">{{ $userRole }}</span>
                    </div>
                    <i class="bi bi-chevron-down crm-chevron d-none d-md-block"></i>
                </button>

                <ul class="dropdown-menu dropdown-menu-end crm-dropdown shadow-lg border-0 rounded-4 mt-2 py-0" style="min-width:275px; overflow:hidden;">
                    <li class="crm-drop-header">
                        <div class="crm-drop-avatar">{{ $userInitial }}</div>
                        <div class="min-w-0 flex-grow-1">
                            <div class="crm-drop-name text-truncate">{{ $userName }}</div>
                            <div class="crm-drop-email text-truncate">{{ $userEmail }}</div>
                            <span class="crm-role-badge">{{ $userRole }}</span>
                        </div>
                    </li>
                    <li class="px-2 pt-2 pb-1">
                        <a class="dropdown-item crm-drop-item" href="{{ route('settings.index') }}">
                            <span class="crm-drop-icon bg-success-subtle text-success"><i class="bi bi-gear-fill"></i></span>
                            Store Settings &amp; Theme
                        </a>
                        <a class="dropdown-item crm-drop-item" href="{{ route('subscription.checkout') }}">
                            <span class="crm-drop-icon bg-warning-subtle text-warning"><i class="bi bi-credit-card-2-front-fill"></i></span>
                            Subscription Plan
                        </a>
                        <a class="dropdown-item crm-drop-item" href="{{ route('terminal.index') }}">
                            <span class="crm-drop-icon bg-primary-subtle text-primary"><i class="bi bi-calculator-fill"></i></span>
                            Open POS Terminal
                        </a>
                    </li>
                    <li><hr class="my-1 mx-2 text-muted opacity-25"></li>
                    <li class="px-2 pb-2">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="dropdown-item crm-drop-item crm-logout-item w-100 text-start border-0 bg-transparent" type="submit">
                                <span class="crm-drop-icon bg-danger-subtle text-danger"><i class="bi bi-box-arrow-right"></i></span>
                                Logout Account
                            </button>
                        </form>
                    </li>
                </ul>
            </div>

        </div>
    </div>
</header>

{{-- ── Incomplete Store Profile Banner ──────────────────── --}}
@if($currentTenant && $isIncomplete)
<div class="crm-banner-incomplete" id="storeProfileBanner">
    <div class="d-flex align-items-center gap-2">
        <i class="bi bi-exclamation-triangle-fill text-warning"></i>
        <span class="fw-semibold small">
            Complete your store profile — add your <strong>Phone Number</strong> &amp; <strong>Store Address</strong> to enable receipts and official records.
        </span>
    </div>
    <button type="button" class="btn btn-warning btn-sm fw-bold rounded-pill px-3 flex-shrink-0"
        data-bs-toggle="modal" data-bs-target="#completeStoreProfileModal">
        <i class="bi bi-pencil-fill me-1"></i> Complete Now
    </button>
</div>

{{-- Complete Store Profile Modal --}}
<div class="modal fade" id="completeStoreProfileModal" tabindex="-1" aria-labelledby="storeProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header pb-0 border-0">
                <div>
                    <h5 class="modal-title fw-bold text-dark" id="storeProfileModalLabel">
                        <i class="bi bi-shop-window me-2 text-success"></i> Complete Store Profile
                    </h5>
                    <p class="text-muted small mb-0 mt-1">Fill in your store details to enable receipts and records.</p>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('store.complete-profile') }}">
                @csrf
                <div class="modal-body pt-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">
                            <i class="bi bi-telephone-fill text-success me-1"></i> Mobile Phone Number <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="phone" class="form-control rounded-3"
                            placeholder="e.g. 0917 123 4567"
                            value="{{ old('phone', $currentTenant->phone) }}" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">
                            <i class="bi bi-geo-alt-fill text-success me-1"></i> Complete Store Address <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="address" class="form-control rounded-3"
                            placeholder="e.g. Brgy. Washington, Surigao City"
                            value="{{ old('address', $currentTenant->address) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">
                            <i class="bi bi-receipt me-1 text-muted"></i> BIR TIN <span class="text-muted">(Optional)</span>
                        </label>
                        <input type="text" name="tin" class="form-control rounded-3"
                            placeholder="e.g. 123-456-789-000"
                            value="{{ old('tin', $currentTenant->tin) }}">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Later</button>
                    <button type="submit" class="btn btn-success rounded-3 fw-bold px-4" style="background:#059669;border:none;">
                        <i class="bi bi-check-circle-fill me-1"></i> Save & Continue
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- ── Topbar Styles (Dynamic Theme Sensitive) ────────────── --}}
<style>
/* Layout */
.crm-topbar {
    height: 64px;
    background: var(--theme-topbar, #0f172a);
    color: var(--theme-topbar-text, #ffffff);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    position: sticky;
    top: 0;
    z-index: 1030;
    padding: 0 1.5rem;
    display: flex;
    align-items: center;
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    transition: background-color 0.25s ease, border-color 0.25s ease;
}
.crm-topbar-inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    gap: 14px;
}
.crm-topbar-left  { display: flex; align-items: center; gap: 12px; min-width: 0; flex: 1; }
.crm-topbar-center{ display: flex; align-items: center; justify-content: center; }
.crm-topbar-right { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }

/* Page Title */
.crm-page-title   { display: flex; flex-direction: column; line-height: 1.25; min-width: 0; }
.crm-title-text   {
    font-family: 'Outfit', sans-serif;
    font-size: 1.05rem;
    font-weight: 800;
    color: var(--theme-topbar-text, #ffffff);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    letter-spacing: -0.01em;
}
.crm-subtitle {
    font-size: 0.72rem;
    color: var(--theme-topbar-text, #ffffff);
    opacity: 0.75;
    font-weight: 500;
}

/* Live Pill */
.crm-online-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: rgba(34, 197, 94, 0.16);
    color: #4ade80;
    border: 1px solid rgba(34, 197, 94, 0.35);
    border-radius: 50px;
    font-size: 0.68rem;
    font-weight: 800;
    padding: 2px 8px;
    white-space: nowrap;
    letter-spacing: 0.3px;
    text-transform: uppercase;
}
.crm-pulse {
    width: 6px;
    height: 6px;
    background: #22c55e;
    border-radius: 50%;
    display: inline-block;
    box-shadow: 0 0 8px #22c55e;
    animation: crmPulse 1.8s ease-in-out infinite;
}
@keyframes crmPulse {
    0%,100% { opacity: 1; transform: scale(1); }
    50%     { opacity: 0.4; transform: scale(1.3); }
}

/* Center Store Identity Capsule */
.crm-store-pill {
    display: flex;
    align-items: center;
    gap: 10px;
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 50px;
    padding: 5px 16px 5px 6px;
    transition: all 0.2s ease;
    backdrop-filter: blur(8px);
}
.crm-store-pill:hover {
    background: rgba(255, 255, 255, 0.18);
    border-color: rgba(255, 255, 255, 0.35);
}
.crm-store-icon {
    width: 32px;
    height: 32px;
    background: var(--theme-primary, #059669);
    border-radius: 50%;
    color: #ffffff;
    font-size: 0.95rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}
.crm-store-info  { display: flex; flex-direction: column; line-height: 1.2; }
.crm-store-name  {
    font-family: 'Outfit', sans-serif;
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--theme-topbar-text, #ffffff);
    white-space: nowrap;
}
.crm-store-status {
    font-size: 0.68rem;
    color: var(--theme-topbar-text, #ffffff);
    opacity: 0.8;
    display: flex;
    align-items: center;
    gap: 5px;
    font-weight: 600;
}
.crm-dot-green   { width: 6px; height: 6px; background: #22c55e; border-radius: 50%; display: inline-block; }

/* Icon Buttons */
.crm-icon-btn {
    width: 38px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    color: var(--theme-topbar-text, #ffffff);
    cursor: pointer;
    transition: all 0.2s ease;
    flex-shrink: 0;
    padding: 0;
}
.crm-icon-btn:hover {
    background: rgba(255, 255, 255, 0.22);
    color: var(--theme-topbar-text, #ffffff);
    transform: translateY(-1px);
    border-color: rgba(255, 255, 255, 0.35);
}

/* Notification Badge */
.crm-notif-badge {
    position: absolute;
    top: -3px;
    right: -3px;
    width: 17px;
    height: 17px;
    background: #ef4444;
    color: #ffffff;
    font-size: 0.58rem;
    font-weight: 800;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid var(--theme-topbar, #0f172a);
    line-height: 1;
}

/* POS CTA Button */
.crm-pos-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: var(--theme-primary, #059669);
    color: #ffffff !important;
    font-family: 'Outfit', sans-serif;
    font-size: 0.84rem;
    font-weight: 700;
    padding: 8px 16px;
    border-radius: 50px;
    text-decoration: none;
    border: none;
    transition: all 0.2s cubic-bezier(0.2, 0.8, 0.2, 1);
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15);
    white-space: nowrap;
}
.crm-pos-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.22);
    filter: brightness(1.08);
}

/* Vertical Divider */
.crm-divider {
    width: 1px;
    height: 28px;
    background: rgba(255, 255, 255, 0.18);
    margin: 0 2px;
}

/* User Button */
.crm-user-btn {
    display: flex;
    align-items: center;
    gap: 10px;
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 50px;
    padding: 4px 12px 4px 4px;
    cursor: pointer;
    transition: all 0.2s ease;
    color: var(--theme-topbar-text, #ffffff);
}
.crm-user-btn::after { display: none; }
.crm-user-btn:hover  {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.35);
}
.crm-avatar {
    width: 32px;
    height: 32px;
    background: var(--theme-primary, #059669);
    color: #ffffff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 0.88rem;
    flex-shrink: 0;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
}
.crm-user-info { display: flex; flex-direction: column; line-height: 1.2; text-align: left; }
.crm-user-name { font-size: 0.82rem; font-weight: 700; color: var(--theme-topbar-text, #ffffff); }
.crm-user-role { font-size: 0.68rem; color: var(--theme-topbar-text, #ffffff); opacity: 0.75; text-transform: capitalize; }
.crm-chevron   { font-size: 0.68rem; color: var(--theme-topbar-text, #ffffff); opacity: 0.7; }

/* Dropdown */
.crm-dropdown {
    background: #ffffff;
    border: 1px solid rgba(226, 232, 240, 0.9) !important;
    box-shadow: 0 16px 40px rgba(15, 23, 42, 0.12) !important;
}
.crm-drop-header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}
.crm-drop-avatar {
    width: 44px;
    height: 44px;
    background: var(--theme-primary, #059669);
    color: #ffffff;
    font-weight: 800;
    font-size: 1.15rem;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.crm-drop-name  { font-size: 0.88rem; font-weight: 800; color: #0f172a; }
.crm-drop-email { font-size: 0.74rem; color: #64748b; max-width: 170px; }
.crm-role-badge {
    display: inline-block;
    margin-top: 4px;
    font-size: 0.64rem;
    font-weight: 700;
    background: rgba(5, 150, 105, 0.12);
    color: #059669;
    border-radius: 50px;
    padding: 1px 8px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}
.crm-drop-item {
    display: flex !important;
    align-items: center;
    gap: 10px;
    font-size: 0.84rem;
    font-weight: 600;
    color: #334155;
    padding: 8px 12px !important;
    border-radius: 10px;
    transition: all 0.15s ease;
}
.crm-drop-item:hover {
    background: #f1f5f9 !important;
    color: #0f172a !important;
}
.crm-drop-icon {
    width: 32px;
    height: 32px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9rem;
    flex-shrink: 0;
}
.crm-logout-item       { color: #ef4444 !important; }
.crm-logout-item:hover { background: #fef2f2 !important; color: #dc2626 !important; }

/* Banner */
.crm-banner-incomplete {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    background: #fffbeb;
    border-bottom: 1px solid #fde68a;
    padding: 10px 24px;
    font-size: 0.84rem;
}

@media (max-width: 991px) {
    .crm-topbar { padding: 0 1rem; }
}
</style>
