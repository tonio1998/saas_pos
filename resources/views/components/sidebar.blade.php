<div
    class="sidebar"
>
    <ul class="sidebar-menu">

        <li class="sidebar-item">
            <a
                href="{{ route('scanner.index') }}"
                class="sidebar-link {{ request()->routeIs('scanner.index') ? 'active' : '' }}"
            >
                <i class="bi bi-upc-scan sidebar-icon"></i>
                <span>Scanner</span>
            </a>
        </li>

        <li class="sidebar-item">
            <a
                href="{{ route('dashboard.index') }}"
                class="sidebar-link {{ request()->routeIs('dashboard.index') ? 'active' : '' }}"
            >
                <i class="bi bi-grid-1x2-fill sidebar-icon"></i>
                <span>Dashboard</span>
            </a>
        </li>

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

            <div class="collapse sidebar-dropdown" id="systemMenu">
                <a
                    href="{{ route('logs.index') }}"
                    class="sidebar-sublink"
                >
                    <i class="bi bi-clock-history sidebar-subicon"></i>
                    <span>All Logs</span>
                </a>

                <a
                    href="{{ route('logs.users') }}"
                    class="sidebar-sublink"
                >
                    <i class="bi bi-graph-up-arrow sidebar-subicon"></i>
                    <span>User Logs</span>
                </a>
            </div>
        </li>

        <li class="sidebar-item">
            <a
                class="sidebar-link"
                data-bs-toggle="collapse"
                href="#studentMenu"
                role="button"
            >
                <i class="bi bi-backpack2-fill sidebar-icon"></i>
                <span>Student Management</span>
                <i class="bi bi-chevron-down dropdown-icon"></i>
            </a>

            <div class="collapse sidebar-dropdown" id="studentMenu">
                <a
                    href="{{ route('students.create') }}"
                    class="sidebar-sublink"
                >
                    <i class="bi bi-person-add sidebar-subicon"></i>
                    <span>Add Student</span>
                </a>

                <a
                    href="{{ route('students.index') }}"
                    class="sidebar-sublink"
                >
                    <i class="bi bi-people-fill sidebar-subicon"></i>
                    <span>Masterlist</span>
                </a>
            </div>
        </li>

        <li class="sidebar-item">
            <a
                class="sidebar-link"
                data-bs-toggle="collapse"
                href="#guardianMenu"
                role="button"
            >
                <i class="bi bi-house-heart-fill sidebar-icon"></i>
                <span>Parent Management</span>
                <i class="bi bi-chevron-down dropdown-icon"></i>
            </a>

            <div class="collapse sidebar-dropdown" id="guardianMenu">
                <a
                    href="{{ route('parents.create') }}"
                    class="sidebar-sublink"
                >
                    <i class="bi bi-person-add sidebar-subicon"></i>
                    <span>Add Parent</span>
                </a>

                <a
                    href="{{ route('parents.index') }}"
                    class="sidebar-sublink"
                >
                    <i class="bi bi-people-fill sidebar-subicon"></i>
                    <span>Masterlist</span>
                </a>
            </div>
        </li>

        <li class="sidebar-item">
            <a
                class="sidebar-link"
                data-bs-toggle="collapse"
                href="#teacherMenu"
                role="button"
            >
                <i class="bi bi-briefcase-fill sidebar-icon"></i>
                <span>Employee Management</span>
                <i class="bi bi-chevron-down dropdown-icon"></i>
            </a>

            <div class="collapse sidebar-dropdown" id="teacherMenu">
                <a
                    href="{{ route('employees.create') }}"
                    class="sidebar-sublink"
                >
                    <i class="bi bi-person-add sidebar-subicon"></i>
                    <span>Add Employee</span>
                </a>

                <a
                    href="{{ route('employees.index') }}"
                    class="sidebar-sublink"
                >
                    <i class="bi bi-person-badge-fill sidebar-subicon"></i>
                    <span>Masterlist</span>
                </a>
            </div>
        </li>

        <li class="sidebar-item">
            <a
                class="sidebar-link"
                data-bs-toggle="collapse"
                href="#smsMenu"
                role="button"
            >
                <i class="bi bi-chat-dots-fill sidebar-icon"></i>
                <span>SMS Management</span>
                <i class="bi bi-chevron-down dropdown-icon"></i>
            </a>

            <div class="collapse sidebar-dropdown" id="smsMenu">
                <a
                    href="{{ route('sms.index') }}"
                    class="sidebar-sublink"
                >
                    <i class="bi bi-envelope-paper-fill sidebar-subicon"></i>
                    <span>SMS Queue</span>
                </a>
            </div>
        </li>

        <li class="sidebar-item">
            <a
                class="sidebar-link"
                data-bs-toggle="collapse"
                href="#usersMenu"
                role="button"
            >
                <i class="bi bi-shield-check sidebar-icon"></i>
                <span>User Management</span>
                <i class="bi bi-chevron-down dropdown-icon"></i>
            </a>

            <div class="collapse sidebar-dropdown" id="usersMenu">
                <a
                    href="{{ route('permissions.index') }}"
                    class="sidebar-sublink"
                >
                    <i class="bi bi-key-fill sidebar-subicon"></i>
                    <span>Permissions</span>
                </a>

                <a
                    href="{{ route('roles.index') }}"
                    class="sidebar-sublink"
                >
                    <i class="bi bi-person-workspace sidebar-subicon"></i>
                    <span>Roles</span>
                </a>

                <a
                    href="{{ route('users.index') }}"
                    class="sidebar-sublink"
                >
                    <i class="bi bi-people-fill sidebar-subicon"></i>
                    <span>Masterlist</span>
                </a>
            </div>
        </li>

        <li class="sidebar-item">
            <a
                href="{{ route('settings.index') }}"
                class="sidebar-link {{ request()->routeIs('settings.index') ? 'active' : '' }}"
            >
                <i class="bi bi-sliders2-vertical sidebar-icon"></i>
                <span>Settings</span>
            </a>
        </li>

    </ul>
</div>
