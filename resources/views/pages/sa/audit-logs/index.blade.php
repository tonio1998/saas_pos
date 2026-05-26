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
                    'Description',
                    'Module',
                    'IP Address',
                    'Date'
                ]"
                :ajax="route('sa.activity-logs.data', request()->query())"
                :datatableColumns="[
                    [
                        'data' => 'actions',
                        'orderable' => false,
                        'searchable' => false,
                        'width' => '60px'
                    ],
                    [
                        'data' => 'user',
                        'orderable' => false,
                        'searchable' => false,
                        'width' => '180px'
                    ],
                    [
                        'data' => 'event',
                        'width' => '120px'
                    ],
                    [
                        'data' => 'description',
                        'orderable' => false,
                        'searchable' => false
                    ],
                    [
                        'data' => 'module',
                        'orderable' => false,
                        'searchable' => false,
                        'width' => '140px'
                    ],
                    [
                        'data' => 'ip_address',
                        'width' => '150px'
                    ],
                    [
                        'data' => 'created_at',
                        'width' => '170px'
                    ]
                ]"
            />

        </div>

    </x-card>

    <style>
        .audit-description{
            display:flex;
            flex-direction:column;
            gap:.35rem;

            max-width:720px;
        }

        .audit-event-title{
            font-size:.88rem;
            font-weight:700;
        }

        .audit-narrative{
            font-size:.83rem;
            line-height:1.75;
            color:#4b5563;
        }

        .audit-description.created .audit-event-title{
            color:#027a48;
        }

        .audit-description.updated .audit-event-title{
            color:#b54708;
        }

        .audit-description.deleted .audit-event-title{
            color:#b42318;
        }
        .audit-description{
            display:flex;
            flex-direction:column;
            gap:.45rem;

            max-width:580px;
        }

        .audit-description .title{
            font-size:.9rem;
            font-weight:700;
        }

        .audit-paragraph{
            display:flex;
            flex-direction:column;
            gap:.38rem;

            font-size:.82rem;
            line-height:1.7;

            color:#4b5563;
        }

        .audit-line strong{
            color:#111827;
            font-weight:700;
        }

        .audit-line .old{
            color:#b42318;
            font-weight:600;
        }

        .audit-line .new{
            color:#027a48;
            font-weight:600;
        }

        .audit-description.created .title{
            color:#027a48;
        }

        .audit-description.updated .title{
            color:#b54708;
        }

        .audit-description.deleted .title{
            color:#b42318;
        }
        .audit-description{
            display:flex;
            flex-direction:column;
            gap:.7rem;
            min-width:320px;
            max-width:520px;
        }

        .audit-description .title{
            font-size:.88rem;
            font-weight:700;
            color:#111827;
        }

        .audit-description .changes{
            display:flex;
            flex-direction:column;
            gap:.55rem;
        }

        .audit-change-row{
            display:flex;
            flex-direction:column;
            gap:.35rem;

            padding:.7rem .85rem;

            border-radius:14px;

            background:#fafafa;

            border:1px solid #f1f1f1;
        }

        .audit-change-row .field{
            font-size:.68rem;
            font-weight:700;
            text-transform:uppercase;
            letter-spacing:.05em;

            color:#6b7280;
        }

        .audit-change-row .values{
            display:flex;
            align-items:center;
            gap:.55rem;

            flex-wrap:wrap;
        }

        .audit-change-row .old{
            padding:.2rem .55rem;

            border-radius:999px;

            background:#fef3f2;

            color:#b42318;

            font-size:.75rem;
            font-weight:600;

            max-width:180px;

            overflow:hidden;
            text-overflow:ellipsis;
            white-space:nowrap;
        }

        .audit-change-row .new{
            padding:.2rem .55rem;

            border-radius:999px;

            background:#ecfdf3;

            color:#027a48;

            font-size:.75rem;
            font-weight:600;

            max-width:180px;

            overflow:hidden;
            text-overflow:ellipsis;
            white-space:nowrap;
        }

        .audit-change-row i{
            font-size:.72rem;
            color:#9ca3af;
        }

        .audit-description.deleted .title{
            color:#b42318;
        }

        .audit-description.created .title{
            color:#027a48;
        }

        .audit-description.updated .title{
            color:#b54708;
        }
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
