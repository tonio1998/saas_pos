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
            @if(session('tenant_id') && auth()->user()->hasRole('SA'))
                <div class="dropdown">
                    <button class="btn btn-light border d-flex align-items-center gap-2 dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="bi bi-buildings"></i>
                        <span class="d-none d-md-inline">{{ session('tenant_name') }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                        <li class="px-3 py-2">
                            <div class="fw-semibold">{{ session('tenant_name') }}</div>
                            <div class="small text-muted">Active Context</div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('sa.tenants.close-context') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger"><i class="bi bi-x-circle me-2"></i>Close School</button>
                            </form>
                        </li>
                    </ul>
                </div>
            @endif

            <div class="d-flex align-items-center">
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
