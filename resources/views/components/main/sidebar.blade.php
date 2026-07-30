<div class="sidebar d-flex flex-column">

    <div class="border-bottom px-4 pb-3">

        <a
            href="{{ route('sa.dashboard.index') }}"
            class="d-flex align-items-center text-decoration-none"
        >

            <img
                src="{{ asset('images/ic_launcher.png') }}"
                alt="Logo"
                class="me-3"
                style="width:52px;height:52px;object-fit:contain;"
            >

            <div>

                <h5 class="fw-bold text-dark mb-0">
                    BantayEskwela
                </h5>

                <small class="text-muted">
                    Super Admin Console
                </small>

            </div>

        </a>

    </div>

    <div class="sidebar-scroll flex-grow-1 py-3">

        <div class="px-3 mb-2">

            <small class="text-uppercase text-muted fw-semibold">
                Main
            </small>

        </div>

        <ul class="sidebar-menu">

            <li class="sidebar-item">

                <a
                    href="{{ route('sa.dashboard.index') }}"
                    class="sidebar-link justify-content-start {{ request()->routeIs('sa.dashboard.*') ? 'active' : '' }}"
                >

                    <i class="bi bi-grid-1x2-fill sidebar-icon"></i>

                    <span>
                        Dashboard
                    </span>

                </a>

            </li>

        </ul>

        <div class="px-3 mt-3 mb-2">

            <small class="text-uppercase text-muted fw-semibold">
                Platform Management
            </small>

        </div>

        <ul class="sidebar-menu">

            <li class="sidebar-item">

                <a
                    class="sidebar-link d-flex justify-content-between align-items-center {{ request()->routeIs('sa.tenants.*') ? '' : 'collapsed' }}"
                    data-bs-toggle="collapse"
                    href="#tenantsMenu"
                    role="button"
                    aria-expanded="{{ request()->routeIs('sa.tenants.*') ? 'true' : 'false' }}"
                >

                    <div class="d-flex align-items-center">

                        <i class="bi bi-buildings-fill sidebar-icon"></i>

                        <span>
                            Tenants
                        </span>

                    </div>

                    <i class="bi bi-chevron-down dropdown-icon"></i>

                </a>

                <div
                    class="collapse sidebar-dropdown {{ request()->routeIs('sa.tenants.*') ? 'show' : '' }}"
                    id="tenantsMenu"
                >

                    <a
                        href="{{ route('sa.tenants.index') }}"
                        class="sidebar-sublink {{ request()->routeIs('sa.tenants.index') ? 'active' : '' }}"
                    >

                        <i class="bi bi-list-ul sidebar-subicon"></i>

                        <span>
                            Tenant List
                        </span>

                    </a>

                    <a
                        href="{{ route('sa.tenants.create') }}"
                        class="sidebar-sublink {{ request()->routeIs('sa.tenants.create') ? 'active' : '' }}"
                    >

                        <i class="bi bi-plus-circle-fill sidebar-subicon"></i>

                        <span>
                            Register Tenant
                        </span>

                    </a>

                </div>

            </li>

            <li class="sidebar-item">

                <a
                    href="{{ route('users.index') }}"
                    class="sidebar-link justify-content-start {{ request()->routeIs('users.*') ? 'active' : '' }}"
                >

                    <i class="bi bi-people-fill sidebar-icon"></i>

                    <span>
                        Users
                    </span>

                </a>

            </li>

            <li class="sidebar-item">

                <a
                    class="sidebar-link d-flex justify-content-between align-items-center {{ request()->routeIs('roles.*') || request()->routeIs('permissions.*') ? '' : 'collapsed' }}"
                    data-bs-toggle="collapse"
                    href="#accessMenu"
                    role="button"
                    aria-expanded="{{ request()->routeIs('roles.*') || request()->routeIs('permissions.*') ? 'true' : 'false' }}"
                >

                    <div class="d-flex align-items-center">

                        <i class="bi bi-shield-lock-fill sidebar-icon"></i>

                        <span>
                            Access Control
                        </span>

                    </div>

                    <i class="bi bi-chevron-down dropdown-icon"></i>

                </a>

                <div
                    class="collapse sidebar-dropdown {{ request()->routeIs('roles.*') || request()->routeIs('permissions.*') ? 'show' : '' }}"
                    id="accessMenu"
                >

                    <a
                        href="{{ route('roles.index') }}"
                        class="sidebar-sublink {{ request()->routeIs('roles.*') ? 'active' : '' }}"
                    >

                        <i class="bi bi-person-badge-fill sidebar-subicon"></i>

                        <span>
                            Roles
                        </span>

                    </a>

                    <a
                        href="{{ route('permissions.index') }}"
                        class="sidebar-sublink {{ request()->routeIs('permissions.*') ? 'active' : '' }}"
                    >

                        <i class="bi bi-key-fill sidebar-subicon"></i>

                        <span>
                            Permissions
                        </span>

                    </a>

                </div>

            </li>

        </ul>

        <div class="px-3 mt-3 mb-2">

            <small class="text-uppercase text-muted fw-semibold">
                Monitoring
            </small>

        </div>

        <ul class="sidebar-menu">
            <li class="sidebar-item">

                <a
                    href="{{ route('sa.activity-logs.index') }}"
                    class="sidebar-link justify-content-start {{ request()->routeIs('sa.activity-logs.*') ? 'active' : '' }}"
                >

                    <i class="bi bi-clock-history sidebar-icon"></i>

                    <span>
                        Audit Logs
                    </span>

                </a>

            </li>

            <li class="sidebar-item">

                <a
                    class="sidebar-link d-flex justify-content-between align-items-center {{ request()->routeIs('sa.security.*') ? '' : 'collapsed' }}"
                    data-bs-toggle="collapse"
                    href="#securityMenu"
                    role="button"
                    aria-expanded="{{ request()->routeIs('sa.security.*') ? 'true' : 'false' }}"
                >

                    <div class="d-flex align-items-center">

                        <i class="bi bi-shield-lock-fill sidebar-icon"></i>

                        <span>
                            Security Controls
                        </span>

                    </div>

                    <i class="bi bi-chevron-down dropdown-icon"></i>

                </a>

                <div
                    class="collapse sidebar-dropdown {{ request()->routeIs('sa.security.*') ? 'show' : '' }}"
                    id="securityMenu"
                >

                    <a
                        href="{{ route('sa.security.login-activities.index') }}"
                        class="sidebar-sublink {{ request()->routeIs('sa.security.login-activities.*') ? 'active' : '' }}"
                    >

                        <i class="bi bi-box-arrow-in-right sidebar-subicon"></i>

                        <span>
                            Login Activities
                        </span>

                    </a>

                    <a
                        href="{{ route('sa.security.active-sessions.index') }}"
                        class="sidebar-sublink {{ request()->routeIs('sa.security.active-sessions.*') ? 'active' : '' }}"
                    >

                        <i class="bi bi-pc-display sidebar-subicon"></i>

                        <span>
                            Active Sessions
                        </span>

                    </a>

                    <a
                        href="{{ route('sa.security.suspicious-activities.index') }}"
                        class="sidebar-sublink {{ request()->routeIs('sa.security.suspicious-activities.*') ? 'active' : '' }}"
                    >

                        <i class="bi bi-exclamation-triangle-fill sidebar-subicon"></i>

                        <span>
                            Suspicious Activities
                        </span>

                    </a>

                </div>

            </li>

        </ul>

        <div class="px-3 mt-3 mb-2">

            <small class="text-uppercase text-muted fw-semibold">
                Analytics
            </small>

        </div>

        <ul class="sidebar-menu">

            <li class="sidebar-item">

                <a
                    href="{{ route('sa.platform-analytics.index') }}"
                    class="sidebar-link justify-content-start {{ request()->routeIs('sa.platform-analytics.*') ? 'active' : '' }}"
                >

                    <i class="bi bi-bar-chart-fill sidebar-icon"></i>

                    <span>
                        Platform Analytics
                    </span>

                </a>

            </li>

        </ul>

        <div class="px-3 mt-3 mb-2">

            <small class="text-uppercase text-muted fw-semibold">
                System
            </small>

        </div>

        <ul class="sidebar-menu">
            <li class="sidebar-item">

                <a
                    href="{{ route('sa.system-settings.index') }}"
                    class="sidebar-link justify-content-start {{ request()->routeIs('sa.system-settings.*') ? 'active' : '' }}"
                >

                    <i class="bi bi-sliders2-vertical sidebar-icon"></i>

                    <span>
                        System Settings
                    </span>

                </a>

            </li>

            <li class="sidebar-item">

                <a
                    href="{{ route('sa.backups.index') }}"
                    class="sidebar-link justify-content-start {{ request()->routeIs('sa.backups.*') ? 'active' : '' }}"
                >

                    <i class="bi bi-database-fill-gear sidebar-icon"></i>

                    <span>
                        Backups
                    </span>

                </a>

            </li>

        </ul>

        <div class="px-3 mt-3 mb-2">

            <small class="text-uppercase text-muted fw-semibold">
                Support
            </small>

        </div>

        <ul class="sidebar-menu">

            <li class="sidebar-item">

                <a
                    href="{{ route('support-center.index') }}"
                    class="sidebar-link justify-content-start {{ request()->routeIs('support-center.*') ? 'active' : '' }}"
                >

                    <i class="bi bi-life-preserver sidebar-icon"></i>

                    <span>
                        Support Center
                    </span>

                </a>

            </li>

        </ul>

    </div>

</div>
