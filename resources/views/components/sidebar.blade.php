<div class="sidebar">
    <ul class="sidebar-menu">
        <li class="sidebar-item">
            <a
                href="{{ route('scanner.index') }}"
                class="sidebar-link {{ request()->routeIs('scanner.index') ? 'active' : '' }}"
            >
                <i class="bi bi-qr-code-scan sidebar-icon"></i>
                <span>Scanner</span>
            </a>
        </li>

        <li class="sidebar-item">
            <a
                href="{{ route('dashboard') }}"
                class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
            >
                <i class="bi bi-speedometer2 sidebar-icon"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li class="sidebar-item">
            <a class="sidebar-link" data-bs-toggle="collapse" href="#systemMenu">
                <i class="bi bi-activity sidebar-icon"></i>
                <span>Logs Monitoring</span>
                <i class="bi bi-chevron-down dropdown-icon"></i>
            </a>
            <div class="collapse sidebar-dropdown" id="systemMenu">
                <a href="{{ route('logs.index') }}" class="sidebar-sublink">
                    <i class="bi bi-clock-history sidebar-subicon"></i>
                    All Logs
                </a>
            </div>
        </li>

        <li class="sidebar-item">
            <a class="sidebar-link" data-bs-toggle="collapse" href="#studentMenu">
                <i class="bi bi-mortarboard sidebar-icon"></i>
                <span>Student Management</span>
                <i class="bi bi-chevron-down dropdown-icon"></i>
            </a>
            <div class="collapse sidebar-dropdown" id="studentMenu">
                <a href="{{ route('students.create') }}" class="sidebar-sublink">
                    <i class="bi bi-person-plus sidebar-subicon"></i>
                    Add Student
                </a>
                <a href="{{ route('students.index') }}" class="sidebar-sublink">
                    <i class="bi bi-people sidebar-subicon"></i>
                    Masterlist
                </a>
            </div>
        </li>

        <li class="sidebar-item">
            <a class="sidebar-link" data-bs-toggle="collapse" href="#guardianMenu">
                <i class="bi bi-people-fill sidebar-icon"></i>
                <span>Parent Management</span>
                <i class="bi bi-chevron-down dropdown-icon"></i>
            </a>
            <div class="collapse sidebar-dropdown" id="guardianMenu">
                <a href="{{ route('parents.create') }}" class="sidebar-sublink">
                    <i class="bi bi-person-plus sidebar-subicon"></i>
                    Add Parent
                </a>
                <a href="{{ route('parents.index') }}" class="sidebar-sublink">
                    <i class="bi bi-person-lines-fill sidebar-subicon"></i>
                    Masterlist
                </a>
            </div>
        </li>

        <li class="sidebar-item">
            <a class="sidebar-link" data-bs-toggle="collapse" href="#teacherMenu">
                <i class="bi bi-person-badge sidebar-icon"></i>
                <span>Employee Management</span>
                <i class="bi bi-chevron-down dropdown-icon"></i>
            </a>
            <div class="collapse sidebar-dropdown" id="teacherMenu">
                <a href="{{ route('employees.create') }}" class="sidebar-sublink">
                    <i class="bi bi-person-plus sidebar-subicon"></i>
                    Add Employee
                </a>
                <a href="{{ route('employees.index') }}" class="sidebar-sublink">
                    <i class="bi bi-person-vcard sidebar-subicon"></i>
                    Masterlist
                </a>
            </div>
        </li>

        <li class="sidebar-item">
            <a class="sidebar-link" data-bs-toggle="collapse" href="#usersMenu">
                <i class="bi bi-shield-lock sidebar-icon"></i>
                <span>User Management</span>
                <i class="bi bi-chevron-down dropdown-icon"></i>
            </a>
            <div class="collapse sidebar-dropdown" id="usersMenu">
                <a href="{{ route('permissions.index') }}" class="sidebar-sublink">
                    <i class="bi bi-key sidebar-subicon"></i>
                    Permissions
                </a>
                <a href="{{ route('roles.index') }}" class="sidebar-sublink">
                    <i class="bi bi-person-gear sidebar-subicon"></i>
                    Roles
                </a>
                <a href="{{ route('users.index') }}" class="sidebar-sublink">
                    <i class="bi bi-people sidebar-subicon"></i>
                    Masterlist
                </a>
            </div>
        </li>

        <li class="sidebar-item">
            <a class="sidebar-link" data-bs-toggle="collapse" href="#schoolYearMenu">
                <i class="bi bi-calendar-week sidebar-icon"></i>
                <span>School Year Management</span>
                <i class="bi bi-chevron-down dropdown-icon"></i>
            </a>
            <div class="collapse sidebar-dropdown" id="schoolYearMenu">
                <a href="{{ route('school_years.index') }}" class="sidebar-sublink">
                    <i class="bi bi-calendar-week sidebar-subicon"></i>
                    Masterlist
                </a>
            </div>
        </li>

        <li class="sidebar-item">
            <a class="sidebar-link" data-bs-toggle="collapse" href="#SemesterMenu">
                <i class="bi bi-calendar-week sidebar-icon"></i>
                <span>Semester Management</span>
                <i class="bi bi-chevron-down dropdown-icon"></i>
            </a>
            <div class="collapse sidebar-dropdown" id="SemesterMenu">
                <a href="{{ route('semesters.index') }}" class="sidebar-sublink">
                    <i class="bi bi-calendar-week sidebar-subicon"></i>
                    Masterlist
                </a>
            </div>
        </li>

        <li class="sidebar-item">
            <a class="sidebar-link" data-bs-toggle="collapse" href="#GradeLevelMenu">
                <i class="bi bi-calendar-week sidebar-icon"></i>
                <span>Grade Level Management</span>
                <i class="bi bi-chevron-down dropdown-icon"></i>
            </a>
            <div class="collapse sidebar-dropdown" id="GradeLevelMenu">
                <a href="{{ route('grade-levels.index') }}" class="sidebar-sublink">
                    <i class="bi bi-calendar-week sidebar-subicon"></i>
                    Masterlist
                </a>
            </div>
        </li>

        <li class="sidebar-item">
            <a class="sidebar-link d-flex align-items-center justify-content-between">
                <span class="d-flex align-items-center gap-2">
                    <i class="bi bi-bar-chart sidebar-icon"></i>
                    <span>Reports</span>
                </span>
                <span class="badge bg-warning text-dark">Soon</span>
            </a>
        </li>

        <li class="sidebar-item">
            <a class="sidebar-link d-flex align-items-center justify-content-between" href="{{ route('settings.index') }}">
                <span class="d-flex align-items-center gap-2">
                    <i class="bi bi-gear sidebar-icon"></i>
                    <span>Settings</span>
                </span>
                <span class="badge bg-warning text-dark">Soon</span>
            </a>
        </li>

    </ul>
</div>
