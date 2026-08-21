@extends('layouts.app')

@section('title', 'Register New Customer | CRM')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('customers.index') }}" class="btn btn-white border rounded-3 p-2 text-dark shadow-xs hover-lift d-inline-flex align-items-center justify-content-center" style="width:38px;height:38px;">
                    <i class="bi bi-arrow-left fs-6"></i>
                </a>
                <div>
                    <h4 class="fw-black text-dark mb-0 font-mono" style="letter-spacing:-0.5px;">Register Customer Profile</h4>
                </div>
            </div>
            <p class="text-muted small mb-0 ms-5 ps-1">
                Add a new customer to your store's CRM, configure loyalty tiers & credit limits.
            </p>
        </div>

        <div>
            <a href="{{ route('customers.index') }}" class="btn btn-white border rounded-3 px-3 py-2 fw-bold text-dark extra-small shadow-xs hover-lift">
                <i class="bi bi-x-circle me-1"></i> Cancel & Return
            </a>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
        <div class="card-header bg-transparent border-bottom p-3.5 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-3 bg-success bg-opacity-10 text-success p-2 d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                    <i class="bi bi-person-plus-fill fs-6"></i>
                </div>
                <h6 class="fw-bold text-dark mb-0">Customer Information & Account Settings</h6>
            </div>
            <span class="badge bg-light text-muted border extra-small font-mono">Store CRM</span>
        </div>

        <div class="card-body p-4">
            <form method="POST" action="{{ route('customers.store') }}">
                @csrf

                <div class="row g-3 mb-4">
                    {{-- Full Name --}}
                    <div class="col-md-6">
                        <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">
                            Customer Full Name <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-person"></i></span>
                            <input type="text" name="CustomerName" class="form-control border-start-0 @error('CustomerName') is-invalid @enderror" value="{{ old('CustomerName') }}" placeholder="e.g. Juan Dela Cruz" required autofocus>
                        </div>
                        @error('CustomerName')
                            <div class="text-danger extra-small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Customer Type --}}
                    <div class="col-md-6">
                        <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">
                            Account Tier / Type <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-crown"></i></span>
                            <select name="customer_type" class="form-select border-start-0" required>
                                <option value="regular" {{ old('customer_type') == 'regular' ? 'selected' : '' }}>🏷️ Regular Suki</option>
                                <option value="vip" {{ old('customer_type') == 'vip' ? 'selected' : '' }}>👑 VIP Customer</option>
                                <option value="business" {{ old('customer_type') == 'business' ? 'selected' : '' }}>🏢 Business / Corporate</option>
                                <option value="senior" {{ old('customer_type') == 'senior' ? 'selected' : '' }}>👵 Senior Citizen (20% Off)</option>
                                <option value="pwd" {{ old('customer_type') == 'pwd' ? 'selected' : '' }}>♿ PWD (20% Off)</option>
                                <option value="credit" {{ old('customer_type') == 'credit' ? 'selected' : '' }}>💳 Credit Customer (Utang Allowed)</option>
                            </select>
                        </div>
                    </div>

                    {{-- Company Name --}}
                    <div class="col-md-6">
                        <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">
                            Company / Organization Name <span class="text-muted fw-normal">(Optional)</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-building"></i></span>
                            <input type="text" name="company_name" class="form-control border-start-0" value="{{ old('company_name') }}" placeholder="e.g. ABC Enterprises">
                        </div>
                    </div>

                    {{-- Mobile Number --}}
                    <div class="col-md-6">
                        <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">
                            Mobile Number <span class="text-muted fw-normal">(Optional)</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-telephone"></i></span>
                            <input type="text" name="mobile_number" class="form-control border-start-0 font-mono" value="{{ old('mobile_number') }}" placeholder="e.g. 0917 123 4567">
                        </div>
                    </div>

                    {{-- Email Address --}}
                    <div class="col-md-6">
                        <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">
                            Email Address <span class="text-muted fw-normal">(Optional)</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" class="form-control border-start-0" value="{{ old('email') }}" placeholder="e.g. customer@gmail.com">
                        </div>
                    </div>

                    {{-- Credit Limit --}}
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">
                            Credit Limit (₱)
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted font-mono">₱</span>
                            <input type="number" step="0.01" min="0" name="credit_limit" class="form-control border-start-0 font-mono" value="{{ old('credit_limit', 0) }}" placeholder="0.00">
                        </div>
                    </div>

                    {{-- Discount Percent --}}
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">
                            Default Discount (%)
                        </label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0" max="100" name="discount_percent" class="form-control border-end-0 font-mono" value="{{ old('discount_percent', 0) }}" placeholder="0">
                            <span class="input-group-text bg-light border-start-0 text-muted font-mono">%</span>
                        </div>
                    </div>

                    {{-- Address --}}
                    <div class="col-12">
                        <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">
                            Billing / Delivery Address <span class="text-muted fw-normal">(Optional)</span>
                        </label>
                        <textarea name="CustomerAddress" rows="2" class="form-control" placeholder="e.g. Purok 3, Barangay San Juan, Tubajon">{{ old('CustomerAddress') }}</textarea>
                    </div>

                    {{-- Remarks --}}
                    <div class="col-12">
                        <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">
                            Internal Customer Notes / Remarks <span class="text-muted fw-normal">(Optional)</span>
                        </label>
                        <textarea name="remarks" rows="2" class="form-control" placeholder="Special preferences, delivery instructions, or notes...">{{ old('remarks') }}</textarea>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                    <a href="{{ route('customers.index') }}" class="btn btn-light border rounded-3 px-3 py-2 fw-bold text-muted extra-small">
                        Cancel
                    </a>

                    <button type="submit" class="btn btn-success rounded-3 px-4 py-2 fw-bold d-flex align-items-center gap-2 shadow-sm hover-lift" style="background:linear-gradient(135deg, #059669 0%, #047857 100%);border:none;">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Save Customer Profile</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
