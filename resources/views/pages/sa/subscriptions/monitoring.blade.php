@extends('layouts.sa')

@section('title', 'Subscription Due Date & Renewal Monitoring — SuperAdmin Console')

@section('content')
<div class="container-fluid py-2 px-3">

    {{-- ── Page Header ────────────────────────────────────── --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-danger text-white fw-bold px-2 py-0.5" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                    <i class="bi bi-clock-history me-1"></i> DUE MONITORING
                </span>
                <span class="text-muted extra-small">Platform Revenue &amp; Retention Sentinel</span>
            </div>
            <h3 class="fw-black text-dark font-mono mb-1" style="font-size: 1.6rem; letter-spacing: -0.02em;">
                Tenant Subscription Expiration &amp; Due Date Monitoring
            </h3>
            <p class="text-muted small mb-0">
                Track stores nearing renewal due dates, manage grace periods, and follow up with owners before accounts lapse.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('sa.subscriptions.verifications') }}" class="btn btn-outline-warning btn-sm rounded-pill fw-bold px-3 shadow-xs text-dark d-flex align-items-center gap-1.5" style="background:#fffbeb; border:1px solid #f59e0b;">
                <i class="bi bi-patch-check-fill text-warning"></i>
                <span>Verifications Queue</span>
                @if(($metrics['pending_verifs'] ?? 0) > 0)
                    <span class="badge bg-danger rounded-pill px-1.5 py-0.5" style="font-size: 0.68rem;">{{ $metrics['pending_verifs'] }}</span>
                @endif
            </a>
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
                        Action Required: {{ $metrics['due_in_3_days'] }} store(s) due within 3 days &amp; {{ $metrics['overdue_expired'] }} overdue!
                    </h6>
                    <p class="text-muted small mb-0">
                        Follow up with store owners to ensure continuity and prevent interruption in their cashiering POS terminal.
                    </p>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-danger btn-sm rounded-pill fw-bold px-3 py-1.5 btn-quick-filter" data-filter="critical_3_days">
                    <i class="bi bi-fire me-1"></i> View Urgent (3 Days)
                </button>
                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill fw-bold px-3 py-1.5 btn-quick-filter" data-filter="overdue">
                    <i class="bi bi-slash-circle me-1"></i> View Overdue
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
            <div class="card border-0 rounded-4 shadow-xs p-3 h-100 bg-white text-center cursor-pointer kpi-card" data-filter="healthy">
                <div class="text-muted extra-small font-mono fw-bold text-uppercase mb-1" style="font-size: 0.68rem;">Healthy Active</div>
                <div class="fs-4 fw-black text-success font-mono mb-1">{{ number_format($metrics['healthy_active']) }}</div>
                <span class="badge bg-success-subtle text-success rounded-pill px-2 py-0.5 extra-small fw-bold">> 7 Days</span>
            </div>
        </div>

        <div class="col-6 col-lg-2">
            <div class="card border-0 rounded-4 shadow-xs p-3 h-100 bg-white text-center cursor-pointer kpi-card" data-filter="all">
                <div class="text-muted extra-small font-mono fw-bold text-uppercase mb-1" style="font-size: 0.68rem;">All Registered</div>
                <div class="fs-4 fw-black text-dark font-mono mb-1">{{ number_format($metrics['total_tenants']) }}</div>
                <span class="badge bg-light text-muted border rounded-pill px-2 py-0.5 extra-small fw-bold">Total Stores</span>
            </div>
        </div>
    </div>

    {{-- ── Filter Strip ────────────────────────────────────── --}}
    <div class="card border-0 rounded-4 shadow-xs bg-white mb-4">
        <div class="card-body p-3.5">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <span class="extra-small font-mono text-muted fw-bold text-uppercase me-1" style="font-size: 0.72rem;">
                        <i class="bi bi-funnel-fill me-1"></i> Filter By Expiration:
                    </span>
                    <button type="button" class="btn btn-sm rounded-pill fw-bold px-3 filter-btn active" data-filter="all">
                        All Stores
                    </button>
                    <button type="button" class="btn btn-sm rounded-pill fw-bold px-3 filter-btn text-danger" data-filter="critical_3_days">
                        <i class="bi bi-fire me-1"></i> Due in 3 Days ({{ $metrics['due_in_3_days'] }})
                    </button>
                    <button type="button" class="btn btn-sm rounded-pill fw-bold px-3 filter-btn text-warning-emphasis" data-filter="due_7_days">
                        <i class="bi bi-clock-history me-1"></i> Due in 7 Days ({{ $metrics['due_in_7_days'] }})
                    </button>
                    <button type="button" class="btn btn-sm rounded-pill fw-bold px-3 filter-btn text-danger" data-filter="overdue">
                        <i class="bi bi-slash-circle me-1"></i> Overdue ({{ $metrics['overdue_expired'] }})
                    </button>
                    <button type="button" class="btn btn-sm rounded-pill fw-bold px-3 filter-btn text-info-emphasis" data-filter="trial">
                        Free Trial ({{ $metrics['trial_expiring'] }})
                    </button>
                    <button type="button" class="btn btn-sm rounded-pill fw-bold px-3 filter-btn text-success" data-filter="healthy">
                        Healthy Active
                    </button>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light text-muted border px-2.5 py-1 extra-small font-mono">
                        <i class="bi bi-headset me-1 text-primary"></i> 24/7 Hotline: 0912 894 1731
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Due Date Monitoring DataTable Card ──────────────── --}}
    <div class="card border-0 rounded-4 shadow-sm bg-white overflow-hidden">
        <div class="card-header bg-white border-bottom py-3 px-3.5 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-shield-check text-success fs-5"></i>
                <h5 class="fw-bold text-dark font-mono fs-6 mb-0">Tenant Renewal &amp; Due Date Sentinel</h5>
            </div>
            <span class="text-muted extra-small font-mono">Real-time live due calculation</span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 w-100" id="dueMonitoringTable">
                    <thead class="bg-light font-mono text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.6px;">
                        <tr>
                            <th class="ps-3.5 py-3 text-muted">Store &amp; Code</th>
                            <th class="py-3 text-muted">Owner Contact</th>
                            <th class="py-3 text-muted">Plan &amp; Rate</th>
                            <th class="py-3 text-muted">Expiration &amp; Due Status</th>
                            <th class="py-3 text-muted">Usage &amp; Health</th>
                            <th class="pe-3.5 py-3 text-end text-muted">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="font-mono"></tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- ── MODAL: Send Renewal Reminder ──────────────────────── --}}
<div class="modal fade" id="modalSendReminder" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 520px;">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
            <div class="modal-header bg-light border-bottom py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 p-1.5 bg-warning text-dark d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="bi bi-bell-fill"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-dark font-mono mb-0">Renewal Reminder Dispatch</h6>
                        <small class="text-muted extra-small" id="reminderStoreHeader"></small>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                <div class="p-3 rounded-3 mb-3" style="background:#fffbeb; border:1px solid #fde68a;">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="extra-small font-mono fw-bold text-uppercase text-warning-emphasis">
                            <i class="bi bi-telephone-outbound me-1"></i> Owner Phone: <strong id="reminderPhoneDisplay" class="text-dark"></strong>
                        </span>
                        <a href="#" id="reminderDirectCallBtn" class="btn btn-xs btn-outline-success rounded-pill fw-bold px-2 py-0.5 extra-small">
                            <i class="bi bi-telephone-fill me-1"></i> Call Now
                        </a>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label extra-small font-mono fw-bold text-uppercase d-flex justify-content-between align-items-center">
                        <span>Pre-Composed SMS / Message Template</span>
                        <button type="button" class="btn btn-link p-0 extra-small text-decoration-none fw-bold" id="btnCopyReminderText">
                            <i class="bi bi-clipboard-check me-1"></i> Copy Message
                        </button>
                    </label>
                    <textarea id="reminderMessageText" class="form-control font-mono small rounded-3 p-2.5" rows="7" style="font-size: 0.8rem; line-height: 1.4;"></textarea>
                </div>

                <div class="d-grid gap-2">
                    <a href="#" id="reminderSmsLink" class="btn btn-primary btn-sm rounded-3 fw-bold py-2 d-flex align-items-center justify-content-center gap-2" style="background:#059669; border:none;">
                        <i class="bi bi-chat-text-fill"></i>
                        <span>Open in Mobile SMS App</span>
                    </a>
                    <a href="#" id="reminderWaLink" target="_blank" class="btn btn-outline-success btn-sm rounded-3 fw-bold py-2 d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-whatsapp"></i>
                        <span>Send via WhatsApp</span>
                    </a>
                </div>
            </div>

            <div class="modal-footer bg-light border-top py-2.5 px-4">
                <button type="button" class="btn btn-light rounded-3 px-3.5 w-100" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- ── MODAL: Extend Subscription / Grant Grace Period ──── --}}
<div class="modal fade" id="modalExtendDue" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 460px;">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
            <div class="modal-header bg-light border-bottom py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 p-1.5 bg-primary text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="bi bi-calendar-plus-fill"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-dark font-mono mb-0">Grant Grace / Extend Due Date</h6>
                        <small class="text-muted extra-small" id="extendStoreHeader"></small>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>

            <form id="formExtendDue">
                @csrf
                <input type="hidden" name="tenant_id" id="extendTenantId">

                <div class="modal-body p-4">
                    <div class="p-2.5 rounded-3 bg-light border mb-3 font-mono extra-small">
                        Current Due Date: <strong id="extendCurrentDue" class="text-dark"></strong>
                    </div>

                    <div class="mb-3">
                        <label class="form-label extra-small font-mono fw-bold text-uppercase">Extension Option <span class="text-danger">*</span></label>
                        <div class="d-grid gap-2">
                            <div class="form-check p-2.5 border rounded-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <input class="form-check-input ms-0 me-2" type="radio" name="extension_type" id="extGrace3" value="days" checked>
                                    <label class="form-check-label fw-bold small" for="extGrace3">
                                        +3 Days Grace Period (Urgent extension)
                                    </label>
                                </div>
                                <span class="badge bg-warning-subtle text-warning-emphasis">+3 Days</span>
                            </div>

                            <div class="form-check p-2.5 border rounded-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <input class="form-check-input ms-0 me-2" type="radio" name="extension_type" id="extGrace7" value="days">
                                    <label class="form-check-label fw-bold small" for="extGrace7">
                                        +7 Days Grace Period (Standard follow-up)
                                    </label>
                                </div>
                                <span class="badge bg-info-subtle text-info-emphasis">+7 Days</span>
                            </div>

                            <div class="form-check p-2.5 border rounded-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <input class="form-check-input ms-0 me-2" type="radio" name="extension_type" id="extMonth30" value="days">
                                    <label class="form-check-label fw-bold small" for="extMonth30">
                                        +30 Days Full Month Renewal
                                    </label>
                                </div>
                                <span class="badge bg-success-subtle text-success">+30 Days</span>
                            </div>

                            <div class="form-check p-2.5 border rounded-3">
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
                        <input type="text" name="remarks" class="form-control rounded-3 form-control-sm" placeholder="e.g. Granted upon call with owner, paying tomorrow">
                    </div>
                </div>

                <div class="modal-footer bg-light border-top py-2.5 px-4">
                    <button type="button" class="btn btn-light rounded-3 px-3.5" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-3 fw-bold px-4" style="background:#059669; border:none;">
                        <i class="bi bi-check-circle-fill me-1"></i> Apply Extension
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
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
            { data: 'store_info', name: 'business_name' },
            { data: 'owner_info', name: 'owner_name' },
            { data: 'plan_info', name: 'subscription.name' },
            { data: 'due_status', name: 'subscription_end' },
            { data: 'usage_stats', orderable: false, searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'pe-3.5 text-end' }
        ],
        order: [[3, 'asc']],
        pageLength: 15,
        lengthMenu: [10, 15, 25, 50, 100],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search store name, code, owner, phone...",
            processing: '<div class="spinner-border text-danger spinner-border-sm me-2"></div> Checking subscription sentinel...',
            emptyTable: "No stores found matching the expiration criteria.",
        }
    });

    // Filter Buttons
    $('.filter-btn, .kpi-card, .btn-quick-filter').on('click', function () {
        currentFilter = $(this).data('filter');
        $('.filter-btn').removeClass('active bg-dark text-white');
        $('.filter-btn[data-filter="' + currentFilter + '"]').addClass('active bg-dark text-white');
        dataTable.draw();
    });

    $('#btnRefreshMonitoring').on('click', function () {
        dataTable.ajax.reload(null, false);
    });

    // Handle Reminder Modal Open
    $(document).on('click', '.btn-send-reminder', function () {
        const btn = $(this);
        const name = btn.data('name');
        const owner = btn.data('owner');
        const phone = btn.data('phone') || '';
        const plan = btn.data('plan');
        const days = parseInt(btn.data('days'));
        const due = btn.data('due');

        $('#reminderStoreHeader').text(name + ' — Plan: ' + plan);
        $('#reminderPhoneDisplay').text(phone || 'No phone recorded');
        $('#reminderDirectCallBtn').attr('href', 'tel:' + phone);

        let urgencyPhrase = '';
        if (days < 0) {
            urgencyPhrase = 'is currently OVERDUE by ' + Math.abs(days) + ' day(s)';
        } else if (days === 0) {
            urgencyPhrase = 'expires TODAY (' + due + ')';
        } else {
            urgencyPhrase = 'will expire in ' + days + ' day(s) on ' + due;
        }

        const reminderMsg = `Good day ${owner}!\n\nThis is a friendly renewal reminder from LikhaPOS Cloud.\n\nYour store "${name}" subscription (${plan}) ${urgencyPhrase}. To prevent interruption of your POS cashier registers and cloud syncing, kindly settle your monthly renewal.\n\nPayment Channels: QRPH, GCash, or Maya\nUpload receipt directly at: http://pos.dev.com/subscription/checkout\n\nNeed assistance? Our 24/7 Support Hotline is always open: 0912 894 1731.\n\nThank you for trusting LikhaPOS!`;

        $('#reminderMessageText').val(reminderMsg);

        // SMS link
        const encodedMsg = encodeURIComponent(reminderMsg);
        $('#reminderSmsLink').attr('href', 'sms:' + phone + '?body=' + encodedMsg);

        // WhatsApp link
        let cleanPhone = phone.replace(/[^0-9]/g, '');
        if (cleanPhone.startsWith('0')) {
            cleanPhone = '63' + cleanPhone.substring(1);
        }
        $('#reminderWaLink').attr('href', 'https://wa.me/' + cleanPhone + '?text=' + encodedMsg);

        $('#modalSendReminder').modal('show');
    });

    // Copy Reminder Text Button
    $('#btnCopyReminderText').on('click', function () {
        const textarea = document.getElementById('reminderMessageText');
        textarea.select();
        document.execCommand('copy');
        Swal.fire({
            icon: 'success',
            title: 'Copied to Clipboard!',
            text: 'Reminder text is ready to paste in SMS, Messenger, or WhatsApp.',
            timer: 2000,
            showConfirmButton: false
        });
    });

    // Handle Extend Due Modal Open
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

    // Toggle custom date input
    $('input[name="extension_type"]').on('change', function () {
        if ($(this).attr('id') === 'extCustom') {
            $('#customDateWrap').removeClass('d-none');
            $('#customDateInput').prop('required', true);
        } else {
            $('#customDateWrap').addClass('d-none');
            $('#customDateInput').prop('required', false);
        }
    });

    // Handle Extend Due Submit
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
                const errorMsg = xhr.responseJSON?.message || 'Failed to extend subscription. Please check input.';
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
