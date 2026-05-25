@extends('layouts.app')

@section('title','Student Logs Monitoring')

@section('content')

    <x-page-header
        title="Student Logs Monitoring"
        subtitle="Daily grouped attendance logs per student"
    >

        <x-slot:action>

            <a
                href="{{ route('scanner.index') }}"
                class="btn btn-primary px-3"
            >
                <i class="bi bi-upc-scan me-1"></i>
                Scanner
            </a>

        </x-slot:action>

    </x-page-header>

    <x-card>

        <div class="monitor-wrapper">

            <div class="monitor-top">

                <div class="monitor-title-group">

                    <div class="monitor-icon">
                        <i class="bi bi-people"></i>
                    </div>

                    <div>

                        <h5 class="monitor-title">
                            Student Daily Attendance
                        </h5>

                        <p class="monitor-subtitle">
                            Select a student to view grouped attendance logs
                        </p>

                    </div>

                </div>

                <div class="monitor-actions">

                    <div class="student-select-wrap">

                        <x-form.group
                            name="UserID"
                            label="User"
                            class="col-12"
                        >

                            <x-form.select
                                id="studentFilter"
                                name="UserID"
                                ajax="{{ route('select2.users') }}"
                                placeholder="Search Employee/Student"
                            />

                        </x-form.group>

                    </div>

                </div>

            </div>

            <div
                id="emptyState"
                class="empty-state"
            >

                <div class="empty-icon">
                    <i class="bi bi-person-lines-fill"></i>
                </div>

                <h5>
                    No Student Selected
                </h5>

                <p>
                    Choose a student to display grouped logs and attendance history.
                </p>

            </div>

            <div
                id="logsContainer"
                style="display:none;"
            >

                <div
                    id="logsWrapper"
                    class="logs-wrapper"
                ></div>

            </div>

        </div>

    </x-card>

@endsection

@push('styles')

    <style>

        .monitor-wrapper{
            display:flex;
            flex-direction:column;
            gap:1.1rem;
        }

        .monitor-top{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:1rem;
            flex-wrap:wrap;
            padding:1rem 1.1rem;
            border-radius:18px;
            background:linear-gradient(
                135deg,
                rgba(0,77,26,.07),
                rgba(255,208,76,.10)
            );
            border:1px solid rgba(0,77,26,.08);
        }

        .monitor-title-group{
            display:flex;
            align-items:center;
            gap:.9rem;
        }

        .monitor-icon{
            width:54px;
            height:54px;
            border-radius:16px;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:1.3rem;
            background:#fff;
            color:#004D1A;
            box-shadow:0 8px 20px rgba(0,0,0,.06);
        }

        .monitor-title{
            margin:0;
            font-size:1.02rem;
            font-weight:700;
            color:#111827;
        }

        .monitor-subtitle{
            margin:.2rem 0 0;
            color:#6b7280;
            font-size:.87rem;
        }

        .monitor-actions{
            display:flex;
            align-items:end;
            gap:1rem;
        }

        .student-select-wrap{
            min-width:320px;
        }

        .form-select{
            height:48px;
            border-radius:14px;
            border:1px solid #dfe3e8;
            font-size:.92rem;
            box-shadow:none !important;
        }

        .form-select:focus{
            border-color:#004D1A;
        }

        .empty-state{
            padding:4rem 1.5rem;
            border-radius:22px;
            border:1px dashed #d8dee7;
            background:#fbfcfd;
            text-align:center;
        }

        .empty-icon{
            width:78px;
            height:78px;
            border-radius:50%;
            margin:0 auto 1rem;
            display:flex;
            align-items:center;
            justify-content:center;
            background:rgba(0,77,26,.08);
            color:#004D1A;
            font-size:2rem;
        }

        .empty-state h5{
            margin-bottom:.45rem;
            font-weight:700;
            color:#111827;
        }

        .empty-state p{
            margin:0;
            color:#6b7280;
            font-size:.9rem;
        }

        .logs-wrapper{
            display:flex;
            flex-direction:column;
            gap:1rem;
        }

        .log-group{
            border-radius:20px;
            overflow:hidden;
            border:1px solid #edf1f5;
            background:#fff;
            box-shadow:0 4px 18px rgba(0,0,0,.03);
        }

        .log-group-header{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:1rem;
            padding:1rem 1.15rem;
            background:linear-gradient(
                135deg,
                #004D1A,
                #016325
            );
            color:#fff;
        }

        .log-date{
            font-size:.95rem;
            font-weight:700;
        }

        .log-count{
            padding:.34rem .7rem;
            border-radius:999px;
            font-size:.78rem;
            background:rgba(255,255,255,.14);
        }

        .log-items{
            padding:.9rem;
            display:flex;
            flex-direction:column;
            gap:.75rem;
        }

        .log-item{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:1rem;
            padding:.82rem .95rem;
            border-radius:16px;
            background:#f8fafc;
            border:1px solid #edf1f4;
            transition:.2s ease;
        }

        .log-item:hover{
            transform:translateY(-1px);
            background:#fff;
            box-shadow:0 8px 18px rgba(0,0,0,.04);
        }

        .log-left{
            display:flex;
            align-items:center;
            gap:.85rem;
        }

        .log-badge{
            width:44px;
            height:44px;
            border-radius:13px;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:.78rem;
            font-weight:700;
            letter-spacing:.4px;
        }

        .badge-in{
            background:rgba(25,135,84,.12);
            color:#198754;
        }

        .badge-out{
            background:rgba(220,53,69,.12);
            color:#dc3545;
        }

        .log-mode{
            font-size:.9rem;
            font-weight:700;
            color:#111827;
            margin-bottom:.15rem;
        }

        .log-meta{
            font-size:.78rem;
            color:#6b7280;
        }

        .log-time{
            font-size:.9rem;
            font-weight:700;
            color:#111827;
            white-space:nowrap;
        }

        .loading-state{
            padding:3rem 1rem;
            text-align:center;
            color:#6b7280;
        }

        @media(max-width:768px){

            .student-select-wrap{
                width:100%;
                min-width:100%;
            }

            .monitor-actions{
                width:100%;
            }

            .log-item{
                flex-direction:column;
                align-items:flex-start;
            }

            .log-time{
                width:100%;
                padding-left:3.4rem;
            }

        }

    </style>

@endpush

@push('scripts')


    <script>

        document.addEventListener('DOMContentLoaded', () => {

            const logsContainer = document.getElementById('logsContainer');
            const logsWrapper = document.getElementById('logsWrapper');
            const emptyState = document.getElementById('emptyState');

            const $studentFilter = $('#studentFilter');

            if (
                !$studentFilter.length ||
                !logsContainer ||
                !logsWrapper ||
                !emptyState
            ) {
                console.error('Required elements not found.');
                return;
            }

            const showLoading = () => {

                logsWrapper.innerHTML = `
                <div class="loading-state">

                    <div class="spinner-border text-success mb-3"></div>

                    <div>
                        Loading attendance logs...
                    </div>

                </div>
            `;

            };

            const showError = (message = 'Failed to load logs.') => {

                logsWrapper.innerHTML = `
                <div class="empty-state">

                    <div class="empty-icon">
                        <i class="bi bi-wifi-off"></i>
                    </div>

                    <h5>
                        Connection Error
                    </h5>

                    <p>
                        ${message}
                    </p>

                </div>
            `;

            };

            const showNoData = () => {

                logsWrapper.innerHTML = `
                <div class="empty-state">

                    <div class="empty-icon">
                        <i class="bi bi-journal-x"></i>
                    </div>

                    <h5>
                        No Logs Found
                    </h5>

                    <p>
                        This user has no attendance records yet.
                    </p>

                </div>
            `;

            };

            const renderLogs = (groups = []) => {

                let html = '';

                groups.forEach(group => {

                    html += `
                    <div class="log-group">

                        <div class="log-group-header">

                            <div class="log-date">
                                <i class="bi bi-calendar-event me-2"></i>
                                ${group.date}
                            </div>

                            <div class="log-count">
                                ${group.total} Logs
                            </div>

                        </div>

                        <div class="log-items">
                `;

                    group.logs.forEach(log => {

                        const mode = String(log.mode || '')
                            .toUpperCase();

                        const isIn = mode.includes('IN');

                        html += `
                        <div class="log-item">

                            <div class="log-left">

                                <div class="log-badge ${isIn ? 'badge-in' : 'badge-out'}">
                                    ${mode}
                                </div>

                                <div>

                                    <div class="log-mode">
                                        ${mode}
                                    </div>

                                    <div class="log-meta">
                                        ${log.device || 'Scanner Device'}
                                    </div>

                                </div>

                            </div>

                            <div class="log-time">
                                ${log.time || '--:--'}
                            </div>

                        </div>
                    `;

                    });

                    html += `
                        </div>

                    </div>
                `;

                });

                logsWrapper.innerHTML = html;

            };

            $studentFilter.on('change', async function () {

                const userId = $(this).val();

                if (!userId) {

                    logsContainer.style.display = 'none';
                    emptyState.style.display = 'block';
                    logsWrapper.innerHTML = '';

                    return;

                }

                emptyState.style.display = 'none';
                logsContainer.style.display = 'block';

                showLoading();

                try {

                    const response = await fetch(
                        `{{ route('logs.users.data') }}?user_id=${encodeURIComponent(userId)}`,
                        {
                            method:'GET',
                            headers:{
                                'Accept':'application/json',
                                'X-Requested-With':'XMLHttpRequest'
                            }
                        }
                    );

                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}`);
                    }

                    const result = await response.json();

                    if (!result.status) {

                        showError(
                            result.message || 'Unable to retrieve logs.'
                        );

                        return;

                    }

                    if (
                        !Array.isArray(result.data) ||
                        !result.data.length
                    ) {

                        showNoData();
                        return;

                    }

                    renderLogs(result.data);

                } catch (error) {

                    console.error(error);

                    showError(
                        'Failed to load user attendance logs.'
                    );

                }

            });

        });

    </script>

@endpush
