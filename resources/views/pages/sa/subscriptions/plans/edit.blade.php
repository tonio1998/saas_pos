@extends('layouts.sa')

@section('title', 'Edit ' . $plan->name . ' — Subscription Plans Studio')

@section('content')
<div class="container-fluid py-3 px-4">

    {{-- Breadcrumb & Navigation --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ route('sa.dashboard.index') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('sa.subscriptions.plans') }}" class="text-decoration-none text-muted">Subscription Plans</a></li>
                <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Edit Plan #{{ $plan->id }} ({{ $plan->name }})</li>
            </ol>
        </nav>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('sa.subscriptions.plans') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-bold">
                <i class="bi bi-arrow-left me-1"></i> Back to Plans Studio
            </a>
        </div>
    </div>

    {{-- Header Banner --}}
    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4 position-relative overflow-hidden">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge {{ $plan->is_promo ? 'bg-warning text-dark' : 'bg-primary text-white' }} fw-bold px-2.5 py-1 rounded-pill" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                        <i class="bi {{ $plan->is_promo ? 'bi-lightning-charge-fill' : 'bi-pencil-square' }} me-1"></i>
                        {{ $plan->is_promo ? 'PROMOTIONAL TIER' : 'STANDARD PLAN' }}
                    </span>
                    <span class="badge bg-light text-dark border px-2 py-0.5 rounded-pill extra-small">
                        Plan ID: #{{ $plan->id }}
                    </span>
                    @if($plan->status === 'active')
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5 rounded-pill extra-small fw-bold">
                            <i class="bi bi-check-circle-fill me-1"></i> Publicly Active
                        </span>
                    @else
                        <span class="badge bg-secondary text-white px-2 py-0.5 rounded-pill extra-small fw-bold">
                            Hidden / Inactive
                        </span>
                    @endif
                </div>
                <h3 class="fw-black text-dark font-mono mb-1" style="font-size: 1.6rem; letter-spacing: -0.02em;">
                    Edit: {{ $plan->name }}
                </h3>
                <p class="text-muted small mb-0">
                    Modify hardware terminal limits (1 physical unit = 1 terminal), product catalog caps, pricing, and promotional terms.
                </p>
            </div>

            {{-- Fast Action / Meta Stats --}}
            <div class="d-flex flex-wrap align-items-center gap-2">
                <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill small fw-bold">
                    <i class="bi bi-shop me-1 text-primary"></i> {{ $plan->tenants()->whereIn('status', ['active', 'locked', 'unlocked'])->count() }} Active Store(s) Enrolled
                </span>
                <a href="{{ route('sa.subscriptions.plans.create') }}" class="btn btn-outline-success btn-sm rounded-pill px-3 fw-semibold">
                    <i class="bi bi-plus-circle me-1"></i> Create New Instead
                </a>
            </div>
        </div>
    </div>

    @if (isset($errors) && $errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 p-3 d-flex align-items-start gap-3">
            <i class="bi bi-exclamation-octagon-fill fs-4 text-danger mt-1"></i>
            <div>
                <h6 class="fw-bold text-danger mb-1">Please correct the following errors:</h6>
                <ul class="mb-0 small text-danger ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- Main Edit Form --}}
    <form action="{{ route('sa.subscriptions.plans.update', $plan->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-4">
            
            {{-- Left Column: Configuration Forms --}}
            <div class="col-lg-8">

                {{-- SECTION 1: IDENTITY & PROMO STATUS --}}
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                        <div class="rounded-circle bg-primary-subtle text-primary p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="bi bi-tag-fill"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-0">1. Plan Identity &amp; Campaign Type</h5>
                            <small class="text-muted">Marketing name, display badge, and promotional classification</small>
                        </div>
                    </div>

                    <div class="row g-3">
                        {{-- Name --}}
                        <div class="col-md-8">
                            <label class="form-label fw-bold text-dark mb-1">
                                Plan / Package Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" id="plan_name" class="form-control form-control-lg fs-6" 
                                   placeholder="e.g. Suki Growth Tier" value="{{ old('name', $plan->name) }}" required>
                            <div class="form-text text-muted small mt-1">
                                <i class="bi bi-info-circle text-primary me-1"></i>
                                <strong>Purpose:</strong> The public marketing title displayed to store owners across the checkout plan picker, receipts, and invoices.
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-dark mb-1">
                                Availability Status <span class="text-danger">*</span>
                            </label>
                            <select name="status" id="plan_status" class="form-select form-select-lg fs-6" required>
                                <option value="active" {{ old('status', $plan->status) === 'active' ? 'selected' : '' }}>Active (Publicly Selectable)</option>
                                <option value="inactive" {{ old('status', $plan->status) === 'inactive' ? 'selected' : '' }}>Inactive (Hidden Draft)</option>
                            </select>
                            <div class="form-text text-muted small mt-1">
                                <i class="bi bi-info-circle text-primary me-1"></i>
                                <strong>Purpose:</strong> Controls whether store owners can see and choose this tier during subscription checkout.
                            </div>
                        </div>

                        {{-- Description --}}
                        <div class="col-12">
                            <label class="form-label fw-bold text-dark mb-1">
                                Short Description / Value Proposition
                            </label>
                            <input type="text" name="description" id="plan_description" class="form-control" 
                                   placeholder="e.g. Perfect for multi-counter minimarts operating morning and evening cashier shifts (₱20/day)" 
                                   value="{{ old('description', $plan->description) }}">
                            <div class="form-text text-muted small mt-1">
                                <i class="bi bi-info-circle text-primary me-1"></i>
                                <strong>Purpose:</strong> A 1-2 sentence summary explaining who this plan is tailored for and why they should choose it.
                            </div>
                        </div>

                        {{-- Badge / Ribbon --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark mb-1">
                                Badge / Ribbon Text (Optional)
                            </label>
                            <input type="text" name="badge_text" id="plan_badge_text" class="form-control" 
                                   placeholder="e.g. ⭐ Most Popular, Flash Deal, 20% Off" value="{{ old('badge_text', $plan->badge_text) }}">
                            <div class="form-text text-muted small mt-1">
                                <i class="bi bi-info-circle text-primary me-1"></i>
                                <strong>Purpose:</strong> High-impact pill label shown at the top corner of the plan card to highlight special offers or top-selling tiers.
                            </div>
                        </div>

                        {{-- Promo Toggle & Featured --}}
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-dark mb-1">Special Promo?</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_promo" id="plan_is_promo" value="1" 
                                       {{ old('is_promo', $plan->is_promo) ? 'checked' : '' }} onchange="togglePromoFields();">
                                <label class="form-check-label fw-bold text-warning" for="plan_is_promo">Promo Package</label>
                            </div>
                            <div class="form-text text-muted small mt-1">
                                <strong>Purpose:</strong> Enables discounted pricing and voucher codes.
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold text-dark mb-1">Featured Tier?</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="featured" id="plan_featured" value="1" 
                                       {{ old('featured', $plan->featured) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold text-success" for="plan_featured">Highlight Card</label>
                            </div>
                            <div class="form-text text-muted small mt-1">
                                <strong>Purpose:</strong> Visually elevates this card with a distinct green border.
                            </div>
                        </div>
                    </div>

                    {{-- Dynamic Promotional Parameters Box --}}
                    <div id="promoDetailsBox" class="p-3.5 mt-3 rounded-3 bg-warning-subtle border border-warning-subtle {{ old('is_promo', $plan->is_promo) ? '' : 'd-none' }}">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-lightning-charge-fill text-warning fs-5"></i>
                            <h6 class="fw-bold text-dark mb-0">Promotional Campaign Settings</h6>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-dark">Promo Discounted Price (₱)</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" step="0.01" name="promo_price" id="plan_promo_price" class="form-control" 
                                           placeholder="e.g. 499.00" value="{{ old('promo_price', $plan->promo_price) }}">
                                </div>
                                <div class="form-text extra-small text-dark mt-1">
                                    <strong>Purpose:</strong> Discounted rate billed during promo. Regular price will be shown with a strikethrough.
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-dark">Promo Voucher Code (Optional)</label>
                                <input type="text" name="promo_code" id="plan_promo_code" class="form-control form-control-sm text-uppercase font-mono" 
                                       placeholder="e.g. SUMMER30" value="{{ old('promo_code', $plan->promo_code) }}">
                                <div class="form-text extra-small text-dark mt-1">
                                    <strong>Purpose:</strong> Specific code store owners can share or copy to claim the promo.
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-dark">Promo Expiration Date</label>
                                <input type="date" name="promo_expires_at" id="plan_promo_expires_at" class="form-control form-control-sm" 
                                       value="{{ old('promo_expires_at', $plan->promo_expires_at ? $plan->promo_expires_at->format('Y-m-d') : '') }}">
                                <div class="form-text extra-small text-dark mt-1">
                                    <strong>Purpose:</strong> Deadline after which this promo offer ends.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION 2: PRICING & BILLING CYCLE --}}
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                        <div class="rounded-circle bg-success-subtle text-success p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-0">2. Pricing, Renewal Cycle &amp; Duration</h5>
                            <small class="text-muted">Set subscription billing rates and duration periods</small>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-dark mb-1">
                                Regular Price (PHP ₱) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text fw-bold">₱</span>
                                <input type="number" step="0.01" name="price" id="plan_price" class="form-control form-control-lg fs-6 font-mono fw-bold text-success" 
                                       placeholder="600.00" value="{{ old('price', $plan->price) }}" required>
                            </div>
                            <div class="form-text text-muted small mt-1">
                                <i class="bi bi-info-circle text-primary me-1"></i>
                                <strong>Purpose:</strong> The standard renewal fee in Philippine Pesos (PHP) billed per cycle.
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold text-dark mb-1">
                                Billing Cycle <span class="text-danger">*</span>
                            </label>
                            <select name="billing_cycle" id="plan_billing_cycle" class="form-select form-select-lg fs-6" required>
                                <option value="monthly" {{ old('billing_cycle', $plan->billing_cycle) === 'monthly' ? 'selected' : '' }}>Monthly (Standard 30 Days)</option>
                                <option value="quarterly" {{ old('billing_cycle', $plan->billing_cycle) === 'quarterly' ? 'selected' : '' }}>Quarterly (90 Days)</option>
                                <option value="semi_annual" {{ old('billing_cycle', $plan->billing_cycle) === 'semi_annual' ? 'selected' : '' }}>Semi-Annual (180 Days)</option>
                                <option value="yearly" {{ old('billing_cycle', $plan->billing_cycle) === 'yearly' ? 'selected' : '' }}>Yearly (365 Days • Annual)</option>
                                <option value="custom" {{ old('billing_cycle', $plan->billing_cycle) === 'custom' ? 'selected' : '' }}>Custom Duration</option>
                            </select>
                            <div class="form-text text-muted small mt-1">
                                <i class="bi bi-info-circle text-primary me-1"></i>
                                <strong>Purpose:</strong> Invoicing recurrence term shown on payment reminders.
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold text-dark mb-1">
                                Duration in Days <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="duration_days" id="plan_duration_days" class="form-control form-control-lg fs-6 font-mono" 
                                   value="{{ old('duration_days', $plan->duration_days) }}" min="1" max="3650" required>
                            <div class="form-text text-muted small mt-1">
                                <i class="bi bi-info-circle text-primary me-1"></i>
                                <strong>Purpose:</strong> Exact calendar days before the store's due date sentinel and renewal invoice trigger.
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark mb-1">
                                Grace Period (Days)
                            </label>
                            <input type="number" name="grace_period_days" id="plan_grace_period_days" class="form-control font-mono" 
                                   value="{{ old('grace_period_days', $plan->grace_period_days ?? 3) }}" min="0" max="90">
                            <div class="form-text text-muted small mt-1">
                                <i class="bi bi-info-circle text-primary me-1"></i>
                                <strong>Purpose:</strong> Days after due date before the store's POS terminal is locked or set to read-only/suspended.
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark mb-1">
                                Display Sort Order
                            </label>
                            <input type="number" name="sort_order" id="plan_sort_order" class="form-control font-mono" 
                                   value="{{ old('sort_order', $plan->sort_order ?? 0) }}" min="0" max="999">
                            <div class="form-text text-muted small mt-1">
                                <i class="bi bi-info-circle text-primary me-1"></i>
                                <strong>Purpose:</strong> Display sequence number (lower numbers appear first on checkout screen).
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION 3: HARDWARE & CAPACITY LIMITS (EXPANDABLE TERMINALS) --}}
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4" style="border-left: 4px solid #059669 !important;">
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-warning-subtle text-warning p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <i class="bi bi-sliders"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-0">3. Hardware &amp; Operational Limits</h5>
                                <small class="text-muted">POS terminal capacity, product SKU caps, and staff account quotas</small>
                            </div>
                        </div>
                        <span class="badge bg-primary text-white px-2.5 py-1 rounded-pill extra-small fw-bold">
                            1 Device / Tablet = 1 POS Terminal
                        </span>
                    </div>

                    <div class="row g-3">
                        {{-- Max POS Terminals (Expandable) --}}
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 bg-light border border-primary border-opacity-25 h-100">
                                <label class="form-label fw-bold text-primary mb-1 d-flex align-items-center gap-1.5">
                                    <i class="bi bi-tablet-landscape-fill fs-5"></i>
                                    <span>Max POS Terminals (Hardware Limit)</span> <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="max_terminals" id="plan_max_terminals" class="form-control form-control-lg font-mono fw-bold border-primary" 
                                       value="{{ old('max_terminals', $plan->max_terminals ?? 1) }}" min="1" max="999" required>
                                <div class="form-text text-dark small mt-2">
                                    <i class="bi bi-shield-lock-fill text-primary me-1"></i>
                                    <strong>Purpose:</strong> <strong>Controls how many physical register units / counters can punch sales concurrently.</strong>
                                    <br>
                                    <span class="text-muted">
                                        • <strong>1 Terminal:</strong> Single counter kiosk, pharmacy, or sari-sari store.
                                        <br>• <strong>3 Terminals:</strong> Medium minimart (Counter 1, Counter 2, Mobile / Pickup).
                                        <br>• <strong>10 Terminals:</strong> Large supermarket or grocery department store.
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Max Products / SKUs --}}
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 bg-light border border-info border-opacity-25 h-100">
                                <label class="form-label fw-bold text-dark mb-1 d-flex align-items-center gap-1.5">
                                    <i class="bi bi-box-seam-fill fs-5 text-info"></i>
                                    <span>Max Products / SKUs (Catalog Cap)</span> <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="max_products" id="plan_max_products" class="form-control form-control-lg font-mono fw-bold" 
                                       value="{{ old('max_products', $plan->max_products) }}" min="1" required>
                                <div class="form-text text-dark small mt-2">
                                    <i class="bi bi-info-circle text-info me-1"></i>
                                    <strong>Purpose:</strong> <strong>Maximum inventory items and variants the store can register.</strong>
                                    <br>
                                    <span class="text-muted">
                                        When store staff try to add products beyond this cap, the system halts them with an upgrade message.
                                        (e.g., 1,000 for Starter, 5,000 for Growth, 50,000 for Pro).
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Cashier Staff Accounts --}}
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-dark mb-1">
                                <i class="bi bi-person-badge-fill text-warning me-1"></i> Max Cashier Accounts <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="max_cashier_accounts" id="plan_max_cashier_accounts" class="form-control font-mono" 
                                   value="{{ old('max_cashier_accounts', $plan->max_cashier_accounts) }}" min="1" required>
                            <div class="form-text text-muted small mt-1">
                                <strong>Purpose:</strong> Separate cashier logins for morning/evening shifts so cash accountability is tracked per person.
                            </div>
                        </div>

                        {{-- Admin Accounts --}}
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-dark mb-1">
                                <i class="bi bi-person-lock text-primary me-1"></i> Max Admin Accounts <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="max_admin_accounts" id="plan_max_admin_accounts" class="form-control font-mono" 
                                   value="{{ old('max_admin_accounts', $plan->max_admin_accounts) }}" min="1" required>
                            <div class="form-text text-muted small mt-1">
                                <strong>Purpose:</strong> Store owner and general manager logins with permission to edit prices and view profit reports.
                            </div>
                        </div>

                        {{-- Total Combined Users --}}
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-dark mb-1">
                                <i class="bi bi-people-fill text-secondary me-1"></i> Total Users Cap <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="max_users" id="plan_max_users" class="form-control font-mono" 
                                   value="{{ old('max_users', $plan->max_users) }}" min="1" required>
                            <div class="form-text text-muted small mt-1">
                                <strong>Purpose:</strong> Overall ceiling of total system accounts allowed under the store.
                            </div>
                        </div>

                        {{-- Suki / Customer Profiles --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark mb-1">
                                <i class="bi bi-journal-bookmark-fill text-secondary me-1"></i> Max Suki Credit Profiles (CRM)
                            </label>
                            <input type="number" name="max_customers" id="plan_max_customers" class="form-control font-mono" 
                                   placeholder="e.g. 3000 (leave blank for unlimited)" value="{{ old('max_customers', $plan->max_customers) }}">
                            <div class="form-text text-muted small mt-1">
                                <strong>Purpose:</strong> Cap on customer profiles recorded for utang ledgers and loyalty history. Leave blank for unmetered suki storage.
                            </div>
                        </div>

                        {{-- Branches --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark mb-1">
                                <i class="bi bi-buildings-fill text-success me-1"></i> Max Branches <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="max_branches" id="plan_max_branches" class="form-control font-mono" 
                                   value="{{ old('max_branches', $plan->max_branches ?? 1) }}" min="1" required>
                            <div class="form-text text-muted small mt-1">
                                <strong>Purpose:</strong> Physical store locations. Set to 1 for standalone stores or 2-5 for multi-branch chains.
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION 4: INCLUSIONS & HIGHLIGHTS --}}
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-info-subtle text-info p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <i class="bi bi-check-all"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-0">4. Included Features &amp; Checklist</h5>
                                <small class="text-muted">Marketing bullets displayed on the subscription card with checkmarks</small>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-success rounded-pill fw-bold" onclick="addInclusionRow();">
                            + Add Feature Bullet
                        </button>
                    </div>

                    <div class="form-text text-muted small mb-3">
                        <i class="bi bi-info-circle text-primary me-1"></i>
                        <strong>Purpose:</strong> These bullet points are showcased directly to store owners on the checkout screen to emphasize the value of this tier.
                    </div>

                    <div id="inclusionsListWrapper" class="d-flex flex-column gap-2">
                        @php
                            $inclusions = is_array($plan->inclusions) ? $plan->inclusions : (is_string($plan->inclusions) ? json_decode($plan->inclusions, true) : []);
                            if (empty($inclusions)) {
                                $inclusions = [
                                    'Lightning Barcode Scanning & Cashiering Terminal',
                                    'Multi-Terminal Support (Expandable Counter Lanes)',
                                    'Real-Time Inventory & Stock Auto-Deduct',
                                    'Suki Utang & Customer Credit Ledger (CRM)',
                                    '24/7 Priority Support Hotline & Support Hub (0912 894 1731)',
                                ];
                            }
                        @endphp

                        @foreach($inclusions as $inc)
                            <div class="input-group">
                                <span class="input-group-text bg-success-subtle text-success"><i class="bi bi-check-lg"></i></span>
                                <input type="text" name="inclusions[]" class="form-control" value="{{ $inc }}">
                                <button type="button" class="btn btn-outline-danger" onclick="this.closest('.input-group').remove();"><i class="bi bi-trash"></i></button>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

            {{-- Right Column: Live Plan Preview & Action Card --}}
            <div class="col-lg-4">
                <div class="sticky-top" style="top: 20px; z-index: 10;">
                    
                    {{-- Live Card Preview --}}
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
                        <div class="card-header bg-dark text-white py-3 px-4 d-flex align-items-center justify-content-between">
                            <span class="fw-bold small text-uppercase" style="letter-spacing: 0.5px;">Live Storefront Preview</span>
                            <span class="badge bg-success rounded-pill extra-small">Checkout View</span>
                        </div>
                        <div class="card-body p-4">
                            <div class="p-3.5 rounded-4 border border-2 border-success bg-white shadow-xs position-relative">
                                <span id="previewBadge" class="badge bg-warning text-dark position-absolute top-0 end-0 translate-middle-y me-3 px-3 py-1 fw-bold rounded-pill shadow-xs" style="font-size: 0.72rem;">
                                    {{ $plan->badge_text ?? '⭐ Most Popular' }}
                                </span>

                                <div class="fw-bold text-uppercase extra-small text-success mb-1" id="previewBilling">
                                    {{ ucfirst($plan->billing_cycle) }} Plan
                                </div>
                                <h4 class="fw-extrabold text-dark mb-1" id="previewTitle">{{ $plan->name }}</h4>
                                <div class="h2 fw-extrabold text-success mb-2 d-flex align-items-baseline gap-2">
                                    <span id="previewPrice">₱{{ number_format($plan->effectivePrice(), 0) }}</span>
                                    <span id="previewOldPrice" class="fs-6 text-muted text-decoration-line-through fw-normal {{ ($plan->is_promo && $plan->promo_price) ? '' : 'd-none' }}">
                                        ₱{{ number_format($plan->price, 0) }}
                                    </span>
                                    <small class="fs-6 text-muted fw-normal">/ {{ $plan->billing_cycle === 'monthly' ? 'month' : $plan->billing_cycle }}</small>
                                </div>
                                <p class="text-secondary extra-small mb-3" id="previewDesc">
                                    {{ $plan->description ?? 'Tailored POS package for growing retail merchants.' }}
                                </p>

                                <div class="pt-3 border-top extra-small text-muted d-flex flex-column gap-2">
                                    <div><i class="bi bi-tablet-landscape-fill text-success me-1.5"></i> <strong id="previewTerminals">{{ $plan->max_terminals ?? 1 }} POS Terminal{{ ($plan->max_terminals ?? 1) > 1 ? 's' : '' }}</strong> (1 Unit = 1 Counter)</div>
                                    <div><i class="bi bi-box-seam-fill text-info me-1.5"></i> Up to <strong id="previewProducts">{{ number_format($plan->max_products) }} SKUs</strong></div>
                                    <div><i class="bi bi-people-fill text-primary me-1.5"></i> <strong id="previewCustomers">{{ $plan->max_customers ? number_format($plan->max_customers) . ' Suki Profiles' : 'Unlimited Suki' }}</strong></div>
                                    <div><i class="bi bi-person-badge-fill text-secondary me-1.5"></i> <strong id="previewCashiers">{{ $plan->max_cashier_accounts }} Cashiers ({{ $plan->max_users }} Users)</strong></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Submission Card --}}
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white text-center">
                        <h6 class="fw-bold text-dark mb-1">Save Plan Changes</h6>
                        <p class="text-muted extra-small mb-3">
                            Updates to terminal capacity, pricing, and catalog limits will apply immediately to stores renewing or purchasing this plan.
                        </p>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold shadow-sm py-2.5">
                                <i class="bi bi-check2-circle me-1"></i> Update &amp; Save Plan
                            </button>
                            <a href="{{ route('sa.subscriptions.plans') }}" class="btn btn-outline-secondary rounded-pill py-2 small fw-semibold">
                                Cancel &amp; Return
                            </a>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </form>

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
        updatePreview();
    }

    function addInclusionRow(value = '') {
        const wrapper = document.getElementById('inclusionsListWrapper');
        const div = document.createElement('div');
        div.className = 'input-group';
        div.innerHTML = `
            <span class="input-group-text bg-success-subtle text-success"><i class="bi bi-check-lg"></i></span>
            <input type="text" name="inclusions[]" class="form-control" value="${value.replace(/"/g, '&quot;')}" placeholder="e.g. Feature benefit">
            <button type="button" class="btn btn-outline-danger" onclick="this.closest('.input-group').remove();"><i class="bi bi-trash"></i></button>
        `;
        wrapper.appendChild(div);
    }

    function updatePreview() {
        const name = document.getElementById('plan_name').value || 'Plan Name';
        const desc = document.getElementById('plan_description').value || 'Plan value description...';
        const price = parseFloat(document.getElementById('plan_price').value) || 0;
        const promoPrice = parseFloat(document.getElementById('plan_promo_price').value) || 0;
        const isPromo = document.getElementById('plan_is_promo').checked;
        const badge = document.getElementById('plan_badge_text').value;
        const billing = document.getElementById('plan_billing_cycle').value;
        const terminals = document.getElementById('plan_max_terminals').value || 1;
        const products = document.getElementById('plan_max_products').value || 1000;
        const customers = document.getElementById('plan_max_customers').value;
        const cashiers = document.getElementById('plan_max_cashier_accounts').value || 1;
        const users = document.getElementById('plan_max_users').value || 2;

        document.getElementById('previewTitle').innerText = name;
        document.getElementById('previewDesc').innerText = desc;
        document.getElementById('previewBilling').innerText = (billing.charAt(0).toUpperCase() + billing.slice(1)) + ' Plan';

        if (isPromo && promoPrice > 0 && promoPrice < price) {
            document.getElementById('previewPrice').innerText = '₱' + promoPrice.toLocaleString('en-US', { minimumFractionDigits: 0 });
            document.getElementById('previewOldPrice').innerText = '₱' + price.toLocaleString('en-US', { minimumFractionDigits: 0 });
            document.getElementById('previewOldPrice').classList.remove('d-none');
        } else {
            document.getElementById('previewPrice').innerText = '₱' + price.toLocaleString('en-US', { minimumFractionDigits: 0 });
            document.getElementById('previewOldPrice').classList.add('d-none');
        }

        const badgeEl = document.getElementById('previewBadge');
        if (badge || isPromo) {
            badgeEl.innerText = badge || 'PROMO OFFER';
            badgeEl.classList.remove('d-none');
        } else {
            badgeEl.classList.add('d-none');
        }

        document.getElementById('previewTerminals').innerText = terminals + ' POS Terminal' + (terminals > 1 ? 's' : '');
        document.getElementById('previewProducts').innerText = parseInt(products).toLocaleString('en-US') + ' SKUs';
        document.getElementById('previewCustomers').innerText = customers ? (parseInt(customers).toLocaleString('en-US') + ' Suki Profiles') : 'Unlimited Suki';
        document.getElementById('previewCashiers').innerText = cashiers + ' Cashiers (' + users + ' Users)';
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('input, select').forEach(el => {
            el.addEventListener('input', updatePreview);
            el.addEventListener('change', updatePreview);
        });
        updatePreview();
    });
</script>
@endpush
@endsection
