@extends('layouts.sa')

@section('title', 'Login Activities')

@section('content')

    <x-page-header
        title="Login Activities"
        subtitle="Monitor successful logins, failed attempts, and user session history"
    >
    </x-page-header>

    <x-card class="login-activity-card">

        <div class="login-activity-toolbar">

            <form
                method="GET"
                class="login-activity-filters"
            >

                <div class="filter-group">

                    <label>
                        User
                    </label>

                    <div class="filter-input">

                        <i class="bi bi-person"></i>

                        <select
                            name="user_id"
                            class="form-select"
                        >
                            <option value="">
                                All Users
                            </option>

                            @foreach($users as $user)

                                <option
                                    value="{{ $user->id }}"
                                    @selected(
                                        request('user_id') == $user->id
                                    )
                                >
                                    {{ $user->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>

                </div>

                <div class="filter-group">

                    <label>
                        Status
                    </label>

                    <div class="filter-input">

                        <i class="bi bi-shield-check"></i>

                        <select
                            name="status"
                            class="form-select"
                        >
                            <option value="">
                                All Status
                            </option>

                            <option
                                value="success"
                                @selected(
                                    request('status') == 'success'
                                )
                            >
                                Success
                            </option>

                            <option
                                value="failed"
                                @selected(
                                    request('status') == 'failed'
                                )
                            >
                                Failed
                            </option>

                            <option
                                value="logout"
                                @selected(
                                    request('status') == 'logout'
                                )
                            >
                                Logout
                            </option>

                        </select>

                    </div>

                </div>

                <div class="filter-action">

                    <button
                        type="submit"
                        class="btn-filter"
                    >

                        <i class="bi bi-funnel-fill"></i>

                        <span>
                        Apply Filters
                    </span>

                    </button>

                </div>

            </form>

        </div>

    </x-card>

    <x-card class="login-table-card">

        <div class="login-table-header">

            <div>

                <h6>
                    Security Login Logs
                </h6>

                <p>
                    Real-time authentication activities and access history
                </p>

            </div>

        </div>

        <div class="login-table-wrapper">

            <x-datatable
                id="loginActivitiesTable"
                :columns="[
                'Status',
                'User',
                'IP Address',
                'Device',
                'Login Time',
                'Logout Time'
            ]"
                :ajax="route(
                'sa.security.login-activities.data',
                request()->query()
            )"
                :datatableColumns="[
                [
                    'data' => 'status',
                    'orderable' => false,
                    'searchable' => false
                ],
                [
                    'data' => 'user_name',
                    'orderable' => false,
                    'searchable' => false
                ],
                [
                    'data' => 'ip_address'
                ],
                [
                    'data' => 'device_info',
                    'orderable' => false,
                    'searchable' => false
                ],
                [
                    'data' => 'logged_in_at'
                ],
                [
                    'data' => 'logged_out_at'
                ]
            ]"
            />

        </div>

    </x-card>

    <style>

        .login-activity-card,
        .login-table-card{
            border:none !important;
            overflow:hidden;
            border-radius:28px !important;
            box-shadow:
                0 1px 2px rgba(15,23,42,.03),
                0 12px 32px rgba(15,23,42,.04);
        }

        .login-activity-toolbar{
            padding:1.4rem;
        }

        .login-activity-filters{
            display:grid;
            grid-template-columns:
        repeat(auto-fit, minmax(220px, 1fr));
            gap:1rem;
        }

        .filter-group{
            display:flex;
            flex-direction:column;
            gap:.55rem;
        }

        .filter-group label{
            font-size:.76rem;
            font-weight:700;
            text-transform:uppercase;
            letter-spacing:.04em;
            color:#6b7280;
        }

        .filter-input{
            position:relative;
        }

        .filter-input i{
            position:absolute;
            left:14px;
            top:50%;
            transform:translateY(-50%);
            font-size:.85rem;
            color:#9ca3af;
            z-index:2;
        }

        .filter-input .form-select{
            height:48px;
            border-radius:16px;
            border:1px solid #ececec;
            background:#fff;
            padding-left:42px;
            font-size:.88rem;
            box-shadow:none !important;
        }

        .filter-input .form-select:focus{
            border-color:#111827;
        }

        .filter-action{
            display:flex;
            align-items:end;
        }

        .btn-filter{
            width:100%;
            height:48px;
            border:none;
            border-radius:16px;
            background:#111827;
            color:#fff;
            font-size:.88rem;
            font-weight:600;
            display:flex;
            align-items:center;
            justify-content:center;
            gap:.55rem;
            transition:.2s ease;
        }

        .btn-filter:hover{
            transform:translateY(-1px);
            opacity:.96;
        }

        .login-table-header{
            padding:1.5rem 1.5rem 1rem;
            border-bottom:1px solid #f5f5f5;
        }

        .login-table-header h6{
            margin:0;
            font-size:1rem;
            font-weight:700;
            color:#111827;
        }

        .login-table-header p{
            margin:.35rem 0 0;
            font-size:.84rem;
            color:#6b7280;
        }

        .login-table-wrapper{
            padding:0 1rem 1rem;
        }

        .dataTables_wrapper{
            padding-top:1rem;
        }

        .dataTables_filter{
            margin-bottom:1rem;
        }

        .dataTables_filter input{
            height:42px !important;
            border-radius:14px !important;
            border:1px solid #ececec !important;
            padding:0 .9rem !important;
            box-shadow:none !important;
        }

        .dataTables_length select{
            height:42px !important;
            border-radius:14px !important;
            border:1px solid #ececec !important;
            box-shadow:none !important;
        }

        table.dataTable{
            border-collapse:separate !important;
            border-spacing:0;
            width:100% !important;
        }

        table.dataTable thead th{
            background:#fafafa !important;
            border-bottom:1px solid #ececec !important;
            padding:1rem !important;
            font-size:.74rem !important;
            font-weight:700 !important;
            text-transform:uppercase;
            letter-spacing:.04em;
            color:#6b7280 !important;
        }

        table.dataTable tbody td{
            padding:1rem !important;
            vertical-align:middle;
            border-bottom:1px solid #f5f5f5 !important;
            font-size:.88rem;
            color:#111827;
        }

        table.dataTable tbody tr{
            transition:.2s ease;
        }

        table.dataTable tbody tr:hover{
            background:#fafafa;
        }

        .login-status{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            height:30px;
            padding:0 .8rem;
            border-radius:999px;
            font-size:.72rem;
            font-weight:700;
            letter-spacing:.03em;
        }

        .login-status.success{
            background:#ecfdf3;
            color:#027a48;
        }

        .login-status.danger{
            background:#fef3f2;
            color:#b42318;
        }

        .login-status.secondary{
            background:#f3f4f6;
            color:#374151;
        }

        .login-user{
            font-weight:600;
            color:#111827;
        }

        .login-ip{
            font-family:monospace;
            font-size:.82rem;
            color:#6b7280;
        }

        .device-info{
            display:flex;
            flex-direction:column;
            gap:.15rem;
        }

        .device-browser{
            font-weight:600;
            color:#111827;
        }

        .device-platform{
            font-size:.76rem;
            color:#6b7280;
        }

        .login-date{
            font-size:.82rem;
            color:#6b7280;
            white-space:nowrap;
        }

        .session-active{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            height:28px;
            padding:0 .7rem;
            border-radius:999px;
            background:#ecfdf3;
            color:#027a48;
            font-size:.72rem;
            font-weight:700;
        }

        .dataTables_paginate .paginate_button{
            border:none !important;
            background:transparent !important;
        }

        .dataTables_paginate .paginate_button.current{
            background:#111827 !important;
            color:#fff !important;
            border-radius:10px !important;
        }

        @media(max-width:768px){

            .login-activity-filters{
                grid-template-columns:1fr;
            }

        }

    </style>

@endsection
