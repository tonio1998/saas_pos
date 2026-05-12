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

<nav class="navbar navbar-expand-lg bg-white px-3 px-lg-4 sticky-top shadow-sm">

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
                            $schoolSettings?->Logo
                                ? asset('storage/' . $schoolSettings->Logo)
                                : asset('images/logo.png')
                        }}"
                        class="logo-img"
                    >

                </div>

                <div class="brand-info d-none d-sm-flex">

                    <div class="system-name">
                        {{ config('app.name') }}
                    </div>

                    <div class="school-name">

                        {{
                            $schoolSettings?->SchoolName
                                ?? 'School Name'
                        }}

                    </div>

                </div>

            </a>

        </div>

        <div class="d-flex align-items-center gap-2 gap-md-3">

            <button
                type="button"
                class="btn btn-light border rounded-pill px-3 d-flex align-items-center gap-2"
                data-bs-toggle="modal"
                data-bs-target="#academicContextModal"
            >

                <i class="bi bi-calendar3"></i>

                <span>

        {{
            $semesterNames[
                $currentSemester
            ] ?? '1st Semester'
        }}

    </span>

                <span class="text-muted">
        •
    </span>

                <span class="fw-semibold">

        SY

        {{
            session(
                'AYFrom',
                $currentYear
            )
        }}

        -

        {{
            session(
                'AYTo',
                $currentYear + 1
            )
        }}

    </span>

            </button>

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

                        {{
                            auth()->user()->name ?? 'User'
                        }}

                    </span>

                </button>

                <ul
                    class="dropdown-menu dropdown-menu-end shadow-sm border-0"
                >

                    <li>

                        <a
                            class="dropdown-item"
                            href="#"
                        >
                            Profile
                        </a>

                    </li>

                    <li>

                        <a
                            class="dropdown-item"
                            href="#"
                        >
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

<div
    class="modal fade"
    id="academicContextModal"
    tabindex="-1"
>

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content border-0 shadow">

            <form
                method="POST"
                action="{{ route('academic-context.store') }}"
            >

                @csrf

                <div class="modal-header border-0">

                    <div>

                        <h5 class="modal-title fw-semibold">
                            Academic Context
                        </h5>

                        <div class="text-muted small">
                            Switch academic year and semester
                        </div>

                    </div>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                    ></button>

                </div>

                <div class="modal-body">

                    <div class="row g-3">

                        <div class="col-12">

                            <label class="form-label">
                                Semester
                            </label>

                            <select
                                name="Semester"
                                class="form-select"
                                required
                            >

                                @foreach($semesterNames as $key => $value)

                                    <option
                                        value="{{ $key }}"

                                        {{
                                            session(
                                                'Semester',
                                                1
                                            ) == $key
                                                ? 'selected'
                                                : ''
                                        }}
                                    >
                                        {{ $value }}
                                    </option>

                                @endforeach

                            </select>

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">
                                AY From
                            </label>

                            <select
                                name="AYFrom"
                                id="AYFrom"
                                class="form-select"
                                required
                            >

                                @for($i = 0; $i < 5; $i++)

                                    @php
                                        $year = $currentYear - $i;
                                    @endphp

                                    <option
                                        value="{{ $year }}"

                                        {{
                                            session(
                                                'AYFrom',
                                                $currentYear
                                            ) == $year
                                                ? 'selected'
                                                : ''
                                        }}
                                    >
                                        {{ $year }}
                                    </option>

                                @endfor

                            </select>

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">
                                AY To
                            </label>

                            <input
                                type="text"
                                name="AYTo"
                                id="AYTo"
                                class="form-control bg-light"
                                readonly

                                value="{{
                                    session(
                                        'AYTo',
                                        $currentYear + 1
                                    )
                                }}"
                            >

                        </div>

                    </div>

                </div>

                <div class="modal-footer border-0">

                    <button
                        type="button"
                        class="btn btn-light"
                        data-bs-dismiss="modal"
                    >
                        Cancel
                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        <i class="bi bi-check"></i>
                        Apply Context
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<script>

    document.addEventListener(
        'DOMContentLoaded',
        function(){

            const ayFrom =
                document.getElementById(
                    'AYFrom'
                );

            const ayTo =
                document.getElementById(
                    'AYTo'
                );

            function updateAYTo(){

                ayTo.value =
                    parseInt(
                        ayFrom.value
                    ) + 1;

            }

            updateAYTo();

            ayFrom.addEventListener(
                'change',
                updateAYTo
            );

        }
    );

</script>
