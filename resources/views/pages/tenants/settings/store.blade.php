@extends('layouts.app')

@section('title', 'Store Settings & POS Branding | LikhaPOS - Cloud POS & CRM')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Executive Header --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
        <div class="d-flex align-items-center gap-2.5">
            <div class="kpi-icon-box blue" style="width:40px;height:40px;font-size:1.15rem;">
                <i class="bi bi-gear-wide-connected"></i>
            </div>
            <div>
                <h4 class="fw-black text-dark mb-0 font-mono" style="letter-spacing:-0.4px;">Store Settings & POS Branding</h4>
                <p class="text-muted extra-small mb-0">Manage business details, store logo, tax TIN, receipt footer, and subscription tier</p>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" id="btnPreviewReceipt" class="btn btn-outline-primary rounded-3 px-3 py-1.5 fw-bold extra-small shadow-xs hover-lift d-flex align-items-center gap-1.5">
                <i class="bi bi-eye-fill"></i>
                <span>Preview Thermal Receipt</span>
            </button>

            <a href="{{ route('subscription.checkout') }}" class="btn btn-warning rounded-3 px-3 py-1.5 fw-bold text-dark extra-small shadow-xs hover-lift d-flex align-items-center gap-1.5" style="background:linear-gradient(135deg, #f59e0b 0%, #d97706 100%);border:none;color:#fff !important;">
                <i class="bi bi-rocket-takeoff-fill"></i>
                <span>Upgrade Subscription Plan</span>
            </a>
        </div>
    </div>

    {{-- Subscription Plan & Free Trial Status Banner --}}
    <div class="p-3.5 mb-3 rounded-4 shadow-sm" style="background:linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important; color:#ffffff !important;">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:48px;height:48px;background:rgba(255,255,255,0.15);color:#fbbf24;font-size:1.4rem;">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                        <span class="badge font-mono extra-small fw-bold px-2.5 py-1 rounded-pill" style="background:#f59e0b;color:#000;">
                            {{ $tenant->subscription->name ?? 'Free Trial Tier (5 Days)' }}
                        </span>
                        @if(($tenant->payment_status ?? 'trial') === 'trial')
                            <span class="badge extra-small fw-bold px-2.5 py-1 rounded-pill" style="background:rgba(16,185,129,0.25);color:#34d399;border:1px solid rgba(52,211,153,0.4);">
                                🟢 5-Day Free Trial Active
                            </span>
                        @else
                            <span class="badge extra-small fw-bold px-2.5 py-1 rounded-pill" style="background:rgba(16,185,129,0.25);color:#34d399;border:1px solid rgba(52,211,153,0.4);">
                                💳 Active Subscription
                            </span>
                        @endif
                    </div>
                    <h5 class="fw-black text-white mb-0 font-mono" style="letter-spacing:-0.3px;">
                        {{ $daysRemaining }} Days Remaining in Plan Period
                    </h5>
                    <small class="text-white-50 extra-small font-mono">
                        Valid until: <strong class="text-white">{{ $tenant->subscription_end ? $tenant->subscription_end->format('F d, Y') : 'N/A' }}</strong>
                        | Limits: <strong class="text-white">{{ $usage['total_users']['limit'] ?? 3 }} Users</strong> ({{ $usage['admins']['limit'] ?? 1 }} Admin, {{ $usage['cashiers']['limit'] ?? 2 }} Cashiers)
                    </small>
                </div>
            </div>

            <a href="{{ route('subscription.checkout') }}" class="btn btn-light rounded-pill px-4 py-2 fw-bold text-dark extra-small shadow-sm hover-lift d-flex align-items-center gap-2">
                <i class="bi bi-credit-card-fill text-warning"></i>
                <span>Upgrade Plan Online</span>
            </a>
        </div>
    </div>

    {{-- Main Store Settings Form --}}
    <form id="storeSettingsForm" action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row g-3">
            {{-- Left Column: Store Logo & Receipt Branding --}}
            <div class="col-lg-4">
                {{-- Store Logo Card --}}
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-3 bg-white">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-image text-primary fs-5"></i>
                        <h6 class="fw-black text-dark mb-0 font-mono">Store Logo & Branding</h6>
                    </div>

                    <div class="text-center p-3 rounded-4 bg-light border mb-3">
                        <div class="position-relative d-inline-block mb-3">
                            @php
                                $hasLogo = $tenant->logo && Storage::disk('public')->exists($tenant->logo);
                                $logoUrl = $hasLogo ? Storage::url($tenant->logo) : asset('images/no_image.jpg');
                            @endphp
                            <img id="logoPreview" src="{{ $logoUrl }}" class="rounded-4 border shadow-sm object-fit-cover" style="width:120px;height:120px;" alt="Store Logo" onerror="this.onerror=null;this.src='{{ asset('images/no_image.jpg') }}';">
                        </div>
                        
                        <div>
                            <label for="logoInput" class="btn btn-sm btn-white border rounded-3 px-3 py-1.5 extra-small fw-bold text-dark shadow-xs hover-lift cursor-pointer">
                                <i class="bi bi-upload me-1 text-primary"></i> Upload Store Logo
                            </label>
                            <input type="file" id="logoInput" name="logo" class="d-none" accept="image/*">
                            <div class="text-muted extra-small mt-2 font-mono">Recommended: Square PNG/JPG (Max 2MB). Used on POS header & printed customer receipts.</div>
                        </div>
                    </div>

                    {{-- Receipt Branding --}}
                    <div class="mb-3">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Currency Symbol</label>
                        <input type="text" name="currency_symbol" class="form-control font-mono text-dark" value="{{ old('currency_symbol', $tenant->currency_symbol ?? '₱') }}" placeholder="₱" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Custom Receipt Header Subtitle</label>
                        <input type="text" name="header_text" class="form-control font-mono extra-small" value="{{ old('header_text', $tenant->header_text) }}" placeholder="e.g. Official Retailer • Open Daily 8AM - 10PM">
                    </div>

                    <div class="mb-2">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Receipt Footer Thank-You Note</label>
                        <textarea name="receipt_footer" rows="3" class="form-control font-mono extra-small" placeholder="e.g. Thank you for shopping with us! Please come back again.">{{ old('receipt_footer', $tenant->footer_text) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Right Column: Store Business Metadata --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-shop text-primary fs-5"></i>
                            <h6 class="fw-black text-dark mb-0 font-mono">Business Store Profile</h6>
                        </div>
                        <span class="badge bg-light border text-dark font-mono extra-small">
                            Code: <strong class="text-primary">{{ $tenant->business_code ?? 'MINI-001' }}</strong>
                        </span>
                    </div>

                    <div class="row g-3">
                        {{-- Business Name --}}
                        <div class="col-md-6">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Store Business Name <span class="text-danger">*</span></label>
                            <input type="text" name="business_name" class="form-control font-mono fw-bold text-dark" value="{{ old('business_name', $tenant->business_name) }}" required>
                        </div>

                        {{-- Owner Name --}}
                        <div class="col-md-6">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Store Owner / Manager Name <span class="text-danger">*</span></label>
                            <input type="text" name="owner_name" class="form-control font-mono text-dark" value="{{ old('owner_name', $tenant->owner_name) }}" required>
                        </div>

                        {{-- Phone Number --}}
                        <div class="col-md-6">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Store Contact Phone</label>
                            <input type="text" name="phone" class="form-control font-mono text-dark" value="{{ old('phone', $tenant->phone) }}" placeholder="e.g. 09171234567">
                        </div>

                        {{-- Email Address --}}
                        <div class="col-md-6">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Store Official Email</label>
                            <input type="email" name="email" class="form-control font-mono text-dark" value="{{ old('email', $tenant->email) }}" placeholder="e.g. store@example.com">
                        </div>

                        {{-- Tax TIN Number --}}
                        <div class="col-md-6">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">BIR Tax TIN Number</label>
                            <input type="text" name="tin" class="form-control font-mono text-dark" value="{{ old('tin', $tenant->tin) }}" placeholder="e.g. 123-456-789-000">
                        </div>

                        {{-- Store Address --}}
                        <div class="col-12">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Physical Store Address</label>
                            <textarea name="address" rows="2" class="form-control font-mono text-dark" placeholder="Enter complete store address for BIR receipt header...">{{ old('address', $tenant->address) }}</textarea>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <div class="pt-4 mt-3 border-top d-flex justify-content-end">
                        <button type="submit" id="btnSaveStoreSettings" class="btn btn-success rounded-3 px-4 py-2 fw-bold d-flex align-items-center gap-2 shadow-xs hover-lift" style="background:linear-gradient(135deg, #059669 0%, #047857 100%);border:none;">
                            <i class="bi bi-check-circle-fill fs-6"></i>
                            <span>Save Store Settings & Logo</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

</div>

{{-- Live Thermal Receipt Preview Modal --}}
<div class="modal fade" id="receiptPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-bottom bg-light py-2 px-3">
                <h6 class="modal-title font-mono fw-bold extra-small text-dark mb-0">
                    <i class="bi bi-receipt-cutoff text-primary me-1"></i> Live BIR Thermal Receipt
                </h6>
                <button type="button" class="btn-close extra-small" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-2 text-center bg-dark">
                <iframe id="receiptPreviewFrame" style="width:100%;height:460px;border:none;background:#fff;" class="rounded-3 shadow-xs"></iframe>
            </div>
            <div class="modal-footer border-top bg-light py-2 px-3 d-flex justify-content-between">
                <small class="extra-small text-muted font-mono">78mm Thermal Receipt Format</small>
                <button type="button" class="btn btn-secondary btn-sm rounded-3 extra-small px-3" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const logoInput = document.getElementById('logoInput');
    const logoPreview = document.getElementById('logoPreview');

    if (logoInput && logoPreview) {
        logoInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (evt) {
                    logoPreview.src = evt.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Handle Store Settings Form AJAX Submit
    document.getElementById('storeSettingsForm')?.addEventListener('submit', async function (e) {
        e.preventDefault();
        const btn = document.getElementById('btnSaveStoreSettings');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving Settings...';

        try {
            const formData = new FormData(this);
            const res = await fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await res.json();
            btn.disabled = false;
            btn.innerHTML = originalText;

            if (res.ok && data.success) {
                if (typeof appAlert === 'function') {
                    await appAlert({ title: 'Settings Saved!', text: data.message, type: 'success' });
                } else if (typeof Swal !== 'undefined') {
                    Swal.fire('Saved!', data.message, 'success');
                } else {
                    alert(data.message);
                }
                if (data.logo_url) {
                    document.getElementById('logoPreview').src = data.logo_url;
                }
            } else {
                const errMsg = data.message || (data.errors ? Object.values(data.errors).flat().join('\n') : 'Unable to save store settings.');
                if (typeof appAlert === 'function') {
                    await appAlert({ title: 'Validation Error', text: errMsg, type: 'warning' });
                } else if (typeof Swal !== 'undefined') {
                    Swal.fire('Error', errMsg, 'error');
                } else {
                    alert(errMsg);
                }
            }
        } catch (err) {
            console.error('Store settings save error:', err);
            btn.disabled = false;
            btn.innerHTML = originalText;
            alert('An error occurred while saving store settings.');
        }
    });

    // Preview Receipt Button Click
    document.getElementById('btnPreviewReceipt')?.addEventListener('click', function() {
        const form = document.getElementById('storeSettingsForm');
        const storeConfig = {
            business_name: form.querySelector('[name="business_name"]').value || 'MINIMART POS STORE',
            owner_name: form.querySelector('[name="owner_name"]').value || '',
            phone: form.querySelector('[name="phone"]').value || '',
            address: form.querySelector('[name="address"]').value || '',
            tin: form.querySelector('[name="tin"]').value || '',
            header_text: form.querySelector('[name="header_text"]').value || '',
            footer_text: form.querySelector('[name="receipt_footer"]').value || 'THANK YOU FOR YOUR PURCHASE!\nPLEASE COME AGAIN',
            logo: document.getElementById('logoPreview')?.src || null,
            currency_symbol: form.querySelector('[name="currency_symbol"]').value || '₱'
        };

        const sampleSale = {
            invoice_no: 'SI-SAMPLE-001',
            cashier_name: '{{ auth()->user()->name ?? "Store Cashier" }}',
            total_amount: 225.00,
            subtotal: 225.00,
            discount_amount: 0.00,
            tendered_amount: 300.00,
            change_amount: 75.00,
            items: [
                { name: 'Pocari Sweat 500ml', qty: 2, price: 65.00, subtotal: 130.00 },
                { name: 'Vienna Sausage 230g', qty: 1, price: 95.00, subtotal: 95.00 }
            ],
            payments: [
                { payment_method: 'cash', amount: 300.00 }
            ]
        };

        let html = '';
        if (typeof window.buildBIRThermalReceiptHTML === 'function') {
            html = window.buildBIRThermalReceiptHTML(sampleSale, storeConfig);
        } else {
            html = '<div style="padding:20px;text-align:center;font-family:sans-serif;">Receipt generator engine loading...</div>';
        }

        const frame = document.getElementById('receiptPreviewFrame');
        if (frame) {
            const doc = frame.contentWindow.document;
            doc.open();
            doc.write(html);
            doc.close();
            const modal = new bootstrap.Modal(document.getElementById('receiptPreviewModal'));
            modal.show();
        }
    });
});
</script>
@endpush
@endsection