@extends('layouts.sa')

@section('content')

    <div class="container-fluid py-3">

        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h3 class="fw-bold mb-1">
                    Platform Analytics
                </h3>

                <div class="text-muted small">
                    Enterprise Monitoring Center
                </div>
            </div>

            <button
                class="btn btn-light border rounded-3 px-3"
                id="refreshAnalytics"
            >
                <i class="bi bi-arrow-clockwise me-2"></i>
                Refresh
            </button>
        </div>

        <div class="row g-3 mb-4">

            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="analytics-card">
                    <div class="analytics-label">
                        Total Users
                    </div>

                    <div
                        class="analytics-value"
                        id="totalUsers"
                    >
                        --
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="analytics-card">
                    <div class="analytics-label">
                        Active Users
                    </div>

                    <div
                        class="analytics-value"
                        id="activeUsers"
                    >
                        --
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="analytics-card">
                    <div class="analytics-label">
                        Schools
                    </div>

                    <div
                        class="analytics-value"
                        id="totalSchools"
                    >
                        --
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="analytics-card">
                    <div class="analytics-label">
                        Active Sessions
                    </div>

                    <div
                        class="analytics-value"
                        id="activeSessions"
                    >
                        --
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="analytics-card">
                    <div class="analytics-label">
                        Suspicious
                    </div>

                    <div
                        class="analytics-value text-danger"
                        id="suspiciousActivities"
                    >
                        --
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="analytics-card">
                    <div class="analytics-label">
                        Today's Logins
                    </div>

                    <div
                        class="analytics-value"
                        id="todayLogins"
                    >
                        --
                    </div>
                </div>
            </div>

        </div>

        <div class="row g-3">

            <div class="col-xl-8">

                <div class="analytics-panel mb-3">

                    <div class="panel-header">
                        <div>
                            <h6 class="mb-1 fw-semibold">
                                Login Trends
                            </h6>

                            <div class="text-muted small">
                                Last 14 days
                            </div>
                        </div>
                    </div>

                    <div class="chart-wrapper">
                        <canvas id="loginTrendChart"></canvas>
                    </div>

                </div>

                <div class="analytics-panel">

                    <div class="panel-header">
                        <div>
                            <h6 class="mb-1 fw-semibold">
                                School Activity
                            </h6>

                            <div class="text-muted small">
                                User distribution
                            </div>
                        </div>
                    </div>

                    <div class="chart-wrapper">
                        <canvas id="schoolChart"></canvas>
                    </div>

                </div>

            </div>

            <div class="col-xl-4">

                <div class="analytics-panel mb-3">

                    <div class="panel-header">
                        <div>
                            <h6 class="mb-1 fw-semibold">
                                Security Analytics
                            </h6>

                            <div class="text-muted small">
                                Suspicious activities
                            </div>
                        </div>
                    </div>

                    <div class="chart-wrapper">
                        <canvas id="securityChart"></canvas>
                    </div>

                </div>

                <div class="analytics-panel">

                    <div class="panel-header">
                        <div>
                            <h6 class="mb-1 fw-semibold">
                                Browser Analytics
                            </h6>

                            <div class="text-muted small">
                                Device usage
                            </div>
                        </div>
                    </div>

                    <div class="chart-wrapper">
                        <canvas id="deviceChart"></canvas>
                    </div>

                </div>

            </div>

        </div>

    </div>

    <style>

        .analytics-card{
            background:#fff;
            border:1px solid #eef1f4;
            border-radius:18px;
            padding:18px;
            box-shadow:
                0 2px 10px rgba(15,23,42,.03);
        }

        .analytics-label{
            font-size:.78rem;
            color:#64748b;
            margin-bottom:10px;
            font-weight:600;
        }

        .analytics-value{
            font-size:1.7rem;
            font-weight:700;
            color:#0f172a;
            line-height:1;
        }

        .analytics-panel{
            background:#fff;
            border:1px solid #eef1f4;
            border-radius:20px;
            padding:18px;
            box-shadow:
                0 2px 10px rgba(15,23,42,.03);
        }

        .panel-header{
            display:flex;
            align-items:center;
            justify-content:space-between;
            margin-bottom:18px;
        }

        .chart-wrapper{
            position:relative;
            height:320px;
        }

        @media(max-width:768px){

            .analytics-card{
                padding:16px;
            }

            .analytics-value{
                font-size:1.4rem;
            }

            .chart-wrapper{
                height:260px;
            }

        }

    </style>

    <script>

        let loginChart;
        let securityChart;
        let deviceChart;
        let schoolChart;

        document.addEventListener('DOMContentLoaded', () => {

            loadOverview();
            loadLoginTrends();
            loadSecurityTrends();
            loadDeviceAnalytics();
            loadSchoolAnalytics();

            document
                .getElementById('refreshAnalytics')
                .addEventListener('click', () => {

                    loadOverview(true);
                    loadLoginTrends(true);
                    loadSecurityTrends(true);
                    loadDeviceAnalytics(true);
                    loadSchoolAnalytics(true);
                });
        });

        async function loadOverview(refresh = false)
        {
            const response = await fetch(
                `{{ route('sa.platform-analytics.overview-data') }}?refresh=${refresh}`
            );

            const data = await response.json();

            document.getElementById('totalUsers').innerHTML =
                data.total_users;

            document.getElementById('activeUsers').innerHTML =
                data.active_users;

            document.getElementById('totalSchools').innerHTML =
                data.total_schools;

            document.getElementById('activeSessions').innerHTML =
                data.active_sessions;

            document.getElementById('suspiciousActivities').innerHTML =
                data.suspicious_activities;

            document.getElementById('todayLogins').innerHTML =
                data.today_logins;
        }

        async function loadLoginTrends(refresh = false)
        {
            const response = await fetch(
                `{{ route('sa.platform-analytics.login-trends') }}?refresh=${refresh}`
            );

            const data = await response.json();

            if(loginChart){
                loginChart.destroy();
            }

            loginChart = new Chart(
                document.getElementById('loginTrendChart'),
                {
                    type:'line',
                    data:{
                        labels:data.map(item => item.date),
                        datasets:[{
                            data:data.map(item => item.total),
                            borderWidth:2,
                            tension:.4
                        }]
                    },
                    options:{
                        responsive:true,
                        maintainAspectRatio:false,
                        plugins:{
                            legend:{
                                display:false
                            }
                        }
                    }
                }
            );
        }

        async function loadSecurityTrends(refresh = false)
        {
            const response = await fetch(
                `{{ route('sa.platform-analytics.security-trends') }}?refresh=${refresh}`
            );

            const data = await response.json();

            if(securityChart){
                securityChart.destroy();
            }

            securityChart = new Chart(
                document.getElementById('securityChart'),
                {
                    type:'bar',
                    data:{
                        labels:data.map(item => item.date),
                        datasets:[{
                            data:data.map(item => item.total),
                            borderWidth:1
                        }]
                    },
                    options:{
                        responsive:true,
                        maintainAspectRatio:false,
                        plugins:{
                            legend:{
                                display:false
                            }
                        }
                    }
                }
            );
        }

        async function loadDeviceAnalytics(refresh = false)
        {
            const response = await fetch(
                `{{ route('sa.platform-analytics.device-analytics') }}?refresh=${refresh}`
            );

            const data = await response.json();

            if(deviceChart){
                deviceChart.destroy();
            }

            deviceChart = new Chart(
                document.getElementById('deviceChart'),
                {
                    type:'doughnut',
                    data:{
                        labels:data.map(item => item.browser),
                        datasets:[{
                            data:data.map(item => item.total)
                        }]
                    },
                    options:{
                        responsive:true,
                        maintainAspectRatio:false
                    }
                }
            );
        }

        async function loadSchoolAnalytics(refresh = false)
        {
            const response = await fetch(
                `{{ route('sa.platform-analytics.school-analytics') }}?refresh=${refresh}`
            );

            const data = await response.json();

            if(schoolChart){
                schoolChart.destroy();
            }

            schoolChart = new Chart(
                document.getElementById('schoolChart'),
                {
                    type:'bar',
                    data:{
                        labels:data.map(item => item.name),
                        datasets:[{
                            data:data.map(item => item.users_count),
                            borderWidth:1
                        }]
                    },
                    options:{
                        responsive:true,
                        maintainAspectRatio:false,
                        plugins:{
                            legend:{
                                display:false
                            }
                        }
                    }
                }
            );
        }

    </script>

@endsection
