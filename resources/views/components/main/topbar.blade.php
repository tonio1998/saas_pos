@php
    $userName    = auth()->user()->name ?? 'Super Administrator';
    $userEmail   = auth()->user()->email ?? 'admin@likhapos.com';
    $userRole    = auth()->check() ? auth()->user()->getRoleNames()->implode(', ') : 'SuperAdmin';
    $userInitial = strtoupper(substr($userName, 0, 1));
    $pendingVerificationsCount = \App\Models\POS\POSTenant::where('payment_status', 'pending_verification')->count();
    $activeTenant = session('tenant_id') ? \App\Models\POS\POSTenant::find(session('tenant_id')) : null;
@endphp

<header class="crm-topbar" id="crmTopbar">
    <div class="crm-topbar-inner">

        {{-- LEFT: Hamburger + Page Title & Status --}}
        <div class="crm-topbar-left">
            <button id="sidebarToggle" class="crm-icon-btn d-lg-none" type="button" aria-label="Toggle Navigation">
                <i class="bi bi-list fs-5"></i>
            </button>
            <div class="crm-page-title">
                <div class="d-flex align-items-center gap-2">
                    <span class="crm-title-text">@yield('title', 'Platform CRM & Monitoring')</span>
                    <span class="badge text-dark fw-bold px-2 py-0.5" style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); font-size: 0.65rem; letter-spacing: 0.4px;">
                        <i class="bi bi-shield-shaded me-1"></i> SA ACTIVE
                    </span>
                </div>
                <span class="crm-subtitle d-none d-sm-block">@yield('shortText', 'LikhaPOS Cloud System Administration')</span>
            </div>
        </div>

        {{-- CENTER: Store Context Indicator (if active) --}}
        <div class="crm-topbar-center d-none d-md-flex">
            @if($activeTenant)
                <div class="crm-store-pill" style="background: rgba(245, 158, 11, 0.15); border-color: rgba(245, 158, 11, 0.35);">
                    <div class="crm-store-icon" style="background: #f59e0b; color: #000;">
                        <i class="bi bi-shop"></i>
                    </div>
                    <div class="crm-store-info">
                        <span class="crm-store-name">{{ $activeTenant->business_name }}</span>
                        <span class="crm-store-status text-warning-emphasis fw-bold">
                            <span class="crm-dot-green"></span> Active Store Context
                        </span>
                    </div>
                </div>
            @else
                <div class="crm-store-pill">
                    <div class="crm-store-icon" style="background: linear-gradient(135deg, #059669 0%, #047857 100%);">
                        <i class="bi bi-cloud-check-fill"></i>
                    </div>
                    <div class="crm-store-info">
                        <span class="crm-store-name">LikhaPOS Cloud</span>
                        <span class="crm-store-status">
                            <span class="crm-dot-green"></span> Multi-Tenant Platform
                        </span>
                    </div>
                </div>
            @endif
        </div>

        {{-- RIGHT: Quick Actions & Profile --}}
        <div class="crm-topbar-right">

            {{-- Verifications Quick Link --}}
            <a href="{{ route('sa.subscriptions.verifications') }}" class="btn btn-sm rounded-pill fw-bold d-none d-sm-inline-flex align-items-center gap-1.5 px-3 shadow-xs {{ $pendingVerificationsCount > 0 ? 'btn-danger' : 'btn-outline-warning text-dark' }}" style="{{ $pendingVerificationsCount > 0 ? '' : 'background: #fef3c7; border: 1px solid #f59e0b;' }} font-size: 0.78rem;">
                <i class="bi bi-patch-check-fill {{ $pendingVerificationsCount > 0 ? '' : 'text-warning-emphasis' }}"></i>
                <span>Verifications</span>
                @if($pendingVerificationsCount > 0)
                    <span class="badge bg-white text-danger rounded-pill px-1.5 py-0.5 ms-1" style="font-size: 0.68rem;">{{ $pendingVerificationsCount }}</span>
                @endif
            </a>

            {{-- Switch / Open Store POS View --}}
            <a href="{{ route('dashboard.index') }}" class="crm-pos-btn d-none d-sm-flex" style="background: linear-gradient(135deg, #059669 0%, #047857 100%);">
                <i class="bi bi-shop"></i>
                <span>Open Store POS</span>
            </a>

            <div class="crm-divider d-none d-md-block"></div>

            {{-- User Profile Dropdown --}}
            <div class="dropdown">
                <button class="crm-user-btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="crm-avatar" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: #000;">
                        {{ $userInitial }}
                    </div>
                    <div class="crm-user-info d-none d-md-block">
                        <span class="crm-user-name">{{ $userName }}</span>
                        <span class="crm-user-role text-warning-emphasis fw-bold">{{ $userRole }}</span>
                    </div>
                    <i class="bi bi-chevron-down crm-chevron d-none d-md-block"></i>
                </button>

                <ul class="dropdown-menu dropdown-menu-end crm-dropdown shadow-lg border-0 rounded-4 mt-2 py-0" style="min-width:275px; overflow:hidden;">
                    <li class="crm-drop-header">
                        <div class="crm-drop-avatar" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: #000;">
                            {{ $userInitial }}
                        </div>
                        <div class="min-w-0 flex-grow-1">
                            <div class="crm-drop-name text-truncate">{{ $userName }}</div>
                            <div class="crm-drop-email text-truncate">{{ $userEmail }}</div>
                            <span class="badge bg-warning text-dark fw-bold" style="font-size: 0.62rem;">SUPERADMIN</span>
                        </div>
                    </li>
                    <li class="px-2 pt-2 pb-1">
                        <a class="dropdown-item crm-drop-item fw-bold" href="{{ route('sa.dashboard.index') }}" style="background: rgba(245, 158, 11, 0.1);">
                            <span class="crm-drop-icon bg-warning text-dark"><i class="bi bi-speedometer2"></i></span>
                            <div>
                                <div class="fw-bold text-dark">SA Dashboard</div>
                                <div class="small text-muted" style="font-size: 0.7rem;">Platform monitoring &amp; KPIs</div>
                            </div>
                        </a>
                        <a class="dropdown-item crm-drop-item" href="{{ route('sa.subscriptions.verifications') }}">
                            <span class="crm-drop-icon bg-warning-subtle text-warning"><i class="bi bi-patch-check-fill"></i></span>
                            <div>
                                <div class="fw-semibold">Verifications</div>
                                <div class="small text-muted" style="font-size: 0.7rem;">Approve payment proofs</div>
                            </div>
                        </a>
                        <a class="dropdown-item crm-drop-item" href="{{ route('sa.tenants.index') }}">
                            <span class="crm-drop-icon bg-info-subtle text-info"><i class="bi bi-buildings"></i></span>
                            <div>
                                <div class="fw-semibold">Tenant Stores</div>
                                <div class="small text-muted" style="font-size: 0.7rem;">Manage all tenants</div>
                            </div>
                        </a>
                        <a class="dropdown-item crm-drop-item" href="{{ route('dashboard.index') }}">
                            <span class="crm-drop-icon bg-success-subtle text-success"><i class="bi bi-shop"></i></span>
                            <div>
                                <div class="fw-semibold text-success">Switch to Store POS</div>
                                <div class="small text-muted" style="font-size: 0.7rem;">Store view mode</div>
                            </div>
                        </a>
                        <a class="dropdown-item crm-drop-item" href="{{ route('sa.system-settings.index') }}">
                            <span class="crm-drop-icon bg-secondary-subtle text-secondary"><i class="bi bi-sliders2-vertical"></i></span>
                            <div>
                                <div class="fw-semibold">System Settings</div>
                                <div class="small text-muted" style="font-size: 0.7rem;">Global platform config</div>
                            </div>
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

{{-- ── Topbar Styles (Dedicated Complete CRM Topbar) ────────── --}}
<style>
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

.crm-divider {
    width: 1px;
    height: 28px;
    background: rgba(255, 255, 255, 0.18);
    margin: 0 2px;
}

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

@media (max-width: 991px) {
    .crm-topbar { padding: 0 1rem; }
}
</style>
