@extends('layouts.auth')

@section('title', 'Minimart POS & CRM Login')

@section('content')
<style>
    .pos-login-wrapper {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 1rem;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 50%, #f0fdf4 100%);
        font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
    }

    .pos-card-container {
        width: 100%;
        max-width: 980px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 24px;
        box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.08);
        overflow: hidden;
        display: flex;
        flex-direction: row;
    }

    @media (max-width: 768px) {
        .pos-card-container {
            flex-direction: column;
        }
        .pos-hero-side {
            display: none !important;
        }
    }

    .pos-hero-side {
        flex: 1.1;
        background: linear-gradient(135deg, #059669, #047857);
        padding: 3.5rem 3rem;
        color: #ffffff;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
    }

    .pos-hero-side::before {
        content: '';
        position: absolute;
        width: 320px;
        height: 320px;
        background: rgba(245, 158, 11, 0.18);
        border-radius: 50%;
        top: -100px;
        right: -100px;
    }

    .pos-form-side {
        flex: 1;
        padding: 3.5rem 3rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
        background: #ffffff;
    }

    .brand-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #fef3c7;
        color: #b45309;
        border: 1px solid rgba(245, 158, 11, 0.4);
        padding: 6px 16px;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        width: fit-content;
    }

    .hero-title {
        font-size: 2.25rem;
        font-weight: 800;
        line-height: 1.25;
        margin-bottom: 1rem;
        color: #ffffff;
    }

    .hero-title span {
        color: #fef3c7;
    }

    .hero-desc {
        color: rgba(255, 255, 255, 0.92);
        font-size: 0.975rem;
        line-height: 1.6;
        margin-bottom: 2rem;
    }

    .feature-pill-list {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .feature-pill {
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: #ffffff;
        padding: 8px 14px;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .feature-pill i {
        color: #fbbf24;
    }

    .form-title {
        color: #0f172a;
        font-size: 1.75rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
    }

    .form-title span {
        color: #059669;
    }

    .form-subtitle {
        color: #64748b;
        font-size: 0.9rem;
        margin-bottom: 2rem;
    }

    .custom-label {
        color: #334155;
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        display: block;
    }

    .custom-input-group {
        position: relative;
        margin-bottom: 1.25rem;
    }

    .custom-input-group i {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #059669;
        font-size: 1.1rem;
    }

    .custom-input {
        width: 100%;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px 16px 14px 46px;
        color: #0f172a;
        font-size: 0.95rem;
        transition: all 0.2s ease;
    }

    .custom-input:focus {
        outline: none;
        border-color: #059669;
        box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.15);
        background: #ffffff;
    }

    .pos-login-btn {
        width: 100%;
        background: linear-gradient(135deg, #059669, #047857);
        color: #ffffff;
        border: 1px solid #059669;
        border-radius: 12px;
        padding: 14px;
        font-weight: 800;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 4px 14px rgba(5, 150, 105, 0.25);
    }

    .pos-login-btn:hover {
        background: linear-gradient(135deg, #047857, #065f46);
        color: #ffffff;
        box-shadow: 0 6px 20px rgba(5, 150, 105, 0.35);
    }

    .register-prompt {
        text-align: center;
        margin-top: 1.75rem;
        color: #64748b;
        font-size: 0.9rem;
    }

    .register-link {
        color: #059669;
        font-weight: 700;
        text-decoration: none;
    }

    .register-link:hover {
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
        margin-bottom: 1.5rem;
        transition: color 0.15s ease;
    }

    .back-home-link:hover {
        color: #059669;
    }
</style>

<div class="pos-login-wrapper">
    <div class="pos-card-container">
        <!-- Hero Side -->
        <div class="pos-hero-side">
            <div>
                <div class="brand-badge">
                    <i class="bi bi-shop"></i> Minimart POS & CRM
                </div>
                <h1 class="hero-title">Accredited BIR POS & <span>Store Engine</span></h1>
                <p class="hero-desc">
                    Fast cashier checkout, 12% VAT calculations, Senior/PWD discount compliance, credit customer ledger, and real-time inventory management.
                </p>
                <div class="feature-pill-list">
                    <span class="feature-pill"><i class="bi bi-shield-check"></i> BIR Compliant Receipts</span>
                    <span class="feature-pill"><i class="bi bi-upc-scan"></i> Barcode Scanner Ready</span>
                    <span class="feature-pill"><i class="bi bi-phone"></i> Mobile & Tablet Touch UI</span>
                    <span class="feature-pill"><i class="bi bi-journal-text"></i> X & Z Reading Reports</span>
                </div>
            </div>
            <div style="margin-top: 2rem; border-top: 1px solid rgba(255,255,255,0.25); padding-top: 1rem;">
                <a href="{{ route('home') }}" class="text-white text-decoration-none small fw-semibold">
                    <i class="bi bi-arrow-left me-1"></i> Back to Main Marketing Website
                </a>
            </div>
        </div>

        <!-- Form Side -->
        <div class="pos-form-side">
            <a href="{{ route('home') }}" class="back-home-link">
                <i class="bi bi-arrow-left"></i> Back to Website
            </a>

            <div>
                <h2 class="form-title">Cashier <span>Sign In</span></h2>
                <p class="form-subtitle">Enter your store login credentials to open POS terminal.</p>
            </div>

            @if(isset($errors) && $errors->any())
                <div style="background: #fef2f2; border: 1px solid #fca5a5; border-radius: 12px; padding: 12px; margin-bottom: 1.5rem; color: #991b1b; font-size: 0.875rem;">
                    <ul style="margin: 0; padding-left: 1.25rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <a href="{{ route('google.redirect') }}" class="btn btn-outline-secondary w-100 py-2.5 mb-3 rounded-3 fw-bold d-flex align-items-center justify-content-center gap-2 border bg-white text-dark shadow-sm">
                <svg width="18" height="18" viewBox="0 0 18 18"><path fill="#4285F4" d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.259h2.908c1.702-1.567 2.684-3.874 2.684-6.617z"/><path fill="#34A853" d="M9 18c2.43 0 4.467-.806 5.956-2.18l-2.908-2.259c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332A8.997 8.997 0 0 0 9 18z"/><path fill="#FBBC05" d="M3.964 10.71A5.41 5.41 0 0 1 3.682 9c0-.593.102-1.17.282-1.71V4.958H.957A8.996 8.996 0 0 0 0 9c0 1.452.348 2.827.957 4.042l3.007-2.332z"/><path fill="#EA4335" d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0A8.997 8.997 0 0 0 .957 4.958L3.964 7.29C4.672 5.163 6.656 3.58 9 3.58z"/></svg>
                <span>Continue with Google</span>
            </a>

            <div class="d-flex align-items-center my-3">
                <hr class="flex-grow-1 m-0 text-muted">
                <span class="px-2 text-muted small fw-semibold">OR LOGIN WITH EMAIL</span>
                <hr class="flex-grow-1 m-0 text-muted">
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label class="custom-label">Email or Username</label>
                    <div class="custom-input-group">
                        <i class="bi bi-person"></i>
                        <input type="text" name="login" class="custom-input" placeholder="cashier@minimart.com" value="{{ old('login') }}" required autofocus>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="custom-label">Password</label>
                    <div class="custom-input-group">
                        <i class="bi bi-lock"></i>
                        <input type="password" name="password" class="custom-input" placeholder="••••••••" required>
                    </div>
                </div>

                <button type="submit" class="pos-login-btn">
                    <span>Open POS Terminal</span>
                    <i class="bi bi-arrow-right"></i>
                </button>
            </form>

            <div class="register-prompt">
                Don't have a Minimart store account? <br>
                <a href="{{ route('register') }}" class="register-link">Register Your Store Now</a>
            </div>
        </div>
    </div>
</div>
@endsection
