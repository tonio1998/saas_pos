@extends('layouts.auth')

@section('title', 'Minimart Store Quick Registration')

@section('content')
<style>
    .pos-register-wrapper {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2.5rem 1rem;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 50%, #f0fdf4 100%);
        font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
    }

    .pos-reg-card {
        width: 100%;
        max-width: 640px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 24px;
        padding: 2.5rem 2rem;
        box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.08);
        color: #0f172a;
    }

    .reg-header {
        text-align: center;
        margin-bottom: 1.75rem;
    }

    .reg-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #fef3c7;
        color: #b45309;
        border: 1px solid rgba(245, 158, 11, 0.4);
        padding: 5px 14px;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 700;
        margin-bottom: 0.75rem;
    }

    .reg-title {
        font-size: 1.85rem;
        font-weight: 800;
        margin-bottom: 0.4rem;
        color: #0f172a;
    }

    .reg-title span {
        color: #059669;
    }

    .reg-subtitle {
        color: #64748b;
        font-size: 0.9rem;
    }

    /* Subscription Level Radio Cards */
    .plan-selector-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        margin-bottom: 1.5rem;
    }

    @media (max-width: 640px) {
        .plan-selector-grid {
            grid-template-columns: 1fr;
        }
    }

    .plan-radio-card {
        border: 2px solid #e2e8f0;
        border-radius: 14px;
        padding: 10px 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: center;
        background: #f8fafc;
    }

    .plan-radio-card:hover {
        border-color: #059669;
        background: #ecfdf5;
    }

    .plan-radio-card.active {
        border-color: #059669;
        background: #ecfdf5;
        box-shadow: 0 4px 12px rgba(5, 150, 105, 0.12);
    }

    .plan-radio-card input {
        display: none;
    }

    .plan-card-name {
        font-weight: 800;
        font-size: 0.85rem;
        color: #0f172a;
    }

    .plan-card-price {
        font-weight: 700;
        font-size: 1.05rem;
        color: #059669;
    }

    .plan-card-limits {
        font-size: 0.72rem;
        color: #64748b;
        margin-top: 2px;
    }

    .reg-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.85rem;
    }

    @media (max-width: 640px) {
        .reg-grid {
            grid-template-columns: 1fr;
        }
    }

    .form-group-full {
        grid-column: 1 / -1;
    }

    .reg-label {
        color: #334155;
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 0.35rem;
        display: block;
    }

    .reg-input-wrap {
        position: relative;
        margin-bottom: 0.75rem;
    }

    .reg-input-wrap i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #059669;
    }

    .reg-input {
        width: 100%;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 14px 12px 42px;
        color: #0f172a;
        font-size: 0.95rem;
        transition: all 0.2s ease;
    }

    .reg-input:focus {
        outline: none;
        border-color: #059669;
        box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.15);
        background: #ffffff;
    }

    .reg-btn {
        width: 100%;
        background: linear-gradient(135deg, #059669, #047857);
        color: #ffffff;
        border: 1px solid #059669;
        border-radius: 12px;
        padding: 14px;
        font-weight: 800;
        font-size: 1.05rem;
        cursor: pointer;
        transition: all 0.2s ease;
        margin-top: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        box-shadow: 0 4px 14px rgba(5, 150, 105, 0.25);
    }

    .reg-btn:hover {
        background: linear-gradient(135deg, #047857, #065f46);
        color: #ffffff;
        box-shadow: 0 6px 20px rgba(5, 150, 105, 0.35);
    }

    .reg-footer {
        text-align: center;
        margin-top: 1.5rem;
        color: #64748b;
        font-size: 0.9rem;
    }

    .login-link {
        color: #059669;
        font-weight: 700;
        text-decoration: none;
    }

    .login-link:hover {
        text-decoration: underline;
    }

    .back-home-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #64748b;
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
        margin-bottom: 1.25rem;
        transition: color 0.15s ease;
    }

    .back-home-link:hover {
        color: #059669;
    }
</style>

<div class="pos-register-wrapper">
    <div class="pos-reg-card">
        <a href="{{ route('home') }}" class="back-home-link">
            <i class="bi bi-arrow-left"></i> Back to Marketing Website
        </a>

        <div class="reg-header">
            <span class="reg-badge">
                <i class="bi bi-lightning-charge-fill"></i> Fast 1-Minute Store Registration
            </span>
            <h1 class="reg-title">Register <span>Your Store Now</span></h1>
            <p class="reg-subtitle">Enter basic info to open your LikhaPOS store account instantly</p>
        </div>

        @if(isset($errors) && $errors->any())
            <div style="background: #fef2f2; border: 1px solid #fca5a5; border-radius: 12px; padding: 12px; margin-bottom: 1.25rem; color: #991b1b; font-size: 0.875rem;">
                <ul style="margin: 0; padding-left: 1.25rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <a href="{{ route('google.redirect') }}" class="btn btn-outline-secondary w-100 py-2.5 mb-3 rounded-3 fw-bold d-flex align-items-center justify-content-center gap-2 border bg-white text-dark shadow-sm">
            <svg width="18" height="18" viewBox="0 0 18 18"><path fill="#4285F4" d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.259h2.908c1.702-1.567 2.684-3.874 2.684-6.617z"/><path fill="#34A853" d="M9 18c2.43 0 4.467-.806 5.956-2.18l-2.908-2.259c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332A8.997 8.997 0 0 0 9 18z"/><path fill="#FBBC05" d="M3.964 10.71A5.41 5.41 0 0 1 3.682 9c0-.593.102-1.17.282-1.71V4.958H.957A8.996 8.996 0 0 0 0 9c0 1.452.348 2.827.957 4.042l3.007-2.332z"/><path fill="#EA4335" d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0A8.997 8.997 0 0 0 .957 4.958L3.964 7.29C4.672 5.163 6.656 3.58 9 3.58z"/></svg>
            <span>Sign Up with Google</span>
        </a>

        <div class="d-flex align-items-center my-3">
            <hr class="flex-grow-1 m-0 text-muted">
            <span class="px-2 text-muted small fw-semibold">OR REGISTER WITH EMAIL</span>
            <hr class="flex-grow-1 m-0 text-muted">
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Subscription Level Selector -->
            <label class="reg-label mb-2">Select Subscription Plan *</label>
            <div class="plan-selector-grid">
                @php
                    $plansList = isset($subscriptions) && count($subscriptions) > 0 ? $subscriptions : [
                        (object)['id' => 1, 'name' => 'Tindahan Starter', 'price' => 299, 'max_admin_accounts' => 1, 'max_cashier_accounts' => 1],
                        (object)['id' => 2, 'name' => 'Suki Growth', 'price' => 599, 'max_admin_accounts' => 1, 'max_cashier_accounts' => 3],
                        (object)['id' => 3, 'name' => 'Negosyo Pro', 'price' => 1299, 'max_admin_accounts' => 5, 'max_cashier_accounts' => 20],
                    ];
                    $currentSelected = old('subscription_id', $selectedPlanId ?? 2);
                @endphp

                @foreach($plansList as $planItem)
                    <label class="plan-radio-card {{ $currentSelected == $planItem->id ? 'active' : '' }}" onclick="selectPlan(this)">
                        <input type="radio" name="subscription_id" value="{{ $planItem->id }}" {{ $currentSelected == $planItem->id ? 'checked' : '' }}>
                        <div class="plan-card-name">{{ $planItem->name }}</div>
                        <div class="plan-card-price">₱{{ number_format($planItem->price, 0) }} <small class="text-muted fs-6">/mo</small></div>
                        <div class="plan-card-limits">{{ $planItem->max_admin_accounts ?? 1 }} Admin • {{ $planItem->max_cashier_accounts ?? 1 }} Cashier</div>
                    </label>
                @endforeach
            </div>

            <div class="reg-grid">
                <div class="form-group-full">
                    <label class="reg-label">Minimart / Store Name *</label>
                    <div class="reg-input-wrap">
                        <i class="bi bi-shop-window"></i>
                        <input type="text" name="business_name" class="reg-input" placeholder="e.g. San Jose Minimart & Grocery" value="{{ old('business_name') }}" required autofocus>
                    </div>
                </div>

                <div>
                    <label class="reg-label">Owner Full Name *</label>
                    <div class="reg-input-wrap">
                        <i class="bi bi-person"></i>
                        <input type="text" name="name" class="reg-input" placeholder="Juan Dela Cruz" value="{{ old('name') }}" required>
                    </div>
                </div>

                <div>
                    <label class="reg-label">Email Address *</label>
                    <div class="reg-input-wrap">
                        <i class="bi bi-envelope"></i>
                        <input type="email" name="email" class="reg-input" placeholder="owner@minimart.com" value="{{ old('email') }}" required>
                    </div>
                </div>

                <div>
                    <label class="reg-label">Password *</label>
                    <div class="reg-input-wrap">
                        <i class="bi bi-lock"></i>
                        <input type="password" name="password" class="reg-input" placeholder="At least 6 characters" required>
                    </div>
                </div>

                <div>
                    <label class="reg-label">Confirm Password *</label>
                    <div class="reg-input-wrap">
                        <i class="bi bi-shield-check"></i>
                        <input type="password" name="password_confirmation" class="reg-input" placeholder="Re-enter password" required>
                    </div>
                </div>
            </div>

            <button type="submit" class="reg-btn">
                <span>Create Store Account & Proceed</span>
                <i class="bi bi-arrow-right"></i>
            </button>
        </form>

        <div class="reg-footer">
            Already registered? <a href="{{ route('login') }}" class="login-link">Sign in to your store</a>
        </div>
    </div>
</div>

<script>
    function selectPlan(card) {
        document.querySelectorAll('.plan-radio-card').forEach(el => el.classList.remove('active'));
        card.classList.add('active');
        const radio = card.querySelector('input[type="radio"]');
        if (radio) radio.checked = true;
    }
</script>
@endsection
