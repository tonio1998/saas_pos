@extends('layouts.app')
@section('title','Dashboard')
@section('content')
    <x-page-header
        title="Smart Campus Dashboard"
        subtitle="Real-time monitoring and school operations overview"
    />

    <div class="row g-4 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="dashboard-card primary">
                <div class="dashboard-glow"></div>
                <div class="card-top">
                    <div>
                        <div class="card-label">
                            TOTAL STUDENTS
                        </div>
                        <div class="card-value" id="students-count">0</div>
                        <div class="card-trend">
                            <i class="bi bi-mortarboard-fill"></i>
                            Registered Students
                        </div>
                    </div>
                    <div class="card-icon">
                        <i class="bi bi-mortarboard-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="dashboard-card success">
                <div class="dashboard-glow"></div>
                <div class="card-top">
                    <div>
                        <div class="card-label">
                            TOTAL EMPLOYEES
                        </div>
                        <div
                            class="card-value"
                            id="employees-count"
                        >
                            0
                        </div>
                        <div class="card-trend">
                            <i class="bi bi-person-badge-fill"></i>
                            Faculty and Staff
                        </div>
                    </div>
                    <div class="card-icon">
                        <i class="bi bi-person-workspace"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="dashboard-card warning">
                <div class="dashboard-glow"></div>
                <div class="card-top">
                    <div>
                        <div class="card-label">
                            INSIDE CAMPUS
                        </div>
                        <div
                            class="card-value"
                            id="inside-campus-count"
                        >
                            0
                        </div>
                        <div class="card-trend">
                            <i class="bi bi-building-check"></i>
                            Active Presence
                        </div>
                    </div>
                    <div class="card-icon">
                        <i class="bi bi-building"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="dashboard-card dark">
                <div class="dashboard-glow"></div>
                <div class="card-top">
                    <div>
                        <div class="card-label">
                            LATE STUDENTS
                        </div>
                        <div
                            class="card-value"
                            id="late-students-count"
                        >
                            0
                        </div>
                        <div class="card-trend">
                            <span class="live-dot"></span>
                            Today's Attendance Alerts
                        </div>
                    </div>
                    <div class="card-icon">
                        <i class="bi bi-clock-history"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-8">
            <div class="modern-card h-100">
                <div class="section-header">
                    <div>
                        <h5 class="section-title">
                            Quick Access
                        </h5>

                        <div class="section-subtitle">
                            Frequently used modules
                        </div>

                    </div>

                </div>

                <div class="quick-grid">

                    <a
                        href="{{ route('students.index') }}"
                        class="quick-item"
                    >
                        <div class="quick-icon primary">
                            <i class="bi bi-mortarboard"></i>
                        </div>

                        <div>
                            <div class="quick-title">
                                Students
                            </div>

                            <div class="quick-subtitle">
                                Manage student records
                            </div>
                        </div>
                    </a>

                    <a
                        href="{{ route('employees.index') }}"
                        class="quick-item"
                    >
                        <div class="quick-icon success">
                            <i class="bi bi-person-workspace"></i>
                        </div>

                        <div>
                            <div class="quick-title">
                                Employees
                            </div>

                            <div class="quick-subtitle">
                                Faculty and staff management
                            </div>
                        </div>
                    </a>

                    <a
                        href="{{ route('scanner.index') }}"
                        class="quick-item"
                    >
                        <div class="quick-icon dark">
                            <i class="bi bi-qr-code-scan"></i>
                        </div>

                        <div>
                            <div class="quick-title">
                                Gate Scanner
                            </div>

                            <div class="quick-subtitle">
                                Open attendance terminal
                            </div>
                        </div>
                    </a>

                    <a
                        href="#"
                        class="quick-item"
                    >
                        <div class="quick-icon warning">
                            <i class="bi bi-bar-chart-fill"></i>
                        </div>

                        <div>
                            <div class="quick-title">
                                Reports
                            </div>

                            <div class="quick-subtitle">
                                Attendance analytics and exports
                            </div>
                        </div>
                    </a>

                </div>

            </div>

        </div>

        <div class="col-xl-4">

            <div class="modern-card h-100">

                <div class="section-header">

                    <div>

                        <h5 class="section-title">
                            System Information
                        </h5>

                        <div class="section-subtitle">
                            Live operational details
                        </div>

                    </div>

                </div>

                <div class="system-list">

                    <div class="system-item">

                        <div class="system-label">
                            Current Date
                        </div>

                        <div class="system-value">
                            {{ now()->format('F d, Y') }}
                        </div>

                    </div>

                    <div class="system-item">

                        <div class="system-label">
                            Current Time
                        </div>

                        <div
                            id="live-clock"
                            class="system-value"
                        >
                            --
                        </div>

                    </div>

                    <div class="system-item">

                        <div class="system-label">
                            SMS Gateway
                        </div>

                        <div class="system-value">

                            @if($schoolSettings?->sms_low_balance)

                                <span class="text-danger fw-bold">

                <i class="bi bi-exclamation-triangle-fill"></i>

                LOW BALANCE / OFFLINE

            </span>

                            @else

                                <span class="text-success fw-bold">

                <i class="bi bi-check-circle-fill"></i>

                ONLINE

            </span>

                            @endif

                        </div>

                    </div>

                    <div class="system-item">

                        <div class="system-label">
                            SMS Failed Count
                        </div>

                        <div class="system-value">

                            {{ $schoolSettings?->sms_failed_count ?? 0 }}

                        </div>

                    </div>

                    <div class="system-item">

                        <div class="system-label">
                            Last SMS Failure
                        </div>

                        <div class="system-value">

                            @if($schoolSettings?->sms_last_failed_at)

                                {{ \Carbon\Carbon::parse($schoolSettings->sms_last_failed_at)->diffForHumans() }}

                            @else

                                Never

                            @endif

                        </div>

                    </div>

                    <div class="system-item">

                        <div class="system-label">
                            Database
                        </div>

                        <div class="system-value text-success">
                            CONNECTED
                        </div>

                    </div>

                    <div class="system-item border-0">

                        <div class="system-label">
                            Logged In User
                        </div>

                        <div class="system-value">
                            {{ auth()->user()->name ?? 'Administrator' }}
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="row g-4">

        <div class="col-xl-7">

            <div class="modern-card">

                <div class="section-header">

                    <div>

                        <h5 class="section-title">
                            Today's Activity Summary
                        </h5>

                        <div class="section-subtitle">
                            Real-time attendance overview
                        </div>

                    </div>

                </div>

                <div class="activity-grid">

                    <div class="activity-box">

                        <div class="activity-icon primary">
                            <i class="bi bi-box-arrow-in-right"></i>
                        </div>

                        <div class="activity-content">

                            <div class="activity-label">
                                Student Entries
                            </div>

                            <div
                                class="activity-value"
                                id="student-entries-count"
                            >
                                0
                            </div>

                        </div>

                    </div>

                    <div class="activity-box">

                        <div class="activity-icon success">
                            <i class="bi bi-box-arrow-left"></i>
                        </div>

                        <div class="activity-content">

                            <div class="activity-label">
                                Student Exits
                            </div>

                            <div
                                class="activity-value"
                                id="student-exits-count"
                            >
                                0
                            </div>

                        </div>

                    </div>

                    <div class="activity-box">

                        <div class="activity-icon warning">
                            <i class="bi bi-person-check-fill"></i>
                        </div>

                        <div class="activity-content">

                            <div class="activity-label">
                                Employees Present
                            </div>

                            <div
                                class="activity-value"
                                id="employees-present-count"
                            >
                                0
                            </div>

                        </div>

                    </div>

                    <div class="activity-box">

                        <div class="activity-icon dark">
                            <i class="bi bi-person-walking"></i>
                        </div>

                        <div class="activity-content">

                            <div class="activity-label">
                                Visitors Today
                            </div>

                            <div
                                class="activity-value"
                                id="visitors-count"
                            >
                                0
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-xl-5">

            <div class="modern-card">

                <div class="section-header">

                    <div>

                        <h5 class="section-title">
                            Live Gate Feed
                        </h5>

                        <div class="section-subtitle">
                            Recent scanner activities
                        </div>

                    </div>

                </div>

                <div
                    id="live-feed"
                    class="feed-list"
                >

                    <div class="empty-feed">

                        <i class="bi bi-arrow-repeat spin"></i>

                        <div>
                            Loading live feed...
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <style>
        .dashboard-card{
            position:relative;
            overflow:hidden;

            border-radius:20px;

            padding:18px;

            min-height:125px;

            color:#fff;

            transition:.2s ease;

            box-shadow:0 8px 20px rgba(0,0,0,.08);
        }

        .dashboard-card:hover{
            transform:translateY(-2px);
        }

        .dashboard-card.primary{
            background:linear-gradient(135deg,#2563eb,#1d4ed8);
        }

        .dashboard-card.success{
            background:linear-gradient(135deg,#16a34a,#15803d);
        }

        .dashboard-card.warning{
            background:linear-gradient(135deg,#f59e0b,#d97706);
        }

        .dashboard-card.dark{
            background:linear-gradient(135deg,#111827,#1f2937);
        }

        .dashboard-glow{
            position:absolute;

            width:120px;
            height:120px;

            border-radius:50%;

            background:rgba(255,255,255,.08);

            top:-40px;
            right:-40px;
        }

        .card-top{
            position:relative;
            z-index:2;

            display:flex;
            justify-content:space-between;
            align-items:flex-start;
        }

        .card-label{
            font-size:11px;
            font-weight:800;
            letter-spacing:.8px;

            opacity:.8;

            margin-bottom:6px;
        }

        .card-value{
            font-size:34px;
            font-weight:1000;
            line-height:1;

            margin-bottom:8px;
        }

        .card-trend{
            display:flex;
            align-items:center;
            gap:6px;

            font-size:12px;
            font-weight:600;

            opacity:.9;
        }

        .card-icon{
            width:52px;
            height:52px;

            border-radius:16px;

            background:rgba(255,255,255,.12);

            display:flex;
            align-items:center;
            justify-content:center;

            font-size:22px;
        }

        .modern-card{
            background:#fff;

            border-radius:20px;

            padding:18px;

            box-shadow:0 6px 18px rgba(0,0,0,.04);

            border:1px solid #eef2f7;
        }

        .section-header{
            margin-bottom:16px;
        }

        .section-title{
            font-size:17px;
            font-weight:900;

            margin-bottom:2px;
        }

        .section-subtitle{
            color:#6b7280;
            font-size:12px;
        }

        .quick-grid{
            display:grid;

            grid-template-columns:
        repeat(auto-fit,minmax(180px,1fr));

            gap:12px;
        }

        .quick-item{
            display:flex;
            align-items:center;
            gap:12px;

            padding:14px;

            border-radius:16px;

            text-decoration:none;

            background:#f8fafc;

            border:1px solid #e5e7eb;

            transition:.2s ease;
        }

        .quick-item:hover{
            transform:translateY(-2px);

            box-shadow:0 8px 18px rgba(0,0,0,.06);
        }

        .quick-icon{
            width:46px;
            height:46px;

            border-radius:14px;

            display:flex;
            align-items:center;
            justify-content:center;

            color:#fff;

            font-size:18px;

            flex-shrink:0;
        }

        .quick-icon.primary{
            background:#2563eb;
        }

        .quick-icon.success{
            background:#16a34a;
        }

        .quick-icon.warning{
            background:#f59e0b;
        }

        .quick-icon.dark{
            background:#111827;
        }

        .quick-title{
            font-size:14px;
            font-weight:800;

            color:#111827;

            margin-bottom:2px;
        }

        .quick-subtitle{
            font-size:11px;

            color:#6b7280;
        }

        .system-list{
            display:flex;
            flex-direction:column;
        }

        .system-item{
            display:flex;
            justify-content:space-between;
            align-items:center;

            padding:10px 0;

            border-bottom:1px solid #f1f5f9;
        }

        .system-label{
            color:#6b7280;

            font-size:12px;
            font-weight:600;
        }

        .system-value{
            font-size:13px;
            font-weight:800;

            color:#111827;
        }

        .activity-grid{
            display:grid;

            grid-template-columns:
        repeat(auto-fit,minmax(180px,1fr));

            gap:12px;
        }

        .activity-box{
            padding:14px;

            border-radius:18px;

            background:#f8fafc;

            border:1px solid #e5e7eb;

            display:flex;
            align-items:center;
            gap:12px;
        }

        .activity-icon{
            width:46px;
            height:46px;

            border-radius:14px;

            display:flex;
            align-items:center;
            justify-content:center;

            color:#fff;

            font-size:18px;

            flex-shrink:0;
        }

        .activity-icon.primary{
            background:#2563eb;
        }

        .activity-icon.success{
            background:#16a34a;
        }

        .activity-icon.warning{
            background:#f59e0b;
        }

        .activity-icon.dark{
            background:#111827;
        }

        .activity-label{
            font-size:11px;
            font-weight:700;

            color:#6b7280;

            margin-bottom:4px;
        }

        .activity-value{
            font-size:22px;
            font-weight:1000;

            color:#111827;

            line-height:1;
        }

        .feed-list{
            display:flex;
            flex-direction:column;

            gap:10px;

            max-height:420px;

            overflow:auto;
        }

        .feed-item{
            display:flex;
            align-items:center;
            gap:10px;

            padding:12px;

            border-radius:16px;

            background:#f8fafc;

            border:1px solid #eef2f7;
        }

        .feed-avatar{
            width:42px;
            height:42px;

            border-radius:50%;

            background:#2563eb;

            color:#fff;

            display:flex;
            align-items:center;
            justify-content:center;

            font-size:16px;

            flex-shrink:0;
        }

        .feed-content{
            flex:1;
        }

        .feed-name{
            font-size:13px;
            font-weight:800;

            color:#111827;

            margin-bottom:2px;
        }

        .feed-meta{
            font-size:11px;

            color:#6b7280;
        }

        .feed-status{
            color:#16a34a;

            font-size:16px;
        }

        .empty-feed{
            padding:30px 10px;

            text-align:center;

            color:#9ca3af;
        }

        .live-dot{
            width:8px;
            height:8px;

            border-radius:50%;

            background:#22c55e;

            display:inline-block;

            animation:pulse 1.3s infinite;
        }

        @keyframes pulse{

            0%{
                opacity:1;
            }

            50%{
                opacity:.3;
            }

            100%{
                opacity:1;
            }

        }

        @media(max-width:768px){

            .card-value{
                font-size:28px;
            }

            .quick-grid,
            .activity-grid{
                grid-template-columns:1fr;
            }

        }

    </style>

    <script>

        function updateClock(){

            const now =
                new Date();

            document.getElementById(
                'live-clock'
            ).innerText =
                now.toLocaleTimeString(
                    [],
                    {
                        hour:'2-digit',
                        minute:'2-digit',
                        second:'2-digit'
                    }
                );

        }

        updateClock();

        setInterval(
            updateClock,
            1000
        );

        loadDashboard();

        setInterval(
            loadDashboard,
            5000
        );

        async function loadDashboard(){

            try{

                const response =
                    await fetch(
                        '/dashboard/data'
                    );

                if(!response.ok){

                    throw new Error(
                        'Failed to fetch dashboard data'
                    );

                }

                const data =
                    await response.json();

                setText(
                    'students-count',
                    formatNumber(
                        data.students
                    )
                );

                setText(
                    'employees-count',
                    formatNumber(
                        data.employees
                    )
                );

                setText(
                    'inside-campus-count',
                    formatNumber(
                        data.insideCampus
                    )
                );

                setText(
                    'late-students-count',
                    formatNumber(
                        data.lateStudents
                    )
                );

                setText(
                    'student-entries-count',
                    formatNumber(
                        data.studentEntries
                    )
                );

                setText(
                    'student-exits-count',
                    formatNumber(
                        data.studentExits
                    )
                );

                setText(
                    'employees-present-count',
                    formatNumber(
                        data.employeesPresent
                    )
                );

                setText(
                    'visitors-count',
                    formatNumber(
                        data.visitors
                    )
                );

                renderFeed(
                    data.recentLogs || []
                );

            }catch(error){

                console.error(error);

            }

        }

        function setText(id,value){

            const element =
                document.getElementById(id);

            if(element){

                element.innerText =
                    value;

            }

        }

        function renderFeed(logs){
            console.log(logs);
            const feed =
                document.getElementById(
                    'live-feed'
                );

            if(!feed){
                return;
            }

            if(!logs.length){

                feed.innerHTML = `
                <div class="empty-feed">

                    <i class="bi bi-database-x"></i>

                    <div>
                        No recent activity
                    </div>

                </div>
            `;

                return;

            }

            let html = '';

            logs.forEach(log => {

                html += `
                <div class="feed-item">

                    <div class="feed-avatar">

                        <i class="bi bi-person"></i>

                    </div>

                    <div class="feed-content">

                        <div class="feed-name">
                            ${log.name ?? 'Unknown'}
                        </div>

                        <div class="feed-meta">
                            ${log.mode ?? 'TIME IN'}
                            •
                            ${log.time ?? '--:--'}
                        </div>

                    </div>

                    <div class="feed-status">

                        <i class="bi bi-check-circle-fill"></i>

                    </div>

                </div>
            `;

            });

            feed.innerHTML =
                html;

        }

        function formatNumber(number){

            return new Intl.NumberFormat().format(
                number || 0
            );

        }

    </script>
@endsection
