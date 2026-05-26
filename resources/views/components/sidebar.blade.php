<div class="sidebar d-flex flex-column">

    <div class="sidebar-scroll flex-grow-1">
        @if(
                        auth()->user()->hasRole('SA') &&
                        !session('school_id')
                    )
            <ul class="sidebar-menu">
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link text-muted">SUPER ADMIN</a>
                </li>
                <li class="sidebar-item">
                    <a
                        href="{{ route('sa.dashboard.index') }}"
                        class="sidebar-link {{ request()->routeIs('sa.dashboard.index') ? 'active' : '' }}"
                    >
                        <i class="bi bi-grid-1x2-fill sidebar-icon"></i>
                        <span>
                    Dashboard
                </span>
                    </a>
                </li>
                @can('schools.view')
                    <li class="sidebar-item">
                        <a href="{{ route('sa.schools.index') }}" class="sidebar-link {{ request()->routeIs('schools.*') ? 'active' : '' }}">
                            <i class="bi bi-buildings sidebar-icon"></i>
                            <span>School Management</span>
                        </a>
                    </li>
                @endcan
                @canany(['permissions.view','roles.view'])
                    <li class="sidebar-item">
                        <a
                            class="sidebar-link"
                            data-bs-toggle="collapse"
                            href="#rolesMenu"
                            role="button"
                        >
                            <i class="bi bi-shield-check sidebar-icon"></i>
                            <span>Role & Permission</span>
                            <i class="bi bi-chevron-down dropdown-icon"></i>
                        </a>

                        <div
                            class="collapse sidebar-dropdown"
                            id="rolesMenu"
                        >

                            @can('permissions.view')
                                <a
                                    href="{{ route('permissions.index') }}"
                                    class="sidebar-sublink"
                                >
                                    <i class="bi bi-key-fill sidebar-subicon"></i>
                                    <span>Permissions</span>
                                </a>
                            @endcan

                            @can('roles.view')
                                <a
                                    href="{{ route('roles.index') }}"
                                    class="sidebar-sublink"
                                >
                                    <i class="bi bi-person-workspace sidebar-subicon"></i>
                                    <span>Roles</span>
                                </a>
                            @endcan

                        </div>

                    </li>
                @endcanany

                <li class="sidebar-item">
                    <a class="sidebar-link" data-bs-toggle="collapse" href="#usersMenu" role="button">
                        <i class="bi bi-shield-check sidebar-icon"></i>
                        <span>User Management</span>
                        <i class="bi bi-chevron-down dropdown-icon"></i>
                    </a>
                    <div class="collapse sidebar-dropdown" id="usersMenu">
                        <a href="{{ route('users.index') }}" class="sidebar-sublink">
                            <i class="bi bi-people-fill sidebar-subicon"></i>
                            <span>Masterlist</span>
                        </a>
                    </div>
                </li>
            </ul>
        @endif

        @if(session('school_id') > 0)
            <ul class="sidebar-menu">

                @can('scanner.view')
                    <li class="sidebar-item">
                        <a
                            href="{{ route('scanner.index') }}"
                            class="sidebar-link {{ request()->routeIs('scanner.index') ? 'active' : '' }}"
                        >
                            <i class="bi bi-upc-scan sidebar-icon"></i>
                            <span>Scanner</span>
                        </a>
                    </li>
                @endcan

                @can('dashboard.view')
                    <li class="sidebar-item">
                        <a
                            href="{{ route('dashboard.index') }}"
                            class="sidebar-link {{ request()->routeIs('dashboard.index') ? 'active' : '' }}"
                        >
                            <i class="bi bi-grid-1x2-fill sidebar-icon"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                @endcan

                @canany([
                    'logs.view',
                    'logs.users.view'
                ])
                    <li class="sidebar-item">

                        <a
                            class="sidebar-link"
                            data-bs-toggle="collapse"
                            href="#systemMenu"
                            role="button"
                        >
                            <i class="bi bi-clipboard-data sidebar-icon"></i>
                            <span>Logs Monitoring</span>
                            <i class="bi bi-chevron-down dropdown-icon"></i>
                        </a>

                        <div
                            class="collapse sidebar-dropdown"
                            id="systemMenu"
                        >

                            @can('logs.view')
                                <a
                                    href="{{ route('logs.index') }}"
                                    class="sidebar-sublink"
                                >
                                    <i class="bi bi-clock-history sidebar-subicon"></i>
                                    <span>All Logs</span>
                                </a>
                            @endcan

                            @can('logs.users.view')
                                <a
                                    href="{{ route('logs.users') }}"
                                    class="sidebar-sublink"
                                >
                                    <i class="bi bi-graph-up-arrow sidebar-subicon"></i>
                                    <span>User Logs</span>
                                </a>
                            @endcan

                        </div>

                    </li>
                @endcanany

                @canany([
                    'students.create',
                    'students.view'
                ])
                    <li class="sidebar-item">

                        <a
                            class="sidebar-link"
                            data-bs-toggle="collapse"
                            href="#studentMenu"
                            role="button"
                        >

                            <i class="bi bi-backpack2-fill sidebar-icon"></i>

                            <span>
                                Student Management
                            </span>

                            <i class="bi bi-chevron-down dropdown-icon"></i>

                        </a>

                        <div
                            class="collapse sidebar-dropdown"
                            id="studentMenu"
                        >

                            @can('students.create')
                                <a
                                    href="{{ route('students.create') }}"
                                    class="sidebar-sublink"
                                >
                                    <i class="bi bi-person-add sidebar-subicon"></i>
                                    <span>Add Student</span>
                                </a>
                            @endcan

                            @can('students.view')
                                <a
                                    href="{{ route('students.index') }}"
                                    class="sidebar-sublink"
                                >
                                    <i class="bi bi-people-fill sidebar-subicon"></i>
                                    <span>Masterlist</span>
                                </a>
                            @endcan

                        </div>

                    </li>
                @endcanany

                @canany([
                    'parents.create',
                    'parents.view'
                ])
                    <li class="sidebar-item">

                        <a
                            class="sidebar-link"
                            data-bs-toggle="collapse"
                            href="#guardianMenu"
                            role="button"
                        >

                            <i class="bi bi-house-heart-fill sidebar-icon"></i>

                            <span>
                                Parent Management
                            </span>

                            <i class="bi bi-chevron-down dropdown-icon"></i>

                        </a>

                        <div
                            class="collapse sidebar-dropdown"
                            id="guardianMenu"
                        >

                            @can('parents.create')
                                <a
                                    href="{{ route('parents.create') }}"
                                    class="sidebar-sublink"
                                >
                                    <i class="bi bi-person-add sidebar-subicon"></i>
                                    <span>Add Parent</span>
                                </a>
                            @endcan

                            @can('parents.view')
                                <a
                                    href="{{ route('parents.index') }}"
                                    class="sidebar-sublink"
                                >
                                    <i class="bi bi-people-fill sidebar-subicon"></i>
                                    <span>Masterlist</span>
                                </a>
                            @endcan

                        </div>

                    </li>
                @endcanany

                @canany([
                    'employees.create',
                    'employees.view'
                ])
                    <li class="sidebar-item">

                        <a
                            class="sidebar-link"
                            data-bs-toggle="collapse"
                            href="#teacherMenu"
                            role="button"
                        >

                            <i class="bi bi-briefcase-fill sidebar-icon"></i>

                            <span>
                                Employee Management
                            </span>

                            <i class="bi bi-chevron-down dropdown-icon"></i>

                        </a>

                        <div
                            class="collapse sidebar-dropdown"
                            id="teacherMenu"
                        >

                            @can('employees.create')
                                <a
                                    href="{{ route('employees.create') }}"
                                    class="sidebar-sublink"
                                >
                                    <i class="bi bi-person-add sidebar-subicon"></i>
                                    <span>Add Employee</span>
                                </a>
                            @endcan

                            @can('employees.view')
                                <a
                                    href="{{ route('employees.index') }}"
                                    class="sidebar-sublink"
                                >
                                    <i class="bi bi-person-badge-fill sidebar-subicon"></i>
                                    <span>Masterlist</span>
                                </a>
                            @endcan

                        </div>

                    </li>
                @endcanany

                @can('sms.view')
                    <li class="sidebar-item">

                        <a
                            class="sidebar-link"
                            data-bs-toggle="collapse"
                            href="#smsMenu"
                            role="button"
                        >

                            <i class="bi bi-chat-dots-fill sidebar-icon"></i>

                            <span>
                                SMS Management
                            </span>

                            <i class="bi bi-chevron-down dropdown-icon"></i>

                        </a>

                        <div
                            class="collapse sidebar-dropdown"
                            id="smsMenu"
                        >

                            <a
                                href="{{ route('sms.index') }}"
                                class="sidebar-sublink"
                            >
                                <i class="bi bi-envelope-paper-fill sidebar-subicon"></i>
                                <span>SMS Queue</span>
                            </a>

                        </div>

                    </li>
                @endcan

                @can('school-users.view')
                    <li class="sidebar-item">

                        <a
                            class="sidebar-link"
                            data-bs-toggle="collapse"
                            href="#usersMenu"
                            role="button"
                        >

                            <i class="bi bi-shield-check sidebar-icon"></i>

                            <span>
                                User Management
                            </span>

                            <i class="bi bi-chevron-down dropdown-icon"></i>

                        </a>

                        <div
                            class="collapse sidebar-dropdown"
                            id="usersMenu"
                        >

                            <a
                                href="{{ route('school-users.index') }}"
                                class="sidebar-sublink"
                            >
                                <i class="bi bi-people-fill sidebar-subicon"></i>
                                <span>Masterlist</span>
                            </a>

                        </div>

                    </li>
                @endcan

                {{--                @can('settings.view')--}}
                {{--                    <li class="sidebar-item">--}}

                {{--                        <a--}}
                {{--                            href="{{ route('settings.index') }}"--}}
                {{--                            class="sidebar-link {{ request()->routeIs('settings.index') ? 'active' : '' }}"--}}
                {{--                        >--}}

                {{--                            <i class="bi bi-sliders2-vertical sidebar-icon"></i>--}}

                {{--                            <span>--}}
                {{--                                Settings--}}
                {{--                            </span>--}}

                {{--                        </a>--}}

                {{--                    </li>--}}
                {{--                @endcan--}}

            </ul>
        @endif

    </div>
    @can('support-center.view')
    @endcan
</div>
