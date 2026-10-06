@extends('layouts.sa')

@section('title', 'Subscription Due Date & Renewal Monitoring — SuperAdmin Console')

@section('content')
<div class="container-fluid py-2 px-3">

    {{-- ── Page Header ────────────────────────────────────── --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-danger text-white fw-bold px-2 py-0.5 rounded-pill" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                    <i class="bi bi-alarm-fill me-1"></i> DUE MONITORING SENTINEL
                </span>
                <span class="text-muted extra-small">Platform Revenue, Retention &amp; Automated Email Reminders</span>
            </div>
            <h3 class="fw-black text-dark font-mono mb-1" style="font-size: 1.6rem; letter-spacing: -0.02em;">
                Tenant Subscription Expiration &amp; Renewal Sentinel
            </h3>
            <p class="text-muted small mb-0">
                Monitor stores nearing billing cut-offs, dispatch automated email renewal invoices, track 7-day sales velocity, and manage grace periods.
            </p>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            <!-- Automated Engine Trigger -->
            <button type="button" class="btn btn-danger btn-sm rounded-pill fw-bold px-3 shadow-xs d-flex align-items-center gap-1.5" id="btnRunAutomatedEngine">
                <i class="bi bi-lightning-charge-fill"></i>
                <span>Run Automated Email Reminders</span>
            </button>

            <!-- CSV Export -->
            <a href="{{ route('sa.subscriptions.monitoring.export-csv') }}" class="btn btn-outline-dark btn-sm rounded-pill fw-bold px-3 shadow-xs d-flex align-items-center gap-1.5" id="btnExportCsv">
                <i class="bi bi-file-earmark-arrow-down-fill text-success"></i>
                <span>Export Due List (CSV)</span>
            </a>

            <!-- Verifications Queue Link -->
            <a href="{{ route('sa.subscriptions.verifications') }}" class="btn btn-outline-warning btn-sm rounded-pill fw-bold px-3 shadow-xs text-dark d-flex align-items-center gap-1.5" style="background:#fffbeb; border:1px solid #f59e0b;">
                <i class="bi bi-patch-check-fill text-warning"></i>
                <span>Verifications Queue</span>
                @if(($metrics['pending_verifs'] ?? 0) > 0)
                    <span class="badge bg-danger rounded-pill px-1.5 py-0.5" style="font-size: 0.68rem;">{{ $metrics['pending_verifs'] }}</span>
                @endif
            </a>

            <!-- Refresh Button -->
            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill fw-bold px-3 shadow-xs d-flex align-items-center gap-1.5" id="btnRefreshMonitoring">
                <i class="bi bi-arrow-clockwise"></i>
                <span>Refresh</span>
            </button>
        </div>
    </div>

    {{-- ── Due Watchlist Alert Banner (if critical accounts exist) ── --}}
    @if(($metrics['due_in_3_days'] + $metrics['overdue_expired']) > 0)
    <div class="alert border-0 rounded-4 shadow-xs p-3.5 mb-4 position-relative overflow-hidden" 
         style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.12) 0%, rgba(245, 158, 11, 0.12) 100%); border-left: 5px solid #ef4444 !important;">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle p-2.5 bg-danger text-white d-flex align-items-center justify-content-center shadow-xs" style="width: 44px; height: 44px;">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                </div>
                <div>
                    <h6 class="fw-bold text-dark font-mono mb-0.5">
                        Urgent Action Required: {{ $metrics['due_in_3_days'] }} store(s) due within 3 days &amp; {{ $metrics['overdue_expired'] }} overdue!
                    </h6>
                    <p class="text-muted small mb-0">
                        Follow up with store owners or dispatch automated email reminders with invoice details &amp; QRPH instructions to maintain POS service continuity.
                    </p>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-danger btn-sm rounded-pill fw-bold px-3 py-1.5 btn-quick-filter" data-filter="critical_3_days">
                    <i class="bi bi-fire me-1"></i> View Urgent (&le; 3 Days)
                </button>
                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill fw-bold px-3 py-1.5 btn-quick-filter" data-filter="overdue">
                    <i class="bi bi-slash-circle me-1"></i> View Overdue
                </button>
                <button type="button" class="btn btn-dark btn-sm rounded-pill fw-bold px-3 py-1.5" id="btnRemindAllCritical">
                    <i class="bi bi-envelope-check-fill text-warning me-1"></i> Email All Urgent Now
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ── KPI Cards ──────────────────────────────────────── --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-2">
            <div class="card border-0 rounded-4 shadow-xs p-3 h-100 bg-white text-center cursor-pointer kpi-card" data-filter="critical_3_days">
                <div class="text-muted extra-small font-mono fw-bold text-uppercase mb-1" style="font-size: 0.68rem;">Due in 3 Days</div>
                <div class="fs-4 fw-black text-danger font-mono mb-1">{{ number_format($metrics['due_in_3_days']) }}</div>
                <span class="badge bg-danger-subtle text-danger rounded-pill px-2 py-0.5 extra-small fw-bold">Critical</span>
            </div>
        </div>

        <div class="col-6 col-lg-2">
            <div class="card border-0 rounded-4 shadow-xs p-3 h-100 bg-white text-center cursor-pointer kpi-card" data-filter="due_7_days">
                <div class="text-muted extra-small font-mono fw-bold text-uppercase mb-1" style="font-size: 0.68rem;">Due in 7 Days</div>
                <div class="fs-4 fw-black text-warning-emphasis font-mono mb-1">{{ number_format($metrics['due_in_7_days']) }}</div>
                <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-2 py-0.5 extra-small fw-bold">Upcoming</span>
            </div>
        </div>

        <div class="col-6 col-lg-2">
            <div class="card border-0 rounded-4 shadow-xs p-3 h-100 bg-white text-center cursor-pointer kpi-card" data-filter="overdue">
                <div class="text-muted extra-small font-mono fw-bold text-uppercase mb-1" style="font-size: 0.68rem;">Overdue / Expired</div>
                <div class="fs-4 fw-black text-danger font-mono mb-1">{{ number_format($metrics['overdue_expired']) }}</div>
                <span class="badge bg-danger-subtle text-danger rounded-pill px-2 py-0.5 extra-small fw-bold">Action Needed</span>
            </div>
        </div>

        <div class="col-6 col-lg-2">
            <div class="card border-0 rounded-4 shadow-xs p-3 h-100 bg-white text-center cursor-pointer kpi-card" data-filter="trial">
                <div class="text-muted extra-small font-mono fw-bold text-uppercase mb-1" style="font-size: 0.68rem;">Trial Expiring</div>
                <div class="fs-4 fw-black text-info-emphasis font-mono mb-1">{{ number_format($metrics['trial_expiring']) }}</div>
                <span class="badge bg-info-subtle text-info-emphasis rounded-pill px-2 py-0.5 extra-small fw-bold">Free Trial</span>
            </div>
        </div>

        <div class="col-6 col-lg-2">
            <div class="card border-0 rounded-4 shadow-xs p-3 h-100 bg-white text-center cursor-pointer kpi-card" data-filter="not_reminded">
                <div class="text-muted extra-small font-mono fw-bold text-uppercase mb-1" style="font-size: 0.68rem;">Reminded Today</div>
                <div class="fs-4 fw-black text-primary font-mono mb-1">{{ number_format($metrics['reminded_today'] ?? 0) }}</div>
                <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-0.5 extra-small fw-bold">Engaged</span>
            </div>
        </div>

        <div class="col-6 col-lg-2">
            <div class="card border-0 rounded-4 shadow-xs p-3 h-100 bg-white text-center cursor-pointer kpi-card" data-filter="healthy">
                <div class="text-muted extra-small font-mono fw-bold text-uppercase mb-1" style="font-size: 0.68rem;">Healthy / Active</div>
                <div class="fs-4 fw-black text-success font-mono mb-1">{{ number_format($metrics['healthy_active']) }}</div>
                <span class="badge bg-success-subtle text-success rounded-pill px-2 py-0.5 extra-small fw-bold">&gt; 7 Days Left</span>
            </div>
        </div>
    </div>

    {{-- ── Main Monitoring Sentinel Card ──────────────────── --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
        
        {{-- Card Header & Filter Nav --}}
        <div class="card-header bg-white border-bottom p-3.5">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <!-- Urgency Filters -->
                <div class="d-flex flex-wrap align-items-center gap-1.5" id="urgencyFilterGroup">
                    <button type="button" class="btn btn-sm rounded-pill px-3 py-1 fw-bold filter-btn active" data-filter="all">
                        All Stores ({{ $metrics['total_tenants'] }})
                    </button>
                    <button type="button" class="btn btn-sm rounded-pill px-3 py-1 fw-bold filter-btn" data-filter="critical_3_days">
                        <i class="bi bi-fire text-danger me-1"></i> Due in 3 Days ({{ $metrics['due_in_3_days'] }})
                    </button>
                    <button type="button" class="btn btn-sm rounded-pill px-3 py-1 fw-bold filter-btn" data-filter="due_7_days">
                        <i class="bi bi-clock-history text-warning me-1"></i> Due in 7 Days ({{ $metrics['due_in_7_days'] }})
                    </button>
                    <button type="button" class="btn btn-sm rounded-pill px-3 py-1 fw-bold filter-btn" data-filter="overdue">
                        <i class="bi bi-x-octagon text-danger me-1"></i> Overdue ({{ $metrics['overdue_expired'] }})
                    </button>
                    <button type="button" class="btn btn-sm rounded-pill px-3 py-1 fw-bold filter-btn" data-filter="not_reminded">
                        <i class="bi bi-envelope-dash text-secondary me-1"></i> Not Reminded Yet
                    </button>
                    <button type="button" class="btn btn-sm rounded-pill px-3 py-1 fw-bold filter-btn" data-filter="trial">
                        <i class="bi bi-stars text-info me-1"></i> Free Trial ({{ $metrics['trial_expiring'] }})
                    </button>
                    <button type="button" class="btn btn-sm rounded-pill px-3 py-1 fw-bold filter-btn" data-filter="healthy">
                        <i class="bi bi-shield-check text-success me-1"></i> Healthy ({{ $metrics['healthy_active'] }})
                    </button>
                </div>

                <!-- Bulk Selection Toolbar -->
                <div class="d-none align-items-center gap-2" id="bulkActionsToolbar">
                    <span class="badge bg-dark text-white rounded-pill px-2.5 py-1 extra-small">
                        <span id="selectedCount">0</span> selected
                    </span>
                    <button type="button" class="btn btn-danger btn-sm rounded-pill fw-bold px-3 py-1 extra-small shadow-xs" id="btnBulkSendEmail">
                        <i class="bi bi-envelope-paper-heart-fill me-1"></i> Send Renewal Emails
                    </button>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 font-sans" id="dueMonitoringTable" style="width: 100%;">
                <thead class="table-light text-uppercase extra-small text-muted font-mono fw-bold">
                    <tr>
                        <th style="width: 32px;" class="ps-3.5 pe-1">
                            <input type="checkbox" class="form-check-input" id="checkSelectAll" title="Select All Stores">
                        </th>
                        <th>Store Tenant</th>
                        <th>Owner &amp; Contacts</th>
                        <th>Plan &amp; Billing</th>
                        <th>Due Date Sentinel</th>
                        <th>7-Day POS Activity</th>
                        <th>Reminder Status</th>
                        <th class="pe-3.5 text-end" style="width: 80px;">Action</th>
                    </tr>
                </thead>
                <tbody class="text-dark small">
                    {{-- Loaded via AJAX DataTables --}}
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- ── MODAL: SEND SINGLE EMAIL RENEWAL REMINDER ──────────── --}}
<div class="modal fade" id="modalSendEmailReminder" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white border-0 py-3 px-4 d-flex align-items-center justify-content-between" style="background:#0f172a !important;">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 p-1.5 bg-danger text-white">
                        <i class="bi bi-envelope-paper-heart-fill fs-5"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-white mb-0" id="emailModalStoreTitle">Send Renewal Notice Email</h6>
                        <span class="extra-small text-white-50">Automated HTML invoice &amp; payment instructions</span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button>
            </div>

            <form id="formSendEmailReminder">
                @csrf
                <input type="hidden" id="emailTenantId" name="tenant_id">
                <div class="modal-body p-4 bg-light">
                    <div class="p-3 bg-white rounded-3 border mb-3">
                        <div class="d-flex justify-content-between extra-small text-muted mb-1">
                            <span>Store Name:</span>
                            <strong class="text-dark" id="emailStoreName"></strong>
                        </div>
                        <div class="d-flex justify-content-between extra-small text-muted mb-1">
                            <span>Current Plan:</span>
                            <span class="fw-bold text-dark font-mono" id="emailStorePlan"></span>
                        </div>
                        <div class="d-flex justify-content-between extra-small text-muted">
                            <span>Due Date / Urgency:</span>
                            <span class="fw-bold text-danger font-mono" id="emailStoreDue"></span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label extra-small font-mono fw-bold text-uppercase text-dark">Recipient Email Address <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted"><i class="bi bi-envelope-at-fill"></i></span>
                            <input type="email" name="email" id="emailRecipientInput" class="form-control" required placeholder="owner@store.com">
                        </div>
                        <div class="extra-small text-muted mt-1">Pre-filled with registered store or owner account email.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label extra-small font-mono fw-bold text-uppercase text-dark">Custom Admin Note (Optional)</label>
                        <textarea name="custom_note" id="emailCustomNote" class="form-control" rows="2" placeholder="e.g. As discussed on call, please upload GCash receipt once completed to reactivate full terminal access..."></textarea>
                    </div>

                    <div class="alert alert-info py-2 px-3 rounded-3 extra-small mb-0 border-0 bg-info-subtle text-info-emphasis">
                        <i class="bi bi-info-circle-fill me-1"></i>
                        Email includes responsive invoice breakdown, QRPH/GCash/Maya payment details, direct receipt upload link, and 24/7 hotline <strong>0912 894 1731</strong>.
                    </div>
                </div>

                <div class="modal-footer bg-white border-top py-2.5 px-4 d-flex justify-content-between">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill fw-bold px-4" id="btnSubmitSendEmail">
                        <i class="bi bi-send-fill me-1"></i> Dispatch Renewal Email
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── MODAL: SEND SMS / WHATSAPP NOTICE ──────────────────── --}}
<div class="modal fade" id="modalSendReminder" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white border-0 py-3 px-4 d-flex align-items-center justify-content-between" style="background:#0f172a !important;">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 p-1.5 bg-warning text-dark">
                        <i class="bi bi-chat-dots-fill fs-5"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-white mb-0" id="reminderStoreTitle">Renewal Notice Dispatcher</h6>
                        <span class="extra-small text-white-50">Quick messaging via SMS, WhatsApp &amp; Clipboard</span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4 bg-light">
                <div class="mb-3">
                    <label class="form-label extra-small font-mono fw-bold text-uppercase text-dark">Generated Renewal Notice Message</label>
                    <textarea class="form-control font-mono extra-small" id="reminderMessageText" rows="9" style="font-size: 0.8rem; line-height: 1.4;"></textarea>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-outline-dark btn-sm rounded-pill fw-bold flex-grow-1" id="btnCopyReminderText">
                        <i class="bi bi-clipboard me-1"></i> Copy Message
                    </button>
                    <a href="#" target="_blank" class="btn btn-success btn-sm rounded-pill fw-bold flex-grow-1" id="reminderWaLink">
                        <i class="bi bi-whatsapp me-1"></i> Open WhatsApp
                    </a>
                    <a href="#" class="btn btn-primary btn-sm rounded-pill fw-bold flex-grow-1" id="reminderSmsLink">
                        <i class="bi bi-chat-text-fill me-1"></i> Direct SMS
                    </a>
                </div>
            </div>

            <div class="modal-footer bg-white border-top py-2.5 px-4">
                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- ── MODAL: EXTEND DUE / GRANT GRACE PERIOD ────────────── --}}
<div class="modal fade" id="modalExtendDue" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white border-0 py-3 px-4 d-flex align-items-center justify-content-between" style="background:#0f172a !important;">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 p-1.5 bg-primary text-white">
                        <i class="bi bi-calendar-plus-fill fs-5"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-white mb-0">Grant Grace Period / Extend Due</h6>
                        <span class="extra-small text-white-50" id="extendStoreHeader">Store Name</span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button>
            </div>

            <form id="formExtendDue">
                @csrf
                <input type="hidden" id="extendTenantId">
                <div class="modal-body p-4 bg-light">
                    <div class="alert alert-info py-2 px-3 rounded-3 extra-small mb-3">
                        <i class="bi bi-info-circle-fill me-1"></i>
                        Current Due Date: <strong id="extendCurrentDue" class="text-dark"></strong>
                    </div>

                    <div class="mb-3">
                        <label class="form-label extra-small font-mono fw-bold text-uppercase">Extension Option <span class="text-danger">*</span></label>
                        <div class="d-grid gap-2">
                            <div class="form-check p-2.5 border rounded-3 d-flex align-items-center justify-content-between bg-white">
                                <div>
                                    <input class="form-check-input ms-0 me-2" type="radio" name="extension_type" id="extGrace3" value="days" checked>
                                    <label class="form-check-label fw-bold small" for="extGrace3">
                                        +3 Days Grace Period (Urgent follow-up)
                                    </label>
                                </div>
                                <span class="badge bg-warning-subtle text-warning-emphasis">+3 Days</span>
                            </div>

                            <div class="form-check p-2.5 border rounded-3 d-flex align-items-center justify-content-between bg-white">
                                <div>
                                    <input class="form-check-input ms-0 me-2" type="radio" name="extension_type" id="extGrace7" value="days">
                                    <label class="form-check-label fw-bold small" for="extGrace7">
                                        +7 Days Grace Period (Standard grace)
                                    </label>
                                </div>
                                <span class="badge bg-info-subtle text-info-emphasis">+7 Days</span>
                            </div>

                            <div class="form-check p-2.5 border rounded-3 d-flex align-items-center justify-content-between bg-white">
                                <div>
                                    <input class="form-check-input ms-0 me-2" type="radio" name="extension_type" id="extMonth30" value="days">
                                    <label class="form-check-label fw-bold small" for="extMonth30">
                                        +30 Days Full Month Renewal
                                    </label>
                                </div>
                                <span class="badge bg-success-subtle text-success">+30 Days</span>
                            </div>

                            <div class="form-check p-2.5 border rounded-3 bg-white">
                                <input class="form-check-input ms-0 me-2" type="radio" name="extension_type" id="extCustom" value="custom_date">
                                <label class="form-check-label fw-bold small" for="extCustom">
                                    Custom Specific Date
                                </label>
                                <div class="mt-2 d-none" id="customDateWrap">
                                    <input type="date" name="custom_date" id="customDateInput" class="form-control form-control-sm rounded-2 font-mono">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label extra-small font-mono fw-bold text-uppercase">Admin Remarks / Note</label>
                        <input type="text" name="remarks" class="form-control rounded-3 form-control-sm" placeholder="e.g. Granted upon call with owner, paying tomorrow via GCash">
                    </div>
                </div>

                <div class="modal-footer bg-white border-top py-2.5 px-4 d-flex justify-content-between">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill fw-bold px-4" style="background:#059669; border:none;">
                        <i class="bi bi-check-circle-fill me-1"></i> Apply Extension
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    let currentFilter = 'all';
    const tableEl = $('#dueMonitoringTable');

    const dataTable = tableEl.DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('sa.subscriptions.monitoring.data') }}",
            data: function (d) {
                d.urgency = currentFilter;
            }
        },
        columns: [
            { data: 'checkbox', orderable: false, searchable: false, className: 'ps-3.5 pe-1' },
            { data: 'store_info', name: 'business_name' },
            { data: 'owner_info', name: 'owner_name' },
            { data: 'plan_info', name: 'subscription.name' },
            { data: 'due_status', name: 'subscription_end' },
            { data: 'activity_health', orderable: false, searchable: false },
            { data: 'reminder_sentinel', orderable: false, searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'pe-3.5 text-end' }
        ],
        order: [[4, 'asc']],
        pageLength: 15,
        lengthMenu: [10, 15, 25, 50, 100],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search store name, code, owner, phone...",
            processing: '<div class="spinner-border text-danger spinner-border-sm me-2"></div> Checking subscription sentinel...',
            emptyTable: "No stores found matching the expiration criteria.",
        },
        drawCallback: function () {
            updateBulkToolbar();
            $('#checkSelectAll').prop('checked', false);
        }
    });

    // ── Filter Buttons ──────────────────────────────────────────
    $('.filter-btn, .kpi-card, .btn-quick-filter').on('click', function () {
        currentFilter = $(this).data('filter');
        $('.filter-btn').removeClass('active bg-dark text-white');
        $('.filter-btn[data-filter="' + currentFilter + '"]').addClass('active bg-dark text-white');
        dataTable.draw();
    });

    $('#btnRefreshMonitoring').on('click', function () {
        dataTable.ajax.reload(null, false);
    });

    // ── Multi-select Checkboxes ─────────────────────────────────
    $('#checkSelectAll').on('change', function () {
        const checked = $(this).is(':checked');
        $('.tenant-checkbox').prop('checked', checked);
        updateBulkToolbar();
    });

    $(document).on('change', '.tenant-checkbox', function () {
        updateBulkToolbar();
    });

    function updateBulkToolbar() {
        const selected = $('.tenant-checkbox:checked');
        const count = selected.length;
        $('#selectedCount').text(count);
        if (count > 0) {
            $('#bulkActionsToolbar').removeClass('d-none').addClass('d-flex');
        } else {
            $('#bulkActionsToolbar').addClass('d-none').removeClass('d-flex');
        }
    }

    // ── Single Email Reminder Modal Open ────────────────────────
    $(document).on('click', '.btn-quick-email', function () {
        const btn = $(this);
        const encId = btn.data('id');
        const name = btn.data('name');
        const email = btn.data('email') || '';
        const plan = btn.data('plan') || 'Standard Plan';
        const due = btn.data('due') || '';

        $('#emailTenantId').val(encId);
        $('#emailStoreName').text(name);
        $('#emailStorePlan').text(plan);
        $('#emailStoreDue').text(due);
        $('#emailRecipientInput').val(email);
        $('#emailCustomNote').val('');
        $('#emailModalStoreTitle').text('Send Renewal Notice: ' + name);

        $('#modalSendEmailReminder').modal('show');
    });

    // ── Submit Single Email Reminder ────────────────────────────
    $('#formSendEmailReminder').on('submit', function (e) {
        e.preventDefault();
        const encId = $('#emailTenantId').val();
        const submitBtn = $('#btnSubmitSendEmail');
        const url = "{{ route('sa.subscriptions.monitoring.send-email', ':id') }}".replace(':id', encId);

        submitBtn.prop('disabled', true).prepend('<span class="spinner-border spinner-border-sm me-1"></span>');

        $.ajax({
            url: url,
            type: "POST",
            data: $(this).serialize(),
            success: function (res) {
                $('#modalSendEmailReminder').modal('hide');
                dataTable.ajax.reload(null, false);
                Swal.fire({
                    icon: 'success',
                    title: 'Renewal Email Dispatched!',
                    text: res.message,
                    timer: 3500,
                    showConfirmButton: false
                });
            },
            error: function (xhr) {
                const err = xhr.responseJSON?.message || 'Failed to dispatch email reminder.';
                Swal.fire({ icon: 'error', title: 'Dispatch Error', text: err });
            },
            complete: function () {
                submitBtn.prop('disabled', false).find('.spinner-border').remove();
            }
        });
    });

    // ── Bulk Email Reminders ────────────────────────────────────
    $('#btnBulkSendEmail').on('click', function () {
        const selectedIds = [];
        $('.tenant-checkbox:checked').each(function () {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length === 0) return;

        Swal.fire({
            title: `Send Renewal Emails to ${selectedIds.length} Stores?`,
            text: 'Each store owner will receive an automated HTML renewal invoice with payment details and 24/7 hotline.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Send Emails',
            confirmButtonColor: '#ef4444',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Dispatching Emails...',
                    html: 'Please wait while emails are delivered.',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                $.ajax({
                    url: "{{ route('sa.subscriptions.monitoring.bulk-remind') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        tenant_ids: selectedIds,
                        target: 'selected'
                    },
                    success: function (res) {
                        dataTable.ajax.reload(null, false);
                        Swal.fire({
                            icon: 'success',
                            title: 'Bulk Dispatch Finished',
                            text: res.message,
                            timer: 3500,
                            showConfirmButton: false
                        });
                    },
                    error: function (xhr) {
                        const err = xhr.responseJSON?.message || 'Error occurred during bulk email dispatch.';
                        Swal.fire({ icon: 'error', title: 'Dispatch Error', text: err });
                    }
                });
            }
        });
    });

    // ── Remind All Critical Stores ──────────────────────────────
    $('#btnRemindAllCritical').on('click', function () {
        Swal.fire({
            title: 'Email All Urgent Accounts (&le; 3 Days & Overdue)?',
            text: 'This will automatically send renewal notices to all store owners in critical renewal state.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Dispatch to All Urgent',
            confirmButtonColor: '#0f172a',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Dispatching Urgent Notices...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                $.ajax({
                    url: "{{ route('sa.subscriptions.monitoring.bulk-remind') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        target: 'all_critical'
                    },
                    success: function (res) {
                        dataTable.ajax.reload(null, false);
                        Swal.fire({
                            icon: 'success',
                            title: 'Dispatched Successfully',
                            text: res.message
                        });
                    },
                    error: function (xhr) {
                        Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Failed to dispatch.' });
                    }
                });
            }
        });
    });

    // ── Run Automated Engine On Demand ──────────────────────────
    $('#btnRunAutomatedEngine').on('click', function () {
        const btn = $(this);
        btn.prop('disabled', true).prepend('<span class="spinner-border spinner-border-sm me-1"></span>');

        $.ajax({
            url: "{{ route('sa.subscriptions.monitoring.run-engine') }}",
            type: "POST",
            data: { _token: "{{ csrf_token() }}" },
            success: function (res) {
                dataTable.ajax.reload(null, false);
                Swal.fire({
                    icon: 'success',
                    title: 'Automated Sentinel Executed',
                    html: '<pre class="text-start bg-light p-3 rounded font-mono" style="font-size:0.75rem;">' + (res.output || res.message) + '</pre>',
                    customClass: { popup: 'swal2-lg' }
                });
            },
            error: function (xhr) {
                Swal.fire({ icon: 'error', title: 'Engine Error', text: xhr.responseJSON?.message || 'Execution failed.' });
            },
            complete: function () {
                btn.prop('disabled', false).find('.spinner-border').remove();
            }
        });
    });

    // ── Toggle Account Status (Suspend / Reactivate) ────────────
    $(document).on('click', '.btn-toggle-status', function () {
        const btn = $(this);
        const encId = btn.data('id');
        const action = btn.data('action');
        const name = btn.data('name');
        const actionText = action === 'suspend' ? 'suspend' : 'reactivate';

        Swal.fire({
            title: `${action === 'suspend' ? 'Suspend' : 'Reactivate'} Store?`,
            text: `Are you sure you want to ${actionText} '${name}'?`,
            icon: action === 'suspend' ? 'warning' : 'question',
            showCancelButton: true,
            confirmButtonText: `Yes, ${actionText}`,
            confirmButtonColor: action === 'suspend' ? '#ef4444' : '#059669',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const url = "{{ route('sa.subscriptions.monitoring.toggle-status', ':id') }}".replace(':id', encId);
                $.ajax({
                    url: url,
                    type: "POST",
                    data: { _token: "{{ csrf_token() }}", action: action },
                    success: function (res) {
                        dataTable.ajax.reload(null, false);
                        Swal.fire({ icon: 'success', title: 'Status Updated', text: res.message, timer: 2500, showConfirmButton: false });
                    },
                    error: function (xhr) {
                        Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Failed to update status.' });
                    }
                });
            }
        });
    });

    // ── SMS / WhatsApp Notice Modal ─────────────────────────────
    $(document).on('click', '.btn-send-reminder', function () {
        const btn = $(this);
        const name = btn.data('name');
        const owner = btn.data('owner');
        const phone = btn.data('phone') || '';
        const plan = btn.data('plan');
        const days = parseInt(btn.data('days'), 10);
        const due = btn.data('due');

        $('#reminderStoreTitle').text('Send Notice: ' + name);

        let urgencyPhrase = '';
        if (days < 0) {
            urgencyPhrase = 'expired ' + Math.abs(days) + ' day(s) ago on ' + due;
        } else if (days === 0) {
            urgencyPhrase = 'expires TODAY (' + due + ')';
        } else {
            urgencyPhrase = 'will expire in ' + days + ' day(s) on ' + due;
        }

        const reminderMsg = `Good day ${owner}!\n\nThis is a renewal reminder from LikhaPOS Cloud.\n\nYour store "${name}" subscription (${plan}) ${urgencyPhrase}. To prevent interruption of your POS cashier registers and cloud syncing, kindly settle your monthly renewal.\n\nPayment Channels: QRPH, GCash, or Maya\nUpload receipt at: http://pos.dev.com/subscription/checkout\n\nNeed assistance? Our 24/7 Support Hotline is always open: 0912 894 1731.\n\nThank you for choosing LikhaPOS!`;

        $('#reminderMessageText').val(reminderMsg);

        const encodedMsg = encodeURIComponent(reminderMsg);
        $('#reminderSmsLink').attr('href', 'sms:' + phone + '?body=' + encodedMsg);

        let cleanPhone = phone.replace(/[^0-9]/g, '');
        if (cleanPhone.startsWith('0')) cleanPhone = '63' + cleanPhone.substring(1);
        $('#reminderWaLink').attr('href', 'https://wa.me/' + cleanPhone + '?text=' + encodedMsg);

        $('#modalSendReminder').modal('show');
    });

    $('#btnCopyReminderText').on('click', function () {
        const textarea = document.getElementById('reminderMessageText');
        textarea.select();
        document.execCommand('copy');
        Swal.fire({ icon: 'success', title: 'Copied!', text: 'Message copied to clipboard.', timer: 2000, showConfirmButton: false });
    });

    // ── Extend Due Modal ────────────────────────────────────────
    $(document).on('click', '.btn-extend-due', function () {
        const btn = $(this);
        const encId = btn.data('id');
        const name = btn.data('name');
        const currentEnd = btn.data('current-end') || 'No expiration set';

        $('#extendTenantId').val(encId);
        $('#extendStoreHeader').text(name);
        $('#extendCurrentDue').text(currentEnd);
        $('#formExtendDue')[0].reset();
        $('#customDateWrap').addClass('d-none');
        $('#modalExtendDue').modal('show');
    });

    $('input[name="extension_type"]').on('change', function () {
        if ($(this).attr('id') === 'extCustom') {
            $('#customDateWrap').removeClass('d-none');
            $('#customDateInput').prop('required', true);
        } else {
            $('#customDateWrap').addClass('d-none');
            $('#customDateInput').prop('required', false);
        }
    });

    $('#formExtendDue').on('submit', function (e) {
        e.preventDefault();
        const form = $(this);
        const encId = $('#extendTenantId').val();
        const extendUrl = "{{ route('sa.subscriptions.monitoring.extend', ':id') }}".replace(':id', encId);
        const submitBtn = form.find('button[type="submit"]');

        let extensionType = $('input[name="extension_type"]:checked').val();
        let days = 3;
        if ($('#extGrace7').is(':checked')) days = 7;
        if ($('#extMonth30').is(':checked')) days = 30;

        let payload = {
            _token: "{{ csrf_token() }}",
            extension_type: extensionType,
            days: days,
            custom_date: $('#customDateInput').val(),
            remarks: $('input[name="remarks"]').val()
        };

        submitBtn.prop('disabled', true).prepend('<span class="spinner-border spinner-border-sm me-1"></span>');

        $.ajax({
            url: extendUrl,
            type: "POST",
            data: payload,
            success: function (res) {
                $('#modalExtendDue').modal('hide');
                dataTable.ajax.reload(null, false);
                Swal.fire({
                    icon: 'success',
                    title: 'Subscription Extended',
                    text: res.message,
                    timer: 3000,
                    showConfirmButton: false
                });
            },
            error: function (xhr) {
                const errorMsg = xhr.responseJSON?.message || 'Failed to extend subscription.';
                Swal.fire({ icon: 'error', title: 'Error', text: errorMsg });
            },
            complete: function () {
                submitBtn.prop('disabled', false).find('.spinner-border').remove();
            }
        });
    });
});
</script>
<style>
.kpi-card {
    transition: all 0.2s ease;
    border: 1px solid rgba(0,0,0,0.05) !important;
}
.kpi-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.08) !important;
    border-color: rgba(5,150,105,0.3) !important;
}
.filter-btn {
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    font-size: 0.78rem;
    transition: all 0.15s ease;
}
.filter-btn:hover {
    background: #e2e8f0;
}
.filter-btn.active {
    background: #0f172a !important;
    color: #ffffff !important;
    border-color: #0f172a !important;
}
.cursor-pointer { cursor: pointer; }
.animate-pulse {
    animation: pulse 1.5s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: .5; }
}
</style>
@endpush
@endsection
