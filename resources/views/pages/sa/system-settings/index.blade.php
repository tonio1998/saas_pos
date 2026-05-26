@extends('layouts.sa')

@section('content')

    <div class="container-fluid py-3">

        <div class="d-flex align-items-center justify-content-between mb-4">

            <div>
                <h3 class="fw-bold mb-1">
                    System Settings
                </h3>

                <div class="text-muted small">
                    Global infrastructure and platform configuration
                </div>
            </div>

            <button
                type="submit"
                form="systemSettingsForm"
                class="btn btn-dark rounded-3 px-4"
            >
                Save Changes
            </button>

        </div>

        <form
            id="systemSettingsForm"
            method="POST"
            action="{{ route('sa.system-settings.update') }}"
        >

            @csrf
            @method('PUT')

            <div class="row g-3">

                <div class="col-xl-8">

                    <div class="settings-card mb-3">

                        <div class="settings-title">
                            GSM Modem Configuration
                        </div>

                        <div class="row g-3">

                            <div class="col-md-4">

                                <label class="form-label">
                                    COM Port
                                </label>

                                <input
                                    type="text"
                                    name="port_com"
                                    class="form-control"
                                    value="{{ $settings->port_com }}"
                                    placeholder="COM3"
                                >

                            </div>

                            <div class="col-md-4">

                                <label class="form-label">
                                    GSM Enabled
                                </label>

                                <div class="form-switch-wrapper">

                                    <div class="form-check form-switch m-0">

                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            name="gsm_enabled"
                                            value="1"
                                            {{ $settings->gsm_enabled ? 'checked' : '' }}
                                        >

                                    </div>

                                </div>

                            </div>

                            <div class="col-md-4">

                                <label class="form-label">
                                    SMS Enabled
                                </label>

                                <div class="form-switch-wrapper">

                                    <div class="form-check form-switch m-0">

                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            name="sms_enabled"
                                            value="1"
                                            {{ $settings->sms_enabled ? 'checked' : '' }}
                                        >

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="settings-card">

                        <div class="settings-title">
                            Runtime Environment
                        </div>

                        <div class="row g-3">

                            <div class="col-md-12">

                                <label class="form-label">
                                    Python Path
                                </label>

                                <input
                                    type="text"
                                    name="python_path"
                                    class="form-control"
                                    value="{{ $settings->python_path }}"
                                    placeholder="C:\Python313\python.exe"
                                >

                            </div>

                            <div class="col-md-12">

                                <label class="form-label">
                                    CA Certificate Path
                                </label>

                                <input
                                    type="text"
                                    name="cacert_path"
                                    class="form-control"
                                    value="{{ $settings->cacert_path }}"
                                    placeholder="C:\wamp64\bin\php\cacert.pem"
                                >

                            </div>

                        </div>

                    </div>

                </div>

                <div class="col-xl-4">

                    <div class="settings-card mb-3">

                        <div class="settings-title">
                            SMS Health Monitoring
                        </div>

                        <div class="metric-item">

                            <div>
                                <div class="metric-label">
                                    Total SMS Sent
                                </div>

                                <div class="metric-value">
                                    {{ number_format($settings->total_sent) }}
                                </div>
                            </div>

                            <div class="metric-icon success">
                                <i class="bi bi-send-check"></i>
                            </div>

                        </div>

                        <div class="metric-item">

                            <div>
                                <div class="metric-label">
                                    Failed SMS
                                </div>

                                <div class="metric-value text-danger">
                                    {{ number_format($settings->sms_failed_count) }}
                                </div>
                            </div>

                            <div class="metric-icon danger">
                                <i class="bi bi-x-octagon"></i>
                            </div>

                        </div>

                        <div class="metric-item">

                            <div>
                                <div class="metric-label">
                                    Low Balance Alerts
                                </div>

                                <div class="metric-value text-warning">
                                    {{ number_format($settings->sms_low_balance) }}
                                </div>
                            </div>

                            <div class="metric-icon warning">
                                <i class="bi bi-exclamation-triangle"></i>
                            </div>

                        </div>

                        <div class="metric-item border-0 pb-0 mb-0">

                            <div>
                                <div class="metric-label">
                                    Last Failed Attempt
                                </div>

                                <div class="small text-muted">
                                    {{ $settings->sms_last_failed_at?->format('M d, Y h:i A') ?? 'No failures detected' }}
                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="settings-card">

                        <div class="settings-title">
                            System Information
                        </div>

                        <div class="system-info-item">

                        <span>
                            Environment
                        </span>

                            <strong>
                                {{ strtoupper(app()->environment()) }}
                            </strong>

                        </div>

                        <div class="system-info-item">

                        <span>
                            Laravel Version
                        </span>

                            <strong>
                                {{ app()->version() }}
                            </strong>

                        </div>

                        <div class="system-info-item border-0 pb-0">

                        <span>
                            Timezone
                        </span>

                            <strong>
                                {{ config('app.timezone') }}
                            </strong>

                        </div>

                    </div>

                </div>

            </div>

        </form>

    </div>

    <style>

        .settings-card{
            background:#fff;
            border:1px solid #eef1f4;
            border-radius:22px;
            padding:22px;
            box-shadow:
                0 2px 10px rgba(15,23,42,.03);
        }

        .settings-title{
            font-size:.95rem;
            font-weight:700;
            color:#0f172a;
            margin-bottom:18px;
        }

        .form-label{
            font-size:.76rem;
            font-weight:700;
            color:#64748b;
            margin-bottom:.45rem;
            text-transform:uppercase;
            letter-spacing:.03em;
        }

        .form-control,
        .form-select{
            min-height:46px;
            border-radius:14px;
            border:1px solid #dbe2ea;
            font-size:.88rem;
            box-shadow:none;
        }

        .form-control:focus,
        .form-select:focus{
            border-color:#cbd5e1;
            box-shadow:none;
        }

        .form-switch-wrapper{
            height:46px;
            display:flex;
            align-items:center;
        }

        .metric-item{
            display:flex;
            align-items:center;
            justify-content:space-between;
            border-bottom:1px solid #eef1f4;
            padding-bottom:16px;
            margin-bottom:16px;
        }

        .metric-label{
            font-size:.72rem;
            font-weight:700;
            color:#94a3b8;
            text-transform:uppercase;
            letter-spacing:.05em;
            margin-bottom:5px;
        }

        .metric-value{
            font-size:1.45rem;
            font-weight:700;
            color:#0f172a;
        }

        .metric-icon{
            width:44px;
            height:44px;
            border-radius:14px;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:1rem;
        }

        .metric-icon.success{
            background:#ecfdf5;
            color:#059669;
        }

        .metric-icon.danger{
            background:#fef2f2;
            color:#dc2626;
        }

        .metric-icon.warning{
            background:#fffbeb;
            color:#d97706;
        }

        .system-info-item{
            display:flex;
            align-items:center;
            justify-content:space-between;
            padding-bottom:14px;
            margin-bottom:14px;
            border-bottom:1px solid #eef1f4;
            font-size:.88rem;
        }

        @media(max-width:768px){

            .settings-card{
                padding:18px;
            }

        }

    </style>

@endsection
