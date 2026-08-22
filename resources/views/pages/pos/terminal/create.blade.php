@extends('layouts.app')
@section('title', 'Register POS Device')
@section('shortText', 'Register and configure a cashiering POS device for your store')
@section('content')
    <x-page-header />

    @php
        $tenantId = auth()->check() ? auth()->user()->tenant_id : null;
        $deviceCheck = $deviceCheck ?? (new \App\Services\Tenant\TenantSubscriptionService())->canCreateDevice($tenantId);
    @endphp

    <form action="{{ route('terminal.store') }}" method="POST">
        @csrf

        <div class="row justify-content-center">
            <div class="col-xl-8">

                <!-- Vibrant Emerald Gradient Card -->
                <div class="rounded-4 p-4 mb-4 shadow position-relative text-white" 
                     style="background: linear-gradient(135deg, #059669 0%, #047857 50%, #0f172a 100%) !important; border: 1px solid rgba(255,255,255,0.15) !important;">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center text-white" 
                                 style="width: 52px; height: 52px; background: rgba(255,255,255,0.2) !important; backdrop-filter: blur(10px); font-size: 1.5rem;">
                                <i class="bi bi-tablet-landscape"></i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge bg-white text-dark fw-bold rounded-pill px-2.5 py-0.5" style="font-size: 0.68rem; text-transform: uppercase;">Subscription Tier</span>
                                    <span style="color: rgba(255,255,255,0.85); font-size: 0.82rem;">Active Store Plan</span>
                                </div>
                                <h4 class="fw-bold mb-1 text-white" style="color: #ffffff !important;">
                                    {{ $deviceCheck['plan_name'] }}
                                </h4>
                                <div style="color: rgba(255,255,255,0.9); font-size: 0.88rem;">
                                    POS Devices Used: <span class="badge bg-warning text-dark fw-bold px-2.5 py-1 ms-1 fs-6">{{ $deviceCheck['current'] }} / {{ $deviceCheck['limit'] }} Allowed</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            @if(!$deviceCheck['allowed'])
                                <a href="{{ route('subscription.checkout') }}" class="btn btn-warning fw-bold px-4 py-2.5 rounded-3 text-dark shadow d-inline-flex align-items-center gap-2">
                                    <i class="bi bi-arrow-up-circle-fill fs-5"></i>
                                    <span>Upgrade Plan for More Devices</span>
                                </a>
                            @else
                                <div class="d-flex align-items-center gap-2 px-3 py-2 rounded-pill shadow-sm" style="background: rgba(255,255,255,0.18) !important; border: 1px solid rgba(255,255,255,0.3) !important;">
                                    <i class="bi bi-check-circle-fill text-warning fs-5"></i>
                                    <span class="fw-bold text-white small" style="color: #ffffff !important;">Device Slots Available</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Clean Form Card -->
                <div class="card border shadow-sm rounded-4 mb-4" style="background: #ffffff !important; border-color: #e2e8f0 !important;">
                    <div class="card-body p-4">
                        @if ($errors->any())
                            <div class="alert alert-danger rounded-3 mb-4">
                                <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Pakisuri ang mga sumusunod na babala:</div>
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                            <div class="rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background-color: #ecfdf5 !important; color: #059669 !important;">
                                <i class="bi bi-tablet-landscape-fill fs-3"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-0">
                                    Register POS Device
                                </h5>
                                <div class="text-muted small">
                                    I-configure ang POS device (tablet, cellphone, o POS counter hardware) para sa cashiering.
                                </div>
                            </div>
                        </div>

                        <!-- Form Fields in Structured Grid -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="terminal_name" class="form-label fw-bold text-dark small mb-1">
                                    Device Name / Counter Label <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       id="terminal_name" 
                                       name="terminal_name" 
                                       class="form-control rounded-3 py-2 px-3 border" 
                                       placeholder="e.g. Main Counter Tablet / Counter 1" 
                                       value="{{ old('terminal_name') }}" 
                                       required
                                       style="border-color: #cbd5e1 !important;">
                                <div class="form-text extra-small text-muted">
                                    Pangalan ng counter o device para madaling ma-identify ng cashier.
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="drawer_id" class="form-label fw-bold text-dark small mb-1">
                                    Cash Drawer <span class="badge bg-light text-muted border fw-normal">Auto-Assigned</span>
                                </label>
                                <select id="drawer_id" name="drawer_id" class="form-select rounded-3 py-2 px-3 border" style="border-color: #cbd5e1 !important;">
                                    <option value="">-- Awtomatikong Gagawa ng Cash Drawer --</option>
                                    @if(isset($drawers))
                                        @foreach($drawers as $drawer)
                                            <option value="{{ $drawer->id }}" @selected(old('drawer_id') == $drawer->id)>
                                                {{ $drawer->drawer_name }} ({{ $drawer->drawer_code }})
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="form-text extra-small text-muted">
                                    <i class="bi bi-info-circle text-success me-1"></i> Iwanang blangko para awtomatikong gawan ng sariling Cash Drawer.
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="status" class="form-label fw-bold text-dark small mb-1">
                                    Device Status <span class="text-danger">*</span>
                                </label>
                                <select id="status" name="status" class="form-select rounded-3 py-2 px-3 border" required style="border-color: #cbd5e1 !important;">
                                    <option value="active" @selected(old('status', 'active') == 'active')>🟢 Active (Pwedeng gamitin agad sa Sales)</option>
                                    <option value="inactive" @selected(old('status') == 'inactive')>🔴 Inactive</option>
                                    <option value="maintenance" @selected(old('status') == 'maintenance')>🟡 Maintenance Mode</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="remarks" class="form-label fw-bold text-dark small mb-1">
                                    Optional Remarks / Notes
                                </label>
                                <input type="text" 
                                       id="remarks" 
                                       name="remarks" 
                                       class="form-control rounded-3 py-2 px-3 border" 
                                       placeholder="e.g. Samsung Tab A7 @ Counter 1" 
                                       value="{{ old('remarks') }}"
                                       style="border-color: #cbd5e1 !important;">
                            </div>
                        </div>

                    </div>

                    <!-- Footer Action Bar -->
                    <div class="card-footer bg-light px-4 py-3 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 border-top" style="border-color: #e2e8f0 !important;">
                        <small class="text-muted">
                            <i class="bi bi-shield-check text-success me-1"></i> Ang bawat POS device ay may konektadong cash drawer para sa audit trail.
                        </small>

                        <div class="d-flex gap-2">
                            <a href="{{ route('terminal.index') }}" class="btn btn-light border px-4 rounded-3 fw-bold text-dark">
                                Cancel
                            </a>

                            @if($deviceCheck['allowed'])
                                <button type="submit" class="btn btn-success px-4 py-2.5 rounded-3 fw-bold text-white shadow-sm d-flex align-items-center gap-2" style="background-color: #059669 !important; border: none !important;">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <span>Register POS Device</span>
                                </button>
                            @else
                                <a href="{{ route('subscription.checkout') }}" class="btn btn-warning px-4 py-2.5 rounded-3 fw-bold text-dark shadow-sm d-flex align-items-center gap-2">
                                    <i class="bi bi-arrow-up-circle-fill fs-5"></i>
                                    <span>Upgrade Plan to Register Device</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>
@endsection
