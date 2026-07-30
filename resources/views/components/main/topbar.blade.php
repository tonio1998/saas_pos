@php
    $semesterNames = [
        1 => '1st Semester',
        2 => '2nd Semester',
        3 => '3rd Semester',
        4 => '4th Semester',
        5 => 'Summer',
    ];

    $currentSemester = session('Semester', 1);
    $AYFrom          = session('AYFrom', 2026);
    $AYTo            = session('AYTo', 2027);

@endphp

<header class="lms-topbar">
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <button id="sidebarToggle" class="btn btn-light border d-lg-none me-3" type="button">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div>
                    <h5 class="fw-bold mb-0">
                        @yield('title', 'Learning Management System')
                    </h5>
                    <small class="text-muted">
                        @yield('shortText', 'Attendance Management Platform')
                    </small>
                </div>
            </div>
            @if(session('school_id') && auth()->user()->hasRole('SA'))
                <div class="dropdown">
                    <button class="btn btn-light border d-flex align-items-center gap-2 dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="bi bi-buildings"></i>
                        <span class="d-none d-md-inline">{{ session('school_name') }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                        <li class="px-3 py-2">
                            <div class="fw-semibold">{{ session('school_name') }}</div>
                            <div class="small text-muted">Active School Context</div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('sa.schools.close-context') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger"><i class="bi bi-x-circle me-2"></i>Close School</button>
                            </form>
                        </li>
                    </ul>
                </div>
            @endif

            <div class="d-flex align-items-center">
                @if(session('school_id'))
                    <button class="btn app-context-btn me-2" data-bs-toggle="modal" data-bs-target="#academicContextModal">
                        <div class="context-icon">
                            <i class="bi bi-mortarboard-fill"></i>
                        </div>
                        <div class="context-body">
                            <span class="context-title">{{ academic()::semesterName() }}</span>
                            <span class="context-subtitle">{{ academic()::schoolYear() }}</span>
                        </div>
                        <i class="bi bi-chevron-expand context-arrow"></i>
                    </button>
                @endif
                <button class="btn btn-light border position-relative me-2" title="Notifications">
                    <i class="bi bi-bell"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">3</span>
                </button>
                <button class="btn btn-light border me-3" title="Attendance Today"><i class="bi bi-calendar2-check"></i></button>

                <div class="dropdown">
                    <button class="btn border bg-white dropdown-toggle d-flex align-items-center"
                        data-bs-toggle="dropdown"
                    >
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width:38px;height:38px;">
                            {{ strtoupper(substr(auth()->user()->name ?? 'U',0,1)) }}
                        </div>
                        <div class="text-start d-none d-md-block">
                            <div class="fw-semibold lh-1">
                                {{ auth()->user()->name ?? 'Administrator' }}
                            </div>
                            <small class="text-muted">
                                {{ auth()->user()->getRoleNames()->implode(', ') }}
                            </small>
                        </div>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                        <li>
                            <h6 class="dropdown-header">
                                Account
                            </h6>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-person-circle me-2"></i>
                                My Profile
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-key me-2"></i>
                                Change Password
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form
                                method="POST"
                                action="{{ route('logout') }}"
                            >
                                @csrf
                                <button
                                    class="dropdown-item text-danger"
                                    type="submit"
                                >
                                    <i class="bi bi-box-arrow-right me-2"></i>
                                    Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>

<div class="modal fade" id="academicContextModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h4 class="fw-bold mb-1">Academic Context</h4>
                    <p class="text-muted mb-0">Select the academic term to use throughout the system.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('context.update') }}">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Semester</label>
                            <select class="form-select" name="Semester" id="Semester">
                                <option value="1" {{ $currentSemester == 1 ? 'selected' : '' }}>1st Semester</option>
                                <option value="2" {{ $currentSemester == 2 ? 'selected' : '' }}>2nd Semester</option>
                                <option value="3" {{ $currentSemester == 3 ? 'selected' : '' }}>3rd Semester</option>
                                <option value="4" {{ $currentSemester == 4 ? 'selected' : '' }}>4th Semester</option>
                                <option value="5" {{ $currentSemester == 5 ? 'selected' : '' }}>Summer</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Academic Year</label>
                            <select class="form-select" name="AYFrom" id="AYFrom">
                                @for($year = now()->year + 1; $year >= 2020; $year--)
                                    <option value="{{ $year }}" {{ $AYFrom == $year ? 'selected' : '' }}>
                                        {{ $year }} - {{ $year + 1 }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary px-4" type="submit">
                        <i class="bi bi-check-circle me-2"></i>
                        Apply Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
