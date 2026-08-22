{{-- ═══════════════════════════════════════════════════════
     LikhaPOS — Premium CRM Topbar
     ════════════════════════════════════════════════════════ --}}

@php
    $currentTenant = auth()->check() && auth()->user()->tenant_id
        ? \App\Models\POS\POSTenant::find(auth()->user()->tenant_id)
        : null;
    $isIncomplete = $currentTenant && (empty($currentTenant->phone) || empty($currentTenant->address));
    $userName    = auth()->user()->name ?? 'Store Owner';
    $userEmail   = auth()->user()->email ?? '';
    $userRole    = auth()->check() ? auth()->user()->getRoleNames()->implode(', ') : 'Admin';
    $userInitial = strtoupper(substr($userName, 0, 1));
    $storeName   = session('tenant_name', $currentTenant?->BusinessName ?? 'My Store');
@endphp

{{-- ── Topbar ─────────────────────────────────────────── --}}
<header class="crm-topbar" id="crmTopbar">
    <div class="crm-topbar-inner">

        {{-- LEFT: Hamburger + Page Title --}}
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
                <span class="crm-subtitle d-none d-sm-block">@yield('shortText', 'Cloud POS & Customer CRM')</span>
            </div>
        </div>

        {{-- CENTER: Store Pill --}}
        <div class="crm-topbar-center d-none d-md-flex">
            <div class="crm-store-pill">
                <div class="crm-store-icon"><i class="bi bi-shop-window"></i></div>
                <div class="crm-store-info">
                    <span class="crm-store-name">{{ $storeName }}</span>
                    <span class="crm-store-status"><span class="crm-dot-green"></span> Active Store</span>
                </div>
            </div>
        </div>

        {{-- RIGHT: Actions + Profile --}}
        <div class="crm-topbar-right">

            {{-- Notification Bell --}}
            <button class="crm-icon-btn position-relative" title="Notifications">
                <i class="bi bi-bell fs-6"></i>
                <span class="crm-notif-badge">3</span>
            </button>

            {{-- Quick Add --}}
            <div class="dropdown d-none d-sm-block">
                <button class="crm-icon-btn" data-bs-toggle="dropdown" title="Quick Add">
                    <i class="bi bi-plus-lg fs-6"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end crm-dropdown shadow-lg border-0 rounded-4 py-2 mt-2" style="min-width:210px;">
                    <li class="dropdown-header small text-muted fw-semibold px-3 pb-1">Quick Add</li>
                    <li>
                        <a class="dropdown-item crm-drop-item" href="{{ route('products.create') }}">
                            <span class="crm-drop-icon bg-primary-subtle text-primary"><i class="bi bi-box-seam"></i></span>
                            New Product
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item crm-drop-item" href="{{ route('customers.create') }}">
                            <span class="crm-drop-icon bg-success-subtle text-success"><i class="bi bi-person-plus"></i></span>
                            New Customer
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item crm-drop-item" href="{{ route('cashiering.cash-transactions.create') }}">
                            <span class="crm-drop-icon bg-warning-subtle text-warning"><i class="bi bi-cash-coin"></i></span>
                            Cash Transaction
                        </a>
                    </li>
                </ul>
            </div>

            {{-- POS Terminal CTA --}}
            <a href="{{ route('terminal.index') }}" class="crm-pos-btn d-none d-sm-flex">
                <i class="bi bi-calculator-fill"></i>
                <span>POS Terminal</span>
            </a>

            <div class="crm-divider d-none d-md-block"></div>

            {{-- User Dropdown --}}
            <div class="dropdown">
                <button class="crm-user-btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="crm-avatar">{{ $userInitial }}</div>
                    <div class="crm-user-info d-none d-md-block">
                        <span class="crm-user-name">{{ $userName }}</span>
                        <span class="crm-user-role">{{ $userRole }}</span>
                    </div>
                    <i class="bi bi-chevron-down crm-chevron d-none d-md-block"></i>
                </button>

                <ul class="dropdown-menu dropdown-menu-end crm-dropdown shadow-lg border-0 rounded-4 mt-2 py-0" style="min-width:265px; overflow:hidden;">
                    <li class="crm-drop-header">
                        <div class="crm-drop-avatar">{{ $userInitial }}</div>
                        <div class="min-w-0">
                            <div class="crm-drop-name">{{ $userName }}</div>
                            <div class="crm-drop-email">{{ $userEmail }}</div>
                            <span class="crm-role-badge">{{ $userRole }}</span>
                        </div>
                    </li>
                    <li class="px-2 pt-1 pb-1">
                        <a class="dropdown-item crm-drop-item" href="{{ route('settings.index') }}">
                            <span class="crm-drop-icon bg-success-subtle text-success"><i class="bi bi-gear-fill"></i></span>
                            Store Settings
                        </a>
                        <a class="dropdown-item crm-drop-item" href="{{ route('subscription.checkout') }}">
                            <span class="crm-drop-icon bg-warning-subtle text-warning"><i class="bi bi-credit-card-2-front-fill"></i></span>
                            Subscription Plan
                        </a>
                        <a class="dropdown-item crm-drop-item" href="{{ route('terminal.index') }}">
                            <span class="crm-drop-icon bg-primary-subtle text-primary"><i class="bi bi-calculator-fill"></i></span>
                            POS Terminal
                        </a>
                    </li>
                    <li><hr class="my-1 mx-2"></li>
                    <li class="px-2 pb-2">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="dropdown-item crm-drop-item crm-logout-item" type="submit">
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

{{-- ── Topbar Styles ─────────────────────────────────────── --}}
<style>
/* Layout */
.crm-topbar {
    height: 62px;
    background: #fff;
    border-bottom: 1px solid #e9ecef;
    position: sticky;
    top: 0;
    z-index: 1030;
    padding: 0 1.25rem;
    display: flex;
    align-items: center;
}
.crm-topbar-inner {
    display: flex; align-items: center; justify-content: space-between;
    width: 100%; gap: 12px;
}
.crm-topbar-left  { display: flex; align-items: center; gap: 10px; min-width: 0; flex: 1; }
.crm-topbar-center{ display: flex; align-items: center; justify-content: center; }
.crm-topbar-right { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }

/* Page Title */
.crm-page-title   { display: flex; flex-direction: column; line-height: 1.2; min-width: 0; }
.crm-title-text   { font-size: .92rem; font-weight: 700; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.crm-subtitle     { font-size: .71rem; color: #94a3b8; }

/* Live Pill */
.crm-online-pill {
    display: inline-flex; align-items: center; gap: 4px;
    background: #f0fdf4; color: #16a34a;
    border: 1px solid #bbf7d0; border-radius: 999px;
    font-size: .67rem; font-weight: 700; padding: 2px 8px;
    white-space: nowrap; letter-spacing: .02em;
}
.crm-pulse {
    width: 6px; height: 6px; background: #22c55e; border-radius: 50%;
    display: inline-block;
    animation: crmPulse 1.8s ease-in-out infinite;
}
@keyframes crmPulse {
    0%,100% { opacity:1; transform:scale(1); }
    50%      { opacity:.4; transform:scale(1.35); }
}

/* Store Pill */
.crm-store-pill {
    display: flex; align-items: center; gap: 10px;
    background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px;
    padding: 6px 14px 6px 8px; transition: border-color .2s;
}
.crm-store-pill:hover { border-color: #22c55e; }
.crm-store-icon {
    width: 32px; height: 32px;
    background: linear-gradient(135deg, #059669, #047857);
    border-radius: 8px; color: #fff; font-size: .9rem;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.crm-store-info  { display: flex; flex-direction: column; line-height: 1.2; }
.crm-store-name  { font-size: .8rem; font-weight: 700; color: #0f172a; }
.crm-store-status{ font-size: .67rem; color: #64748b; display: flex; align-items: center; gap: 4px; }
.crm-dot-green   { width:6px; height:6px; background:#22c55e; border-radius:50%; display:inline-block; }

/* Icon Buttons */
.crm-icon-btn {
    width: 36px; height: 36px;
    display: flex; align-items: center; justify-content: center;
    background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px;
    color: #475569; cursor: pointer; transition: all .2s;
    flex-shrink: 0; padding: 0;
}
.crm-icon-btn:hover { background:#f1f5f9; color:#059669; border-color:#d1fae5; }

/* Notification Badge */
.crm-notif-badge {
    position: absolute; top: -2px; right: -2px;
    width: 16px; height: 16px;
    background: #ef4444; color: #fff;
    font-size: .53rem; font-weight: 700;
    border-radius: 50%; display: flex; align-items: center; justify-content: center;
    border: 2px solid #fff; line-height: 1;
}

/* POS CTA */
.crm-pos-btn {
    display: inline-flex; align-items: center; gap: 6px;
    background: linear-gradient(135deg, #059669, #047857);
    color: #fff !important; font-size: .8rem; font-weight: 700;
    padding: 7px 14px; border-radius: 10px;
    text-decoration: none; border: none; transition: all .2s;
    box-shadow: 0 2px 8px rgba(5,150,105,.25); white-space: nowrap;
}
.crm-pos-btn:hover {
    background: linear-gradient(135deg,#047857,#065f46);
    box-shadow: 0 4px 14px rgba(5,150,105,.35);
    transform: translateY(-1px);
}

/* Divider */
.crm-divider { width:1px; height:28px; background:#e2e8f0; margin:0 2px; }

/* User Button */
.crm-user-btn {
    display: flex; align-items: center; gap: 8px;
    background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px;
    padding: 5px 10px 5px 5px; cursor: pointer; transition: all .2s;
}
.crm-user-btn::after { display:none; }
.crm-user-btn:hover  { border-color:#d1fae5; background:#f0fdf4; }
.crm-avatar {
    width: 34px; height: 34px;
    background: linear-gradient(135deg,#059669,#047857);
    color: #fff; border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: .9rem; flex-shrink: 0;
}
.crm-user-info { display:flex; flex-direction:column; line-height:1.2; text-align:left; }
.crm-user-name { font-size:.8rem; font-weight:700; color:#0f172a; }
.crm-user-role { font-size:.67rem; color:#64748b; text-transform:capitalize; }
.crm-chevron   { font-size:.65rem; color:#94a3b8; }

/* Dropdown */
.crm-dropdown  { background:#fff; }
.crm-drop-header {
    display: flex; align-items: center; gap: 12px;
    padding: 14px 16px;
    background: linear-gradient(135deg,#f0fdf4,#ecfdf5);
    border-bottom: 1px solid #e9ecef;
}
.crm-drop-avatar {
    width:42px; height:42px;
    background:linear-gradient(135deg,#059669,#047857);
    color:#fff; font-weight:800; font-size:1.1rem; border-radius:12px;
    display:flex; align-items:center; justify-content:center; flex-shrink:0;
}
.crm-drop-name  { font-size:.85rem; font-weight:700; color:#0f172a; }
.crm-drop-email { font-size:.72rem; color:#64748b; max-width:160px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.crm-role-badge {
    display:inline-block; margin-top:3px;
    font-size:.62rem; font-weight:700;
    background:#d1fae5; color:#059669;
    border-radius:999px; padding:1px 8px;
    text-transform:uppercase; letter-spacing:.04em;
}
.crm-drop-item {
    display:flex !important; align-items:center; gap:10px;
    font-size:.82rem; font-weight:600; color:#374151;
    padding:7px 10px !important; border-radius:8px; transition:background .15s;
}
.crm-drop-item:hover { background:#f8fafc !important; color:#059669 !important; }
.crm-drop-icon {
    width:28px; height:28px; border-radius:7px;
    display:flex; align-items:center; justify-content:center;
    font-size:.8rem; flex-shrink:0;
}
.crm-logout-item         { color:#ef4444 !important; }
.crm-logout-item:hover   { background:#fef2f2 !important; color:#dc2626 !important; }

/* Banner */
.crm-banner-incomplete {
    display:flex; align-items:center; justify-content:space-between; gap:12px;
    background:#fffbeb; border-bottom:1px solid #fde68a;
    padding:8px 20px; font-size:.82rem;
}

@media (max-width:991px) { .crm-topbar { padding:0 .75rem; } }
</style>
