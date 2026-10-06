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
                        <span class="crm-store-status text-warning">
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
            <a href="{{ route('sa.subscriptions.verifications') }}" class="btn btn-sm rounded-pill fw-bold d-none d-sm-inline-flex align-items-center gap-1.5 px-3 shadow-xs {{ $pendingVerificationsCount > 0 ? 'btn-danger' : 'btn-outline-light text-white' }}" style="font-size: 0.78rem;">
                <i class="bi bi-patch-check-fill"></i>
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
                        <span class="crm-user-role text-warning">{{ $userRole }}</span>
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
