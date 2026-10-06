@php
    $supportUser = auth()->user();
    $supportTenant = $supportUser?->tenant ?? (session('tenant_id') ? \App\Models\POS\POSTenant::find(session('tenant_id')) : null);
    $defaultPhone = $supportTenant?->phone ?? $supportUser?->phone ?? '';
    $hotlineNumber = '0912 894 1731';
    $hotlineRaw = '09128941731';
    $hotlineIntl = '639128941731';
@endphp

<!-- 24/7 Floating Support Trigger Button -->
<div id="likha247SupportTrigger" class="position-fixed" style="bottom: 24px; right: 24px; z-index: 1040;">
    <button type="button" class="btn btn-dark rounded-pill px-3 py-2.5 shadow-lg d-flex align-items-center gap-2 border-2 border-success"
            data-bs-toggle="modal" data-bs-target="#supportHubModal"
            style="background: #0f172a; box-shadow: 0 8px 24px rgba(15, 23, 42, 0.35) !important; transition: all 0.25s ease;"
            id="openSupportHubBtn">
        <span class="position-relative d-inline-flex align-items-center justify-content-center">
            <span class="spinner-grow spinner-grow-sm text-success" style="width: 10px; height: 10px; animation-duration: 1.5s;" role="status"></span>
        </span>
        <i class="bi bi-headset fs-5 text-warning"></i>
        <div class="text-start lh-1 d-none d-sm-block">
            <div class="fw-bold text-white extra-small" style="letter-spacing: 0.3px;">24/7 SUPPORT</div>
            <div class="extra-small text-success-emphasis" style="font-size: 0.65rem;">Always Online</div>
        </div>
    </button>
</div>

<!-- Responsive Adjustment: Lift above mobile bottom bar if screen < 768px -->
<style>
@media (max-width: 767.98px) {
    #likha247SupportTrigger {
        bottom: 84px !important;
        right: 16px !important;
    }
}
.support-channel-card {
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 12px 14px;
    background: #ffffff;
    transition: all 0.2s ease;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 12px;
}
.support-channel-card:hover {
    border-color: #059669;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.06);
    background: #f8fafc;
}
.support-hotline-banner {
    background: linear-gradient(135deg, #064e3b 0%, #065f46 60%, #047857 100%);
    border-radius: 18px;
    padding: 18px 20px;
    color: #ffffff;
}
</style>

<!-- 24/7 Customer Support Hub Modal -->
<div class="modal fade" id="supportHubModal" tabindex="-1" aria-labelledby="supportHubModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-2xl rounded-4 overflow-hidden">
            <!-- Modal Header -->
            <div class="modal-header border-0 bg-dark text-white px-4 py-3.5 d-flex align-items-center justify-content-between" style="background: #0f172a !important;">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="background: rgba(34, 197, 94, 0.2);">
                        <i class="bi bi-headset fs-4 text-success"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h5 class="modal-title fw-bold text-white mb-0 fs-6" id="supportHubModalLabel">LikhaPOS 24/7 Customer Support Hub</h5>
                            <span class="badge bg-success rounded-pill extra-small px-2 py-0.5">● 24/7 LIVE</span>
                        </div>
                        <div class="extra-small text-white-50 mt-0.5">Immediate assistance for store cashiers, hardware, subscriptions &amp; emergencies</div>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4 bg-light">
                <!-- Emergency Hotline Showcase Banner -->
                <div class="support-hotline-banner mb-4 shadow-sm">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge bg-warning text-dark fw-bold px-2 py-0.5 rounded-pill extra-small">
                                    <i class="bi bi-telephone-inbound-fill me-1"></i> PRIORITY DIRECT HOTLINE
                                </span>
                                <span class="extra-small text-emerald-200 text-white-50">On-Call Duty Available Now</span>
                            </div>
                            <div class="h3 fw-bold text-white mb-1 font-monospace" style="letter-spacing: 0.5px;">
                                {{ $hotlineNumber }}
                            </div>
                            <div class="extra-small text-white-50">
                                Call anytime 24 hours a day, 7 days a week for urgent cashier lockouts, renewal verification, or hardware setup.
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="tel:{{ $hotlineRaw }}" class="btn btn-warning rounded-pill px-3 py-2 fw-bold text-dark d-inline-flex align-items-center gap-2 shadow-sm">
                                <i class="bi bi-telephone-fill"></i>
                                <span>Call Directly</span>
                            </a>
                            <button type="button" class="btn btn-outline-light rounded-pill px-3 py-2 fw-bold extra-small" onclick="copySupportHotline('{{ $hotlineNumber }}')">
                                <i class="bi bi-clipboard me-1"></i>
                                <span id="copyHotlineBtnText">Copy Number</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Instant Messaging Channels Row -->
                <div class="row g-2.5 mb-4">
                    <div class="col-sm-4">
                        <a href="https://wa.me/{{ $hotlineIntl }}?text={{ urlencode('Hello LikhaPOS Support, I need assistance with my store: ' . ($supportTenant?->business_name ?? 'Likha Store')) }}" 
                           target="_blank" class="support-channel-card h-100">
                            <div class="rounded-3 p-2 bg-success text-white flex-shrink-0">
                                <i class="bi bi-whatsapp fs-5"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="fw-bold text-dark small text-truncate">WhatsApp Chat</div>
                                <div class="extra-small text-muted text-truncate">Fast chat &amp; screenshots</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-4">
                        <a href="viber://chat?number=%2B{{ $hotlineIntl }}" target="_blank" class="support-channel-card h-100">
                            <div class="rounded-3 p-2 text-white flex-shrink-0" style="background: #7360f2;">
                                <i class="bi bi-chat-dots-fill fs-5"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="fw-bold text-dark small text-truncate">Viber Direct</div>
                                <div class="extra-small text-muted text-truncate">Live support chat</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-4">
                        <a href="sms:{{ $hotlineRaw }}?body={{ urlencode('LikhaPOS Support: ' . ($supportTenant?->business_name ?? 'Store')) }}" class="support-channel-card h-100">
                            <div class="rounded-3 p-2 bg-primary text-white flex-shrink-0">
                                <i class="bi bi-chat-square-text-fill fs-5"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="fw-bold text-dark small text-truncate">SMS / Text Message</div>
                                <div class="extra-small text-muted text-truncate">Standard cellular SMS</div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Tabbed Interaction: Quick Callback vs Submit Ticket -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                    <div class="card-header bg-white border-bottom p-0">
                        <ul class="nav nav-tabs border-0 px-3 pt-2" id="supportTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active fw-bold small text-dark pb-3 border-0 border-bottom border-3 border-success" 
                                        id="callback-tab" data-bs-toggle="tab" data-bs-target="#callback-tab-pane" type="button" role="tab">
                                    <i class="bi bi-telephone-outbound-fill text-success me-1.5"></i> Request 24/7 Callback
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-bold small text-muted pb-3 border-0" 
                                        id="ticket-tab" data-bs-toggle="tab" data-bs-target="#ticket-tab-pane" type="button" role="tab">
                                    <i class="bi bi-ticket-detailed-fill text-primary me-1.5"></i> Submit Support Ticket
                                </button>
                            </li>
                            @if(auth()->check() && (auth()->user()->hasRole('SA') || auth()->user()->is_super_admin))
                                <li class="nav-item ms-auto my-auto pe-2">
                                    <a href="{{ route('support-center.index') }}" class="btn btn-sm btn-outline-dark rounded-3 extra-small fw-bold">
                                        <i class="bi bi-speedometer2 me-1"></i> SA Tickets Console
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>

                    <div class="card-body p-4">
                        <div class="tab-content" id="supportTabsContent">
                            <!-- TAB 1: Instant Emergency Callback -->
                            <div class="tab-pane fade show active" id="callback-tab-pane" role="tabpanel" tabindex="0">
                                <form id="urgentCallbackForm">
                                    @csrf
                                    <div class="alert alert-info py-2.5 px-3 rounded-3 extra-small mb-3 d-flex align-items-center gap-2 border-0 bg-info-subtle text-info-emphasis">
                                        <i class="bi bi-info-circle-fill fs-5 flex-shrink-0"></i>
                                        <span>Enter your mobile number below. Our 24/7 on-call technical desk will dial you back immediately.</span>
                                    </div>

                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label extra-small fw-bold text-dark text-uppercase">Your Contact Number <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light text-muted"><i class="bi bi-phone"></i></span>
                                                <input type="text" name="phone" id="callbackPhone" class="form-control" 
                                                       placeholder="0912 345 6789" value="{{ $defaultPhone }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label extra-small fw-bold text-dark text-uppercase">Category / Urgency</label>
                                            <select name="category" id="callbackCategory" class="form-select">
                                                <option value="POS Terminal Lockout / Urgent">POS Terminal Lockout (Urgent)</option>
                                                <option value="Subscription Due & Renewal Assistance">Subscription Due &amp; Renewal</option>
                                                <option value="Barcode Scanner & Hardware Setup">Barcode Scanner &amp; Hardware</option>
                                                <option value="Cash Shift & Sales Discrepancy">Cash Shift &amp; Sales Discrepancy</option>
                                                <option value="General Question / Inquiries">General Assistance</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label extra-small fw-bold text-dark text-uppercase">What issue are you experiencing? <span class="text-danger">*</span></label>
                                        <textarea name="issue" id="callbackIssue" class="form-control" rows="2" 
                                                  placeholder="E.g., Cannot finalize sale on register, or need payment approval for GCash renewal..." required></textarea>
                                    </div>

                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="extra-small text-muted">
                                            <i class="bi bi-clock-history me-1"></i> Average callback turnaround: &lt; 5 minutes
                                        </span>
                                        <button type="submit" id="submitCallbackBtn" class="btn btn-success rounded-3 fw-bold px-4 py-2 d-inline-flex align-items-center gap-2 shadow-sm">
                                            <i class="bi bi-telephone-outbound-fill"></i>
                                            <span>Request Emergency Callback</span>
                                        </button>
                                    </div>
                                    <div id="callbackAlertBox" class="mt-3 d-none"></div>
                                </form>
                            </div>

                            <!-- TAB 2: Submit Support Ticket -->
                            <div class="tab-pane fade" id="ticket-tab-pane" role="tabpanel" tabindex="0">
                                <form id="supportTicketForm">
                                    @csrf
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-8">
                                            <label class="form-label extra-small fw-bold text-dark text-uppercase">Ticket Subject <span class="text-danger">*</span></label>
                                            <input type="text" name="subject" id="ticketSubject" class="form-control" 
                                                   placeholder="Summary of the issue..." required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label extra-small fw-bold text-dark text-uppercase">Priority Level <span class="text-danger">*</span></label>
                                            <select name="priority" id="ticketPriority" class="form-select" required>
                                                <option value="critical">Critical (Store Cannot Operate)</option>
                                                <option value="high" selected>High (Major Feature Impaired)</option>
                                                <option value="medium">Medium (Normal Request)</option>
                                                <option value="low">Low (Question / Cosmetic)</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label extra-small fw-bold text-dark text-uppercase">Category</label>
                                            <select name="category" id="ticketCategory" class="form-select">
                                                <option value="POS Register & Sales">POS Register &amp; Sales</option>
                                                <option value="Inventory & Barcodes">Inventory &amp; Barcodes</option>
                                                <option value="Subscription & Billing">Subscription &amp; Billing</option>
                                                <option value="Hardware / Receipt Printer">Hardware / Receipt Printer</option>
                                                <option value="Reports & BIR">Reports &amp; BIR Compliance</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label extra-small fw-bold text-dark text-uppercase">Contact Mobile Number</label>
                                            <input type="text" name="phone" id="ticketPhone" class="form-control" 
                                                   placeholder="0912 345 6789" value="{{ $defaultPhone }}">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label extra-small fw-bold text-dark text-uppercase">Detailed Description <span class="text-danger">*</span></label>
                                        <textarea name="description" id="ticketDescription" class="form-control" rows="3" 
                                                  placeholder="Provide step-by-step details or error messages encountered..." required></textarea>
                                    </div>

                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="extra-small text-muted">
                                            <i class="bi bi-shield-check me-1"></i> Tracked directly by LikhaPOS 24/7 Operations
                                        </span>
                                        <button type="submit" id="submitTicketBtn" class="btn btn-primary rounded-3 fw-bold px-4 py-2 d-inline-flex align-items-center gap-2 shadow-sm">
                                            <i class="bi bi-send-fill"></i>
                                            <span>Submit Support Ticket</span>
                                        </button>
                                    </div>
                                    <div id="ticketAlertBox" class="mt-3 d-none"></div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer border-top bg-white px-4 py-2.5 d-flex align-items-center justify-content-between">
                <div class="extra-small text-muted">
                    <i class="bi bi-clock me-1 text-success"></i> 24/7 Desk Operational Status: <strong class="text-success">Normal / Ready</strong>
                </div>
                <button type="button" class="btn btn-secondary btn-sm rounded-3 px-3" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function copySupportHotline(number) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(number).then(() => {
            const btnText = document.getElementById('copyHotlineBtnText');
            if (btnText) {
                const orig = btnText.innerText;
                btnText.innerText = 'Copied!';
                setTimeout(() => { btnText.innerText = orig; }, 2500);
            }
        });
    } else {
        alert('Support Hotline: ' + number);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // Tab switching style hook
    const supportTabBtns = document.querySelectorAll('#supportTabs button[data-bs-toggle="tab"]');
    supportTabBtns.forEach(btn => {
        btn.addEventListener('shown.bs.tab', (e) => {
            supportTabBtns.forEach(b => {
                b.classList.remove('border-bottom', 'border-3', 'border-success', 'text-dark');
                b.classList.add('text-muted');
            });
            e.target.classList.add('border-bottom', 'border-3', 'border-success', 'text-dark');
            e.target.classList.remove('text-muted');
        });
    });

    // 1. Submit Callback Request
    const callbackForm = document.getElementById('urgentCallbackForm');
    if (callbackForm) {
        callbackForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const submitBtn = document.getElementById('submitCallbackBtn');
            const alertBox = document.getElementById('callbackAlertBox');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Requesting...';
            alertBox.classList.add('d-none');

            try {
                const formData = new FormData(callbackForm);
                const resp = await fetch("{{ route('support.request-callback') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                const data = await resp.json();

                if (resp.ok && data.success) {
                    alertBox.className = 'alert alert-success py-2.5 px-3 rounded-3 extra-small mb-0';
                    alertBox.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> ' + data.message;
                    alertBox.classList.remove('d-none');
                    callbackForm.reset();
                } else {
                    throw new Error(data.message || 'Unable to submit callback request. Please dial 0912 894 1731 directly.');
                }
            } catch (err) {
                alertBox.className = 'alert alert-danger py-2.5 px-3 rounded-3 extra-small mb-0';
                alertBox.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1"></i> ' + err.message;
                alertBox.classList.remove('d-none');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-telephone-outbound-fill"></i> <span>Request Emergency Callback</span>';
            }
        });
    }

    // 2. Submit Support Ticket
    const ticketForm = document.getElementById('supportTicketForm');
    if (ticketForm) {
        ticketForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const submitBtn = document.getElementById('submitTicketBtn');
            const alertBox = document.getElementById('ticketAlertBox');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Submitting...';
            alertBox.classList.add('d-none');

            try {
                const formData = new FormData(ticketForm);
                const resp = await fetch("{{ route('support.create-ticket') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                const data = await resp.json();

                if (resp.ok && data.success) {
                    alertBox.className = 'alert alert-success py-2.5 px-3 rounded-3 extra-small mb-0';
                    alertBox.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> ' + data.message;
                    alertBox.classList.remove('d-none');
                    ticketForm.reset();
                } else {
                    throw new Error(data.message || 'Unable to submit ticket. Please call 0912 894 1731.');
                }
            } catch (err) {
                alertBox.className = 'alert alert-danger py-2.5 px-3 rounded-3 extra-small mb-0';
                alertBox.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1"></i> ' + err.message;
                alertBox.classList.remove('d-none');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-send-fill"></i> <span>Submit Support Ticket</span>';
            }
        });
    }
});
</script>
