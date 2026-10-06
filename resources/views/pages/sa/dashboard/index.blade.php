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
        border-radius: 20px;
        padding: 1.35rem 1.75rem;
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
                <span class="text-muted small">• Live CRM &amp; Store Monitoring</span>
            </div>
            <h2 class="h3 fw-bold text-dark mb-0">Platform Overview &amp; Store Analytics</h2>
            <p class="text-muted small mb-0">Monitor all registered retail stores, track real-time POS sales, oversee subscription due dates, and verify manual payments.</p>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <a href="{{ route('sa.subscriptions.monitoring') }}" class="btn btn-danger rounded-3 fw-bold px-3 py-2 text-white d-flex align-items-center gap-2 shadow-sm">
                <i class="bi bi-alarm-fill"></i>
                <span>Due Date Sentinel</span>
                @if(($metrics['dueSoonCount'] ?? 0) > 0)
                    <span class="badge bg-white text-danger rounded-pill px-2 py-0.5">{{ $metrics['dueSoonCount'] }}</span>
                @endif
            </a>
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

    <!-- Alert Banners -->
    <div class="row g-3 mb-4">
        @if($metrics['pendingVerificationsCount'] > 0)
            <div class="col-lg-{{ ($metrics['dueSoonCount'] ?? 0) > 0 ? '6' : '12' }}">
                <div class="crm-alert-card shadow-sm h-100" style="background: linear-gradient(135deg, #fef3c7 0%, #fffbeb 100%); border: 2px solid #f59e0b;">
                    <div class="d-flex align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle p-2.5 bg-warning text-dark flex-shrink-0 shadow-sm">
                                <i class="bi bi-hourglass-split fs-4"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0.5">
                                    {{ $metrics['pendingVerificationsCount'] }} Subscription Payment(s) Awaiting Verification
                                </h6>
                                <p class="text-secondary extra-small mb-0">
                                    Store owners uploaded payment receipts via QRPH, GCash, or Maya.
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('sa.subscriptions.verifications') }}" class="btn btn-dark btn-sm rounded-3 fw-bold px-3 py-2 text-nowrap">
                            Review Receipts
                        </a>
                    </div>
                </div>
            </div>
        @endif

        @if(($metrics['dueSoonCount'] ?? 0) > 0)
            <div class="col-lg-{{ $metrics['pendingVerificationsCount'] > 0 ? '6' : '12' }}">
                <div class="crm-alert-card shadow-sm h-100" style="background: linear-gradient(135deg, #fee2e2 0%, #fef2f2 100%); border: 2px solid #ef4444;">
                    <div class="d-flex align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle p-2.5 bg-danger text-white flex-shrink-0 shadow-sm">
                                <i class="bi bi-alarm-fill fs-4"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-danger mb-0.5">
                                    {{ $metrics['dueSoonCount'] }} Store(s) Expiring Within 7 Days!
                                </h6>
                                <p class="text-secondary extra-small mb-0">
                                    Proactively reach out to store owners or extend grace periods to maintain service continuity.
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('sa.subscriptions.monitoring') }}" class="btn btn-danger btn-sm rounded-3 fw-bold px-3 py-2 text-nowrap">
                            View Sentinel
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>

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
                            <i class="bi bi-check-circle-fill me-1"></i>{{ $metrics['activeTenants'] }} Active Subscriptions
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
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Pending Verifications</div>
                        <div class="h2 fw-bold {{ $metrics['pendingVerificationsCount'] > 0 ? 'text-warning-emphasis' : 'text-dark' }} mb-1">
                            {{ $metrics['pendingVerificationsCount'] }}
                        </div>
                        <div class="extra-small text-muted">
                            {{ $metrics['trialTenants'] }} in Free Trial
                        </div>
                    </div>
                    <div class="crm-metric-icon {{ $metrics['pendingVerificationsCount'] > 0 ? 'bg-warning text-dark shadow-sm' : 'bg-light text-muted' }}">
                        <i class="bi bi-credit-card-2-front-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Due Sentinel KPI -->
        <div class="col-sm-6 col-xl-3">
            <div class="crm-metric-card {{ ($metrics['due3DaysCount'] ?? 0) > 0 ? 'border-danger bg-danger-subtle' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Expiring Soon (&le;7d)</div>
                        <div class="h2 fw-bold {{ ($metrics['due3DaysCount'] ?? 0) > 0 ? 'text-danger' : 'text-dark' }} mb-1">
                            {{ $metrics['dueSoonCount'] ?? 0 }}
                        </div>
                        <div class="extra-small {{ ($metrics['due3DaysCount'] ?? 0) > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>{{ $metrics['due3DaysCount'] ?? 0 }} urgent (&le;3 days)
                        </div>
                    </div>
                    <div class="crm-metric-icon {{ ($metrics['dueSoonCount'] ?? 0) > 0 ? 'bg-danger text-white shadow-sm' : 'bg-light text-muted' }}">
                        <i class="bi bi-alarm-fill"></i>
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
    </div>

    <!-- Due Date Sentinel Watchlist Widget -->
    @if(isset($tenantsDueSoon) && $tenantsDueSoon->isNotEmpty())
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 border-start border-4 border-danger">
            <div class="card-header bg-white border-bottom p-3.5 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-danger text-white px-2.5 py-1 rounded-pill fw-bold">
                        <i class="bi bi-alarm-fill me-1"></i> DUE DATE SENTINEL
                    </span>
                    <h5 class="fw-bold text-dark mb-0">Subscription Expiration Watchlist</h5>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('sa.subscriptions.monitoring') }}" class="btn btn-sm btn-outline-danger rounded-3 fw-bold">
                        Open Full Due Sentinel <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase extra-small text-muted fw-bold">
                        <tr>
                            <th class="ps-4">Store Name</th>
                            <th>Contact / Owner</th>
                            <th>Current Plan</th>
                            <th>Expiration Date</th>
                            <th>Days Remaining</th>
                            <th class="text-end pe-4">Quick Renewal Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tenantsDueSoon as $dueTenant)
                            @php
                                $days = $dueTenant->days_until_due;
                                $status = $dueTenant->due_status;
                            @endphp
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">{{ $dueTenant->business_name }}</div>
                                    <div class="extra-small text-muted font-monospace">{{ $dueTenant->business_code }}</div>
                                </td>
                                <td>
                                    <div class="small fw-semibold text-dark">{{ $dueTenant->owner_name }}</div>
                                    @if($dueTenant->phone)
                                        <a href="tel:{{ $dueTenant->phone }}" class="extra-small text-decoration-none text-primary fw-bold">
                                            <i class="bi bi-telephone-fill me-0.5"></i> {{ $dueTenant->phone }}
                                        </a>
                                    @else
                                        <span class="extra-small text-muted">No phone</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="small fw-bold text-dark">{{ $dueTenant->subscription?->name ?? 'Standard' }}</div>
                                    <div class="extra-small text-muted">₱{{ number_format($dueTenant->subscription?->price ?? 300, 2) }}/mo</div>
                                </td>
                                <td>
                                    <div class="small fw-bold {{ $days !== null && $days < 0 ? 'text-danger' : 'text-dark' }}">
                                        {{ $dueTenant->subscription_end ? \Carbon\Carbon::parse($dueTenant->subscription_end)->format('M d, Y') : '—' }}
                                    </div>
                                    <div class="extra-small text-muted">
                                        {{ $dueTenant->subscription_end ? \Carbon\Carbon::parse($dueTenant->subscription_end)->diffForHumans() : '' }}
                                    </div>
                                </td>
                                <td>
                                    @if($days === null)
                                        <span class="badge bg-secondary-subtle text-secondary rounded-pill extra-small">No Expiry</span>
                                    @elseif($days < 0)
                                        <span class="badge bg-danger text-white rounded-pill extra-small px-2.5 py-1">
                                            <i class="bi bi-x-octagon-fill me-1"></i> OVERDUE ({{ abs($days) }}d ago)
                                        </span>
                                    @elseif($days === 0)
                                        <span class="badge bg-danger text-white rounded-pill extra-small px-2.5 py-1 animate-pulse">
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i> EXPIRES TODAY
                                        </span>
                                    @elseif($days <= 3)
                                        <span class="badge bg-danger-subtle text-danger rounded-pill extra-small px-2.5 py-1 fw-bold">
                                            <i class="bi bi-clock-fill me-1"></i> {{ $days }} DAYS LEFT
                                        </span>
                                    @elseif($days <= 7)
                                        <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill extra-small px-2.5 py-1 fw-bold">
                                            {{ $days }} DAYS LEFT
                                        </span>
                                    @else
                                        <span class="badge bg-success-subtle text-success rounded-pill extra-small px-2.5 py-1">
                                            {{ $days }} days left
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-inline-flex align-items-center gap-1.5">
                                        @if($dueTenant->phone)
                                            <a href="tel:{{ $dueTenant->phone }}" class="btn btn-sm btn-outline-primary rounded-3 py-1 px-2.5" title="Direct Phone Call">
                                                <i class="bi bi-telephone-outbound-fill"></i>
                                            </a>
                                        @endif
                                        <a href="{{ route('sa.subscriptions.monitoring') }}" class="btn btn-sm btn-dark rounded-3 fw-bold py-1 px-2.5">
                                            <i class="bi bi-clock-history me-1"></i> Manage
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Live Subscription Verification Queue -->
    @if($pendingVerifications->isNotEmpty())
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-header bg-white border-bottom p-3.5 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-warning text-dark px-2.5 py-1 rounded-pill fw-bold">
                        <i class="bi bi-bell-fill me-1"></i> URGENT ACTION
                    </span>
                    <h5 class="fw-bold text-dark mb-0">Subscription Payment Verifications Queue</h5>
                </div>
                <a href="{{ route('sa.subscriptions.verifications') }}" class="btn btn-sm btn-outline-secondary rounded-3">
                    All Verifications <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase extra-small text-muted fw-bold">
                        <tr>
                            <th class="ps-4">Store Name</th>
                            <th>Plan &amp; Amount</th>
                            <th>Channel</th>
                            <th>Reference Number</th>
                            <th>Sender</th>
                            <th>Receipt</th>
                            <th class="text-end pe-4">Action</th>
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
                                    <div class="fw-bold text-dark">{{ $plan?->name ?? 'Standard' }}</div>
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
                                            <i class="bi bi-image me-1"></i> Receipt
                                        </a>
                                    @else
                                        <span class="text-muted extra-small">No receipt</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <form method="POST" action="{{ route('sa.subscriptions.approve', $pending->id) }}" class="d-inline" onsubmit="return confirm('Approve payment and activate {{ $pending->business_name }}?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success rounded-3 fw-bold shadow-sm px-3">
                                            <i class="bi bi-check-lg me-1"></i> Approve
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
                <div class="card-header bg-white border-bottom p-3.5 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-shop me-2 text-primary"></i>Store Tenants &amp; Context Switcher</h5>
                        <small class="text-muted">Enter store context as any tenant for real-time support, configuration, and diagnostics.</small>
                    </div>
                    <a href="{{ route('sa.tenants.index') }}" class="btn btn-sm btn-light border rounded-3 fw-bold">
                        View All ({{ $metrics['totalTenants'] }})
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-uppercase extra-small text-muted fw-bold">
                            <tr>
                                <th class="ps-4">Store Name</th>
                                <th>Owner &amp; Phone</th>
                                <th>Subscription</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Action</th>
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
                                        <div class="extra-small text-muted">{{ $tenant->phone ?? 'No phone' }}</div>
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
                                            <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1 extra-small">Active</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2.5 py-1 extra-small">{{ ucfirst($tenant->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <!-- Switch Context as Tenant -->
                                        <a href="{{ route('sa.tenants.show', encrypt($tenant->id)) }}" class="btn btn-sm btn-outline-success rounded-3 fw-bold d-inline-flex align-items-center gap-1" title="Access store as tenant">
                                            <i class="bi bi-box-arrow-in-right"></i>
                                            <span>Open Store Context</span>
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
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-pie-chart-fill me-2 text-warning"></i>Plan Distribution</h5>
                <div class="d-flex flex-column gap-3">
                    @foreach($planBreakdown as $plan)
                        @php
                            $percentage = $metrics['totalTenants'] > 0 ? round(($plan['count'] / $metrics['totalTenants']) * 100) : 0;
                        @endphp
                        <div>
                            <div class="d-flex justify-content-between small fw-bold mb-1">
                                <span class="text-dark">{{ $plan['name'] }} (₱{{ number_format($plan['price'], 0) }})</span>
                                <span class="text-muted">{{ $plan['count'] }} stores ({{ $percentage }}%)</span>
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
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-clock-history me-2 text-primary"></i>Platform Activity Stream</h6>
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
                        <div class="text-muted extra-small text-center py-3">No recent platform activities recorded.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
