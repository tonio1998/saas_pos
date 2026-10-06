@php
    $pendingPaymentsCount = \App\Models\POS\POSTenant::where('payment_status', 'pending_verification')->count();
    $dueSoonCount = \App\Models\POS\POSTenant::whereNotNull('subscription_end')
        ->where('subscription_end', '<=', now()->addDays(7)->endOfDay())
        ->where('status', '!=', 'inactive')
        ->count();
    $activeTenantContext = session('tenant_id') ? \App\Models\POS\POSTenant::find(session('tenant_id')) : null;
@endphp

<div class="sidebar-inner d-flex flex-column h-100">

    <!-- Brand Header -->
    <div class="sidebar-brand-wrapper position-relative">
        <a href="{{ route('sa.dashboard.index') }}" class="sidebar-brand-link text-decoration-none">
            <div class="sidebar-brand-fallback">
                <div class="sidebar-brand-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <i class="bi bi-shield-shaded text-white"></i>
                </div>
                <div class="sidebar-brand-text">
                    <div class="sidebar-brand-title">Likha<span class="sidebar-brand-accent">POS</span></div>
                    <div class="d-flex align-items-center gap-1.5 mt-0.5">
                        <span class="badge bg-warning text-dark fw-bold px-1.5 py-0.5" style="font-size: 0.65rem; letter-spacing: 0.4px;">
                            SUPERADMIN
                        </span>
                        <span class="sidebar-brand-sub text-truncate">Console</span>
                    </div>
                </div>
            </div>
        </a>

        <!-- Mobile Close Button -->
        <button type="button" id="sidebarCloseBtn" class="sidebar-close-btn d-lg-none" aria-label="Close Sidebar">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <!-- Quick Switcher Banner / Store Context Status Strip -->
    <div class="sidebar-branch-strip">
        @if($activeTenantContext)
            <div class="p-2.5 rounded-3 border" style="background: rgba(245, 158, 11, 0.12); border-color: rgba(245, 158, 11, 0.4) !important;">
                <div class="d-flex align-items-center justify-content-between mb-1.5">
                    <span class="extra-small fw-bold text-uppercase" style="color: #b45309; font-size: 0.65rem; letter-spacing: 0.3px;">
                        <i class="bi bi-shop me-1"></i> Store Context Active
                    </span>
                    <span class="badge fw-bold" style="background: rgba(34, 197, 94, 0.2); color: #15803d; font-size: 0.65rem;">Live</span>
                </div>
                <div class="fw-bold small text-truncate mb-2" style="color: #0f172a; font-size: 0.85rem;" title="{{ $activeTenantContext->business_name }}">
                    {{ $activeTenantContext->business_name }}
                </div>
                <div class="d-flex gap-1.5">
                    <a href="{{ route('dashboard.index') }}" class="btn btn-warning btn-sm fw-bold flex-grow-1 py-1 text-dark shadow-xs" style="background: #fbbf24; border: 1px solid #d97706; font-size: 0.78rem;">
                        <i class="bi bi-arrow-right-circle me-1"></i> Open Store
                    </a>
                    <form method="POST" action="{{ route('sa.tenants.close-context') }}" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary btn-sm py-1 px-2.5" title="Exit Store Context" style="font-size: 0.78rem;">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </form>
                </div>
            </div>
        @else
            <div class="sidebar-branch-card d-flex align-items-center justify-content-between gap-2">
                <div class="d-flex align-items-center gap-1.5 min-w-0 flex-grow-1">
                    <i class="bi bi-hdd-network-fill text-warning flex-shrink-0" style="font-size: 0.9rem;"></i>
                    <span class="sidebar-branch-name text-truncate">Platform Root Mode</span>
                </div>
                <a href="{{ route('dashboard.index') }}" class="badge text-decoration-none fw-bold" style="background: rgba(5, 150, 105, 0.15); color: #059669; font-size: 0.68rem; padding: 4px 8px; border-radius: 6px;">
                    <i class="bi bi-shop me-1"></i> Store View
                </a>
            </div>
        @endif
    </div>

    <!-- Navigation Menu Items -->
    <div class="sidebar-scroll flex-grow-1 overflow-auto">

        <!-- SECTION: MAIN -->
        <div class="sidebar-section-title">
            Main
        </div>
        <ul class="sidebar-menu list-unstyled mb-0">
            <li class="sidebar-item">
                <a href="{{ route('sa.dashboard.index') }}" class="sidebar-link {{ request()->routeIs('sa.dashboard.*') ? 'active' : '' }}">
                    <span class="sidebar-icon-wrap">
                        <i class="bi bi-speedometer2 sidebar-icon"></i>
                    </span>
                    <span class="sidebar-label">Platform CRM</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('dashboard.index') }}" class="sidebar-link" style="background: rgba(5, 150, 105, 0.08); border: 1px dashed rgba(5, 150, 105, 0.4);">
                    <span class="sidebar-icon-wrap" style="background: rgba(5, 150, 105, 0.18); color: #059669;">
                        <i class="bi bi-shop sidebar-icon"></i>
                    </span>
                    <span class="sidebar-label fw-bold" style="color: #059669;">Open Store POS View</span>
                    <i class="bi bi-box-arrow-up-right ms-auto extra-small" style="color: #059669; font-size: 0.72rem;"></i>
                </a>
            </li>
        </ul>

        <!-- SECTION: PLATFORM MANAGEMENT -->
        <div class="sidebar-section-title">
            Platform Management
        </div>
        <ul class="sidebar-menu list-unstyled mb-0">
            <!-- Tenants Accordion -->
            <li class="sidebar-item">
                <a class="sidebar-link {{ request()->routeIs('sa.tenants.*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#tenantsMenu" role="button" aria-expanded="{{ request()->routeIs('sa.tenants.*') ? 'true' : 'false' }}">
                    <span class="sidebar-icon-wrap">
                        <i class="bi bi-buildings-fill sidebar-icon"></i>
                    </span>
                    <span class="sidebar-label">Tenants &amp; Stores</span>
                    <i class="bi bi-chevron-down dropdown-icon ms-auto"></i>
                </a>
                <div class="collapse {{ request()->routeIs('sa.tenants.*') ? 'show' : '' }} sidebar-dropdown" id="tenantsMenu">
                    <a href="{{ route('sa.tenants.index') }}" class="sidebar-sublink {{ request()->routeIs('sa.tenants.index') ? 'active' : '' }}">
                        <i class="bi bi-list-ul sidebar-subicon"></i>
                        <span>Tenant Stores List</span>
                    </a>
                    <a href="{{ route('sa.tenants.create') }}" class="sidebar-sublink {{ request()->routeIs('sa.tenants.create') ? 'active' : '' }}">
                        <i class="bi bi-plus-circle-fill sidebar-subicon"></i>
                        <span>Register New Tenant</span>
                    </a>
                </div>
            </li>

            <!-- Payment Verifications -->
            <li class="sidebar-item">
                <a href="{{ route('sa.subscriptions.verifications') }}" class="sidebar-link {{ request()->routeIs('sa.subscriptions.verifications*') ? 'active' : '' }}">
                    <span class="sidebar-icon-wrap">
                        <i class="bi bi-patch-check-fill sidebar-icon text-warning"></i>
                    </span>
                    <span class="sidebar-label">Verifications</span>
                    @if($pendingPaymentsCount > 0)
                        <span class="badge bg-danger rounded-pill ms-auto px-2 py-0.5" style="font-size: 0.7rem;">{{ $pendingPaymentsCount }}</span>
                    @endif
                </a>
            </li>

            <!-- Due Date Monitoring Sentinel -->
            <li class="sidebar-item">
                <a href="{{ route('sa.subscriptions.monitoring') }}" class="sidebar-link {{ request()->routeIs('sa.subscriptions.monitoring*') ? 'active' : '' }}">
                    <span class="sidebar-icon-wrap">
                        <i class="bi bi-alarm-fill sidebar-icon text-danger"></i>
                    </span>
                    <span class="sidebar-label">Due Sentinel</span>
                    @if($dueSoonCount > 0)
                        <span class="badge bg-danger rounded-pill ms-auto px-2 py-0.5" style="font-size: 0.7rem;">{{ $dueSoonCount }}</span>
                    @endif
                </a>
            </li>

            <!-- Subscription & Promo Plans Studio -->
            <li class="sidebar-item">
                <a href="{{ route('sa.subscriptions.plans') }}" class="sidebar-link {{ request()->routeIs('sa.subscriptions.plans*') ? 'active' : '' }}">
                    <span class="sidebar-icon-wrap">
                        <i class="bi bi-tags-fill sidebar-icon text-success"></i>
                    </span>
                    <span class="sidebar-label">Plans & Promos</span>
                </a>
            </li>

            <!-- Users -->
            <li class="sidebar-item">
                <a href="{{ route('sa.users.index') }}" class="sidebar-link {{ request()->routeIs('sa.users.*') ? 'active' : '' }}">
                    <span class="sidebar-icon-wrap">
                        <i class="bi bi-people-fill sidebar-icon"></i>
                    </span>
                    <span class="sidebar-label">System Users</span>
                </a>
            </li>

            <!-- Access Control Accordion -->
            <li class="sidebar-item">
                <a class="sidebar-link {{ request()->routeIs('roles.*') || request()->routeIs('permissions.*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#accessMenu" role="button" aria-expanded="{{ request()->routeIs('roles.*') || request()->routeIs('permissions.*') ? 'true' : 'false' }}">
                    <span class="sidebar-icon-wrap">
                        <i class="bi bi-shield-lock-fill sidebar-icon"></i>
                    </span>
                    <span class="sidebar-label">Access Control</span>
                    <i class="bi bi-chevron-down dropdown-icon ms-auto"></i>
                </a>
                <div class="collapse {{ request()->routeIs('roles.*') || request()->routeIs('permissions.*') ? 'show' : '' }} sidebar-dropdown" id="accessMenu">
                    <a href="{{ route('roles.index') }}" class="sidebar-sublink {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                        <i class="bi bi-person-badge-fill sidebar-subicon"></i>
                        <span>Roles</span>
                    </a>
                    <a href="{{ route('permissions.index') }}" class="sidebar-sublink {{ request()->routeIs('permissions.*') ? 'active' : '' }}">
                        <i class="bi bi-key-fill sidebar-subicon"></i>
                        <span>Permissions</span>
                    </a>
                </div>
            </li>
        </ul>

        <!-- SECTION: MONITORING & SECURITY -->
        <div class="sidebar-section-title">
            Monitoring &amp; Security
        </div>
        <ul class="sidebar-menu list-unstyled mb-0">
            <li class="sidebar-item">
                <a href="{{ route('sa.activity-logs.index') }}" class="sidebar-link {{ request()->routeIs('sa.activity-logs.*') ? 'active' : '' }}">
                    <span class="sidebar-icon-wrap">
                        <i class="bi bi-clock-history sidebar-icon"></i>
                    </span>
                    <span class="sidebar-label">Audit Logs</span>
                </a>
            </li>

            <li class="sidebar-item">
                <a class="sidebar-link {{ request()->routeIs('sa.security.*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#securityMenu" role="button" aria-expanded="{{ request()->routeIs('sa.security.*') ? 'true' : 'false' }}">
                    <span class="sidebar-icon-wrap">
                        <i class="bi bi-shield-check sidebar-icon"></i>
                    </span>
                    <span class="sidebar-label">Security Controls</span>
                    <i class="bi bi-chevron-down dropdown-icon ms-auto"></i>
                </a>
                <div class="collapse {{ request()->routeIs('sa.security.*') ? 'show' : '' }} sidebar-dropdown" id="securityMenu">
                    <a href="{{ route('sa.security.login-activities.index') }}" class="sidebar-sublink {{ request()->routeIs('sa.security.login-activities.*') ? 'active' : '' }}">
                        <i class="bi bi-box-arrow-in-right sidebar-subicon"></i>
                        <span>Login Activities</span>
                    </a>
                    <a href="{{ route('sa.security.active-sessions.index') }}" class="sidebar-sublink {{ request()->routeIs('sa.security.active-sessions.*') ? 'active' : '' }}">
                        <i class="bi bi-pc-display sidebar-subicon"></i>
                        <span>Active Sessions</span>
                    </a>
                    <a href="{{ route('sa.security.suspicious-activities.index') }}" class="sidebar-sublink {{ request()->routeIs('sa.security.suspicious-activities.*') ? 'active' : '' }}">
                        <i class="bi bi-exclamation-triangle-fill sidebar-subicon text-danger"></i>
                        <span>Suspicious Activities</span>
                    </a>
                </div>
            </li>
        </ul>

        <!-- SECTION: ANALYTICS & SYSTEM -->
        <div class="sidebar-section-title">
            Analytics &amp; System
        </div>
        <ul class="sidebar-menu list-unstyled mb-0">
            <li class="sidebar-item">
                <a href="{{ route('sa.platform-analytics.index') }}" class="sidebar-link {{ request()->routeIs('sa.platform-analytics.*') ? 'active' : '' }}">
                    <span class="sidebar-icon-wrap">
                        <i class="bi bi-bar-chart-fill sidebar-icon"></i>
                    </span>
                    <span class="sidebar-label">Platform Analytics</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('sa.system-settings.index') }}" class="sidebar-link {{ request()->routeIs('sa.system-settings.*') ? 'active' : '' }}">
                    <span class="sidebar-icon-wrap">
                        <i class="bi bi-sliders2-vertical sidebar-icon"></i>
                    </span>
                    <span class="sidebar-label">System Settings</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('sa.backups.index') }}" class="sidebar-link {{ request()->routeIs('sa.backups.*') ? 'active' : '' }}">
                    <span class="sidebar-icon-wrap">
                        <i class="bi bi-database-fill-gear sidebar-icon"></i>
                    </span>
                    <span class="sidebar-label">Database Backups</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('support-center.index') }}" class="sidebar-link {{ request()->routeIs('support-center.*') ? 'active' : '' }}">
                    <span class="sidebar-icon-wrap">
                        <i class="bi bi-life-preserver sidebar-icon"></i>
                    </span>
                    <span class="sidebar-label">Support Center</span>
                </a>
            </li>
        </ul>

    </div>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer mt-auto">
        <a href="{{ route('terminal.index') }}" class="sidebar-pos-launch-btn">
            <i class="bi bi-calculator-fill"></i>
            <span>Open POS Terminal</span>
        </a>
    </div>

</div>

{{-- ── Sidebar Styles (Responsive Theme Adaptation) ───────── --}}
<style>
.sidebar-inner {
    padding: 16px 14px 18px;
    background: var(--theme-sidebar, #0f172a);
    color: var(--theme-sidebar-text, #1e293b);
}

.sidebar-brand-wrapper {
    padding: 4px 6px 14px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.08);
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.sidebar-brand-link {
    width: 100%;
    display: flex;
    align-items: center;
}
.sidebar-brand-fallback {
    display: flex;
    align-items: center;
    gap: 12px;
    width: 100%;
}
.sidebar-brand-icon {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}
.sidebar-brand-text {
    line-height: 1.2;
    min-width: 0;
}
.sidebar-brand-title {
    font-family: 'Outfit', sans-serif;
    font-size: 1.2rem;
    font-weight: 800;
    color: var(--theme-sidebar-text, #0f172a);
    letter-spacing: -0.01em;
}
.sidebar-brand-accent {
    color: var(--theme-primary, #059669);
}
.sidebar-brand-sub {
    font-size: 0.72rem;
    color: #64748b;
    font-weight: 600;
}

.sidebar-close-btn {
    position: absolute;
    right: 4px;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(0, 0, 0, 0.06);
    border: none;
    border-radius: 10px;
    color: var(--theme-sidebar-text, #0f172a);
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    cursor: pointer;
    transition: background 0.15s ease;
}
.sidebar-close-btn:hover {
    background: rgba(0, 0, 0, 0.12);
}

.sidebar-branch-strip {
    padding: 10px 4px 12px;
    margin-bottom: 6px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.06);
}
.sidebar-branch-card {
    background: rgba(0, 0, 0, 0.03);
    border: 1px solid rgba(0, 0, 0, 0.08);
    border-radius: 12px;
    padding: 7px 11px;
    min-width: 0;
}
.sidebar-branch-name {
    font-size: 0.77rem;
    font-weight: 700;
    color: var(--theme-sidebar-text, #1e293b);
    letter-spacing: 0.15px;
    line-height: 1.2;
}

.sidebar-scroll {
    padding-right: 4px;
}
.sidebar-scroll::-webkit-scrollbar {
    width: 4px;
}
.sidebar-scroll::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.12);
    border-radius: 10px;
}

.sidebar-section-title {
    font-size: 0.68rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.9px;
    color: #64748b;
    padding: 14px 10px 6px;
}

.sidebar-item {
    margin-bottom: 4px;
}
.sidebar-link {
    display: flex;
    align-items: center;
    padding: 9px 12px;
    border-radius: 12px;
    color: var(--theme-sidebar-text, #334155);
    font-size: 0.86rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s cubic-bezier(0.2, 0.8, 0.2, 1);
    min-height: 42px;
    gap: 11px;
}
.sidebar-link:hover {
    background: rgba(0, 0, 0, 0.05);
    color: var(--theme-primary, #059669);
    transform: translateX(3px);
}
.sidebar-link.active {
    background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
    color: #ffffff !important;
    font-weight: 700;
    box-shadow: 0 4px 14px rgba(5, 150, 105, 0.25);
}

.sidebar-icon-wrap {
    width: 32px;
    height: 32px;
    border-radius: 10px;
    background: rgba(0, 0, 0, 0.04);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 1.02rem;
    transition: all 0.2s ease;
    color: #64748b;
}
.sidebar-link.active .sidebar-icon-wrap {
    background: rgba(255, 255, 255, 0.22);
    color: #ffffff !important;
}

.sidebar-label {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    font-size: 0.86rem;
}

.dropdown-icon {
    font-size: 0.72rem;
    transition: transform 0.2s ease;
    opacity: 0.6;
}
.sidebar-link[aria-expanded="true"] .dropdown-icon {
    transform: rotate(180deg);
}

.sidebar-dropdown {
    padding-left: 10px;
    margin: 4px 0 8px 16px;
    border-left: 2px solid rgba(0, 0, 0, 0.08);
}
.sidebar-sublink {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 7px 10px;
    min-height: 34px;
    font-size: 0.82rem;
    font-weight: 600;
    color: #64748b;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.15s ease;
    margin-bottom: 2px;
    white-space: nowrap;
}
.sidebar-sublink:hover {
    background: rgba(0, 0, 0, 0.04);
    color: var(--theme-primary, #059669);
    transform: translateX(2px);
}
.sidebar-sublink.active {
    background: rgba(5, 150, 105, 0.12);
    color: var(--theme-primary, #059669) !important;
    font-weight: 700;
}
.sidebar-subicon {
    font-size: 0.95rem;
    width: 18px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.sidebar-footer {
    padding-top: 14px;
    border-top: 1px solid rgba(0, 0, 0, 0.06);
}
.sidebar-pos-launch-btn {
    width: 100%;
    padding: 11px 16px;
    min-height: 44px;
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
    color: #ffffff !important;
    border: none;
    border-radius: 12px;
    font-family: 'Outfit', sans-serif;
    font-size: 0.88rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    text-decoration: none;
    box-shadow: 0 4px 14px rgba(5, 150, 105, 0.25);
    transition: all 0.2s ease;
}
.sidebar-pos-launch-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(5, 150, 105, 0.35);
    filter: brightness(1.08);
}
</style>
