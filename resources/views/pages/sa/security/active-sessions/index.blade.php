@extends('layouts.sa')

@section('title', 'Active Sessions')

@section('content')

    <x-page-header
        title="Active Sessions"
        subtitle="Monitor currently active user sessions across the platform"
    >
    </x-page-header>

    <x-card class="session-table-card">

        <div class="session-table-header">

            <div>

                <h6>
                    Online Sessions
                </h6>

                <p>
                    Track logged-in devices and revoke suspicious sessions
                </p>

            </div>

        </div>

        <div class="session-table-wrapper">

            <x-datatable
                id="activeSessionsTable"
                :columns="[
                'Status',
                'User',
                'Device',
                'IP Address',
                'Login Time',
                'Actions'
            ]"
                :ajax="route('sa.security.active-sessions.data')"
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
                    'data' => 'device',
                    'orderable' => false,
                    'searchable' => false
                ],
                [
                    'data' => 'ip_address'
                ],
                [
                    'data' => 'logged_in_at'
                ],
                [
                    'data' => 'actions',
                    'orderable' => false,
                    'searchable' => false
                ]
            ]"
            />

        </div>

    </x-card>

    <style>

        .session-table-card{
            border:none !important;
            overflow:hidden;
            border-radius:28px !important;
            box-shadow:
                0 1px 2px rgba(15,23,42,.03),
                0 12px 32px rgba(15,23,42,.04);
        }

        .session-table-header{
            padding:1.5rem 1.5rem 1rem;
            border-bottom:1px solid #f5f5f5;
        }

        .session-table-header h6{
            margin:0;
            font-size:1rem;
            font-weight:700;
            color:#111827;
        }

        .session-table-header p{
            margin:.35rem 0 0;
            font-size:.84rem;
            color:#6b7280;
        }

        .session-table-wrapper{
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

        .session-status{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            height:30px;
            padding:0 .8rem;
            border-radius:999px;
            background:#ecfdf3;
            color:#027a48;
            font-size:.72rem;
            font-weight:700;
            letter-spacing:.03em;
        }

        .session-user{
            font-weight:600;
            color:#111827;
        }

        .session-device{
            display:flex;
            flex-direction:column;
            gap:.15rem;
        }

        .session-device div{
            font-weight:600;
            color:#111827;
        }

        .session-device small{
            font-size:.76rem;
            color:#6b7280;
        }

        .session-ip{
            font-family:monospace;
            font-size:.82rem;
            color:#6b7280;
        }

        .session-date{
            font-size:.82rem;
            color:#6b7280;
            white-space:nowrap;
        }

        .btn-revoke-session{
            width:36px;
            height:36px;
            border:none;
            border-radius:12px;
            background:#fef3f2;
            color:#b42318;
            display:flex;
            align-items:center;
            justify-content:center;
            transition:.2s ease;
        }

        .btn-revoke-session:hover{
            background:#b42318;
            color:#fff;
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

    </style>

@endsection
