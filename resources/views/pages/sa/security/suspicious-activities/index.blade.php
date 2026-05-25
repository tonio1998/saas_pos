@extends('layouts.sa')

@section('title', 'Suspicious Activities')

@section('content')

    <x-page-header
        title="Suspicious Activities"
        subtitle="Monitor security threats, suspicious logins, and abnormal activities"
    >
    </x-page-header>

    <x-card class="security-alert-card">

        <div class="security-alert-header">

            <div>

                <h6>
                    Threat Monitoring Center
                </h6>

                <p>
                    Real-time detection of suspicious authentication and access patterns
                </p>

            </div>

        </div>

        <div class="security-alert-table">

            <x-datatable
                id="suspiciousActivitiesTable"
                :columns="[
                'Severity',
                'User',
                'Type',
                'Description',
                'IP Address',
                'Detected'
            ]"
                :ajax="route('sa.security.suspicious-activities.data')"
                :datatableColumns="[
                [
                    'data' => 'severity',
                    'orderable' => false,
                    'searchable' => false
                ],
                [
                    'data' => 'user_name',
                    'orderable' => false,
                    'searchable' => false
                ],
                [
                    'data' => 'type',
                    'orderable' => false,
                    'searchable' => false
                ],
                [
                    'data' => 'description',
                    'orderable' => false,
                    'searchable' => false
                ],
                [
                    'data' => 'ip_address'
                ],
                [
                    'data' => 'detected_at'
                ]
            ]"
            />

        </div>

    </x-card>

    <style>

        .security-alert-card{
            border:none !important;
            overflow:hidden;
            border-radius:28px !important;
            box-shadow:
                0 1px 2px rgba(15,23,42,.03),
                0 12px 32px rgba(15,23,42,.04);
        }

        .security-alert-header{
            padding:1.5rem 1.5rem 1rem;
            border-bottom:1px solid #f5f5f5;
        }

        .security-alert-header h6{
            margin:0;
            font-size:1rem;
            font-weight:700;
            color:#111827;
        }

        .security-alert-header p{
            margin:.35rem 0 0;
            font-size:.84rem;
            color:#6b7280;
        }

        .security-alert-table{
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

        .security-severity{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            height:30px;
            padding:0 .85rem;
            border-radius:999px;
            font-size:.72rem;
            font-weight:700;
            letter-spacing:.04em;
        }

        .security-severity.info{
            background:#eff8ff;
            color:#175cd3;
        }

        .security-severity.warning{
            background:#fffaeb;
            color:#b54708;
        }

        .security-severity.critical{
            background:#fef3f2;
            color:#b42318;
        }

        .security-user{
            font-weight:600;
            color:#111827;
        }

        .security-type{
            font-weight:600;
            color:#374151;
        }

        .security-description{
            max-width:420px;
            color:#6b7280;
            line-height:1.5;
        }

        .security-ip{
            font-family:monospace;
            font-size:.82rem;
            color:#6b7280;
        }

        .security-date{
            font-size:.82rem;
            color:#6b7280;
            white-space:nowrap;
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

            .security-description{
                max-width:100%;
            }

        }

    </style>

@endsection
