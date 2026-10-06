@extends('layouts.sa')

@section('title', 'Subscription Payment Verifications')

@section('content')
<div class="container-fluid px-0">
    <x-page-header
        title="Subscription Payment Verifications"
        subtitle="Suriin ang mga payment reference mula sa QRPH, GCash, o Maya at i-activate ang subscription ng mga tindahan."
    >
        <x-slot:action>
            <a href="{{ route('sa.tenants.index') }}" class="btn btn-outline-secondary rounded-3">
                <i class="bi bi-buildings me-1"></i> Lahat ng Tenants
            </a>
        </x-slot:action>
    </x-page-header>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 d-flex align-items-center" role="alert">
            <i class="bi bi-check-circle-fill fs-5 me-2 text-success"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show rounded-3 shadow-sm border-0 d-flex align-items-center" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-5 me-2 text-warning"></i>
            <div>{{ session('warning') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Stat Counters -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 {{ $counts['pending'] > 0 ? 'bg-warning-subtle border border-warning' : 'bg-white' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Pending Verifications</div>
                        <div class="h3 fw-bold mb-0 {{ $counts['pending'] > 0 ? 'text-warning-emphasis' : 'text-dark' }}">
                            {{ $counts['pending'] }}
                        </div>
                    </div>
                    <div class="rounded-3 p-3 bg-warning text-dark shadow-sm">
                        <i class="bi bi-hourglass-split fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Active / Paid Subscriptions</div>
                        <div class="h3 fw-bold mb-0 text-success">{{ $counts['paid'] }}</div>
                    </div>
                    <div class="rounded-3 p-3 bg-success-subtle text-success">
                        <i class="bi bi-patch-check-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Rejected / Incomplete</div>
                        <div class="h3 fw-bold mb-0 text-danger">{{ $counts['rejected'] }}</div>
                    </div>
                    <div class="rounded-3 p-3 bg-danger-subtle text-danger">
                        <i class="bi bi-x-circle-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Total Tenants Registered</div>
                        <div class="h3 fw-bold mb-0 text-primary">{{ $counts['all'] }}</div>
                    </div>
                    <div class="rounded-3 p-3 bg-primary-subtle text-primary">
                        <i class="bi bi-buildings fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white border-bottom p-3">
            <ul class="nav nav-pills card-header-pills gap-2">
                <li class="nav-item">
                    <a class="nav-link rounded-3 px-3 py-2 fw-bold {{ $statusFilter === 'pending' ? 'active bg-warning text-dark' : 'text-secondary' }}" 
                       href="{{ route('sa.subscriptions.verifications', ['tab' => 'pending']) }}">
                        <i class="bi bi-hourglass-top me-1"></i> Nangangailangan ng Verification
                        @if($counts['pending'] > 0)
                            <span class="badge bg-danger rounded-pill ms-1">{{ $counts['pending'] }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link rounded-3 px-3 py-2 fw-bold {{ $statusFilter === 'paid' ? 'active bg-success text-white' : 'text-secondary' }}" 
                       href="{{ route('sa.subscriptions.verifications', ['tab' => 'paid']) }}">
                        <i class="bi bi-check2-circle me-1"></i> Bayad na / Aktibo ({{ $counts['paid'] }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link rounded-3 px-3 py-2 fw-bold {{ $statusFilter === 'rejected' ? 'active bg-danger text-white' : 'text-secondary' }}" 
                       href="{{ route('sa.subscriptions.verifications', ['tab' => 'rejected']) }}">
                        <i class="bi bi-x-circle me-1"></i> Tinanggihan / Rejected ({{ $counts['rejected'] }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link rounded-3 px-3 py-2 fw-bold {{ $statusFilter === 'all' ? 'active bg-dark text-white' : 'text-secondary' }}" 
                       href="{{ route('sa.subscriptions.verifications', ['tab' => 'all']) }}">
                        <i class="bi bi-list-ul me-1"></i> Lahat ({{ $counts['all'] }})
                    </a>
                </li>
            </ul>
        </div>

        <div class="card-body p-0">
            @if($tenants->isEmpty())
                <div class="text-center py-5">
                    <div class="display-6 text-muted mb-2"><i class="bi bi-check-all"></i></div>
                    <h5 class="fw-bold text-dark">Walang mga transaksyon sa ilalim ng tab na ito</h5>
                    <p class="text-muted small">Lahat ng subscription payments ay naproseso na o walang nakabinbin.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-uppercase extra-small text-muted fw-bold">
                            <tr>
                                <th class="ps-4">Tindahan / Tenant</th>
                                <th>Plan & Halaga</th>
                                <th>Paraan ng Bayad</th>
                                <th>Reference Number</th>
                                <th>Nagbayad (Sender)</th>
                                <th>Resibo / Proof</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Aksyon</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tenants as $tenant)
                                @php
                                    $targetPlan = $tenant->pendingPlan ?? $tenant->subscription;
                                    $methodBadge = match(strtolower($tenant->payment_method ?? '')) {
                                        'qrph' => ['bg' => '#0284c7', 'text' => 'QRPH Universal', 'icon' => 'bi-qr-code'],
                                        'gcash' => ['bg' => '#005CE6', 'text' => 'GCash', 'icon' => 'bi-wallet2'],
                                        'maya' => ['bg' => '#10B981', 'text' => 'Maya', 'icon' => 'bi-credit-card-2-front'],
                                        default => ['bg' => '#6b7280', 'text' => strtoupper($tenant->payment_method ?? 'MANUAL'), 'icon' => 'bi-cash'],
                                    };
                                @endphp
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-3 p-2 bg-light border text-dark fw-bold text-center" style="width: 42px; height: 42px;">
                                                <i class="bi bi-shop fs-5"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $tenant->business_name }}</div>
                                                <div class="extra-small text-muted font-monospace">
                                                    <code>{{ $tenant->business_code }}</code> • {{ $tenant->owner_name }}
                                                </div>
                                                @if($tenant->phone)
                                                    <a href="tel:{{ $tenant->phone }}" class="extra-small text-primary text-decoration-none">
                                                        <i class="bi bi-telephone me-1"></i>{{ $tenant->phone }}
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $targetPlan?->name ?? 'Standard Plan' }}</div>
                                        <div class="text-success fw-bold small">
                                            ₱{{ number_format($tenant->payment_amount ?? $targetPlan?->price ?? 0, 2) }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge text-white px-2 py-1.5 rounded-pill" style="background-color: {{ $methodBadge['bg'] }}; font-size: 0.78rem;">
                                            <i class="bi {{ $methodBadge['icon'] }} me-1"></i> {{ $methodBadge['text'] }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($tenant->payment_reference)
                                            <div class="d-flex align-items-center gap-1.5">
                                                <code class="fw-bold px-2 py-1 bg-light border rounded text-dark fs-6">{{ $tenant->payment_reference }}</code>
                                                <button type="button" class="btn btn-sm btn-light border p-1" title="Kopyahin" onclick="navigator.clipboard.writeText('{{ $tenant->payment_reference }}'); alert('Reference # copied!');">
                                                    <i class="bi bi-clipboard"></i>
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-muted extra-small">Walang reference #</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="small fw-bold text-dark">{{ $tenant->payment_sender_name ?? '—' }}</div>
                                        @if($tenant->payment_sender_phone)
                                            <div class="extra-small text-muted font-monospace">
                                                <a href="tel:{{ $tenant->payment_sender_phone }}" class="text-decoration-none text-muted">
                                                    <i class="bi bi-telephone-fill me-1 text-primary"></i>{{ $tenant->payment_sender_phone }}
                                                </a>
                                            </div>
                                        @endif
                                        @if($tenant->payment_submitted_at)
                                            <div class="extra-small text-muted mt-0.5">
                                                {{ $tenant->payment_submitted_at->diffForHumans() }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($tenant->payment_proof)
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-3 d-inline-flex align-items-center gap-1"
                                                    onclick="showProofModal('{{ asset('storage/' . $tenant->payment_proof) }}', '{{ addslashes($tenant->business_name) }} - Ref: {{ $tenant->payment_reference }}')">
                                                <i class="bi bi-image"></i>
                                                <span>Tingnan ang Resibo</span>
                                            </button>
                                        @else
                                            <span class="badge bg-light text-muted border">Walang Screenshot</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($tenant->payment_status === 'pending_verification')
                                            <span class="badge bg-warning text-dark px-2.5 py-1.5 rounded-pill fw-bold">
                                                <i class="bi bi-hourglass-split me-1"></i> Awaiting Verification
                                            </span>
                                        @elseif($tenant->payment_status === 'paid')
                                            <span class="badge bg-success text-white px-2.5 py-1.5 rounded-pill fw-bold">
                                                <i class="bi bi-check-circle-fill me-1"></i> Verified & Paid
                                            </span>
                                        @elseif($tenant->payment_status === 'rejected')
                                            <span class="badge bg-danger text-white px-2.5 py-1.5 rounded-pill fw-bold" title="{{ $tenant->payment_notes }}">
                                                <i class="bi bi-x-circle-fill me-1"></i> Rejected
                                            </span>
                                        @else
                                            <span class="badge bg-secondary text-white px-2.5 py-1.5 rounded-pill">
                                                {{ ucfirst($tenant->payment_status) }}
                                            </span>
                                        @endif

                                        @if($tenant->payment_notes)
                                            <div class="extra-small text-danger mt-1 text-truncate" style="max-width: 140px;" title="{{ $tenant->payment_notes }}">
                                                {{ $tenant->payment_notes }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        @if($tenant->payment_status === 'pending_verification')
                                            <div class="d-flex justify-content-end gap-1.5">
                                                <!-- Approve Form -->
                                                <form method="POST" action="{{ route('sa.subscriptions.approve', $tenant->id) }}" onsubmit="return confirm('Kumpirmahin ang pag-apruba sa bayad ng {{ $tenant->business_name }} ({{ $targetPlan?->name }})? Magiging aktibo agad ang kanilang POS.');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success rounded-3 fw-bold d-inline-flex align-items-center gap-1 shadow-sm px-2.5">
                                                        <i class="bi bi-check-lg"></i>
                                                        <span>Aprubahan</span>
                                                    </button>
                                                </form>

                                                <!-- Reject Modal Trigger -->
                                                <button type="button" class="btn btn-sm btn-outline-danger rounded-3 fw-bold"
                                                        onclick="openRejectModal({{ $tenant->id }}, '{{ addslashes($tenant->business_name) }}')">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            </div>
                                        @else
                                            <!-- Allow SA to manually re-approve if needed -->
                                            <form method="POST" action="{{ route('sa.subscriptions.approve', $tenant->id) }}" onsubmit="return confirm('Gusto mo bang i-renew / i-activate ang {{ $tenant->business_name }}?');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-light border rounded-3 text-muted" title="Re-activate / Extend">
                                                    <i class="bi bi-arrow-clockwise"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($tenants->hasPages())
                    <div class="p-3 border-top d-flex justify-content-end">
                        {{ $tenants->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

<!-- Receipt Proof Preview Modal -->
<div class="modal fade" id="proofModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white p-3 border-0">
                <h6 class="modal-title fw-bold" id="proofModalTitle"><i class="bi bi-receipt me-2"></i>Proof of Payment</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-3 bg-light">
                <img id="proofModalImage" src="" alt="Proof Screenshot" class="img-fluid rounded-3 shadow-sm" style="max-height: 75vh; object-fit: contain;">
            </div>
            <div class="modal-footer bg-white border-top p-2 justify-content-between">
                <a id="proofModalDownload" href="" target="_blank" class="btn btn-sm btn-outline-primary rounded-3">
                    <i class="bi bi-box-arrow-up-right me-1"></i> Buksan sa Bagong Tab
                </a>
                <button type="button" class="btn btn-sm btn-secondary rounded-3" data-bs-dismiss="modal">Isara</button>
            </div>
        </div>
    </div>
</div>

<!-- Rejection Reason Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form id="rejectForm" method="POST" action="">
                @csrf
                <div class="modal-header bg-danger text-white p-3 border-0">
                    <h6 class="modal-title fw-bold"><i class="bi bi-exclamation-octagon me-2"></i>Tanggihan ang Payment Verification</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="small text-muted mb-3">
                        Tukuyin ang dahilan upang makita ito ng may-ari ng <strong id="rejectTenantName" class="text-dark"></strong> sa kanilang screen at makapag-submit ng tamang reference o resibo.
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Dahilan ng Rejection:</label>
                        <select class="form-select rounded-3 mb-2" onchange="if(this.value) document.getElementById('rejectionReasonText').value = this.value;">
                            <option value="">-- Pumili ng karaniwang dahilan --</option>
                            <option value="Hindi tugma ang Reference Number sa aming GCash/Maya records.">Hindi tugma ang Reference Number</option>
                            <option value="Malabo o putol ang screenshot ng resibo. Paki-upload ang buong transaction receipt.">Malabo o putol ang screenshot ng resibo</option>
                            <option value="Kulang ang naipadalang halaga kumpara sa napiling plan.">Kulang ang naipadalang halaga</option>
                            <option value="Hindi pa pumasok ang pondo sa aming wallet. Paki-check muli kung pumasok na sa inyong e-wallet.">Hindi pumasok ang pondo</option>
                        </select>
                        <textarea name="rejection_reason" id="rejectionReasonText" class="form-control rounded-3" rows="3" required placeholder="Hal. Hindi tugma ang reference number, pakitawagan ang 0912-894-1731..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top p-3">
                    <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Kanselahin</button>
                    <button type="submit" class="btn btn-danger rounded-3 fw-bold px-3">
                        <i class="bi bi-x-circle me-1"></i> I-reject ang Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function showProofModal(imgUrl, title) {
        document.getElementById('proofModalImage').src = imgUrl;
        document.getElementById('proofModalDownload').href = imgUrl;
        document.getElementById('proofModalTitle').innerText = title;
        new bootstrap.Modal(document.getElementById('proofModal')).show();
    }

    function openRejectModal(tenantId, tenantName) {
        document.getElementById('rejectTenantName').innerText = tenantName;
        document.getElementById('rejectForm').action = '/sa/subscriptions/verifications/' + tenantId + '/reject';
        document.getElementById('rejectionReasonText').value = '';
        new bootstrap.Modal(document.getElementById('rejectModal')).show();
    }
</script>
@endsection
