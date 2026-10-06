@extends('layouts.sa')

@section('title', 'SuperAdmin CRM & Platform Monitoring — LikhaPOS')

@section('content')
<style>
    .crm-metric-card {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        transition: all 0.2s ease;
        position: relative;
        overflow: hidden;
    }
    .crm-metric-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.07);
        border-color: #cbd5e1;
    }
    .crm-metric-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }
    .crm-alert-card {
        background: linear-gradient(135deg, #fef3c7 0%, #fffbeb 100%);
        border: 2px solid #f59e0b;
        border-radius: 20px;
        padding: 1.5rem 1.75rem;
    }
</style>

<div class="container-fluid px-0">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-warning text-dark fw-bold px-2.5 py-1 rounded-pill">
                    <i class="bi bi-shield-lock-fill me-1"></i> PLATFORM SUPERADMIN CONSOLE
                </span>
                <span class="text-muted small">• Live CRM & Store Monitoring</span>
            </div>
            <h2 class="h3 fw-bold text-dark mb-0">Platform Overview & Store Analytics</h2>
            <p class="text-muted small mb-0">Subaybayan ang lahat ng registered stores, benta sa POS, at mag-apruba ng subscription payments.</p>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <a href="{{ route('sa.subscriptions.verifications') }}" class="btn btn-warning rounded-3 fw-bold px-3 py-2 text-dark d-flex align-items-center gap-2 shadow-sm" style="background: linear-gradient(135deg, #fbbf24, #f59e0b); border: 1px solid #d97706;">
                <i class="bi bi-patch-check-fill"></i>
                <span>Verifications</span>
                @if($metrics['pendingVerificationsCount'] > 0)
                    <span class="badge bg-danger rounded-pill px-2 py-0.5">{{ $metrics['pendingVerificationsCount'] }}</span>
                @endif
            </a>
            <a href="{{ route('sa.tenants.index') }}" class="btn btn-outline-dark rounded-3 fw-bold px-3 py-2 d-flex align-items-center gap-1.5">
                <i class="bi bi-buildings-fill"></i>
                <span>Tenants List</span>
            </a>
            <a href="{{ route('dashboard.index') }}" class="btn btn-success rounded-3 fw-bold px-3 py-2 d-flex align-items-center gap-1.5 shadow-sm">
                <i class="bi bi-shop"></i>
                <span>Store POS View</span>
            </a>
        </div>
    </div>

    <!-- Alert Banner if Pending Subscription Verifications Exist -->
    @if($metrics['pendingVerificationsCount'] > 0)
        <div class="crm-alert-card mb-4 shadow-sm">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle p-3 bg-warning text-dark flex-shrink-0 shadow-sm">
                        <i class="bi bi-hourglass-split fs-3"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-1">
                            May {{ $metrics['pendingVerificationsCount'] }} Subscription Payment(s) na Naghihintay ng Approval!
                        </h5>
                        <p class="text-secondary small mb-0">
                            May mga tindahan na nagsumite ng kanilang resibo mula sa QRPH, GCash, o Maya. Aprubahan upang maging aktibo agad ang kanilang POS engine.
                        </p>
                    </div>
                </div>
                <a href="{{ route('sa.subscriptions.verifications') }}" class="btn btn-dark rounded-3 fw-bold px-3 py-2.5 flex-shrink-0 d-inline-flex align-items-center gap-2">
                    <i class="bi bi-check-circle-fill text-warning"></i>
                    <span>Suriin ang mga Resibo Ngayon</span>
                </a>
            </div>
        </div>
    @endif

    <!-- Core CRM Metric Cards -->
    <div class="row g-3 mb-4">
        <!-- Stores Registered -->
        <div class="col-sm-6 col-xl-3">
            <div class="crm-metric-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Registered Stores</div>
                        <div class="h2 fw-bold text-dark mb-1">{{ $metrics['totalTenants'] }}</div>
                        <div class="extra-small text-success fw-bold">
                            <i class="bi bi-check-circle-fill me-1"></i>{{ $metrics['activeTenants'] }} Aktibong Tindahan
                        </div>
                    </div>
                    <div class="crm-metric-icon bg-primary-subtle text-primary">
                        <i class="bi bi-shop"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Verifications -->
        <div class="col-sm-6 col-xl-3">
            <div class="crm-metric-card {{ $metrics['pendingVerificationsCount'] > 0 ? 'border-warning bg-warning-subtle' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Pending Payments</div>
                        <div class="h2 fw-bold {{ $metrics['pendingVerificationsCount'] > 0 ? 'text-warning-emphasis' : 'text-dark' }} mb-1">
                            {{ $metrics['pendingVerificationsCount'] }}
                        </div>
                        <div class="extra-small text-muted">
                            {{ $metrics['trialTenants'] }} sa Free Trial
                        </div>
                    </div>
                    <div class="crm-metric-icon {{ $metrics['pendingVerificationsCount'] > 0 ? 'bg-warning text-dark shadow-sm' : 'bg-light text-muted' }}">
                        <i class="bi bi-credit-card-2-front-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total POS Sales Volume -->
        <div class="col-sm-6 col-xl-3">
            <div class="crm-metric-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Platform POS Sales</div>
                        <div class="h3 fw-bold text-success mb-1">₱{{ number_format($metrics['totalSalesVolume'], 0) }}</div>
                        <div class="extra-small text-muted">
                            Today: <strong>₱{{ number_format($metrics['todaySalesVolume'], 2) }}</strong>
                        </div>
                    </div>
                    <div class="crm-metric-icon bg-success-subtle text-success">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Catalog & CRM Suki Count -->
        <div class="col-sm-6 col-xl-3">
            <div class="crm-metric-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Catalog & Suki CRM</div>
                        <div class="h3 fw-bold text-dark mb-1">{{ number_format($metrics['totalProducts']) }} SKUs</div>
                        <div class="extra-small text-primary fw-bold">
                            <i class="bi bi-people-fill me-1"></i>{{ number_format($metrics['totalCustomers']) }} Suki Customers
                        </div>
                    </div>
                    <div class="crm-metric-icon bg-info-subtle text-info">
                        <i class="bi bi-boxes"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Subscription Verification Queue (Actionable Right Here!) -->
    @if($pendingVerifications->isNotEmpty())
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-header bg-white border-bottom p-3.5 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-warning text-dark px-2.5 py-1 rounded-pill fw-bold">
                        <i class="bi bi-bell-fill me-1"></i> URGENT ACTION
                    </span>
                    <h5 class="fw-bold text-dark mb-0">Subscription Payment Verifications Queue</h5>
                </div>
                <a href="{{ route('sa.subscriptions.verifications') }}" class="btn btn-sm btn-outline-secondary rounded-3">
                    Lahat ng Verifications <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase extra-small text-muted fw-bold">
                        <tr>
                            <th class="ps-4">Tindahan</th>
                            <th>Plano & Halaga</th>
                            <th>Channel</th>
                            <th>Reference Number</th>
                            <th>Nagbayad</th>
                            <th>Resibo</th>
                            <th class="text-end pe-4">Aksyon</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingVerifications as $pending)
                            @php
                                $plan = $pending->pendingPlan ?? $pending->subscription;
                                $channelColor = match(strtolower($pending->payment_method ?? '')) {
                                    'qrph' => '#0284c7',
                                    'gcash' => '#005CE6',
                                    'maya' => '#10B981',
                                    default => '#6b7280',
                                };
                            @endphp
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">{{ $pending->business_name }}</div>
                                    <div class="extra-small text-muted font-monospace">{{ $pending->business_code }} • {{ $pending->owner_name }}</div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $plan?->name ?? 'Suki Growth' }}</div>
                                    <div class="text-success fw-bold small">₱{{ number_format($pending->payment_amount ?? $plan?->price ?? 0, 2) }}</div>
                                </td>
                                <td>
                                    <span class="badge text-white px-2 py-1 rounded-pill extra-small" style="background-color: {{ $channelColor }};">
                                        {{ strtoupper($pending->payment_method ?? 'MANUAL') }}
                                    </span>
                                </td>
                                <td>
                                    <code class="fw-bold px-2 py-1 bg-light border rounded text-dark fs-6">{{ $pending->payment_reference ?? '—' }}</code>
                                </td>
                                <td>
                                    <div class="small fw-bold text-dark">{{ $pending->payment_sender_name ?? '—' }}</div>
                                    @if($pending->payment_sender_phone)
                                        <div class="extra-small text-muted font-monospace">{{ $pending->payment_sender_phone }}</div>
                                    @endif
                                </td>
                                <td>
                                    @if($pending->payment_proof)
                                        <a href="{{ asset('storage/' . $pending->payment_proof) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-3">
                                            <i class="bi bi-image me-1"></i> Resibo
                                        </a>
                                    @else
                                        <span class="text-muted extra-small">Walang image</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <form method="POST" action="{{ route('sa.subscriptions.approve', $pending->id) }}" class="d-inline" onsubmit="return confirm('Aprubahan at i-activate ang {{ $pending->business_name }}?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success rounded-3 fw-bold shadow-sm px-3">
                                            <i class="bi bi-check-lg me-1"></i> Aprubahan
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Two Column: Store CRM List & Plan Breakdown -->
    <div class="row g-4 mb-4">
        <!-- Stores CRM List & Quick Context Switcher -->
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-header bg-white border-bottom p-3.5 d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-shop me-2 text-primary"></i>Store Tenants & Context Switcher</h5>
                        <small class="text-muted">Maaaring pumasok bilang tenant sa kahit saang tindahan para sa direct support o testing.</small>
                    </div>
                    <a href="{{ route('sa.tenants.index') }}" class="btn btn-sm btn-light border rounded-3 fw-bold">
                        Tingnan Lahat ({{ $metrics['totalTenants'] }})
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-uppercase extra-small text-muted fw-bold">
                            <tr>
                                <th class="ps-4">Tindahan</th>
                                <th>May-ari & Telepono</th>
                                <th>Subscription</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Aksyon</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentTenants as $tenant)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark">{{ $tenant->business_name }}</div>
                                        <div class="extra-small text-muted font-monospace">{{ $tenant->business_code }}</div>
                                    </td>
                                    <td>
                                        <div class="small fw-semibold text-dark">{{ $tenant->owner_name }}</div>
                                        <div class="extra-small text-muted">{{ $tenant->phone ?? 'Walang phone' }}</div>
                                    </td>
                                    <td>
                                        <div class="small fw-bold text-dark">{{ $tenant->subscription?->name ?? 'Standard' }}</div>
                                        <div class="extra-small text-muted">
                                            @if($tenant->payment_status === 'paid')
                                                <span class="text-success"><i class="bi bi-patch-check-fill me-0.5"></i> Paid</span>
                                            @elseif($tenant->payment_status === 'trial')
                                                <span class="text-warning"><i class="bi bi-clock-fill me-0.5"></i> Trial</span>
                                            @elseif($tenant->payment_status === 'pending_verification')
                                                <span class="text-danger fw-bold"><i class="bi bi-hourglass-split me-0.5"></i> Verification</span>
                                            @else
                                                {{ ucfirst($tenant->payment_status) }}
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($tenant->status === 'active')
                                            <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1 extra-small">Aktibo</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2.5 py-1 extra-small">{{ ucfirst($tenant->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <!-- Switch Context as Tenant -->
                                        <a href="{{ route('sa.tenants.show', encrypt($tenant->id)) }}" class="btn btn-sm btn-outline-success rounded-3 fw-bold d-inline-flex align-items-center gap-1" title="I-access ang tindahang ito bilang tenant">
                                            <i class="bi bi-box-arrow-in-right"></i>
                                            <span>Bumukas bilang Tenant</span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Plan Breakdown & Platform Distribution -->
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-pie-chart-fill me-2 text-warning"></i>Plano Distribution</h5>
                <div class="d-flex flex-column gap-3">
                    @foreach($planBreakdown as $plan)
                        @php
                            $percentage = $metrics['totalTenants'] > 0 ? round(($plan['count'] / $metrics['totalTenants']) * 100) : 0;
                        @endphp
                        <div>
                            <div class="d-flex justify-content-between small fw-bold mb-1">
                                <span class="text-dark">{{ $plan['name'] }} (₱{{ number_format($plan['price'], 0) }})</span>
                                <span class="text-muted">{{ $plan['count'] }} tindahan ({{ $percentage }}%)</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage }}%;" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Recent System Activity Logs -->
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-clock-history me-2 text-primary"></i>Kamakailang Platform Activity</h6>
                <div class="d-flex flex-column gap-2.5" style="max-height: 280px; overflow-y: auto;">
                    @forelse($recentActivities as $act)
                        <div class="p-2 rounded-3 bg-light border-0 extra-small">
                            <div class="d-flex justify-content-between">
                                <span class="fw-bold text-dark">{{ $act['name'] }}</span>
                                <span class="text-muted">{{ $act['time'] }}</span>
                            </div>
                            <div class="text-secondary mt-0.5">{{ $act['description'] }}</div>
                        </div>
                    @empty
                        <div class="text-muted extra-small text-center py-3">Walang kamakailang aktibidad.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
