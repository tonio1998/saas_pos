@extends('layouts.app')

@section('title','Gate Monitoring Reports')

@section('content')

    <div class="container-fluid px-2 px-lg-3">

        <x-page-header
            title="Gate Monitoring Reports"
            subtitle="Campus NFC entry and exit analytics"
        >

            <x-slot:action>

                <div class="top-toolbar">

                    <button class="btn btn-toolbar btn-excel">
                        <i class="bi bi-file-earmark-excel"></i>
                        <span>Excel</span>
                    </button>

                    <button class="btn btn-toolbar btn-pdf">
                        <i class="bi bi-file-earmark-pdf"></i>
                        <span>PDF</span>
                    </button>

                    <button class="btn btn-toolbar btn-print">
                        <i class="bi bi-printer"></i>
                        <span>Print</span>
                    </button>

                </div>

            </x-slot:action>

        </x-page-header>

        <div class="row g-3 mb-4">

            <div class="col-xl-3 col-md-6">

                <div class="modern-stat-card stat-primary">

                    <div class="stat-glow"></div>

                    <div class="d-flex justify-content-between align-items-start">

                        <div>

                            <div class="stat-label">
                                Entries Today
                            </div>

                            <div class="stat-number">
                                {{ number_format($totalEntriesToday) }}
                            </div>

                            <div class="stat-desc">
                                Campus entry transactions
                            </div>

                        </div>

                        <div class="stat-icon">
                            <i class="bi bi-box-arrow-in-right"></i>
                        </div>

                    </div>

                </div>

            </div>

            <div class="col-xl-3 col-md-6">

                <div class="modern-stat-card stat-success">

                    <div class="stat-glow"></div>

                    <div class="d-flex justify-content-between align-items-start">

                        <div>

                            <div class="stat-label">
                                Exits Today
                            </div>

                            <div class="stat-number">
                                {{ number_format($totalExitsToday) }}
                            </div>

                            <div class="stat-desc">
                                Campus exit transactions
                            </div>

                        </div>

                        <div class="stat-icon">
                            <i class="bi bi-box-arrow-right"></i>
                        </div>

                    </div>

                </div>

            </div>

            <div class="col-xl-3 col-md-6">

                <div class="modern-stat-card stat-warning">

                    <div class="stat-glow"></div>

                    <div class="d-flex justify-content-between align-items-start">

                        <div>

                            <div class="stat-label">
                                Inside Campus
                            </div>

                            <div class="stat-number">
                                {{ number_format($currentlyInside) }}
                            </div>

                            <div class="stat-desc">
                                Active monitored users
                            </div>

                        </div>

                        <div class="stat-icon">
                            <i class="bi bi-people"></i>
                        </div>

                    </div>

                </div>

            </div>

            <div class="col-xl-3 col-md-6">

                <div class="modern-stat-card stat-danger">

                    <div class="stat-glow"></div>

                    <div class="d-flex justify-content-between align-items-start">

                        <div>

                            <div class="stat-label">
                                Failed Attempts
                            </div>

                            <div class="stat-number">
                                {{ number_format($failedAttempts) }}
                            </div>

                            <div class="stat-desc">
                                Denied or invalid scans
                            </div>

                        </div>

                        <div class="stat-icon">
                            <i class="bi bi-shield-exclamation"></i>
                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="smart-filter-wrapper mb-4">

            <div class="smart-filter-top">

                <div>

                    <div class="smart-filter-title">
                        Smart Filters
                    </div>

                    <div class="smart-filter-subtitle">
                        Monitor and refine gate activity in real-time
                    </div>

                </div>

                <div class="smart-filter-actions">

                    <button
                        type="button"
                        class="btn btn-filter-action"
                        id="btnRefresh"
                    >
                        <i class="bi bi-arrow-clockwise"></i>
                        <span>Refresh</span>
                    </button>

                    <button
                        type="button"
                        class="btn btn-filter-reset"
                        id="btnResetFilters"
                    >
                        <i class="bi bi-x-circle"></i>
                        <span>Reset</span>
                    </button>

                </div>

            </div>

            <div class="smart-filter-grid">

                <div class="smart-filter-item">

                    <label class="smart-label">
                        Date From
                    </label>

                    <div class="smart-input-wrap">

                        <i class="bi bi-calendar-event"></i>

                        <input
                            type="date"
                            class="form-control smart-input"
                            id="filterDateFrom"
                        >

                    </div>

                </div>

                <div class="smart-filter-item">

                    <label class="smart-label">
                        Date To
                    </label>

                    <div class="smart-input-wrap">

                        <i class="bi bi-calendar2-check"></i>

                        <input
                            type="date"
                            class="form-control smart-input"
                            id="filterDateTo"
                        >

                    </div>

                </div>

                <div class="smart-filter-item">

                    <label class="smart-label">
                        Gate
                    </label>

                    <div class="smart-input-wrap">

                        <i class="bi bi-building"></i>

                        <select
                            class="form-select smart-input"
                            id="filterGate"
                        >

                            <option value="">
                                All Gates
                            </option>

                            @foreach($gates as $gate)

                                <option value="{{ $gate }}">
                                    {{ $gate }}
                                </option>

                            @endforeach

                        </select>

                    </div>

                </div>

                <div class="smart-filter-item">

                    <label class="smart-label">
                        Mode
                    </label>

                    <div class="smart-input-wrap">

                        <i class="bi bi-arrow-left-right"></i>

                        <select
                            class="form-select smart-input"
                            id="filterMode"
                        >

                            <option value="">
                                All Modes
                            </option>

                            <option value="IN">
                                IN
                            </option>

                            <option value="OUT">
                                OUT
                            </option>

                        </select>

                    </div>

                </div>

                <div class="smart-filter-item">

                    <label class="smart-label">
                        Status
                    </label>

                    <div class="smart-input-wrap">

                        <i class="bi bi-shield-check"></i>

                        <select
                            class="form-select smart-input"
                            id="filterStatus"
                        >

                            <option value="">
                                All Status
                            </option>

                            <option value="ALLOWED">
                                Allowed
                            </option>

                            <option value="DENIED">
                                Denied
                            </option>

                            <option value="LATE">
                                Late
                            </option>

                        </select>

                    </div>

                </div>

                <div class="smart-filter-item search-filter">

                    <label class="smart-label">
                        Search User
                    </label>

                    <div class="smart-input-wrap">

                        <i class="bi bi-search"></i>

                        <input
                            type="text"
                            class="form-control smart-input"
                            id="filterSearch"
                            placeholder="Name, ID, department..."
                        >

                    </div>

                </div>

            </div>

        </div>

        <div class="logs-container">

            <div class="logs-header">

                <div>

                    <div class="logs-title">
                        Daily Gate Logs
                    </div>

                    <div class="logs-subtitle">
                        Real-time campus entry and exit transactions
                    </div>

                </div>

            </div>

            <div class="table-responsive">

                <x-datatable
                    id="gateLogsTable"

                    :columns="[
                    'Actions',
                    'User',
                    'Department',
                    'Gate',
                    'Mode',
                    'Status',
                    'Device',
                    'Time'
                ]"

                    :ajax="route('reports.gate-logs.data')"

                    :datatableColumns="[

                    [
                        'data'=>'actions',
                        'name'=>'actions',
                        'orderable'=>false,
                        'searchable'=>false,
                        'width'=>'70px'
                    ],

                    [
                        'data'=>'user',
                        'name'=>'user.name'
                    ],

                    [
                        'data'=>'department',
                        'name'=>'department'
                    ],

                    [
                        'data'=>'gate',
                        'name'=>'gate_name'
                    ],

                    [
                        'data'=>'mode',
                        'name'=>'mode'
                    ],

                    [
                        'data'=>'status',
                        'name'=>'status'
                    ],

                    [
                        'data'=>'device',
                        'name'=>'device'
                    ],

                    [
                        'data'=>'time',
                        'name'=>'created_at'
                    ]

                ]"
                />

            </div>

        </div>

    </div>

    <style>

        :root{
            --primary:#0f766e;
            --primary-light:#14b8a6;
            --success:#15803d;
            --warning:#d97706;
            --danger:#dc2626;

            --bg:#f5f7fb;
            --card:#ffffff;

            --text:#0f172a;
            --muted:#64748b;
            --border:#e2e8f0;
        }

        body{
            background:
                radial-gradient(circle at top right, rgba(20,184,166,.08), transparent 20%),
                radial-gradient(circle at bottom left, rgba(59,130,246,.06), transparent 20%),
                var(--bg);
        }

        .top-toolbar{
            display:flex;
            align-items:center;
            gap:.6rem;
            flex-wrap:wrap;
        }

        .btn-toolbar{
            height:42px;
            border:none;
            border-radius:14px;
            padding:0 .95rem;
            display:flex;
            align-items:center;
            gap:.45rem;
            font-size:.84rem;
            font-weight:700;
            color:#fff;
            transition:.2s ease;
            box-shadow:
                0 4px 12px rgba(15,23,42,.08);
        }

        .btn-toolbar:hover{
            transform:translateY(-1px);
            color:#fff;
        }

        .btn-excel{
            background:
                linear-gradient(
                    135deg,
                    #15803d,
                    #16a34a
                );
        }

        .btn-pdf{
            background:
                linear-gradient(
                    135deg,
                    #b91c1c,
                    #dc2626
                );
        }

        .btn-print{
            background:
                linear-gradient(
                    135deg,
                    #1e293b,
                    #334155
                );
        }

        .modern-stat-card{
            position:relative;
            overflow:hidden;
            border-radius:22px;
            padding:1rem 1.1rem;
            min-height:128px;
            color:#fff;
            transition:.2s ease;
            box-shadow:
                0 10px 25px rgba(15,23,42,.06);
        }

        .modern-stat-card:hover{
            transform:translateY(-2px);
        }

        .stat-primary{
            background:
                linear-gradient(
                    135deg,
                    #0f766e 0%,
                    #14b8a6 100%
                );
        }

        .stat-success{
            background:
                linear-gradient(
                    135deg,
                    #166534 0%,
                    #22c55e 100%
                );
        }

        .stat-warning{
            background:
                linear-gradient(
                    135deg,
                    #b45309 0%,
                    #f59e0b 100%
                );
        }

        .stat-danger{
            background:
                linear-gradient(
                    135deg,
                    #b91c1c 0%,
                    #ef4444 100%
                );
        }

        .stat-glow{
            position:absolute;
            width:130px;
            height:130px;
            right:-40px;
            top:-40px;
            border-radius:50%;
            background:rgba(255,255,255,.10);
        }

        .stat-label{
            font-size:.7rem;
            font-weight:700;
            letter-spacing:.4px;
            text-transform:uppercase;
            opacity:.9;
        }

        .stat-number{
            font-size:1.9rem;
            font-weight:800;
            line-height:1;
            margin-top:.75rem;
        }

        .stat-desc{
            margin-top:.7rem;
            font-size:.78rem;
            opacity:.92;
        }

        .stat-icon{
            width:50px;
            height:50px;
            border-radius:16px;
            background:rgba(255,255,255,.16);
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:1.2rem;
            backdrop-filter:blur(8px);
            flex-shrink:0;
        }

        .smart-filter-wrapper{
            position:sticky;
            top:12px;
            z-index:20;

            padding:1rem;
            border-radius:24px;

            background:
                linear-gradient(
                    145deg,
                    rgba(255,255,255,.96),
                    rgba(255,255,255,.88)
                );

            border:1px solid rgba(226,232,240,.9);

            box-shadow:
                0 8px 30px rgba(15,23,42,.05);
        }

        .smart-filter-top{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:1rem;
            margin-bottom:1rem;
            flex-wrap:wrap;
        }

        .smart-filter-title{
            font-size:.98rem;
            font-weight:800;
            color:var(--text);
        }

        .smart-filter-subtitle{
            margin-top:.15rem;
            font-size:.8rem;
            color:var(--muted);
        }

        .smart-filter-actions{
            display:flex;
            align-items:center;
            gap:.6rem;
            flex-wrap:wrap;
        }

        .btn-filter-action,
        .btn-filter-reset{
            height:42px;
            border:none;
            border-radius:14px;
            padding:0 .95rem;
            display:flex;
            align-items:center;
            gap:.45rem;
            font-size:.83rem;
            font-weight:700;
            transition:.2s ease;
        }

        .btn-filter-action{
            color:#fff;

            background:
                linear-gradient(
                    135deg,
                    #0f766e,
                    #14b8a6
                );

            box-shadow:
                0 6px 16px rgba(20,184,166,.18);
        }

        .btn-filter-reset{
            background:#f1f5f9;
            color:#0f172a;
        }

        .btn-filter-action:hover,
        .btn-filter-reset:hover{
            transform:translateY(-1px);
        }

        .smart-filter-grid{
            display:grid;
            grid-template-columns:
            repeat(auto-fit,minmax(180px,1fr));
            gap:.8rem;
        }

        .smart-label{
            display:block;
            margin-bottom:.45rem;
            font-size:.68rem;
            font-weight:800;
            text-transform:uppercase;
            letter-spacing:.45px;
            color:#64748b;
        }

        .smart-input-wrap{
            position:relative;
        }

        .smart-input-wrap i{
            position:absolute;
            left:14px;
            top:50%;
            transform:translateY(-50%);
            z-index:2;
            color:#94a3b8;
            font-size:.88rem;
        }

        .smart-input{
            height:46px;
            border:none !important;
            border-radius:16px !important;

            background:#f8fafc !important;

            padding-left:42px !important;

            font-size:.86rem;

            transition:.2s ease;

            box-shadow:none !important;
        }

        .smart-input:hover{
            background:#f1f5f9 !important;
        }

        .smart-input:focus{
            background:#fff !important;

            box-shadow:
                0 0 0 4px rgba(20,184,166,.10) !important;
        }

        .smart-input.active-filter{
            background:#ecfeff !important;

            box-shadow:
                inset 0 0 0 1px rgba(20,184,166,.20);
        }

        .search-filter{
            grid-column:span 2;
        }

        .logs-container{
            overflow:hidden;

            border-radius:24px;

            background:
                linear-gradient(
                    145deg,
                    rgba(255,255,255,.95),
                    rgba(255,255,255,.90)
                );

            border:1px solid rgba(226,232,240,.8);

            box-shadow:
                0 8px 30px rgba(15,23,42,.05);
        }

        .logs-header{
            padding:1rem 1.2rem;
            border-bottom:1px solid #f1f5f9;
        }

        .logs-title{
            font-size:1rem;
            font-weight:800;
            color:#0f172a;
        }

        .logs-subtitle{
            margin-top:.2rem;
            font-size:.8rem;
            color:#64748b;
        }

        .table-responsive{
            padding:.8rem;
        }

        @media(max-width:991px){

            .search-filter{
                grid-column:span 1;
            }

        }

        @media(max-width:768px){

            .modern-stat-card{
                min-height:118px;
            }

            .stat-number{
                font-size:1.6rem;
            }

            .smart-filter-wrapper,
            .logs-container{
                border-radius:20px;
            }

            .smart-filter-top{
                flex-direction:column;
                align-items:flex-start;
            }

            .smart-filter-actions{
                width:100%;
            }

            .btn-filter-action,
            .btn-filter-reset{
                flex:1;
                justify-content:center;
            }

        }

    </style>

    <script>

        document.addEventListener('DOMContentLoaded', function () {

            function initializePage() {

                if (typeof window.jQuery === 'undefined') {

                    setTimeout(initializePage, 100);

                    return;

                }

                const $ = window.jQuery;

                if (!$.fn.DataTable) {

                    setTimeout(initializePage, 100);

                    return;

                }

                const table = $.fn.dataTable.isDataTable('#gateLogsTable')
                    ? $('#gateLogsTable').DataTable()
                    : null;

                if (!table) {
                    return;
                }

                function updateActiveFilters() {

                    $('.smart-input').each(function () {

                        const hasValue = $(this).val();

                        if (hasValue) {

                            $(this).addClass('active-filter');

                        } else {

                            $(this).removeClass('active-filter');

                        }

                    });

                }

                function reloadTable() {

                    updateActiveFilters();

                    const params = {

                        date_from:
                            $('#filterDateFrom').val(),

                        date_to:
                            $('#filterDateTo').val(),

                        gate:
                            $('#filterGate').val(),

                        mode:
                            $('#filterMode').val(),

                        status:
                            $('#filterStatus').val(),

                        custom_search:
                            $('#filterSearch').val()

                    };

                    table.ajax.url(
                        "{{ route('reports.gate-logs.data') }}?" + $.param(params)
                    ).load();

                }

                $('#filterDateFrom').on('change', reloadTable);
                $('#filterDateTo').on('change', reloadTable);
                $('#filterGate').on('change', reloadTable);
                $('#filterMode').on('change', reloadTable);
                $('#filterStatus').on('change', reloadTable);

                let searchTimer;

                $('#filterSearch').on('keyup', function () {

                    clearTimeout(searchTimer);

                    searchTimer = setTimeout(function () {

                        reloadTable();

                    }, 400);

                });

                $('.smart-input').on('change keyup', function () {

                    updateActiveFilters();

                });

                $('#btnRefresh').on('click', function () {

                    reloadTable();

                });

                $('#btnResetFilters').on('click', function () {

                    $('#filterDateFrom').val('');
                    $('#filterDateTo').val('');
                    $('#filterGate').val('');
                    $('#filterMode').val('');
                    $('#filterStatus').val('');
                    $('#filterSearch').val('');

                    updateActiveFilters();
                    reloadTable();

                });

                updateActiveFilters();

            }

            initializePage();

        });

    </script>

@endsection
