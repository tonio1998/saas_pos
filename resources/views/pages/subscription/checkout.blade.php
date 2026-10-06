@extends('layouts.app')

@section('title', 'Subscription Payment & Activation — LikhaPOS')

@section('content')
<style>
    .pay-container {
        max-width: 980px;
        margin: 2rem auto 4rem auto;
        padding: 0 1rem;
        font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
    }

    .pay-hero {
        background: linear-gradient(135deg, #064e3b 0%, #065f46 60%, #022c22 100%);
        border-radius: 28px 28px 0 0;
        color: #ffffff;
        padding: 2.75rem 2.5rem;
        position: relative;
        overflow: hidden;
        border-bottom: 4px solid #fbbf24;
    }

    .pay-hero::before {
        content: '';
        position: absolute;
        top: -60px;
        right: -60px;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(251, 191, 36, 0.18) 0%, rgba(255,255,255,0) 70%);
        pointer-events: none;
    }

    .pay-card {
        background: #ffffff;
        border-radius: 28px;
        box-shadow: 0 25px 50px -12px rgba(6, 78, 59, 0.18);
        border: 1px solid rgba(16, 185, 129, 0.2);
        overflow: hidden;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 18px;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        margin-bottom: 1.25rem;
        text-transform: uppercase;
    }

    .status-pill.pending-verify {
        background: rgba(245, 158, 11, 0.2);
        color: #fbbf24;
        border: 1px solid rgba(251, 191, 36, 0.4);
        box-shadow: 0 0 15px rgba(245, 158, 11, 0.25);
    }

    .status-pill.active {
        background: rgba(16, 185, 129, 0.2);
        color: #34d399;
        border: 1px solid rgba(52, 211, 153, 0.4);
    }

    .status-pill.rejected {
        background: rgba(239, 68, 68, 0.2);
        color: #f87171;
        border: 1px solid rgba(239, 68, 68, 0.4);
    }

    .step-badge {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #059669;
        color: white;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        box-shadow: 0 4px 10px rgba(5, 150, 105, 0.3);
    }

    .plan-card {
        border: 2px solid #e2e8f0;
        border-radius: 20px;
        padding: 1.75rem;
        cursor: pointer;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        background: #ffffff;
    }

    .plan-card:hover {
        border-color: #10b981;
        transform: translateY(-3px);
        box-shadow: 0 12px 25px -8px rgba(5, 150, 105, 0.15);
    }

    .plan-card.selected {
        border-color: #059669;
        background: #f0fdf4;
        box-shadow: 0 12px 30px -5px rgba(5, 150, 105, 0.22);
    }

    .plan-card.selected::after {
        content: '✓ Napili';
        position: absolute;
        top: 14px;
        right: 16px;
        background: #059669;
        color: white;
        font-size: 0.72rem;
        font-weight: 800;
        padding: 3px 10px;
        border-radius: 50px;
        text-transform: uppercase;
    }

    .method-tab-btn {
        flex: 1;
        padding: 1.1rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 16px;
        background: #f8fafc;
        font-weight: 700;
        font-size: 0.95rem;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        color: #334155;
    }

    .method-tab-btn:hover {
        background: #ffffff;
        border-color: #cbd5e1;
    }

    .method-tab-btn.active {
        border-color: #059669;
        background: #ffffff;
        color: #059669;
        box-shadow: 0 8px 20px -5px rgba(5, 150, 105, 0.2);
    }

    .qr-container {
        background: #f8fafc;
        border: 2px dashed #059669;
        border-radius: 20px;
        padding: 2rem;
        text-align: center;
    }

    .qr-wrapper {
        background: #ffffff;
        padding: 14px;
        border-radius: 18px;
        display: inline-block;
        box-shadow: 0 10px 25px rgba(0,0,0,0.06);
        border: 1px solid #e2e8f0;
        margin-bottom: 1rem;
    }

    .qr-image {
        width: 200px;
        height: 200px;
        object-fit: contain;
        display: block;
    }

    .copy-pill {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        padding: 8px 16px;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        font-family: monospace;
        font-size: 1.1rem;
        font-weight: 700;
        color: #0f172a;
        cursor: pointer;
        transition: all 0.2s;
    }

    .copy-pill:hover {
        border-color: #059669;
        background: #ecfdf5;
        color: #059669;
    }

    .hotline-card {
        background: linear-gradient(135deg, #fef3c7 0%, #fffbeb 100%);
        border: 2px solid #f59e0b;
        border-radius: 20px;
        padding: 1.75rem;
    }

    .btn-submit-payment {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        color: #ffffff;
        border: none;
        border-radius: 16px;
        padding: 18px;
        font-size: 1.15rem;
        font-weight: 800;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        box-shadow: 0 12px 25px -5px rgba(5, 150, 105, 0.4);
        transition: all 0.25s ease;
    }

    .btn-submit-payment:hover {
        background: linear-gradient(135deg, #047857 0%, #065f46 100%);
        color: #fbbf24;
        box-shadow: 0 16px 30px -5px rgba(5, 150, 105, 0.6);
        transform: translateY(-2px);
    }

    .receipt-upload-box {
        border: 2px dashed #cbd5e1;
        border-radius: 16px;
        padding: 1.5rem;
        text-align: center;
        background: #f8fafc;
        cursor: pointer;
        transition: all 0.2s;
    }

    .receipt-upload-box:hover {
        border-color: #059669;
        background: #f0fdf4;
    }

    .badge-channel {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 12px;
        border-radius: 50px;
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
    }
</style>

<div class="pay-container">
    <div class="pay-card">
        <!-- Header -->
        <div class="pay-hero">
            @if($tenant && $tenant->isPendingVerification())
                <span class="status-pill pending-verify">
                    <i class="bi bi-hourglass-split"></i> Kasalukuyang Bine-verify ng SuperAdmin
                </span>
            @elseif($tenant && $tenant->isPaymentRejected())
                <span class="status-pill rejected">
                    <i class="bi bi-exclamation-octagon-fill"></i> Kailangan ng Pagwawasto
                </span>
            @elseif($tenant && $tenant->isPaid())
                <span class="status-pill active">
                    <i class="bi bi-patch-check-fill"></i> Aktibo ang Subscription
                </span>
            @else
                <span class="status-pill pending-verify">
                    <i class="bi bi-clock-history"></i> Walang Aktibong Subscription — Magbayad para Ma-activate
                </span>
            @endif

            <h1 class="h2 fw-bold text-white mb-2">{{ $tenant->business_name ?? 'JM Convenience Store' }}</h1>
            <p class="text-white-50 mb-0 fs-6">
                Manual Online Payment via QRPH, GCash, o Maya. Walang external payment gateway fee — direkta sa SuperAdmin na may mabilisang phone/text follow-up.
            </p>
        </div>

        @if(session('success'))
            <div class="alert alert-success m-4 mb-0 rounded-4 border-0 shadow-sm d-flex align-items-center gap-3">
                <i class="bi bi-check-circle-fill text-success fs-3"></i>
                <div class="fw-semibold">{{ session('success') }}</div>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning m-4 mb-0 rounded-4 border-0 shadow-sm d-flex align-items-center gap-3">
                <i class="bi bi-exclamation-triangle-fill text-warning fs-3"></i>
                <div class="fw-semibold">{{ session('warning') }}</div>
            </div>
        @endif

        {{-- STATE 1: AWAITING SUPERADMIN VERIFICATION --}}
        @if($tenant && $tenant->isPendingVerification() && !$isEditing)
            @php
                $pendingPlan = $tenant->pendingPlan ?? $tenant->subscription ?? $plans->first();
                $methodName = match(strtolower($tenant->payment_method ?? '')) {
                    'qrph' => 'QRPH Universal Standard',
                    'gcash' => 'GCash Express / QR',
                    'maya' => 'Maya (PayMaya)',
                    default => strtoupper($tenant->payment_method ?? 'Manual E-Wallet'),
                };
            @endphp
            <div class="p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="d-inline-flex p-3 rounded-circle bg-warning-subtle text-warning mb-3 shadow-sm">
                        <i class="bi bi-clock-history display-4"></i>
                    </div>
                    <h3 class="fw-bold text-dark mb-1">Naisumite na ang Inyong Bayad!</h3>
                    <p class="text-muted">
                        Kasalukuyang bine-verify ng aming SuperAdmin ang inyong transaksyon upang ma-activate ang inyong tindahan.
                    </p>
                </div>

                <!-- Transaction Details Receipt Box -->
                <div class="card border rounded-4 shadow-xs mb-4 overflow-hidden">
                    <div class="card-header bg-light py-3 px-4 d-flex align-items-center justify-content-between">
                        <div class="fw-bold text-dark"><i class="bi bi-receipt me-2 text-success"></i>Detalye ng Naisumiteng Bayad</div>
                        <span class="badge bg-warning text-dark px-3 py-1.5 rounded-pill fw-bold">
                            <i class="bi bi-hourglass-split me-1"></i> Awaiting Approval
                        </span>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-sm-6 col-md-4">
                                <small class="text-muted d-block">Subscription Plan</small>
                                <span class="fw-bold text-dark fs-5">{{ $pendingPlan?->name ?? 'Suki Growth' }}</span>
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <small class="text-muted d-block">Halagang Binayaran</small>
                                <span class="fw-bold text-success fs-5">₱{{ number_format($tenant->payment_amount ?? $pendingPlan?->price ?? 0, 2) }}</span>
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <small class="text-muted d-block">Payment Channel</small>
                                <span class="badge bg-primary text-white rounded-pill px-2.5 py-1 fw-bold mt-1">
                                    {{ $methodName }}
                                </span>
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <small class="text-muted d-block">Reference Number</small>
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <code class="fw-bold px-2 py-1 bg-light border rounded text-dark fs-6">{{ $tenant->payment_reference ?? '—' }}</code>
                                    <button type="button" class="btn btn-sm btn-light border p-1" onclick="navigator.clipboard.writeText('{{ $tenant->payment_reference }}'); alert('Reference copied!');">
                                        <i class="bi bi-clipboard"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <small class="text-muted d-block">Nagbayad (Sender)</small>
                                <span class="fw-bold text-dark">{{ $tenant->payment_sender_name ?? '—' }}</span>
                                @if($tenant->payment_sender_phone)
                                    <small class="text-muted font-monospace d-block">({{ $tenant->payment_sender_phone }})</small>
                                @endif
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <small class="text-muted d-block">Petsa at Oras</small>
                                <span class="text-dark small fw-semibold">
                                    {{ $tenant->payment_submitted_at ? $tenant->payment_submitted_at->format('M d, Y h:i A') : 'Bago lamang' }}
                                </span>
                                @if($tenant->payment_submitted_at)
                                    <small class="text-muted d-block">({{ $tenant->payment_submitted_at->diffForHumans() }})</small>
                                @endif
                            </div>
                        </div>

                        @if($tenant->payment_proof)
                            <hr class="my-3 text-muted opacity-25">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-image text-primary fs-4"></i>
                                    <div>
                                        <div class="fw-bold text-dark small">Uploaded Resibo Screenshot</div>
                                        <small class="text-muted">Naka-attach ang kopya para sa SuperAdmin</small>
                                    </div>
                                </div>
                                <a href="{{ asset('storage/' . $tenant->payment_proof) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-3">
                                    <i class="bi bi-eye me-1"></i> Tingnan ang Resibo
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Follow-up Hotline Banner (Direct Call / Text) -->
                <div class="hotline-card shadow-sm mb-4">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        <div>
                            <span class="badge bg-warning text-dark fw-bold mb-2">
                                <i class="bi bi-telephone-inbound-fill me-1"></i> PRIORITY ACTIVATION HOTLINE
                            </span>
                            <h4 class="fw-bold text-dark mb-1">Gusto mo bang ma-activate agad ang inyong tindahan?</h4>
                            <p class="text-secondary small mb-0">
                                Maaari kayong tumawag o mag-text sa aming SuperAdmin para i-follow up ang inyong Reference Number (<strong>{{ $tenant->payment_reference }}</strong>). Karaniwang na-aactivate sa loob ng <strong>5 hanggang 15 minuto</strong>!
                            </p>
                        </div>
                        <div class="d-flex flex-column flex-sm-row gap-2 flex-shrink-0">
                            <a href="tel:09128941731" class="btn btn-success rounded-3 fw-bold px-3 py-2.5 d-flex align-items-center justify-content-center gap-2 shadow-sm">
                                <i class="bi bi-telephone-fill"></i>
                                <span>Tawagan: 0912 894 1731</span>
                            </a>
                            <a href="sms:09128941731?body={{ urlencode('Magandang araw po! Nais ko pong i-follow up ang subscription activation para sa ' . $tenant->business_name . ' na may Reference #: ' . $tenant->payment_reference) }}" 
                               class="btn btn-dark rounded-3 fw-bold px-3 py-2.5 d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-chat-dots-fill"></i>
                                <span>Mag-Text sa Admin</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Refresh / Edit Actions -->
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 pt-2">
                    <a href="{{ route('subscription.checkout', ['resubmit' => 1]) }}" class="btn btn-outline-secondary rounded-3">
                        <i class="bi bi-pencil-square me-1"></i> Maling Reference? Baguhin o Mag-upload Muli
                    </a>
                    <div class="d-flex gap-2">
                        <a href="{{ route('subscription.checkout') }}" class="btn btn-light border rounded-3 text-dark fw-bold">
                            <i class="bi bi-arrow-clockwise me-1"></i> I-refresh ang Status
                        </a>
                        <a href="{{ route('dashboard.index') }}" class="btn btn-primary rounded-3 fw-bold">
                            <i class="bi bi-speedometer2 me-1"></i> Pumunta sa Dashboard
                        </a>
                    </div>
                </div>
            </div>

        {{-- STATE 2: PAYMENT FORM (New / Upgrade / Re-submission) --}}
        @else
            @if($tenant && $tenant->isPaymentRejected())
                <div class="m-4 mb-0 alert alert-danger border-0 rounded-4 shadow-sm d-flex align-items-start gap-3">
                    <i class="bi bi-exclamation-octagon-fill fs-3 text-danger mt-1"></i>
                    <div>
                        <h6 class="fw-bold text-danger mb-1">Hindi Na-verify ang Inyong Huling Pagbabayad</h6>
                        <p class="small text-danger-emphasis mb-0">
                            <strong>Dahilan:</strong> {{ $tenant->payment_notes ?? 'Hindi tugma ang reference number o malabo ang naisumiteng resibo.' }}
                        </p>
                        <p class="extra-small text-muted mt-1 mb-0">
                            Paki-check ang tamang Reference Number o mag-upload ng malinaw na screenshot sa form sa ibaba at i-submit muli.
                        </p>
                    </div>
                </div>
            @endif

            <form id="paymentForm" method="POST" action="{{ route('subscription.pay') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="plan_id" id="selectedPlanInput" value="{{ $tenant->pending_plan_id ?? ($tenant->subscription_id ?? 2) }}">
                <input type="hidden" name="payment_method" id="selectedMethodInput" value="qrph">

                <div class="p-4 p-md-5">
                    <!-- STEP 1: PLAN SELECTION -->
                    <div class="mb-5">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="step-badge">1</span>
                            <h4 class="fw-bold text-dark mb-0">Piliin ang Inyong Subscription Plan</h4>
                        </div>
                        <p class="text-muted small mb-4">
                            Pumili ng buwanang plano na angkop sa laki ng inyong minimart o grocery.
                        </p>

                        <div class="row g-3">
                            @foreach($plans as $plan)
                                @php
                                    $isPreSelected = ($tenant->pending_plan_id ?? ($tenant->subscription_id ?? 2)) == $plan->id;
                                    $effectivePrice = $plan->effectivePrice();
                                    $hasPromoDiscount = $plan->is_promo && $plan->promo_price && $plan->promo_price < $plan->price;
                                    $badgeText = $plan->badge_text ?: (str_contains(strtolower($plan->name), 'growth') || $plan->sort_order == 2 ? '⭐ Pinakasikat' : null);
                                @endphp
                                <div class="col-md-4">
                                    <div class="plan-card h-100 {{ $isPreSelected ? 'selected' : '' }}" 
                                         onclick="selectPlan('{{ $plan->id }}', {{ $effectivePrice }}, '{{ addslashes($plan->name) }}', this)">
                                        @if($badgeText)
                                            <span class="badge {{ $plan->is_promo ? 'bg-danger text-white' : 'bg-warning text-dark' }} position-absolute top-0 end-0 translate-middle-y me-3 px-3 py-1 fw-bold rounded-pill shadow-sm" style="font-size: 0.72rem;">
                                                {{ $badgeText }}
                                            </span>
                                        @endif

                                        <div class="fw-bold text-uppercase extra-small text-success mb-1">
                                            {{ $plan->billing_cycle ?? 'Monthly' }} Plan
                                        </div>
                                        <h4 class="fw-bold text-dark mb-1">{{ $plan->name }}</h4>
                                        <div class="h2 fw-bold text-success mb-2 d-flex align-items-baseline gap-2">
                                            <span>₱{{ number_format($effectivePrice, 0) }}</span>
                                            @if($hasPromoDiscount)
                                                <span class="fs-6 text-muted text-decoration-line-through fw-normal">₱{{ number_format($plan->price, 0) }}</span>
                                            @endif
                                            <small class="fs-6 text-muted fw-normal">/ buwan</small>
                                        </div>
                                        <p class="text-secondary extra-small mb-3" style="min-height: 38px;">
                                            {{ $plan->description }}
                                        </p>

                                        <div class="pt-3 border-top extra-small text-muted d-flex flex-column gap-2">
                                            <div><i class="bi bi-tablet-landscape-fill text-success me-1.5"></i> <strong>{{ $plan->max_terminals ?? 1 }} POS Terminal{{ ($plan->max_terminals ?? 1) > 1 ? 's' : '' }}</strong> (1 Unit = 1 Counter)</div>
                                            <div><i class="bi bi-box-seam-fill text-info me-1.5"></i> Hanggang <strong>{{ number_format($plan->max_products) }} SKUs</strong></div>
                                            <div><i class="bi bi-people-fill text-primary me-1.5"></i> <strong>{{ $plan->max_customers ? number_format($plan->max_customers) . ' Suki' : 'Walang Limitang Suki' }}</strong></div>
                                            <div><i class="bi bi-person-badge-fill text-secondary me-1.5"></i> <strong>{{ $plan->max_users }} User Accounts</strong> ({{ $plan->max_cashier_accounts }} Cashiers)</div>
                                            @if($plan->allow_multi_branch)
                                                <div class="text-success fw-bold"><i class="bi bi-buildings-fill me-1.5"></i> Multi-Branch: {{ $plan->max_branches }} Branches</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- STEP 2: CHOOSE PAYMENT CHANNEL & VIEW QR -->
                    <div class="mb-5">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="step-badge">2</span>
                            <h4 class="fw-bold text-dark mb-0">Piliin ang Paraan ng Pagbabayad</h4>
                        </div>
                        <p class="text-muted small mb-4">
                            Piliin kung QRPH Universal, GCash, o Maya. I-scan ang QR code o ipadala sa nakasaad na numero.
                        </p>

                        <!-- Method Tabs -->
                        <div class="d-flex flex-column flex-sm-row gap-3 mb-4">
                            <button type="button" class="method-tab-btn active" onclick="selectPaymentMethod('qrph', this)">
                                <i class="bi bi-qr-code-scan fs-3 text-info"></i>
                                <span class="fw-bold">QRPH Universal</span>
                                <small class="extra-small text-muted">Lahat ng Bangko & E-Wallets</small>
                            </button>
                            <button type="button" class="method-tab-btn" onclick="selectPaymentMethod('gcash', this)">
                                <i class="bi bi-wallet2 fs-3 text-primary"></i>
                                <span class="fw-bold">GCash</span>
                                <small class="extra-small text-muted">Express Send o Scan QR</small>
                            </button>
                            <button type="button" class="method-tab-btn" onclick="selectPaymentMethod('maya', this)">
                                <i class="bi bi-credit-card-2-front fs-3 text-success"></i>
                                <span class="fw-bold">Maya (PayMaya)</span>
                                <small class="extra-small text-muted">Send Money o QR Scan</small>
                            </button>
                        </div>

                        <!-- QR Code & Instructions Box -->
                        <div class="qr-container">
                            <div class="row align-items-center justify-content-center g-4">
                                <div class="col-md-5 text-center">
                                    <div class="qr-wrapper">
                                        <!-- Dynamic QR Image based on selected channel -->
                                        <img id="qrDisplayImage" 
                                             src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=09128941731-LIKHAPOS-SUBSCRIPTION" 
                                             class="qr-image" 
                                             alt="LikhaPOS QR Code">
                                    </div>
                                    <div class="extra-small text-muted">
                                        <i class="bi bi-camera-fill me-1"></i> I-scan gamit ang iyong E-Wallet App camera
                                    </div>
                                </div>
                                <div class="col-md-7 text-start ps-md-4 border-start-md">
                                    <div class="badge bg-success-subtle text-success fw-bold px-3 py-1.5 rounded-pill mb-2" id="channelNoticeBadge">
                                        <i class="bi bi-check-circle-fill me-1"></i> QRPH National Standard Accepted
                                    </div>
                                    <h5 class="fw-bold text-dark mb-1">Opisyal na Account ng LikhaPOS</h5>
                                    <p class="text-muted small mb-3">
                                        Ipadala ang eksaktong halaga sa mga sumusunod na detalye:
                                    </p>

                                    <div class="mb-3">
                                        <div class="extra-small text-muted text-uppercase fw-bold">Pangalan ng Account</div>
                                        <div class="h6 fw-bold text-dark mb-0">Antonio Jr Piloton (LikhaPOS)</div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="extra-small text-muted text-uppercase fw-bold">Mobile / Account Number</div>
                                        <div class="d-flex align-items-center gap-2 mt-1">
                                            <span class="copy-pill" onclick="copyNumber('09128941731')">
                                                <i class="bi bi-telephone-fill text-success"></i>
                                                <span>0912 894 1731</span>
                                                <i class="bi bi-copy text-muted ms-1 fs-6"></i>
                                            </span>
                                            <span id="copyToast" class="badge bg-dark text-white extra-small" style="display: none;">Nakopya!</span>
                                        </div>
                                    </div>

                                    <div>
                                        <div class="extra-small text-muted text-uppercase fw-bold">Halagang Babayaran</div>
                                        <div class="h3 fw-bold text-success mb-0" id="displayPayAmount">
                                            ₱600.00
                                        </div>
                                        <small class="text-muted extra-small" id="displayPlanName">para sa Suki Growth (1 buwan)</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 3: SUBMIT TRANSACTION DETAILS & PROOF -->
                    <div class="mb-5">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="step-badge">3</span>
                            <h4 class="fw-bold text-dark mb-0">I-enter ang Detalye ng Resibo</h4>
                        </div>
                        <p class="text-muted small mb-4">
                            Ilagay ang Reference Number mula sa iyong GCash/Maya SMS o resibo, at i-upload ang screenshot para sa mabilisang verification.
                        </p>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small">
                                    Reference Number <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       name="reference_no" 
                                       id="referenceInput"
                                       class="form-control form-control-lg rounded-3 font-monospace fw-bold" 
                                       placeholder="Hal. 100234981249 o 9021..." 
                                       value="{{ old('reference_no', $tenant->payment_reference ?? '') }}"
                                       required>
                                <div class="form-text extra-small">Ang 13-digit transaction number sa iyong GCash/Maya receipt.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small">
                                    Pangalan ng Nagbayad (Sender Name) <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       name="sender_name" 
                                       class="form-control form-control-lg rounded-3" 
                                       placeholder="Hal. Juan Dela Cruz" 
                                       value="{{ old('sender_name', $tenant->payment_sender_name ?? $tenant->owner_name ?? '') }}"
                                       required>
                                <div class="form-text extra-small">Pangalan na nakalagay sa iyong GCash / Maya account.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small">
                                    Mobile Number ng Nagbayad (Sender Contact)
                                </label>
                                <input type="text" 
                                       name="sender_phone" 
                                       class="form-control form-control-lg rounded-3 font-monospace" 
                                       placeholder="Hal. 0917-xxx-xxxx" 
                                       value="{{ old('sender_phone', $tenant->payment_sender_phone ?? $tenant->phone ?? '') }}">
                                <div class="form-text extra-small">Upang matawagan ka namin kung may problema sa resibo.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small">
                                    Screenshot ng Resibo (Proof of Payment)
                                </label>
                                <input type="file" 
                                       name="proof_image" 
                                       id="proofFileInput"
                                       class="form-control form-control-lg rounded-3" 
                                       accept="image/png, image/jpeg, image/jpg, image/webp"
                                       onchange="previewReceipt(this)">
                                <div class="form-text extra-small">Opsyonal ngunit lubos na inirerekomenda para sa 5-minutong activation.</div>
                            </div>

                            <!-- Image Preview Area -->
                            <div class="col-12" id="previewContainer" style="display: none;">
                                <div class="p-3 bg-light rounded-4 border d-flex align-items-center gap-3">
                                    <img id="previewImage" src="" alt="Resibo Preview" class="rounded-3 shadow-xs" style="max-height: 100px; max-width: 120px; object-fit: contain;">
                                    <div>
                                        <div class="fw-bold text-dark small" id="previewFileName">Resibo napili</div>
                                        <div class="extra-small text-success"><i class="bi bi-check-circle-fill me-1"></i> Handa nang i-upload kasama ng form</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold text-muted small">
                                    Karagdagang Mensahe o Notes (Opsyonal)
                                </label>
                                <input type="text" 
                                       name="notes" 
                                       class="form-control rounded-3" 
                                       placeholder="Hal. Nagsend na po ako bandang 1:30 PM, salamat!" 
                                       value="{{ old('notes') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Follow-up Notice Banner Before Submit -->
                    <div class="hotline-card shadow-sm mb-4">
                        <div class="d-flex align-items-start gap-3">
                            <div class="p-2 rounded-circle bg-warning text-dark flex-shrink-0">
                                <i class="bi bi-info-circle-fill fs-5"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Paalala sa Pag-activate:</h6>
                                <p class="text-secondary small mb-2">
                                    Dahil manual ang verification, <strong>hindi awtomatikong mag-aactivate ang POS</strong> hangga't hindi pa ito na-verify ng SuperAdmin.
                                </p>
                                <div class="small fw-bold text-dark">
                                    📞 Para sa mabilisang activation (5-15 mins), tumawag o mag-text agad sa: 
                                    <a href="tel:09128941731" class="text-success text-decoration-none">0912 894 1731</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-submit-payment" id="submitPaymentBtn">
                        <i class="bi bi-send-check-fill"></i>
                        <span>I-submit ang Bayad para sa SuperAdmin Verification</span>
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>

<script>
    // Plans data
    const plansData = {
        @foreach($plans as $plan)
            '{{ $plan->id }}': {
                name: '{{ addslashes($plan->name) }}',
                price: {{ $plan->price }},
                cycle: '{{ $plan->billing_cycle ?? 'buwan' }}'
            },
        @endforeach
    };

    function selectPlan(planId, price, name, el) {
        document.querySelectorAll('.plan-card').forEach(c => c.classList.remove('selected'));
        el.classList.add('selected');
        document.getElementById('selectedPlanInput').value = planId;

        // Update display text
        const formatted = new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(price);
        document.getElementById('displayPayAmount').innerText = formatted;
        document.getElementById('displayPlanName').innerText = 'para sa ' + name + ' (1 buwan)';

        // Update QR Code
        updateQRCode();
    }

    function selectPaymentMethod(method, el) {
        document.querySelectorAll('.method-tab-btn').forEach(b => b.classList.remove('active'));
        el.classList.add('active');
        document.getElementById('selectedMethodInput').value = method;

        const badge = document.getElementById('channelNoticeBadge');
        if (method === 'qrph') {
            badge.className = 'badge bg-info-subtle text-info fw-bold px-3 py-1.5 rounded-pill mb-2';
            badge.innerHTML = '<i class="bi bi-qr-code me-1"></i> QRPH Universal Standard Accepted (Any Bank/E-Wallet)';
        } else if (method === 'gcash') {
            badge.className = 'badge bg-primary-subtle text-primary fw-bold px-3 py-1.5 rounded-pill mb-2';
            badge.innerHTML = '<i class="bi bi-wallet2 me-1"></i> GCash Express Send / QR Code';
        } else if (method === 'maya') {
            badge.className = 'badge bg-success-subtle text-success fw-bold px-3 py-1.5 rounded-pill mb-2';
            badge.innerHTML = '<i class="bi bi-credit-card-2-front me-1"></i> Maya (PayMaya) Direct Send / QR Code';
        }

        updateQRCode();
    }

    function updateQRCode() {
        const method = document.getElementById('selectedMethodInput').value;
        const planId = document.getElementById('selectedPlanInput').value;
        const plan = plansData[planId] || { name: 'Suki Growth', price: 600 };
        
        // Data payload encoded in QR
        const qrPayload = encodeURIComponent(`LIKHAPOS-${method.toUpperCase()}-09128941731-AMOUNT-${plan.price}`);
        document.getElementById('qrDisplayImage').src = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${qrPayload}`;
    }

    function copyNumber(num) {
        navigator.clipboard.writeText(num).then(() => {
            const toast = document.getElementById('copyToast');
            toast.style.display = 'inline-block';
            setTimeout(() => {
                toast.style.display = 'none';
            }, 2500);
        });
    }

    function previewReceipt(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewImage').src = e.target.result;
                document.getElementById('previewFileName').innerText = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
                document.getElementById('previewContainer').style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    }

    // Initialize display amounts on page load
    document.addEventListener('DOMContentLoaded', () => {
        const planId = document.getElementById('selectedPlanInput')?.value;
        if (planId && plansData[planId]) {
            const plan = plansData[planId];
            const formatted = new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(plan.price);
            const amtEl = document.getElementById('displayPayAmount');
            const nameEl = document.getElementById('displayPlanName');
            if (amtEl) amtEl.innerText = formatted;
            if (nameEl) nameEl.innerText = 'para sa ' + plan.name + ' (1 buwan)';
        }
    });
</script>
@endsection
