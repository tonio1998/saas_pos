@extends('layouts.sa')

@section('title', 'Super Admin Dashboard')

@section('content')

    <x-page-header
        title="Super Admin Dashboard"
        subtitle="Platform-wide monitoring, tenant analytics, and operational overview"
    />

    <div class="row g-4 mb-4">

        <div class="col-12 col-sm-6 col-xl-3">

            <div class="sa-card sa-primary">

                <div class="sa-card-pattern"></div>

                <div class="sa-card-body">

                    <div>

                        <div class="sa-label">
                            TOTAL SCHOOLS
                        </div>

                        <div
                            class="sa-value"
                            id="schools-count"
                        >
                            0
                        </div>

                        <div class="sa-meta">

                            <i class="bi bi-buildings"></i>

                            Registered Institutions

                        </div>

                    </div>

                    <div class="sa-icon">
                        <i class="bi bi-buildings-fill"></i>
                    </div>

                </div>

            </div>

        </div>

        <div class="col-12 col-sm-6 col-xl-3">

            <div class="sa-card sa-success">

                <div class="sa-card-pattern"></div>

                <div class="sa-card-body">

                    <div>

                        <div class="sa-label">
                            ACTIVE USERS
                        </div>

                        <div
                            class="sa-value"
                            id="users-count"
                        >
                            0
                        </div>

                        <div class="sa-meta">

                            <i class="bi bi-people-fill"></i>

                            Platform-wide Accounts

                        </div>

                    </div>

                    <div class="sa-icon">
                        <i class="bi bi-person-workspace"></i>
                    </div>

                </div>

            </div>

        </div>

        <div class="col-12 col-sm-6 col-xl-3">

            <div class="sa-card sa-warning">

                <div class="sa-card-pattern"></div>

                <div class="sa-card-body">

                    <div>

                        <div class="sa-label">
                            TOTAL SMS SENT
                        </div>

                        <div
                            class="sa-value"
                            id="sms-count"
                        >
                            0
                        </div>

                        <div class="sa-meta">

                            <i class="bi bi-chat-dots-fill"></i>

                            Platform Notifications

                        </div>

                    </div>

                    <div class="sa-icon">
                        <i class="bi bi-envelope-paper-fill"></i>
                    </div>

                </div>

            </div>

        </div>

        <div class="col-12 col-sm-6 col-xl-3">

            <div
                class="sa-card sa-dark"
                id="gsm-card"
            >

                <div class="sa-card-pattern"></div>

                <div class="sa-card-body">

                    <div>

                        <div class="sa-label">
                            GSM STATUS
                        </div>

                        <div
                            class="sa-value fs-4"
                            id="gsm-status"
                        >
                            UNKNOWN
                        </div>

                        <div
                            class="sa-meta"
                            id="gsm-meta"
                        >

                            <span class="live-dot"></span>

                            Waiting for modem...

                        </div>

                    </div>

                    <div class="sa-icon">
                        <i class="bi bi-router-fill"></i>
                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="row g-4 mb-4">

        <div class="col-xl-8">

            <div class="sa-panel">

                <div class="sa-panel-header">

                    <div>

                        <h5 class="sa-title">
                            Platform Management
                        </h5>

                        <div class="sa-subtitle">
                            Administrative tools and controls
                        </div>

                    </div>

                </div>

                <div class="sa-grid">

                    <a
                        href="{{ route('sa.schools.index') }}"
                        class="sa-quick"
                    >

                        <div class="sa-quick-icon primary">
                            <i class="bi bi-buildings"></i>
                        </div>

                        <div>

                            <div class="sa-quick-title">
                                School Management
                            </div>

                            <div class="sa-quick-subtitle">
                                Manage all registered schools
                            </div>

                        </div>

                    </a>

                    <a
                        href="{{ route('users.index') }}"
                        class="sa-quick"
                    >

                        <div class="sa-quick-icon success">
                            <i class="bi bi-people-fill"></i>
                        </div>

                        <div>

                            <div class="sa-quick-title">
                                User Management
                            </div>

                            <div class="sa-quick-subtitle">
                                Platform-wide user administration
                            </div>

                        </div>

                    </a>

                    <a
                        href="{{ route('roles.index') }}"
                        class="sa-quick"
                    >

                        <div class="sa-quick-icon warning">
                            <i class="bi bi-person-badge-fill"></i>
                        </div>

                        <div>

                            <div class="sa-quick-title">
                                Roles & Permissions
                            </div>

                            <div class="sa-quick-subtitle">
                                Access control and security
                            </div>

                        </div>

                    </a>

                </div>

            </div>

        </div>

        <div class="col-xl-4">

            <div class="sa-panel h-100">

                <div class="sa-panel-header">

                    <div>

                        <h5 class="sa-title">
                            System Information
                        </h5>

                        <div class="sa-subtitle">
                            Real-time environment details
                        </div>

                    </div>

                </div>

                <div class="sa-system-list">

                    <div class="sa-system-item">

                        <div class="sa-system-label">
                            Current Date
                        </div>

                        <div class="sa-system-value">
                            {{ now()->format('F d, Y') }}
                        </div>

                    </div>

                    <div class="sa-system-item">

                        <div class="sa-system-label">
                            Current Time
                        </div>

                        <div
                            id="live-clock"
                            class="sa-system-value"
                        >
                            --
                        </div>

                    </div>

                    <div class="sa-system-item">

                        <div class="sa-system-label">
                            Database
                        </div>

                        <div class="sa-system-value text-success">
                            CONNECTED
                        </div>

                    </div>

                    <div class="sa-system-item">

                        <div class="sa-system-label">
                            Environment
                        </div>

                        <div class="sa-system-value">
                            {{ strtoupper(app()->environment()) }}
                        </div>

                    </div>

                    <div class="sa-system-item border-0">

                        <div class="sa-system-label">
                            Logged In
                        </div>

                        <div class="sa-system-value">
                            {{ auth()->user()->name }}
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="row g-4 mb-4">

        <div class="col-12">

            <div class="sa-panel">

                <div class="sa-panel-header">

                    <div>

                        <h5 class="sa-title">
                            GSM Modem Diagnostics
                        </h5>

                        <div class="sa-subtitle">
                            Real-time SMS gateway monitoring
                        </div>

                    </div>

                </div>

                <div class="row g-4">

                    <div class="col-md-3">

                        <div class="analytics-box">

                            <div class="analytics-icon primary">
                                <i class="bi bi-broadcast-pin"></i>
                            </div>

                            <div>

                                <div class="analytics-label">
                                    Signal
                                </div>

                                <div
                                    class="analytics-value"
                                    id="gsm-signal"
                                >
                                    UNKNOWN
                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-3">

                        <div class="analytics-box">

                            <div class="analytics-icon warning">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                            </div>

                            <div>

                                <div class="analytics-label">
                                    Failed SMS
                                </div>

                                <div
                                    class="analytics-value"
                                    id="gsm-failed-count"
                                >
                                    0
                                </div>

                            </div>

                        </div>

                    </div>


                    <div class="col-md-3">
                        <div class="analytics-box">
                            <div class="analytics-icon dark">
                                <i class="bi bi-cpu-fill"></i>
                            </div>
                            <div>
                                <div class="analytics-label">
                                    Provider
                                </div>
                                <div
                                    class="analytics-value"
                                    id="gsm-provider"
                                >
                                    GSM
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">

                    <div
                        class="alert alert-warning mb-0"
                        id="gsm-error-box"
                    >

                        <strong>
                            Last Error:
                        </strong>

                        <span id="gsm-last-error">
                        ...
                    </span>

                        <div
                            class="small mt-1"
                            id="gsm-last-failed"
                        >
                            --
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="row g-4">

        <div class="col-xl-7">

            <div class="sa-panel">

                <div class="sa-panel-header">

                    <div>

                        <h5 class="sa-title">
                            Platform Analytics
                        </h5>

                        <div class="sa-subtitle">
                            Real-time operational metrics
                        </div>

                    </div>

                </div>

                <div class="analytics-grid">

                    <div class="analytics-box">

                        <div class="analytics-icon primary">
                            <i class="bi bi-server"></i>
                        </div>

                        <div>

                            <div class="analytics-label">
                                Active Schools
                            </div>

                            <div
                                class="analytics-value"
                                id="active-schools-count"
                            >
                                0
                            </div>

                        </div>

                    </div>

                    <div class="analytics-box">

                        <div class="analytics-icon success">
                            <i class="bi bi-person-check-fill"></i>
                        </div>

                        <div>

                            <div class="analytics-label">
                                Online Users
                            </div>

                            <div
                                class="analytics-value"
                                id="online-users-count"
                            >
                                0
                            </div>

                        </div>

                    </div>

                    <div class="analytics-box">

                        <div class="analytics-icon warning">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                        </div>

                        <div>

                            <div class="analytics-label">
                                Failed SMS
                            </div>

                            <div
                                class="analytics-value"
                                id="failed-sms-count"
                            >
                                0
                            </div>

                        </div>

                    </div>

                    <div class="analytics-box">

                        <div class="analytics-icon dark">
                            <i class="bi bi-shield-lock-fill"></i>
                        </div>

                        <div>

                            <div class="analytics-label">
                                Security Logs
                            </div>

                            <div
                                class="analytics-value"
                                id="security-logs-count"
                            >
                                0
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-xl-5">

            <div class="sa-panel">

                <div class="sa-panel-header">

                    <div>

                        <h5 class="sa-title">
                            Recent Platform Activity
                        </h5>

                        <div class="sa-subtitle">
                            Latest system-wide events
                        </div>

                    </div>

                </div>

                <div
                    id="platform-feed"
                    class="platform-feed"
                >

                    <div class="feed-empty">

                        <i class="bi bi-arrow-repeat spin"></i>

                        <div>
                            Loading activities...
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <script>

        function updateClock(){

            const now = new Date();

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

        function setText(
            id,
            value
        ){

            const element =
                document.getElementById(id);

            if(element){

                element.innerText =
                    value ?? '--';

            }

        }

        function formatNumber(number){

            return new Intl.NumberFormat()
                .format(number || 0);

        }

        function updateSMSUI(settings){

            const gsmCard =
                document.getElementById(
                    'gsm-card'
                );

            gsmCard.classList.remove(
                'sa-dark',
                'sa-success',
                'sa-warning',
                'sa-danger'
            );

            if(
                settings?.status
                === 'online'
            ){

                gsmCard.classList.add(
                    'sa-success'
                );

            }

            else if(
                settings?.status
                === 'error'
            ){

                gsmCard.classList.add(
                    'sa-danger'
                );

            }

            else{

                gsmCard.classList.add(
                    'sa-warning'
                );

            }

            setText(
                'gsm-status',
                (
                    settings?.status
                    || 'unknown'
                ).toUpperCase()
            );

            setText(
                'gsm-meta',
                (
                    settings?.signal_status
                    || 'unknown'
                ).toUpperCase()
            );

            setText(
                'gsm-signal',
                (
                    settings?.signal_status
                    || 'unknown'
                ).toUpperCase()
            );

            setText(
                'gsm-failed-count',
                formatNumber(
                    settings?.failed_count
                )
            );

            setText(
                'gsm-provider',
                (
                    settings?.provider
                    || 'gsm'
                ).toUpperCase()
            );

            setText(
                'gsm-last-error',
                settings?.last_error
                || 'No GSM errors detected.'
            );

            setText(
                'gsm-last-failed',
                settings?.last_failed_at
                || '--'
            );
        }

        async function loadDashboard(){

            try{

                const response =
                    await fetch(
                        '/sa/dashboard/data'
                    );

                if(!response.ok){

                    throw new Error(
                        'Failed to load dashboard'
                    );

                }

                const data =
                    await response.json();

                setText(
                    'schools-count',
                    formatNumber(
                        data.schools
                    )
                );

                setText(
                    'users-count',
                    formatNumber(
                        data.users
                    )
                );

                setText(
                    'sms-count',
                    formatNumber(
                        data.smsSent
                    )
                );

                setText(
                    'active-schools-count',
                    formatNumber(
                        data.activeSchools
                    )
                );

                setText(
                    'online-users-count',
                    formatNumber(
                        data.onlineUsers
                    )
                );

                setText(
                    'failed-sms-count',
                    formatNumber(
                        data.failedSms
                    )
                );

                setText(
                    'security-logs-count',
                    formatNumber(
                        data.securityLogs
                    )
                );

                updateSMSUI(
                    data.smsSettings
                );

                renderFeed(
                    data.recentActivities || []
                );

            }catch(error){

                console.error(error);

            }

        }

        function renderFeed(items){

            const feed =
                document.getElementById(
                    'platform-feed'
                );

            if(!feed){
                return;
            }

            if(!items.length){

                feed.innerHTML = `
                <div class="feed-empty">

                    <i class="bi bi-database-x"></i>

                    <div>
                        No recent activity
                    </div>

                </div>
            `;

                return;
            }

            let html = '';

            items.forEach(item => {

                html += `
                <div class="feed-item">

                    <div class="feed-avatar">

                        <i class="bi bi-person"></i>

                    </div>

                    <div class="feed-content">

                        <div class="feed-name">
                            ${item.name}
                        </div>

                        <div class="feed-meta">
                            ${item.description}
                        </div>

                        <div
                            class="
                                text-muted
                                small
                            "
                        >
                            ${item.ip || ''}
                        </div>

                    </div>

                    <div class="feed-time">
                        ${item.time}
                    </div>

                </div>
            `;
            });

            feed.innerHTML = html;
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

    </script>

@endsection
