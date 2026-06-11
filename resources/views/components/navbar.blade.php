@php

    $currentYear = now()->year;

    $semesterNames = [
        1 => '1st Semester',
        2 => '2nd Semester',
        3 => 'Summer',
    ];

    $currentSemester =
        session('Semester',1);

@endphp

<nav class="navbar navbar-expand-lg app-navbar sticky-top border-b-2">

    <div class="container-fluid d-flex align-items-center justify-content-between">

        <div class="d-flex align-items-center gap-2">

            <button
                id="toggleSidebar"
                class="btn nav-icon d-lg-none"
            >
                <i class="bi bi-list"></i>
            </button>

            <a
                class="navbar-brand d-flex align-items-center gap-2 gap-md-3 m-0"
            >

                <div class="brand-logo">

                    <img
                        src="{{
                            $tenantSettings?->logo
                                ? asset('storage/' . $tenantSettings->logo)
                                : asset('images/logo.png')
                        }}"
                        class="logo-img"
                    >

                </div>

                <div class="brand-info d-none d-sm-flex">

                    <div class="system-name">
                        <?= $tenantSettings?->business_name ?? 'SAFETRACK: A QR & NFC-Based Student Monitoring and Alert System' ?>
                    </div>

                    <div class="school-name">
                        {{ $tenantSettings?->owner_name ?? 'SURIGAO DEL NORTE STATE UNIVERSITY' }}
                    </div>

                </div>

            </a>

        </div>

        <div class="d-flex align-items-center gap-2 gap-md-3">

            @if(session('tenant_id') && auth()->user()->hasRole('SA'))

                <div class="dropdown">

                    <button
                        class="btn btn-light border d-flex align-items-center gap-2 dropdown-toggle"
                        data-bs-toggle="dropdown"
                    >

                        <i class="bi bi-buildings"></i>

                        <span class="d-none d-md-inline">
                            {{ session('tenant_name') }}
                        </span>

                    </button>

                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">

                        <li class="px-3 py-2">

                            <div class="fw-semibold">
                                {{ session('tenant_name') }}
                            </div>

                            <div class="small text-muted">
                                Active Tenant Context
                            </div>

                        </li>

                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>

                            <form
                                method="POST"
                                action="{{ route('sa.tenants.close-context') }}"
                            >

                                @csrf

                                <button
                                    type="submit"
                                    class="dropdown-item text-danger"
                                >
                                    <i class="bi bi-x-circle me-2"></i>
                                    Close School
                                </button>

                            </form>

                        </li>

                    </ul>

                </div>

            @endif

            <button class="nav-icon position-relative">
                <i class="bi bi-bell"></i>
            </button>

            <div class="dropdown">

                <button
                    class="nav-user dropdown-toggle"
                    data-bs-toggle="dropdown"
                >

                    <span class="user-avatar">

                        {{
                            strtoupper(
                                substr(
                                    auth()->user()->name ?? 'U',
                                    0,
                                    1
                                )
                            )
                        }}

                    </span>

                    <span class="user-name d-none d-md-inline">
                        {{ auth()->user()->name ?? 'User' }}
                    </span>

                </button>

                <ul class="dropdown-menu dropdown-menu-end app-dropdown">

                    <li>
                        <a class="dropdown-item" href="#">
                            Profile
                        </a>
                    </li>

                    <li>
                        <a class="dropdown-item" href="#">
                            Settings
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
                                type="submit"
                                class="dropdown-item text-danger"
                            >
                                Logout
                            </button>

                        </form>

                    </li>

                </ul>

            </div>

        </div>

    </div>

</nav>
