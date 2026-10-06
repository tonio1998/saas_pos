@extends('layouts.sa')

@section('title', 'Subscription Plans & Special Promos Studio — SuperAdmin Console')

@section('content')
<div class="container-fluid py-2 px-3">

    {{-- ── Header ─────────────────────────────────────────── --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-success text-white fw-bold px-2 py-0.5 rounded-pill" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                    <i class="bi bi-tags-fill me-1"></i> MONETIZATION &amp; PACKAGES
                </span>
                <span class="text-muted extra-small">Dynamic Tier Definitions, Expandable Terminals &amp; Promo Campaigns</span>
            </div>
            <h3 class="fw-black text-dark font-mono mb-1" style="font-size: 1.6rem; letter-spacing: -0.02em;">
                Subscription Plans &amp; Promos Studio
            </h3>
            <p class="text-muted small mb-0">
                Configure retail plans, adjust POS terminal caps (1 unit = 1 terminal), set SKU catalog limits, and launch custom promotional campaigns.
            </p>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-bold" onclick="window.location.reload();">
                <i class="bi bi-arrow-clockwise me-1"></i> Refresh
            </button>
            <a href="{{ route('sa.subscriptions.plans.create') }}" class="btn btn-success btn-sm rounded-pill fw-bold px-3.5 shadow-sm d-flex align-items-center gap-1.5 text-white"
               style="background:#059669; border-color:#059669;">
                <i class="bi bi-plus-circle-fill"></i>
                <span>Create New Plan or Promo</span>
            </a>
        </div>
    </div>

    {{-- ── KPI Cards ──────────────────────────────────────── --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted extra-small text-uppercase fw-bold">Standard Plans</span>
                    <div class="rounded-circle bg-success-subtle text-success p-2 d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                        <i class="bi bi-shield-check fs-6"></i>
                    </div>
                </div>
                <div class="fs-3 fw-extrabold text-dark">{{ $totalActivePlans }}</div>
                <div class="extra-small text-muted mt-1">Core active subscription tiers</div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted extra-small text-uppercase fw-bold">Active Promos</span>
                    <div class="rounded-circle bg-warning-subtle text-warning p-2 d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                        <i class="bi bi-lightning-charge-fill fs-6"></i>
                    </div>
                </div>
                <div class="fs-3 fw-extrabold text-warning">{{ $totalPromos }}</div>
                <div class="extra-small text-muted mt-1">Special limited-time offers</div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted extra-small text-uppercase fw-bold">Terminal Capacity</span>
                    <div class="rounded-circle bg-primary-subtle text-primary p-2 d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                        <i class="bi bi-tablet-landscape-fill fs-6"></i>
                    </div>
                </div>
                <div class="fs-3 fw-extrabold text-primary">{{ number_format($totalTerminalsLimit) }}</div>
                <div class="extra-small text-muted mt-1">Total provisioned POS terminals</div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted extra-small text-uppercase fw-bold">Projected MRR</span>
                    <div class="rounded-circle bg-emerald-subtle text-emerald p-2 d-flex align-items-center justify-content-center" style="width:36px;height:36px;background:#ecfdf5;color:#059669;">
                        <i class="bi bi-currency-dollar fs-6"></i>
                    </div>
                </div>
                <div class="fs-3 fw-extrabold text-dark">₱{{ number_format($monthlyMRR, 2) }}</div>
                <div class="extra-small text-muted mt-1">Across {{ $totalSubscribedStores }} enrolled store(s)</div>
            </div>
        </div>
    </div>

    {{-- ── Filter Tabs & Search Bar ────────────────────────── --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <ul class="nav nav-pills gap-1">
                <li class="nav-item">
                    <a href="{{ route('sa.subscriptions.plans', ['tab' => 'all']) }}" 
                       class="nav-link rounded-pill px-3 py-1.5 fw-bold {{ $tab === 'all' ? 'active bg-dark text-white' : 'text-secondary' }}">
                        All ({{ $plans->count() }})
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('sa.subscriptions.plans', ['tab' => 'standard']) }}" 
                       class="nav-link rounded-pill px-3 py-1.5 fw-bold {{ $tab === 'standard' ? 'active bg-success text-white' : 'text-secondary' }}">
                        Standard Plans ({{ $totalActivePlans }})
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('sa.subscriptions.plans', ['tab' => 'promos']) }}" 
                       class="nav-link rounded-pill px-3 py-1.5 fw-bold {{ $tab === 'promos' ? 'active bg-warning text-dark' : 'text-secondary' }}">
                        Promos &amp; Offers ({{ $totalPromos }})
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('sa.subscriptions.plans', ['tab' => 'inactive']) }}" 
                       class="nav-link rounded-pill px-3 py-1.5 fw-bold {{ $tab === 'inactive' ? 'active bg-secondary text-white' : 'text-secondary' }}">
                        Inactive / Archived
                    </a>
                </li>
            </ul>

            <form action="{{ route('sa.subscriptions.plans') }}" method="GET" class="d-flex align-items-center gap-2">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <div class="input-group input-group-sm" style="min-width: 260px;">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="Search plans or promo codes..." value="{{ request('search') }}">
                </div>
                <button type="submit" class="btn btn-sm btn-dark rounded-3 px-3">Search</button>
            </form>
        </div>
    </div>

    {{-- ── Plan Cards Grid ─────────────────────────────────── --}}
    <div class="row g-4">
        @forelse($plans as $plan)
            @php
                $isPromo = (bool) $plan->is_promo;
                $effectivePrice = $plan->effectivePrice();
                $hasDiscount = $isPromo && $plan->promo_price && $plan->promo_price < $plan->price;
                $isArchived = $plan->status === 'inactive' || $plan->archived;
            @endphp
            <div class="col-md-6 col-xl-4">
                <div class="card border rounded-4 shadow-sm h-100 position-relative overflow-hidden transition-all {{ $isArchived ? 'opacity-75 bg-light' : 'bg-white' }}"
                     style="transition: all 0.2s ease;">
                    
                    {{-- Ribbon / Badge --}}
                    @if($plan->badge_text)
                        <div class="position-absolute top-0 end-0 m-3">
                            <span class="badge {{ $isPromo ? 'bg-warning text-dark' : 'bg-success text-white' }} rounded-pill px-3 py-1 fw-bold shadow-xs" style="font-size: 0.72rem; letter-spacing: 0.3px;">
                                {{ $plan->badge_text }}
                            </span>
                        </div>
                    @elseif($isPromo)
                        <div class="position-absolute top-0 end-0 m-3">
                            <span class="badge bg-warning text-dark rounded-pill px-3 py-1 fw-bold shadow-xs" style="font-size: 0.72rem;">
                                <i class="bi bi-lightning-fill"></i> PROMO OFFER
                            </span>
                        </div>
                    @endif

                    <div class="card-body p-4 d-flex flex-column">
                        {{-- Type / Status Indicator --}}
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge rounded-pill {{ $plan->status === 'active' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} px-2.5 py-1 extra-small fw-bold">
                                {{ strtoupper($plan->status) }}
                            </span>
                            <span class="text-muted extra-small text-uppercase fw-bold">
                                {{ ucfirst($plan->billing_cycle ?? 'monthly') }} • {{ $plan->duration_days }} Days
                            </span>
                        </div>

                        {{-- Plan Name & Description --}}
                        <h4 class="fw-bold text-dark mb-1">{{ $plan->name }}</h4>
                        <p class="text-secondary small mb-3" style="min-height: 40px; font-size: 0.85rem;">
                            {{ $plan->description ?: 'No detailed description provided.' }}
                        </p>

                        {{-- Pricing Box --}}
                        <div class="p-3 rounded-3 mb-3" style="background: #f8fafc; border: 1px dashed #e2e8f0;">
                            <div class="d-flex align-items-baseline gap-2">
                                <span class="h2 fw-extrabold text-success mb-0 font-mono">
                                    ₱{{ number_format($effectivePrice, 2) }}
                                </span>
                                @if($hasDiscount)
                                    <span class="text-muted text-decoration-line-through small font-mono">
                                        ₱{{ number_format($plan->price, 2) }}
                                    </span>
                                    <span class="badge bg-danger text-white rounded-pill extra-small px-2">
                                        SAVE {{ round((1 - ($plan->promo_price / $plan->price)) * 100) }}%
                                    </span>
                                @endif
                                <span class="text-muted small">/ {{ $plan->billing_cycle ?? 'month' }}</span>
                            </div>

                            @if($isPromo && $plan->promo_code)
                                <div class="d-flex align-items-center gap-2 mt-2 pt-2 border-top">
                                    <span class="extra-small text-muted fw-bold">PROMO CODE:</span>
                                    <code class="fw-bold px-2 py-0.5 bg-white border rounded text-dark small">{{ $plan->promo_code }}</code>
                                    <button type="button" class="btn btn-sm btn-light border p-0 px-1 extra-small" title="Copy code" onclick="navigator.clipboard.writeText('{{ $plan->promo_code }}'); alert('Code copied!');">
                                        <i class="bi bi-clipboard"></i>
                                    </button>
                                </div>
                            @endif

                            @if($isPromo && $plan->promo_expires_at)
                                <div class="extra-small text-danger mt-1">
                                    <i class="bi bi-alarm me-1"></i> Expires: {{ $plan->promo_expires_at->format('M d, Y') }}
                                </div>
                            @endif
                        </div>

                        {{-- Core Entitlements --}}
                        <div class="mb-3">
                            <div class="text-uppercase text-secondary fw-bold extra-small mb-2" style="letter-spacing: 0.5px;">Entitlements &amp; Limits</div>
                            <div class="d-flex flex-column gap-2 extra-small text-dark">
                                <div class="d-flex align-items-center justify-content-between p-2 rounded-2 bg-light">
                                    <span><i class="bi bi-tablet-landscape-fill text-primary me-2"></i><strong>POS Terminals</strong></span>
                                    <span class="badge bg-primary text-white rounded-pill px-2.5 py-1 fw-bold">
                                        {{ $plan->max_terminals ?? 1 }} Terminal{{ ($plan->max_terminals ?? 1) > 1 ? 's' : '' }}
                                    </span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between p-2 rounded-2 bg-light">
                                    <span><i class="bi bi-box-seam-fill text-info me-2"></i><strong>Product Catalog</strong></span>
                                    <span class="fw-bold">{{ number_format($plan->max_products) }} SKUs</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between p-2 rounded-2 bg-light">
                                    <span><i class="bi bi-people-fill text-warning me-2"></i><strong>Cashier Staff</strong></span>
                                    <span class="fw-bold">{{ $plan->max_cashier_accounts }} Accounts ({{ $plan->max_users }} Total)</span>
                                </div>
                                @if($plan->max_customers)
                                    <div class="d-flex align-items-center justify-content-between p-2 rounded-2 bg-light">
                                        <span><i class="bi bi-journal-bookmark-fill text-secondary me-2"></i><strong>Suki Credit Profiles</strong></span>
                                        <span class="fw-bold">{{ number_format($plan->max_customers) }}</span>
                                    </div>
                                @endif
                                <div class="d-flex align-items-center justify-content-between p-2 rounded-2 bg-light">
                                    <span><i class="bi bi-buildings-fill text-success me-2"></i><strong>Store Branches</strong></span>
                                    <span class="fw-bold">{{ $plan->max_branches ?? 1 }} Branch</span>
                                </div>
                            </div>
                        </div>

                        {{-- Feature Highlights --}}
                        @if(!empty($plan->inclusions) && is_array($plan->inclusions))
                            <div class="mb-3 flex-grow-1">
                                <div class="text-uppercase text-secondary fw-bold extra-small mb-2" style="letter-spacing: 0.5px;">Included Features</div>
                                <ul class="list-unstyled extra-small mb-0 d-flex flex-column gap-1.5">
                                    @foreach(array_slice($plan->inclusions, 0, 4) as $inc)
                                        <li class="d-flex align-items-center gap-1.5 text-secondary">
                                            <i class="bi bi-check-circle-fill text-success flex-shrink-0"></i>
                                            <span>{{ $inc }}</span>
                                        </li>
                                    @endforeach
                                    @if(count($plan->inclusions) > 4)
                                        <li class="text-muted extra-small fst-italic ps-3">
                                            + {{ count($plan->inclusions) - 4 }} more features...
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        @endif

                        {{-- Footer Action Bar --}}
                        <div class="pt-3 border-top mt-auto d-flex align-items-center justify-content-between">
                            <div>
                                <span class="badge bg-light text-dark border px-2 py-1 extra-small fw-bold">
                                    <i class="bi bi-shop me-1 text-primary"></i> {{ $plan->active_tenants_count ?? 0 }} Enrolled Store(s)
                                </span>
                            </div>

                            <div class="d-flex align-items-center gap-1">
                                <a href="{{ route('sa.subscriptions.plans.edit', $plan->id) }}" class="btn btn-sm btn-outline-primary rounded-3 px-2 py-1" title="Edit Plan & Limits (Dedicated Page)">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-warning rounded-3 px-2 py-1" title="Clone as Promo" onclick="clonePlan({{ $plan->id }}, '{{ addslashes($plan->name) }}');">
                                    <i class="bi bi-copy"></i>
                                </button>
                                <button type="button" class="btn btn-sm {{ $plan->status === 'active' ? 'btn-outline-secondary' : 'btn-outline-success' }} rounded-3 px-2 py-1" title="Toggle Status" onclick="togglePlanStatus({{ $plan->id }});">
                                    <i class="bi {{ $plan->status === 'active' ? 'bi-pause-fill' : 'bi-play-fill' }}"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-3 px-2 py-1" title="Delete / Archive" onclick="deletePlan({{ $plan->id }}, '{{ addslashes($plan->name) }}');">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white">
                    <div class="mb-3">
                        <i class="bi bi-tags display-4 text-muted"></i>
                    </div>
                    <h5 class="fw-bold text-dark">No subscription plans found in this filter</h5>
                    <p class="text-muted small mb-3">Create your first custom plan or promotional offer using the studio button above.</p>
                    <div>
                        <a href="{{ route('sa.subscriptions.plans.create') }}" class="btn btn-success rounded-pill px-4 fw-bold">
                            + Create New Plan or Promo
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

</div>

{{-- ── Plan Create / Edit Modal Studio ──────────────────── --}}
<div class="modal fade" id="planModal" tabindex="-1" aria-labelledby="planModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
            <div class="modal-header bg-dark text-white py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-success text-white p-2 d-flex align-items-center justify-content-center" style="width:34px;height:34px;">
                        <i class="bi bi-tags-fill"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="planModalLabel">Create Subscription Plan or Promo</h5>
                        <div class="extra-small text-white-50">Configure capacity, expandable terminals, pricing, and promotional terms</div>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="planForm" onsubmit="submitPlanForm(event);">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" id="planId" value="">

                <div class="modal-body p-4 bg-light">
                    
                    {{-- Preset Quick Fill Buttons --}}
                    <div class="mb-3 p-3 bg-white rounded-3 border">
                        <div class="extra-small text-muted fw-bold text-uppercase mb-2">⚡ Quick Fill Presets</div>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill fw-bold" onclick="applyPreset('starter');">
                                🏬 Starter (1 Terminal • 1,000 SKUs • ₱300)
                            </button>
                            <button type="button" class="btn btn-xs btn-outline-primary rounded-pill fw-bold" onclick="applyPreset('growth');">
                                ⭐ Suki Growth (3 Terminals • 5,000 SKUs • ₱600)
                            </button>
                            <button type="button" class="btn btn-xs btn-outline-success rounded-pill fw-bold" onclick="applyPreset('pro');">
                                🏢 Negosyo Pro (10 Terminals • 50,000 SKUs • ₱1,299)
                            </button>
                            <button type="button" class="btn btn-xs btn-outline-warning rounded-pill fw-bold" onclick="applyPreset('promo3');">
                                ⚡ Summer Promo (3 Terminals • ₱499 Special)
                            </button>
                        </div>
                    </div>

                    {{-- Section 1: Basic Plan Identity --}}
                    <div class="card border-0 shadow-xs rounded-3 p-3 bg-white mb-3">
                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-info-circle text-primary me-2"></i>Plan Identity &amp; Type</h6>
                        
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label small fw-bold text-dark">Plan / Package Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="plan_name" class="form-control" placeholder="e.g. Suki Growth Tier" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-dark">Status <span class="text-danger">*</span></label>
                                <select name="status" id="plan_status" class="form-select">
                                    <option value="active">Active (Visible to Stores)</option>
                                    <option value="inactive">Inactive / Hidden</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-bold text-dark">Short Description / Subtitle</label>
                                <input type="text" name="description" id="plan_description" class="form-control" placeholder="e.g. Most popular for multi-counter minimarts (₱20/day)">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-dark">Badge / Ribbon Text</label>
                                <input type="text" name="badge_text" id="plan_badge_text" class="form-control" placeholder="e.g. Pinakasikat, 30% OFF, Flash Promo">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-dark">Is Special Promo?</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="is_promo" id="plan_is_promo" value="1" onchange="togglePromoFields();">
                                    <label class="form-check-label small fw-bold text-warning" for="plan_is_promo">Promo Package</label>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-dark">Featured Tier?</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="featured" id="plan_featured" value="1">
                                    <label class="form-check-label small fw-bold text-success" for="plan_featured">Highlight</label>
                                </div>
                            </div>
                        </div>

                        {{-- Conditional Promo Term fields --}}
                        <div id="promoDetailsBox" class="p-3 mt-3 rounded-3 bg-warning-subtle border border-warning-subtle d-none">
                            <div class="extra-small text-dark fw-bold text-uppercase mb-2"><i class="bi bi-tag-fill me-1"></i>Promo Campaign Settings</div>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label extra-small fw-bold text-dark">Promo Price (₱)</label>
                                    <input type="number" step="0.01" name="promo_price" id="plan_promo_price" class="form-control form-control-sm" placeholder="e.g. 499.00">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label extra-small fw-bold text-dark">Promo Code</label>
                                    <input type="text" name="promo_code" id="plan_promo_code" class="form-control form-control-sm text-uppercase" placeholder="e.g. SUMMER30">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label extra-small fw-bold text-dark">Promo Expiration Date</label>
                                    <input type="date" name="promo_expires_at" id="plan_promo_expires_at" class="form-control form-control-sm">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section 2: Pricing & Billing Cycle --}}
                    <div class="card border-0 shadow-xs rounded-3 p-3 bg-white mb-3">
                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-cash-stack text-success me-2"></i>Pricing &amp; Duration</h6>
                        
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-dark">Regular Price (₱) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" step="0.01" name="price" id="plan_price" class="form-control" placeholder="600.00" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-dark">Billing Cycle <span class="text-danger">*</span></label>
                                <select name="billing_cycle" id="plan_billing_cycle" class="form-select" required>
                                    <option value="monthly">Monthly</option>
                                    <option value="quarterly">Quarterly (3 Months)</option>
                                    <option value="semi_annual">Semi-Annual (6 Months)</option>
                                    <option value="yearly">Yearly (12 Months)</option>
                                    <option value="custom">Custom Duration</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-dark">Duration (Days) <span class="text-danger">*</span></label>
                                <input type="number" name="duration_days" id="plan_duration_days" class="form-control" value="30" min="1" required>
                            </div>
                        </div>
                    </div>

                    {{-- Section 3: Hardware & Capacity Limits (USER CORE REQUIREMENT) --}}
                    <div class="card border-0 shadow-xs rounded-3 p-3 bg-white mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="fw-bold text-dark mb-0"><i class="bi bi-sliders text-warning me-2"></i>Hardware &amp; Operational Limits</h6>
                            <span class="badge bg-primary-subtle text-primary extra-small fw-bold">1 Unit = 1 Distinct Terminal</span>
                        </div>
                        
                        <div class="row g-3">
                            {{-- POS Terminals (Expandable) --}}
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-primary">
                                    <i class="bi bi-tablet-landscape-fill me-1"></i> Max POS Terminals <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="max_terminals" id="plan_max_terminals" class="form-control border-primary" value="3" min="1" max="999" required>
                                <div class="extra-small text-muted mt-1">E.g., 1 for single counter, 3 for minimart, 10 for supermarket.</div>
                            </div>

                            {{-- Products / SKUs --}}
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-dark">
                                    <i class="bi bi-box-seam-fill me-1 text-info"></i> Max Products / SKUs <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="max_products" id="plan_max_products" class="form-control" value="5000" min="1" required>
                                <div class="extra-small text-muted mt-1">Maximum allowed active items in catalog.</div>
                            </div>

                            {{-- Cashiers Limit --}}
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-dark">
                                    <i class="bi bi-person-badge-fill me-1 text-secondary"></i> Max Cashier Staff <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="max_cashier_accounts" id="plan_max_cashier_accounts" class="form-control" value="6" min="1" required>
                                <div class="extra-small text-muted mt-1">Cashier account logins allowed.</div>
                            </div>

                            {{-- Admin Accounts --}}
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-dark">Max Admins</label>
                                <input type="number" name="max_admin_accounts" id="plan_max_admin_accounts" class="form-control" value="2" min="1" required>
                            </div>

                            {{-- Total Users --}}
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-dark">Total Users Cap</label>
                                <input type="number" name="max_users" id="plan_max_users" class="form-control" value="8" min="1" required>
                            </div>

                            {{-- Customer / Suki Limit --}}
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-dark">Max Suki Profiles</label>
                                <input type="number" name="max_customers" id="plan_max_customers" class="form-control" placeholder="e.g. 3000 (blank = unli)">
                            </div>

                            {{-- Branches --}}
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-dark">Max Branches</label>
                                <input type="number" name="max_branches" id="plan_max_branches" class="form-control" value="1" min="1" required>
                            </div>
                        </div>
                    </div>

                    {{-- Section 4: Inclusions & Feature Bullets --}}
                    <div class="card border-0 shadow-xs rounded-3 p-3 bg-white">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="fw-bold text-dark mb-0"><i class="bi bi-check-all text-success me-2"></i>Feature Inclusions Bullet Points</h6>
                            <button type="button" class="btn btn-xs btn-outline-success rounded-pill fw-bold" onclick="addInclusionRow();">
                                + Add Feature Bullet
                            </button>
                        </div>
                        
                        <div id="inclusionsListWrapper" class="d-flex flex-column gap-2 mb-2">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-success-subtle text-success"><i class="bi bi-check"></i></span>
                                <input type="text" name="inclusions[]" class="form-control" value="Lightning Barcode Scanning &amp; Cashiering">
                                <button type="button" class="btn btn-outline-danger" onclick="this.closest('.input-group').remove();"><i class="bi bi-x"></i></button>
                            </div>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-success-subtle text-success"><i class="bi bi-check"></i></span>
                                <input type="text" name="inclusions[]" class="form-control" value="Multi-Terminal Support (Expandable Counters)">
                                <button type="button" class="btn btn-outline-danger" onclick="this.closest('.input-group').remove();"><i class="bi bi-x"></i></button>
                            </div>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-success-subtle text-success"><i class="bi bi-check"></i></span>
                                <input type="text" name="inclusions[]" class="form-control" value="Real-Time Inventory &amp; Auto-Deduct Stock">
                                <button type="button" class="btn btn-outline-danger" onclick="this.closest('.input-group').remove();"><i class="bi bi-x"></i></button>
                            </div>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-success-subtle text-success"><i class="bi bi-check"></i></span>
                                <input type="text" name="inclusions[]" class="form-control" value="Suki Utang &amp; Customer Credit Ledger">
                                <button type="button" class="btn btn-outline-danger" onclick="this.closest('.input-group').remove();"><i class="bi bi-x"></i></button>
                            </div>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-success-subtle text-success"><i class="bi bi-check"></i></span>
                                <input type="text" name="inclusions[]" class="form-control" value="24/7 Priority Hotline &amp; Technical Support (0912 894 1731)">
                                <button type="button" class="btn btn-outline-danger" onclick="this.closest('.input-group').remove();"><i class="bi bi-x"></i></button>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer py-3 px-4 bg-white border-top">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold" id="btnSubmitPlan" style="background:#059669; border-color:#059669;">
                        Save Plan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function togglePromoFields() {
        const isPromo = document.getElementById('plan_is_promo').checked;
        const box = document.getElementById('promoDetailsBox');
        if (isPromo) {
            box.classList.remove('d-none');
        } else {
            box.classList.add('d-none');
        }
    }

    function addInclusionRow(value = '') {
        const wrapper = document.getElementById('inclusionsListWrapper');
        const div = document.createElement('div');
        div.className = 'input-group input-group-sm';
        div.innerHTML = `
            <span class="input-group-text bg-success-subtle text-success"><i class="bi bi-check"></i></span>
            <input type="text" name="inclusions[]" class="form-control" value="${value.replace(/"/g, '&quot;')}" placeholder="e.g. Feature benefit">
            <button type="button" class="btn btn-outline-danger" onclick="this.closest('.input-group').remove();"><i class="bi bi-x"></i></button>
        `;
        wrapper.appendChild(div);
    }

    function openCreateModal() {
        document.getElementById('planForm').reset();
        document.getElementById('planId').value = '';
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('planModalLabel').innerText = 'Create Subscription Plan or Promo';
        document.getElementById('btnSubmitPlan').innerText = 'Create Plan';
        togglePromoFields();
    }

    function openEditModal(plan) {
        document.getElementById('planForm').reset();
        document.getElementById('planId').value = plan.id;
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('planModalLabel').innerText = 'Edit Plan: ' + plan.name;
        document.getElementById('btnSubmitPlan').innerText = 'Update Plan';

        document.getElementById('plan_name').value = plan.name || '';
        document.getElementById('plan_description').value = plan.description || '';
        document.getElementById('plan_status').value = plan.status || 'active';
        document.getElementById('plan_badge_text').value = plan.badge_text || '';
        document.getElementById('plan_is_promo').checked = !!plan.is_promo;
        document.getElementById('plan_featured').checked = !!plan.featured;

        document.getElementById('plan_price').value = plan.price || '';
        document.getElementById('plan_billing_cycle').value = plan.billing_cycle || 'monthly';
        document.getElementById('plan_duration_days').value = plan.duration_days || 30;

        document.getElementById('plan_promo_price').value = plan.promo_price || '';
        document.getElementById('plan_promo_code').value = plan.promo_code || '';
        document.getElementById('plan_promo_expires_at').value = plan.promo_expires_at ? plan.promo_expires_at.substring(0, 10) : '';

        // Hardware limits
        document.getElementById('plan_max_terminals').value = plan.max_terminals || 1;
        document.getElementById('plan_max_products').value = plan.max_products || 1000;
        document.getElementById('plan_max_cashier_accounts').value = plan.max_cashier_accounts || 1;
        document.getElementById('plan_max_admin_accounts').value = plan.max_admin_accounts || 1;
        document.getElementById('plan_max_users').value = plan.max_users || 2;
        document.getElementById('plan_max_customers').value = plan.max_customers || '';
        document.getElementById('plan_max_branches').value = plan.max_branches || 1;

        // Inclusions
        const wrapper = document.getElementById('inclusionsListWrapper');
        wrapper.innerHTML = '';
        if (Array.isArray(plan.inclusions) && plan.inclusions.length > 0) {
            plan.inclusions.forEach(inc => addInclusionRow(inc));
        } else {
            addInclusionRow('Barcode Scanning & POS Register');
            addInclusionRow('Terminal Support (Expandable)');
            addInclusionRow('Inventory Management');
        }

        togglePromoFields();
        const modal = new bootstrap.Modal(document.getElementById('planModal'));
        modal.show();
    }

    function applyPreset(type) {
        if (type === 'starter') {
            document.getElementById('plan_name').value = 'Tindahan Starter';
            document.getElementById('plan_description').value = 'Single-register kiosk, sari-sari store & bakery (₱10/day)';
            document.getElementById('plan_price').value = 300;
            document.getElementById('plan_max_terminals').value = 1;
            document.getElementById('plan_max_products').value = 1000;
            document.getElementById('plan_max_cashier_accounts').value = 2;
            document.getElementById('plan_max_admin_accounts').value = 1;
            document.getElementById('plan_max_users').value = 3;
            document.getElementById('plan_max_customers').value = 300;
            document.getElementById('plan_badge_text').value = '';
            document.getElementById('plan_is_promo').checked = false;
        } else if (type === 'growth') {
            document.getElementById('plan_name').value = 'Suki Growth';
            document.getElementById('plan_description').value = 'Most popular for multi-counter minimarts (₱20/day)';
            document.getElementById('plan_price').value = 600;
            document.getElementById('plan_max_terminals').value = 3;
            document.getElementById('plan_max_products').value = 5000;
            document.getElementById('plan_max_cashier_accounts').value = 6;
            document.getElementById('plan_max_admin_accounts').value = 2;
            document.getElementById('plan_max_users').value = 8;
            document.getElementById('plan_max_customers').value = 3000;
            document.getElementById('plan_badge_text').value = '⭐ Pinakasikat';
            document.getElementById('plan_is_promo').checked = false;
        } else if (type === 'pro') {
            document.getElementById('plan_name').value = 'Negosyo Pro';
            document.getElementById('plan_description').value = 'Enterprise grade for multi-branch supermarket chains (₱43/day)';
            document.getElementById('plan_price').value = 1299;
            document.getElementById('plan_max_terminals').value = 10;
            document.getElementById('plan_max_products').value = 50000;
            document.getElementById('plan_max_cashier_accounts').value = 20;
            document.getElementById('plan_max_admin_accounts').value = 5;
            document.getElementById('plan_max_users').value = 25;
            document.getElementById('plan_max_customers').value = 20000;
            document.getElementById('plan_badge_text').value = 'Whale Enterprise';
            document.getElementById('plan_is_promo').checked = false;
        } else if (type === 'promo3') {
            document.getElementById('plan_name').value = 'Summer 3-Terminal Bundle';
            document.getElementById('plan_description').value = 'Special promotion: 3 Terminals at 20% discount';
            document.getElementById('plan_price').value = 600;
            document.getElementById('plan_promo_price').value = 499;
            document.getElementById('plan_max_terminals').value = 3;
            document.getElementById('plan_max_products').value = 8000;
            document.getElementById('plan_max_cashier_accounts').value = 6;
            document.getElementById('plan_max_admin_accounts').value = 2;
            document.getElementById('plan_max_users').value = 8;
            document.getElementById('plan_badge_text').value = 'FLASH PROMO • 20% OFF';
            document.getElementById('plan_promo_code').value = 'SUMMER3T';
            document.getElementById('plan_is_promo').checked = true;
        }
        togglePromoFields();
    }

    async function submitPlanForm(e) {
        e.preventDefault();
        const form = document.getElementById('planForm');
        const planId = document.getElementById('planId').value;
        const method = document.getElementById('formMethod').value;
        const btn = document.getElementById('btnSubmitPlan');

        const url = planId ? `/sa/subscriptions/plans/${planId}` : '/sa/subscriptions/plans';
        const formData = new FormData(form);

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await res.json();
            if (res.ok && data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            } else {
                let errText = data.message || 'Validation failed.';
                if (data.errors) {
                    errText = Object.values(data.errors).flat().join('<br>');
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Check Form Errors',
                    html: errText
                });
            }
        } catch (err) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: err.message
            });
        } finally {
            btn.disabled = false;
            btn.innerText = planId ? 'Update Plan' : 'Save Plan';
        }
    }

    function togglePlanStatus(id) {
        fetch(`/sa/subscriptions/plans/${id}/toggle-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Status Updated',
                    text: data.message,
                    timer: 1200,
                    showConfirmButton: false
                }).then(() => window.location.reload());
            }
        });
    }

    function clonePlan(id, name) {
        Swal.fire({
            title: 'Duplicate Plan as Promo?',
            text: `Clone "${name}" into a new editable promotional package?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#059669',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Clone Plan'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/sa/subscriptions/plans/${id}/clone`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Plan Cloned!',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => window.location.reload());
                    }
                });
            }
        });
    }

    function deletePlan(id, name) {
        Swal.fire({
            title: 'Delete or Archive Plan?',
            text: `Are you sure you want to delete "${name}"? If stores are currently enrolled, it will safely be set to inactive/archived.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Delete'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/sa/subscriptions/plans/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Action Completed',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => window.location.reload());
                    }
                });
            }
        });
    }
</script>
@endpush
@endsection
