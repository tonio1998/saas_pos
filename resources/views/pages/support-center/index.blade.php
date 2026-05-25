@extends('layouts.sa')

@section('content')

    <div class="container-fluid py-3">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h3 class="fw-bold mb-1">
                    Support Center
                </h3>

                <div class="text-muted small">
                    Enterprise issue and incident management
                </div>
            </div>

            <button
                class="btn btn-dark rounded-3"
                data-bs-toggle="modal"
                data-bs-target="#ticketModal"
            >
                Create Ticket
            </button>

        </div>

        <div class="row g-3 mb-4">

            <div class="col-xl-3 col-md-6">

                <div class="metric-card">

                    <div class="metric-label">
                        Open Tickets
                    </div>

                    <div class="metric-value">
                        {{ \App\Models\SupportTicket::where('status', 'open')->count() }}
                    </div>

                </div>

            </div>

            <div class="col-xl-3 col-md-6">

                <div class="metric-card">

                    <div class="metric-label">
                        In Progress
                    </div>

                    <div class="metric-value text-primary">
                        {{ \App\Models\SupportTicket::where('status', 'in_progress')->count() }}
                    </div>

                </div>

            </div>

            <div class="col-xl-3 col-md-6">

                <div class="metric-card">

                    <div class="metric-label">
                        Resolved
                    </div>

                    <div class="metric-value text-success">
                        {{ \App\Models\SupportTicket::where('status', 'resolved')->count() }}
                    </div>

                </div>

            </div>

            <div class="col-xl-3 col-md-6">

                <div class="metric-card">

                    <div class="metric-label">
                        Critical
                    </div>

                    <div class="metric-value text-danger">
                        {{ \App\Models\SupportTicket::where('priority', 'critical')->count() }}
                    </div>

                </div>

            </div>

        </div>

        <div class="support-card">

            <div class="table-responsive">

                <table
                    class="table align-middle"
                    id="ticketTable"
                    width="100%"
                >

                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Ticket #</th>
                        <th>Subject</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th></th>
                    </tr>
                    </thead>

                </table>

            </div>

        </div>

    </div>

    <div
        class="modal fade"
        id="ticketModal"
        tabindex="-1"
    >

        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">

            <form
                method="POST"
                action="{{ route('support-center.store') }}"
                class="modal-content ticket-modal border-0"
            >

                @csrf

                <div class="modal-header ticket-modal-header border-0">

                    <div>

                        <div class="ticket-modal-subtitle">
                            SAFETRACK Support Desk
                        </div>

                        <h4 class="fw-bold mb-0">
                            Create Support Ticket
                        </h4>

                    </div>

                    <button
                        type="button"
                        class="btn-close shadow-none"
                        data-bs-dismiss="modal"
                    ></button>

                </div>

                <div class="modal-body pt-0">

                    <div class="ticket-hero mb-4">

                        <div class="ticket-hero-icon">
                            <i class="bi bi-shield-check"></i>
                        </div>

                        <div>

                            <div class="ticket-hero-title">
                                Report SAFETRACK System Concern
                            </div>

                            <div class="ticket-hero-text">
                                Submit technical issues, module concerns, access requests, incidents, or operational problems.
                            </div>

                        </div>

                    </div>

                    <div class="row g-4">

                        <div class="col-lg-8">

                            <div class="ticket-section">

                                <div class="ticket-section-title">
                                    Ticket Information
                                </div>

                                <div class="row g-3">

                                    <div class="col-12">

                                        <label class="form-label">
                                            Subject
                                        </label>

                                        <input
                                            type="text"
                                            name="subject"
                                            class="form-control form-control-modern"
                                            placeholder="Example: QR Attendance Scanner not syncing"
                                            required
                                        >

                                    </div>

                                    <div class="col-md-6">

                                        <label class="form-label">
                                            Priority Level
                                        </label>

                                        <select
                                            name="priority"
                                            class="form-select form-control-modern"
                                        >
                                            <option value="low">
                                                Low Priority
                                            </option>

                                            <option value="medium" selected>
                                                Medium Priority
                                            </option>

                                            <option value="high">
                                                High Priority
                                            </option>

                                            <option value="critical">
                                                Critical Issue
                                            </option>
                                        </select>

                                    </div>

                                    <div class="col-md-6">

                                        <label class="form-label">
                                            SAFETRACK Module
                                        </label>

                                        <select
                                            name="category"
                                            class="form-select form-control-modern"
                                        >

                                            <option value="">
                                                Select Module
                                            </option>
                                            <option value="dashboard">
                                                Dashboard & Analytics
                                            </option>
                                            <option value="attendance">
                                                Attendance Monitoring
                                            </option>
                                            <option value="qr_nfc">
                                                QR & NFC Scanning
                                            </option>
                                            <option value="student_management">
                                                Student Management
                                            </option>
                                            <option value="parent_portal">
                                                Parent Portal
                                            </option>
                                            <option value="sms_notifications">
                                                SMS Notifications
                                            </option>
                                            <option value="reports">
                                                Reports & Exports
                                            </option>
                                            <option value="account_access">
                                                Accounts & Permissions
                                            </option>
                                            <option value="system_settings">
                                                System Settings
                                            </option>
                                            <option value="others">
                                                Others
                                            </option>

                                        </select>

                                    </div>

                                    <div class="col-12">

                                        <label class="form-label">
                                            Issue Description
                                        </label>

                                        <textarea
                                            name="description"
                                            rows="7"
                                            class="form-control form-control-modern"
                                            placeholder="Describe the issue, affected users, error messages, device used, or steps to reproduce..."
                                            required
                                        ></textarea>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="col-lg-4">

                            <div class="ticket-side-card">

                                <div class="ticket-side-title">
                                    Support Guidelines
                                </div>

                                <div class="ticket-guide-list">

                                    <div class="ticket-guide-item">

                                        <div class="ticket-guide-icon">
                                            <i class="bi bi-check-circle"></i>
                                        </div>

                                        <div>
                                            Include exact error messages if available.
                                        </div>

                                    </div>

                                    <div class="ticket-guide-item">

                                        <div class="ticket-guide-icon">
                                            <i class="bi bi-check-circle"></i>
                                        </div>

                                        <div>
                                            Specify affected SAFETRACK module.
                                        </div>

                                    </div>

                                    <div class="ticket-guide-item">

                                        <div class="ticket-guide-icon">
                                            <i class="bi bi-check-circle"></i>
                                        </div>

                                        <div>
                                            Critical incidents are prioritized automatically.
                                        </div>

                                    </div>

                                    <div class="ticket-guide-item">

                                        <div class="ticket-guide-icon">
                                            <i class="bi bi-check-circle"></i>
                                        </div>

                                        <div>
                                            Add reproduction steps for technical bugs.
                                        </div>

                                    </div>

                                </div>

                            </div>

                            <div class="ticket-priority-card mt-3">

                                <div class="ticket-priority-title">
                                    Priority Legend
                                </div>

                                <div class="priority-item">
                                    <span class="priority-dot low"></span>
                                    Low — Minor issue
                                </div>

                                <div class="priority-item">
                                    <span class="priority-dot medium"></span>
                                    Medium — Normal support concern
                                </div>

                                <div class="priority-item">
                                    <span class="priority-dot high"></span>
                                    High — Operational disruption
                                </div>

                                <div class="priority-item">
                                    <span class="priority-dot critical"></span>
                                    Critical — System downtime/security issue
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="modal-footer border-0 pt-0">

                    <button
                        type="button"
                        class="btn btn-light px-4 rounded-4"
                        data-bs-dismiss="modal"
                    >
                        Cancel
                    </button>

                    <button
                        class="btn btn-dark px-4 rounded-4 d-flex align-items-center gap-2"
                    >
                        <i class="bi bi-send"></i>
                        Submit Ticket
                    </button>

                </div>

            </form>

        </div>

    </div>

    <style>
        .ticket-modal{
            border-radius:28px;
            overflow:hidden;
            background:#fff;
            box-shadow:
                0 30px 60px rgba(15,23,42,.14);
        }

        .ticket-modal-header{
            padding:26px 28px 10px;
        }

        .ticket-modal-subtitle{
            font-size:.72rem;
            font-weight:700;
            letter-spacing:.08em;
            text-transform:uppercase;
            color:#64748b;
            margin-bottom:6px;
        }

        .ticket-hero{
            display:flex;
            align-items:center;
            gap:18px;
            padding:18px;
            border-radius:22px;
            background:
                linear-gradient(
                    135deg,
                    #0f172a 0%,
                    #1e293b 100%
                );
            color:#fff;
        }

        .ticket-hero-icon{
            width:58px;
            height:58px;
            border-radius:18px;
            display:flex;
            align-items:center;
            justify-content:center;
            background:rgba(255,255,255,.12);
            font-size:1.4rem;
        }

        .ticket-hero-title{
            font-size:1rem;
            font-weight:700;
            margin-bottom:4px;
        }

        .ticket-hero-text{
            font-size:.86rem;
            opacity:.8;
        }

        .ticket-section{
            background:#fff;
            border:1px solid #edf2f7;
            border-radius:24px;
            padding:22px;
        }

        .ticket-section-title{
            font-size:.9rem;
            font-weight:700;
            color:#0f172a;
            margin-bottom:18px;
        }

        .form-control-modern{
            min-height:52px;
            border-radius:16px;
            border:1px solid #dbe3ec;
            background:#f8fafc;
            padding-inline:16px;
            box-shadow:none!important;
            transition:.2s ease;
        }

        textarea.form-control-modern{
            min-height:auto;
            padding-top:14px;
        }

        .form-control-modern:focus{
            background:#fff;
            border-color:#0f172a;
        }

        .ticket-side-card,
        .ticket-priority-card{
            background:#fff;
            border:1px solid #edf2f7;
            border-radius:24px;
            padding:22px;
        }

        .ticket-side-title,
        .ticket-priority-title{
            font-size:.9rem;
            font-weight:700;
            margin-bottom:18px;
            color:#0f172a;
        }

        .ticket-guide-list{
            display:flex;
            flex-direction:column;
            gap:14px;
        }

        .ticket-guide-item{
            display:flex;
            align-items:flex-start;
            gap:12px;
            font-size:.88rem;
            color:#475569;
        }

        .ticket-guide-icon{
            width:28px;
            height:28px;
            border-radius:10px;
            background:#ecfdf5;
            color:#16a34a;
            display:flex;
            align-items:center;
            justify-content:center;
            flex-shrink:0;
        }

        .priority-item{
            display:flex;
            align-items:center;
            gap:10px;
            font-size:.87rem;
            color:#475569;
            margin-bottom:14px;
        }

        .priority-dot{
            width:10px;
            height:10px;
            border-radius:50%;
        }

        .priority-dot.low{
            background:#22c55e;
        }

        .priority-dot.medium{
            background:#f59e0b;
        }

        .priority-dot.high{
            background:#f97316;
        }

        .priority-dot.critical{
            background:#ef4444;
        }

        @media(max-width:991px){

            .ticket-modal-header{
                padding:22px 20px 10px;
            }

            .ticket-section,
            .ticket-side-card,
            .ticket-priority-card{
                border-radius:20px;
                padding:18px;
            }

            .ticket-hero{
                align-items:flex-start;
            }
        }
        .metric-card{
            background:#fff;
            border-radius:20px;
            border:1px solid #eef1f4;
            padding:20px;
            box-shadow:
                0 2px 10px rgba(15,23,42,.03);
        }

        .metric-label{
            font-size:.75rem;
            font-weight:700;
            color:#94a3b8;
            text-transform:uppercase;
            letter-spacing:.05em;
            margin-bottom:8px;
        }

        .metric-value{
            font-size:1.7rem;
            font-weight:700;
            color:#0f172a;
        }
        .support-card{
            background:#fff;
            border-radius:22px;
            border:1px solid #eef1f4;
            padding:20px;
            box-shadow:
                0 2px 10px rgba(15,23,42,.03);
        }

        .form-control,
        .form-select{
            min-height:46px;
            border-radius:14px;
            border:1px solid #dbe2ea;
            box-shadow:none;
        }

        .table thead th{
            font-size:.78rem;
            color:#64748b;
            border-bottom:1px solid #eef1f4;
        }

        .table tbody td{
            vertical-align:middle;
            border-color:#f1f5f9;
        }

    </style>

    <script>

        document.addEventListener('DOMContentLoaded', () => {

            $('#ticketTable').DataTable({

                processing:true,

                serverSide:true,

                ajax:
                    '{{ route('support-center.datatable') }}',

                columns:[
                    {
                        data:'DT_RowIndex',
                        orderable:false,
                        searchable:false
                    },
                    {data:'ticket_no'},
                    {data:'subject'},
                    {data:'priority_badge'},
                    {data:'status_badge'},
                    {data:'created_at'},
                    {data:'action'},
                ]
            });
        });

    </script>

@endsection
