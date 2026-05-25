@extends('layouts.sa')

@section('title', 'Activity Logs')

@section('content')

    <x-page-header
        title="Activity Logs"
        subtitle="Monitor platform activities and audit events"
    >
    </x-page-header>

    <x-card class="audit-filter-card">

        <form
            method="GET"
            class="audit-filters"
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
                    Event
                </label>

                <div class="filter-input">

                    <i class="bi bi-activity"></i>

                    <select
                        name="event"
                        class="form-select"
                    >
                        <option value="">
                            All Events
                        </option>

                        <option
                            value="created"
                            @selected(request('event') == 'created')
                        >
                            Created
                        </option>

                        <option
                            value="updated"
                            @selected(request('event') == 'updated')
                        >
                            Updated
                        </option>

                        <option
                            value="deleted"
                            @selected(request('event') == 'deleted')
                        >
                            Deleted
                        </option>

                    </select>

                </div>

            </div>

            <div class="filter-group">

                <label>
                    From
                </label>

                <div class="filter-input">

                    <i class="bi bi-calendar-event"></i>

                    <input
                        type="date"
                        name="from"
                        value="{{ request('from') }}"
                        class="form-control"
                    >

                </div>

            </div>

            <div class="filter-group">

                <label>
                    To
                </label>

                <div class="filter-input">

                    <i class="bi bi-calendar-check"></i>

                    <input
                        type="date"
                        name="to"
                        value="{{ request('to') }}"
                        class="form-control"
                    >

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

    </x-card>

    <x-card class="audit-table-card">

        <div class="audit-table-wrapper">

            <x-datatable
                id="auditLogsTable"
                :columns="[
                'Actions',
                'User',
                'Event',
                'Module',
                'IP Address',
                'Date'
            ]"
                :ajax="route('sa.activity-logs.data', request()->query())"
                :datatableColumns="[
                [
                    'data' => 'actions',
                    'orderable' => false,
                    'searchable' => false
                ],
                [
                    'data' => 'user',
                    'orderable' => false,
                    'searchable' => false
                ],
                [
                    'data' => 'event'
                ],
                [
                    'data' => 'module',
                    'orderable' => false,
                    'searchable' => false
                ],
                [
                    'data' => 'ip_address'
                ],
                [
                    'data' => 'created_at'
                ]
            ]"
            />

        </div>

    </x-card>

    <style>

        .audit-filter-card,
        .audit-table-card{
            border:none !important;
            overflow:hidden;
            border-radius:28px !important;
            box-shadow:
                0 1px 2px rgba(15,23,42,.03),
                0 12px 32px rgba(15,23,42,.04);
        }

        .audit-filters{
            display:grid;
            grid-template-columns:
        repeat(auto-fit, minmax(220px, 1fr));
            gap:1rem;
            padding:1.4rem;
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

        .filter-input .form-select,
        .filter-input .form-control{
            height:48px;
            border-radius:16px;
            border:1px solid #ececec;
            background:#fff;
            padding-left:42px;
            font-size:.88rem;
            box-shadow:none !important;
        }

        .filter-input .form-select:focus,
        .filter-input .form-control:focus{
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
            background: var(--theme-bg);
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

        .audit-table-header{
            padding:1.5rem 1.5rem 1rem;
            border-bottom:1px solid #f5f5f5;
        }

        .audit-table-header h6{
            margin:0;
            font-size:1rem;
            font-weight:700;
            color:#111827;
        }

        .audit-table-header p{
            margin:.35rem 0 0;
            font-size:.84rem;
            color:#6b7280;
        }

        .audit-table-wrapper{
            padding:0 1rem 1rem;
        }

        .dataTables_wrapper{
            padding-top:1rem;
        }

        .dataTables_filter{
            margin-bottom:1rem;
        }

        .audit-user{
            font-weight:600;
            color:#111827;
        }

        .audit-event{
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

        .audit-event.success{
            background:#ecfdf3;
            color:#027a48;
        }

        .audit-event.warning{
            background:#fffaeb;
            color:#b54708;
        }

        .audit-event.danger{
            background:#fef3f2;
            color:#b42318;
        }

        .audit-event.secondary{
            background:#f3f4f6;
            color:#374151;
        }

        .audit-module{
            font-weight:600;
            color:#374151;
        }

        .audit-ip{
            font-family:monospace;
            font-size:.82rem;
            color:#6b7280;
        }

        .audit-date{
            font-size:.82rem;
            color:#6b7280;
            white-space:nowrap;
        }

        .audit-action-btn{
            width:36px;
            height:36px;
            border:none;
            border-radius:12px;
            background:#f5f5f5;
            color:#111827;
            display:flex;
            align-items:center;
            justify-content:center;
            transition:.2s ease;
        }

        .audit-action-btn:hover{
            background:#111827;
            color:#fff;
        }
        @media(max-width:768px){

            .audit-filters{
                grid-template-columns:1fr;
            }

        }

    </style>

@endsection
