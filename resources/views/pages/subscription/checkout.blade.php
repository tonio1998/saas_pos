@extends('layouts.app')

@section('title', 'Online Subscription Payment')

@section('content')
<style>
    .pay-container {
        max-width: 950px;
        margin: 2rem auto;
        padding: 0 1rem;
        font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
    }

    .pay-card {
        background: #ffffff;
        border-radius: 24px;
        box-shadow: 0 20px 40px -15px rgba(5, 150, 105, 0.15);
        border: 1px solid rgba(16, 185, 129, 0.2);
        overflow: hidden;
    }

    .pay-header {
        background: linear-gradient(135deg, #064e3b, #022c22);
        color: #ffffff;
        padding: 2.5rem;
        position: relative;
        border-bottom: 3px solid #fbbf24;
    }

    .status-badge-pending {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(245, 158, 11, 0.25);
        color: #fbbf24;
        border: 1px solid rgba(251, 191, 36, 0.5);
        padding: 6px 16px;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .status-badge-paid {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(16, 185, 129, 0.25);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.5);
        padding: 6px 16px;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .plan-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        padding: 2rem;
    }

    @media (max-width: 768px) {
        .plan-grid {
            grid-template-columns: 1fr;
        }
    }

    .plan-card {
        border: 2px solid #e2e8f0;
        border-radius: 18px;
        padding: 1.75rem;
        cursor: pointer;
        transition: all 0.25s ease;
        position: relative;
    }

    .plan-card.selected {
        border-color: #059669;
        background: #ecfdf5;
        box-shadow: 0 10px 25px -5px rgba(5, 150, 105, 0.2);
    }

    .popular-tag {
        position: absolute;
        top: -12px;
        right: 20px;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: #ffffff;
        padding: 4px 14px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        box-shadow: 0 4px 10px rgba(245, 158, 11, 0.3);
    }

    .payment-methods {
        padding: 0 2rem 2rem 2rem;
    }

    .method-tab {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .method-btn {
        flex: 1;
        padding: 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 14px;
        background: #f8fafc;
        font-weight: 700;
        font-size: 0.95rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: all 0.2s ease;
    }

    .method-btn.active {
        border-color: #059669;
        background: #ffffff;
        color: #059669;
        box-shadow: 0 4px 14px rgba(5, 150, 105, 0.15);
    }

    .qr-box {
        background: #ecfdf5;
        border: 2px dashed #059669;
        border-radius: 18px;
        padding: 2rem;
        text-align: center;
        margin-bottom: 1.5rem;
    }

    .qr-img {
        width: 180px;
        height: 180px;
        object-fit: contain;
        margin: 0 auto 1rem auto;
        display: block;
        background: #ffffff;
        padding: 10px;
        border-radius: 14px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    }

    .submit-pay-btn {
        width: 100%;
        background: linear-gradient(135deg, #059669, #047857);
        color: #ffffff;
        border: 1px solid #10b981;
        border-radius: 14px;
        padding: 16px;
        font-size: 1.1rem;
        font-weight: 800;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        box-shadow: 0 10px 20px -5px rgba(5, 150, 105, 0.4);
    }

    .submit-pay-btn:hover {
        background: linear-gradient(135deg, #047857, #065f46);
        color: #fbbf24;
        box-shadow: 0 14px 24px -5px rgba(5, 150, 105, 0.6);
    }
</style>

<div class="pay-container">
    <div class="pay-card">
        <div class="pay-header">
            @if($tenant && $tenant->isPaid())
                <span class="status-badge-paid"><i class="bi bi-check-circle-fill"></i> SUBSCRIPTION ACTIVE</span>
            @else
                <span class="status-badge-pending"><i class="bi bi-clock-history"></i> ACTION REQUIRED: ONLINE PAYMENT PENDING</span>
            @endif

            <h1 class="h2 fw-bold text-white mb-2">{{ $tenant->business_name ?? 'Minimart POS' }}</h1>
            <p class="text-white-50 mb-0">Select your subscription plan and pay online to activate your complete BIR-ready POS engine.</p>
        </div>

        <form id="paymentForm" method="POST" action="{{ route('subscription.pay') }}">
            @csrf
            <input type="hidden" name="plan_id" id="selectedPlan" value="monthly">
            <input type="hidden" name="payment_method" id="selectedMethod" value="gcash">

            <!-- Plan Selection -->
            <div class="plan-grid">
                @foreach($plans as $plan)
                    <div class="plan-card {{ $plan['id'] === 'monthly' ? 'selected' : '' }}" onclick="selectPlan('{{ $plan['id'] }}', this)">
                        @if($plan['popular'])
                            <span class="popular-tag">Best Value</span>
                        @endif
                        <h4 class="fw-bold mb-1" style="color: #064e3b;">{{ $plan['name'] }}</h4>
                        <div class="h2 fw-bold mb-2" style="color: #059669;">₱{{ number_format($plan['price'], 2) }} <small class="fs-6 text-muted">/ {{ $plan['period'] }}</small></div>
                        <p class="text-secondary small mb-0">{{ $plan['description'] }}</p>
                    </div>
                @endforeach
            </div>

            <!-- Payment Gateway Selection -->
            <div class="payment-methods">
                <h5 class="fw-bold mb-3" style="color: #064e3b;"><i class="bi bi-credit-card-2-front text-warning me-2"></i>Choose Online Payment Channel</h5>
                <div class="method-tab">
                    <button type="button" class="method-btn active" onclick="selectMethod('gcash', this)">
                        <i class="bi bi-qr-code-scan text-success fs-5"></i> GCash QR / Online
                    </button>
                    <button type="button" class="method-btn" onclick="selectMethod('maya', this)">
                        <i class="bi bi-wallet2 text-success fs-5"></i> Maya Payment
                    </button>
                    <button type="button" class="method-btn" onclick="selectMethod('card', this)">
                        <i class="bi bi-credit-card text-warning fs-5"></i> Debit / Credit Card
                    </button>
                </div>

                <!-- GCash QR Box -->
                <div class="qr-box" id="qrBox">
                    <h6 class="fw-bold mb-1" style="color: #064e3b;">Scan GCash QR Code to Pay</h6>
                    <p class="text-muted small mb-3">Open your GCash App > Scan QR > Enter Amount <strong>₱999.00</strong></p>
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=GCASH-POS-MINIMART-PAYMENT" class="qr-img" alt="GCash QR Code">
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <label class="form-label fw-bold small" style="color: #064e3b;">GCash Reference No. (Optional for Instant Simulation)</label>
                            <input type="text" name="reference_no" class="form-control text-center font-monospace" placeholder="e.g. 10023498124">
                        </div>
                    </div>
                </div>

                <button type="submit" class="submit-pay-btn">
                    <i class="bi bi-shield-lock-fill"></i>
                    <span>Verify Payment & Activate Store POS</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function selectPlan(planId, el) {
        document.querySelectorAll('.plan-card').forEach(c => c.classList.remove('selected'));
        el.classList.add('selected');
        document.getElementById('selectedPlan').value = planId;
    }

    function selectMethod(method, el) {
        document.querySelectorAll('.method-btn').forEach(b => b.classList.remove('active'));
        el.classList.add('active');
        document.getElementById('selectedMethod').value = method;
    }
</script>
@endsection
