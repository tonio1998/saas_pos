<div class="sidebar d-flex flex-column">

    <div class="sidebar-scroll flex-grow-1">

        <ul class="sidebar-menu">

            <li class="sidebar-title">
                MAIN
            </li>

            <li class="sidebar-item">

                <a
                    href="{{ route('sa.dashboard.index') }}"
                    class="sidebar-link {{ request()->routeIs('sa.dashboard.*') ? 'active' : '' }}"
                >

                    <i class="bi bi-grid-1x2-fill sidebar-icon"></i>

                    <span>
                        Dashboard
                    </span>

                </a>

            </li>

            <li class="sidebar-divider"></li>

            <li class="sidebar-title">
                PLATFORM MANAGEMENT
            </li>

            <li class="sidebar-item">

                <a
                    class="sidebar-link"
                    data-bs-toggle="collapse"
                    href="#schoolsMenu"
                    role="button"
                >

                    <i class="bi bi-buildings-fill sidebar-icon"></i>

                    <span>
                        Schools
                    </span>

                    <i class="bi bi-chevron-down dropdown-icon"></i>

                </a>

                <div
                    class="collapse sidebar-dropdown"
                    id="schoolsMenu"
                >

                    <a
                        href="{{ route('sa.schools.index') }}"
                        class="sidebar-sublink"
                    >

                        <i class="bi bi-list-ul sidebar-subicon"></i>

                        <span>
                            School List
                        </span>

                    </a>

                    <a
                        href="{{ route('sa.schools.create') }}"
                        class="sidebar-sublink"
                    >

                        <i class="bi bi-plus-circle-fill sidebar-subicon"></i>

                        <span>
                            Register School
                        </span>

                    </a>

                </div>

            </li>

            <li class="sidebar-item">

                <a
                    href="{{ route('users.index') }}"
                    class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}"
                >

                    <i class="bi bi-people-fill sidebar-icon"></i>

                    <span>
                        Users
                    </span>

                </a>

            </li>

            <li class="sidebar-item">

                <a
                    class="sidebar-link"
                    data-bs-toggle="collapse"
                    href="#accessMenu"
                    role="button"
                >

                    <i class="bi bi-shield-lock-fill sidebar-icon"></i>

                    <span>
                        Access Control
                    </span>

                    <i class="bi bi-chevron-down dropdown-icon"></i>

                </a>

                <div
                    class="collapse sidebar-dropdown"
                    id="accessMenu"
                >

                    <a
                        href="{{ route('roles.index') }}"
                        class="sidebar-sublink"
                    >

                        <i class="bi bi-person-badge-fill sidebar-subicon"></i>

                        <span>
                            Roles
                        </span>

                    </a>

                    <a
                        href="{{ route('permissions.index') }}"
                        class="sidebar-sublink"
                    >

                        <i class="bi bi-key-fill sidebar-subicon"></i>

                        <span>
                            Permissions
                        </span>

                    </a>

                </div>

            </li>

            <li class="sidebar-divider"></li>

            <li class="sidebar-title">
                MONITORING
            </li>

            <li class="sidebar-item">

                <a
                    href="{{ route('sa.activity-logs.index') }}"
                    class="sidebar-link {{ request()->routeIs('sa.activity-logs.*') ? 'active' : '' }}"
                >

                    <i class="bi bi-clock-history sidebar-icon"></i>

                    <span>
                        Audit Logs
                    </span>

                </a>

            </li>

            <li class="sidebar-item">

                <a
                    href="{{ route('sms.index') }}"
                    class="sidebar-link {{ request()->routeIs('sms.*') ? 'active' : '' }}"
                >

                    <i class="bi bi-chat-dots-fill sidebar-icon"></i>

                    <span>
                        SMS Monitoring
                    </span>

                </a>

            </li>

            <li class="sidebar-item">

                <a
                    class="sidebar-link"
                    data-bs-toggle="collapse"
                    href="#SecurityMenu"
                    role="button"
                >

                    <i class="bi bi-shield-lock-fill sidebar-icon"></i>

                    <span>
                        Security Controls
                    </span>

                    <i class="bi bi-chevron-down dropdown-icon"></i>

                </a>

                <div
                    class="collapse sidebar-dropdown"
                    id="SecurityMenu"
                >

                    <a
                        href="{{ route('sa.security.login-activities.index') }}"
                        class="sidebar-sublink"
                    >

                        <i class="bi bi-person-badge-fill sidebar-subicon"></i>

                        <span>
                            Login Activities
                        </span>

                    </a>

                    <a
                        href="{{ route('sa.security.active-sessions.index') }}"
                        class="sidebar-sublink"
                    >

                        <i class="bi bi-key-fill sidebar-subicon"></i>

                        <span>
                            Active Sessions
                        </span>

                    </a>

                    <a
                        href="{{ route('sa.security.suspicious-activities.index') }}"
                        class="sidebar-sublink"
                    >

                        <i class="bi bi-key-fill sidebar-subicon"></i>

                        <span>
                            Suspicious Activities
                        </span>

                    </a>

                </div>

            </li>

            <li class="sidebar-divider"></li>

            <li class="sidebar-title">
                ANALYTICS
            </li>

            <li class="sidebar-item">

                <a
                    href="{{ route('sa.platform-analytics.index') }}"
                    class="sidebar-link"
                >

                    <i class="bi bi-bar-chart-fill sidebar-icon"></i>

                    <span>
                        Platform Analytics
                    </span>

                </a>

            </li>

            <li class="sidebar-divider"></li>

            <li class="sidebar-title">
                SYSTEM
            </li>

            <li class="sidebar-item">

                <a
                    href="{{ route('sa.system-settings.index') }}"
                    class="sidebar-link {{ request()->routeIs('sa.system-settings.*') ? 'active' : '' }}"
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
                    class="sidebar-link {{ request()->routeIs('sa.backups.*') ? 'active' : '' }}"
                >

                    <i class="bi bi-database-fill-gear sidebar-icon"></i>

                    <span>
            Backups
        </span>

                </a>

            </li>

            <li class="sidebar-divider"></li>

            <li class="sidebar-title">
                SUPPORT
            </li>

            <li class="sidebar-item">

                <a
                    href="{{ route('support-center.index') }}"
                    class="sidebar-link  {{ request()->routeIs('support-center.*') ? 'active' : '' }}"
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
