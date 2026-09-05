@extends('layouts.app')

@section('title', 'Store Settings & POS Branding | LikhaPOS - Cloud POS & CRM')

@section('content')
@php
    $themeSettings = $tenant->theme_settings ?? [];
    $crmSettings = $tenant->crm_settings ?? [];

    $currentPreset = $themeSettings['preset'] ?? 'emerald';
    $primaryColor = $themeSettings['primary_color'] ?? '#059669';
    $topbarColor = $themeSettings['topbar_color'] ?? '#064E3B';
    $topbarTextColor = $themeSettings['topbar_text_color'] ?? '#FFFFFF';
    $sidebarColor = $themeSettings['sidebar_color'] ?? '#0F172A';
    $sidebarTextColor = $themeSettings['sidebar_text_color'] ?? '#CBD5E1';
    $sidebarActive = $themeSettings['sidebar_active_color'] ?? '#059669';
    $sidebarActiveTextColor = $themeSettings['sidebar_active_text_color'] ?? '#FFFFFF';
    $accentColor = $themeSettings['accent_color'] ?? '#10B981';
    $darkMode = !empty($themeSettings['dark_mode']);

    $pointsPerPeso = (float)($crmSettings['points_per_peso'] ?? 0.01);
    if ($pointsPerPeso > 10) $pointsPerPeso = 0.01;
    $defaultCreditLimit = $crmSettings['default_credit_limit'] ?? 5000;
    $smsReceiptEnabled = isset($crmSettings['sms_receipt_enabled']) ? (bool)$crmSettings['sms_receipt_enabled'] : true;
    $smsUtangReminderEnabled = isset($crmSettings['sms_utang_reminder_enabled']) ? (bool)$crmSettings['sms_utang_reminder_enabled'] : true;
@endphp

<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Executive Header --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
        <div class="d-flex align-items-center gap-2.5">
            <div class="kpi-icon-box emerald" style="width:40px;height:40px;font-size:1.15rem;">
                <i class="bi bi-palette-fill"></i>
            </div>
            <div>
                <h4 class="fw-black text-dark mb-0 font-mono" style="letter-spacing:-0.4px;">Store Settings, Theme & CRM Engine</h4>
                <p class="text-muted extra-small mb-0">Customize system appearance with live preview, store logo, thermal receipt, and CRM loyalty settings</p>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" id="btnPreviewReceipt" class="btn btn-outline-primary rounded-3 px-3 py-1.5 fw-bold extra-small shadow-xs hover-lift d-flex align-items-center gap-1.5">
                <i class="bi bi-receipt-cutoff"></i>
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

        {{-- Row 1: System Theme Customizer with Interactive Live Preview --}}
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-brush-fill text-success fs-5"></i>
                    <div>
                        <h6 class="fw-black text-dark mb-0 font-mono">System Theme Customizer & Live Preview</h6>
                        <small class="text-muted extra-small">Customize POS header, sidebar, buttons and highlights across Web & Mobile</small>
                    </div>
                </div>
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 font-mono fw-bold extra-small">
                    <i class="bi bi-broadcast me-1"></i> Real-time Live Sandbox
                </span>
            </div>

            <div class="row g-4">
                {{-- Theme Controls (Left) --}}
                <div class="col-lg-6">
                    {{-- 1. Categorized Industry-Calibrated Theme Presets --}}
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted mb-0">POS Theme Combinations</label>
                        <span class="badge bg-light text-dark border font-mono extra-small">18 Curated Palettes</span>
                    </div>

                    {{-- Category Filter Pills --}}
                    <div class="d-flex gap-1 mb-2.5 overflow-x-auto pb-1 flex-nowrap" id="themeCategoryPills">
                        <button type="button" class="btn btn-sm btn-category-pill active-category-pill rounded-pill px-2.5 py-0.5 extra-small fw-bold" data-category="all">All</button>
                        <button type="button" class="btn btn-sm btn-category-pill rounded-pill px-2.5 py-0.5 extra-small fw-bold" data-category="retail">🛒 Retail & Marts</button>
                        <button type="button" class="btn btn-sm btn-category-pill rounded-pill px-2.5 py-0.5 extra-small fw-bold" data-category="cafe">☕ Café & Dining</button>
                        <button type="button" class="btn btn-sm btn-category-pill rounded-pill px-2.5 py-0.5 extra-small fw-bold" data-category="pharma">💊 Pharma & Beauty</button>
                        <button type="button" class="btn btn-sm btn-category-pill rounded-pill px-2.5 py-0.5 extra-small fw-bold" data-category="hardware">🛠️ Hardware & Auto</button>
                        <button type="button" class="btn btn-sm btn-category-pill rounded-pill px-2.5 py-0.5 extra-small fw-bold" data-category="dark">🌙 Night & OLED</button>
                    </div>

                    {{-- 20 Rich Presets Grid --}}
                    <div class="row g-2 mb-3" id="presetsGrid" style="max-height: 240px; overflow-y: auto; padding-right: 4px;">
                        
                        {{-- 1. Retail: Emerald Mint --}}
                        <div class="col-6 preset-card-item" data-category="retail">
                            <button type="button" class="btn btn-preset w-100 p-2 text-start rounded-3 border {{ $currentPreset === 'emerald' ? 'active-preset' : '' }}" data-preset="emerald" data-topbar="#064E3B" data-topbar-text="#FFFFFF" data-primary="#059669" data-sidebar="#0F172A" data-sidebar-text="#CBD5E1" data-sidebar-active="#059669" data-sidebar-active-text="#FFFFFF" data-accent="#10B981">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <div class="d-flex align-items-center gap-1.5">
                                        <span class="rounded-circle" style="width:12px;height:12px;background:#064E3B;border:1px solid #fff;"></span>
                                        <span class="rounded-circle" style="width:12px;height:12px;background:#059669;"></span>
                                    </div>
                                    <span class="badge bg-success-subtle text-success" style="font-size:0.6rem;">Grocery</span>
                                </div>
                                <div class="fw-bold extra-small text-dark font-mono">Emerald Mint</div>
                                <small class="text-muted" style="font-size:0.65rem;">Default LikhaPOS</small>
                            </button>
                        </div>

                        {{-- 2. Retail: Clean Minimalist White (LIGHT SIDEBAR) --}}
                        <div class="col-6 preset-card-item" data-category="retail">
                            <button type="button" class="btn btn-preset w-100 p-2 text-start rounded-3 border {{ $currentPreset === 'clean_white' ? 'active-preset' : '' }}" data-preset="clean_white" data-topbar="#0F172A" data-topbar-text="#FFFFFF" data-primary="#059669" data-sidebar="#FFFFFF" data-sidebar-text="#334155" data-sidebar-active="#059669" data-sidebar-active-text="#FFFFFF" data-accent="#10B981">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <div class="d-flex align-items-center gap-1.5">
                                        <span class="rounded-circle" style="width:12px;height:12px;background:#0F172A;border:1px solid #059669;"></span>
                                        <span class="rounded-circle" style="width:12px;height:12px;background:#FFFFFF;border:1px solid #94A3B8;"></span>
                                    </div>
                                    <span class="badge bg-light text-dark border" style="font-size:0.6rem;">Light Mode</span>
                                </div>
                                <div class="fw-bold extra-small text-dark font-mono">Minimalist Pure White</div>
                                <small class="text-muted" style="font-size:0.65rem;">White Sidebar & Crisp Dark Text</small>
                            </button>
                        </div>

                        {{-- 3. Retail: 7-Eleven Orange --}}
                        <div class="col-6 preset-card-item" data-category="retail">
                            <button type="button" class="btn btn-preset w-100 p-2 text-start rounded-3 border {{ $currentPreset === 'mart_orange' ? 'active-preset' : '' }}" data-preset="mart_orange" data-topbar="#7C2D12" data-topbar-text="#FFFFFF" data-primary="#EA580C" data-sidebar="#0F172A" data-sidebar-text="#CBD5E1" data-sidebar-active="#EA580C" data-sidebar-active-text="#FFFFFF" data-accent="#F97316">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <div class="d-flex align-items-center gap-1.5">
                                        <span class="rounded-circle" style="width:12px;height:12px;background:#7C2D12;border:1px solid #fff;"></span>
                                        <span class="rounded-circle" style="width:12px;height:12px;background:#EA580C;"></span>
                                    </div>
                                    <span class="badge bg-warning-subtle text-warning-emphasis" style="font-size:0.6rem;">Convenience</span>
                                </div>
                                <div class="fw-bold extra-small text-dark font-mono">7-Eleven Citrus</div>
                                <small class="text-muted" style="font-size:0.65rem;">Fast-Paced Mart</small>
                            </button>
                        </div>

                        {{-- 4. Retail: Sapphire Hypermarket --}}
                        <div class="col-6 preset-card-item" data-category="retail">
                            <button type="button" class="btn btn-preset w-100 p-2 text-start rounded-3 border {{ $currentPreset === 'ocean' ? 'active-preset' : '' }}" data-preset="ocean" data-topbar="#1E3A8A" data-topbar-text="#FFFFFF" data-primary="#2563EB" data-sidebar="#0F172A" data-sidebar-text="#CBD5E1" data-sidebar-active="#2563EB" data-sidebar-active-text="#FFFFFF" data-accent="#38BDF8">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <div class="d-flex align-items-center gap-1.5">
                                        <span class="rounded-circle" style="width:12px;height:12px;background:#1E3A8A;border:1px solid #fff;"></span>
                                        <span class="rounded-circle" style="width:12px;height:12px;background:#2563EB;"></span>
                                    </div>
                                    <span class="badge bg-primary-subtle text-primary" style="font-size:0.6rem;">Supermarket</span>
                                </div>
                                <div class="fw-bold extra-small text-dark font-mono">Sapphire Blue</div>
                                <small class="text-muted" style="font-size:0.65rem;">Corporate Retail</small>
                            </button>
                        </div>

                        {{-- 5. Retail: Nordic Light Porcelain (LIGHT) --}}
                        <div class="col-6 preset-card-item" data-category="retail">
                            <button type="button" class="btn btn-preset w-100 p-2 text-start rounded-3 border {{ $currentPreset === 'nordic_light' ? 'active-preset' : '' }}" data-preset="nordic_light" data-topbar="#F8FAFC" data-topbar-text="#0F172A" data-primary="#0284C7" data-sidebar="#FFFFFF" data-sidebar-text="#1E293B" data-sidebar-active="#0284C7" data-sidebar-active-text="#FFFFFF" data-accent="#38BDF8">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <div class="d-flex align-items-center gap-1.5">
                                        <span class="rounded-circle" style="width:12px;height:12px;background:#F8FAFC;border:1px solid #0284C7;"></span>
                                        <span class="rounded-circle" style="width:12px;height:12px;background:#0284C7;"></span>
                                    </div>
                                    <span class="badge bg-info-subtle text-info-emphasis" style="font-size:0.6rem;">Light Clean</span>
                                </div>
                                <div class="fw-bold extra-small text-dark font-mono">Nordic Porcelain</div>
                                <small class="text-muted" style="font-size:0.65rem;">All Light Minimalist POS</small>
                            </button>
                        </div>

                        {{-- 6. Retail: Nordic Teal --}}
                        <div class="col-6 preset-card-item" data-category="retail">
                            <button type="button" class="btn btn-preset w-100 p-2 text-start rounded-3 border {{ $currentPreset === 'nordic_teal' ? 'active-preset' : '' }}" data-preset="nordic_teal" data-topbar="#134E4A" data-topbar-text="#FFFFFF" data-primary="#0D9488" data-sidebar="#0F172A" data-sidebar-text="#CBD5E1" data-sidebar-active="#0D9488" data-sidebar-active-text="#FFFFFF" data-accent="#2DD4BF">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <div class="d-flex align-items-center gap-1.5">
                                        <span class="rounded-circle" style="width:12px;height:12px;background:#134E4A;border:1px solid #fff;"></span>
                                        <span class="rounded-circle" style="width:12px;height:12px;background:#0D9488;"></span>
                                    </div>
                                    <span class="badge bg-info-subtle text-info-emphasis" style="font-size:0.6rem;">Department</span>
                                </div>
                                <div class="fw-bold extra-small text-dark font-mono">Nordic Teal</div>
                                <small class="text-muted" style="font-size:0.65rem;">Modern Clean</small>
                            </button>
                        </div>

                        {{-- 7. Café: Espresso & Warm Caramel --}}
                        <div class="col-6 preset-card-item" data-category="cafe">
                            <button type="button" class="btn btn-preset w-100 p-2 text-start rounded-3 border {{ $currentPreset === 'espresso' ? 'active-preset' : '' }}" data-preset="espresso" data-topbar="#3E2723" data-topbar-text="#FFFFFF" data-primary="#D97706" data-sidebar="#1C1917" data-sidebar-text="#E7E5E4" data-sidebar-active="#D97706" data-sidebar-active-text="#FFFFFF" data-accent="#FBBF24">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <div class="d-flex align-items-center gap-1.5">
                                        <span class="rounded-circle" style="width:12px;height:12px;background:#3E2723;border:1px solid #fff;"></span>
                                        <span class="rounded-circle" style="width:12px;height:12px;background:#D97706;"></span>
                                    </div>
                                    <span class="badge bg-secondary-subtle text-dark" style="font-size:0.6rem;">Coffee Shop</span>
                                </div>
                                <div class="fw-bold extra-small text-dark font-mono">Espresso Caramel</div>
                                <small class="text-muted" style="font-size:0.65rem;">Café & Bistro</small>
                            </button>
                        </div>

                        {{-- 8. Café: French Bakery Rose Gold --}}
                        <div class="col-6 preset-card-item" data-category="cafe">
                            <button type="button" class="btn btn-preset w-100 p-2 text-start rounded-3 border {{ $currentPreset === 'bakery_rose' ? 'active-preset' : '' }}" data-preset="bakery_rose" data-topbar="#831843" data-topbar-text="#FFFFFF" data-primary="#DB2777" data-sidebar="#18181B" data-sidebar-text="#E4E4E7" data-sidebar-active="#DB2777" data-sidebar-active-text="#FFFFFF" data-accent="#F472B6">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <div class="d-flex align-items-center gap-1.5">
                                        <span class="rounded-circle" style="width:12px;height:12px;background:#831843;border:1px solid #fff;"></span>
                                        <span class="rounded-circle" style="width:12px;height:12px;background:#DB2777;"></span>
                                    </div>
                                    <span class="badge bg-danger-subtle text-danger" style="font-size:0.6rem;">Bakery</span>
                                </div>
                                <div class="fw-bold extra-small text-dark font-mono">Bakery Rose Gold</div>
                                <small class="text-muted" style="font-size:0.65rem;">Pastry & Sweets</small>
                            </button>
                        </div>

                        {{-- 9. Café: Japanese Matcha & Sage --}}
                        <div class="col-6 preset-card-item" data-category="cafe">
                            <button type="button" class="btn btn-preset w-100 p-2 text-start rounded-3 border {{ $currentPreset === 'matcha' ? 'active-preset' : '' }}" data-preset="matcha" data-topbar="#14532D" data-topbar-text="#FFFFFF" data-primary="#16A34A" data-sidebar="#052E16" data-sidebar-text="#DCFCE7" data-sidebar-active="#16A34A" data-sidebar-active-text="#FFFFFF" data-accent="#4ADE80">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <div class="d-flex align-items-center gap-1.5">
                                        <span class="rounded-circle" style="width:12px;height:12px;background:#14532D;border:1px solid #fff;"></span>
                                        <span class="rounded-circle" style="width:12px;height:12px;background:#16A34A;"></span>
                                    </div>
                                    <span class="badge bg-success-subtle text-success" style="font-size:0.6rem;">Milk Tea</span>
                                </div>
                                <div class="fw-bold extra-small text-dark font-mono">Matcha & Sage</div>
                                <small class="text-muted" style="font-size:0.65rem;">Tea & Healthy Eats</small>
                            </button>
                        </div>

                        {{-- 10. Café: Tropical Mango Sunset --}}
                        <div class="col-6 preset-card-item" data-category="cafe">
                            <button type="button" class="btn btn-preset w-100 p-2 text-start rounded-3 border {{ $currentPreset === 'amber' ? 'active-preset' : '' }}" data-preset="amber" data-topbar="#78350F" data-topbar-text="#FFFFFF" data-primary="#D97706" data-sidebar="#18181B" data-sidebar-text="#FEF3C7" data-sidebar-active="#D97706" data-sidebar-active-text="#FFFFFF" data-accent="#FBBF24">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <div class="d-flex align-items-center gap-1.5">
                                        <span class="rounded-circle" style="width:12px;height:12px;background:#78350F;border:1px solid #fff;"></span>
                                        <span class="rounded-circle" style="width:12px;height:12px;background:#D97706;"></span>
                                    </div>
                                    <span class="badge bg-warning-subtle text-warning-emphasis" style="font-size:0.6rem;">Resto Bar</span>
                                </div>
                                <div class="fw-bold extra-small text-dark font-mono">Sunset Amber</div>
                                <small class="text-muted" style="font-size:0.65rem;">Juice & Casual Bar</small>
                            </button>
                        </div>

                        {{-- 11. Pharma: MedCare Cyan --}}
                        <div class="col-6 preset-card-item" data-category="pharma">
                            <button type="button" class="btn btn-preset w-100 p-2 text-start rounded-3 border {{ $currentPreset === 'pharma' ? 'active-preset' : '' }}" data-preset="pharma" data-topbar="#0E7490" data-topbar-text="#FFFFFF" data-primary="#0891B2" data-sidebar="#0F172A" data-sidebar-text="#CBD5E1" data-sidebar-active="#0891B2" data-sidebar-active-text="#FFFFFF" data-accent="#22D3EE">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <div class="d-flex align-items-center gap-1.5">
                                        <span class="rounded-circle" style="width:12px;height:12px;background:#0E7490;border:1px solid #fff;"></span>
                                        <span class="rounded-circle" style="width:12px;height:12px;background:#0891B2;"></span>
                                    </div>
                                    <span class="badge bg-info-subtle text-info-emphasis" style="font-size:0.6rem;">Pharmacy</span>
                                </div>
                                <div class="fw-bold extra-small text-dark font-mono">MedCare Cyan</div>
                                <small class="text-muted" style="font-size:0.65rem;">Drugstore & Clinic</small>
                            </button>
                        </div>

                        {{-- 12. Beauty: Velvet Lavender & Violet --}}
                        <div class="col-6 preset-card-item" data-category="pharma">
                            <button type="button" class="btn btn-preset w-100 p-2 text-start rounded-3 border {{ $currentPreset === 'cosmetics' ? 'active-preset' : '' }}" data-preset="cosmetics" data-topbar="#581C87" data-topbar-text="#FFFFFF" data-primary="#9333EA" data-sidebar="#1E1B4B" data-sidebar-text="#EDE9FE" data-sidebar-active="#9333EA" data-sidebar-active-text="#FFFFFF" data-accent="#C084FC">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <div class="d-flex align-items-center gap-1.5">
                                        <span class="rounded-circle" style="width:12px;height:12px;background:#581C87;border:1px solid #fff;"></span>
                                        <span class="rounded-circle" style="width:12px;height:12px;background:#9333EA;"></span>
                                    </div>
                                    <span class="badge bg-purple-subtle text-purple" style="font-size:0.6rem;background:#FAF5FF;color:#9333EA;">Cosmetics</span>
                                </div>
                                <div class="fw-bold extra-small text-dark font-mono">Beauty Violet</div>
                                <small class="text-muted" style="font-size:0.65rem;">Salon & Skincare</small>
                            </button>
                        </div>

                        {{-- 13. Hardware: Heavy-Duty Industrial Amber --}}
                        <div class="col-6 preset-card-item" data-category="hardware">
                            <button type="button" class="btn btn-preset w-100 p-2 text-start rounded-3 border {{ $currentPreset === 'hardware' ? 'active-preset' : '' }}" data-preset="hardware" data-topbar="#1E293B" data-topbar-text="#FFFFFF" data-primary="#F59E0B" data-sidebar="#0F172A" data-sidebar-text="#CBD5E1" data-sidebar-active="#F59E0B" data-sidebar-active-text="#FFFFFF" data-accent="#FBBF24">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <div class="d-flex align-items-center gap-1.5">
                                        <span class="rounded-circle" style="width:12px;height:12px;background:#1E293B;border:1px solid #fff;"></span>
                                        <span class="rounded-circle" style="width:12px;height:12px;background:#F59E0B;"></span>
                                    </div>
                                    <span class="badge bg-warning-subtle text-warning-emphasis" style="font-size:0.6rem;">Hardware</span>
                                </div>
                                <div class="fw-bold extra-small text-dark font-mono">Industrial CAT Amber</div>
                                <small class="text-muted" style="font-size:0.65rem;">Auto & Construction</small>
                            </button>
                        </div>

                        {{-- 14. Hardware: Fast Lane Apex Red --}}
                        <div class="col-6 preset-card-item" data-category="hardware">
                            <button type="button" class="btn btn-preset w-100 p-2 text-start rounded-3 border {{ $currentPreset === 'crimson' ? 'active-preset' : '' }}" data-preset="crimson" data-topbar="#881337" data-topbar-text="#FFFFFF" data-primary="#E11D48" data-sidebar="#0F172A" data-sidebar-text="#CBD5E1" data-sidebar-active="#E11D48" data-sidebar-active-text="#FFFFFF" data-accent="#FB7185">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <div class="d-flex align-items-center gap-1.5">
                                        <span class="rounded-circle" style="width:12px;height:12px;background:#881337;border:1px solid #fff;"></span>
                                        <span class="rounded-circle" style="width:12px;height:12px;background:#E11D48;"></span>
                                    </div>
                                    <span class="badge bg-danger-subtle text-danger" style="font-size:0.6rem;">Auto & Sports</span>
                                </div>
                                <div class="fw-bold extra-small text-dark font-mono">Fast Lane Apex Red</div>
                                <small class="text-muted" style="font-size:0.65rem;">Motor & High-Energy</small>
                            </button>
                        </div>

                        {{-- 15. Dark: Cyberpunk Neon --}}
                        <div class="col-6 preset-card-item" data-category="dark">
                            <button type="button" class="btn btn-preset w-100 p-2 text-start rounded-3 border {{ $currentPreset === 'cyberpunk' ? 'active-preset' : '' }}" data-preset="cyberpunk" data-topbar="#090D16" data-topbar-text="#10B981" data-primary="#10B981" data-sidebar="#030712" data-sidebar-text="#6EE7B7" data-sidebar-active="#10B981" data-sidebar-active-text="#000000" data-accent="#06B6D4">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <div class="d-flex align-items-center gap-1.5">
                                        <span class="rounded-circle" style="width:12px;height:12px;background:#090D16;border:1px solid #10B981;"></span>
                                        <span class="rounded-circle" style="width:12px;height:12px;background:#10B981;"></span>
                                    </div>
                                    <span class="badge bg-dark text-success" style="font-size:0.6rem;border:1px solid #10B981;">Cyber Neon</span>
                                </div>
                                <div class="fw-bold extra-small text-dark font-mono">Cyberpunk Emerald</div>
                                <small class="text-muted" style="font-size:0.65rem;">Tech & Vape Lounge</small>
                            </button>
                        </div>

                        {{-- 16. Dark: Stealth AMOLED Black --}}
                        <div class="col-6 preset-card-item" data-category="dark">
                            <button type="button" class="btn btn-preset w-100 p-2 text-start rounded-3 border {{ $currentPreset === 'dark' ? 'active-preset' : '' }}" data-preset="dark" data-topbar="#000000" data-topbar-text="#FFFFFF" data-primary="#10B981" data-sidebar="#09090B" data-sidebar-text="#A1A1AA" data-sidebar-active="#10B981" data-sidebar-active-text="#000000" data-accent="#34D399">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <div class="d-flex align-items-center gap-1.5">
                                        <span class="rounded-circle" style="width:12px;height:12px;background:#000000;border:1px solid #475569;"></span>
                                        <span class="rounded-circle" style="width:12px;height:12px;background:#10B981;"></span>
                                    </div>
                                    <span class="badge bg-dark text-light" style="font-size:0.6rem;">OLED Black</span>
                                </div>
                                <div class="fw-bold extra-small text-dark font-mono">Stealth AMOLED</div>
                                <small class="text-muted" style="font-size:0.65rem;">Night Shift Cashier</small>
                            </button>
                        </div>

                        {{-- 17. Dark: Deep Space Indigo --}}
                        <div class="col-6 preset-card-item" data-category="dark">
                            <button type="button" class="btn btn-preset w-100 p-2 text-start rounded-3 border {{ $currentPreset === 'indigo' ? 'active-preset' : '' }}" data-preset="indigo" data-topbar="#312E81" data-topbar-text="#FFFFFF" data-primary="#6366F1" data-sidebar="#111827" data-sidebar-text="#C7D2FE" data-sidebar-active="#6366F1" data-sidebar-active-text="#FFFFFF" data-accent="#818CF8">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <div class="d-flex align-items-center gap-1.5">
                                        <span class="rounded-circle" style="width:12px;height:12px;background:#312E81;border:1px solid #fff;"></span>
                                        <span class="rounded-circle" style="width:12px;height:12px;background:#6366F1;"></span>
                                    </div>
                                    <span class="badge bg-primary-subtle text-primary" style="font-size:0.6rem;">Space Indigo</span>
                                </div>
                                <div class="fw-bold extra-small text-dark font-mono">Deep Space Indigo</div>
                                <small class="text-muted" style="font-size:0.65rem;">Modern Tech Hub</small>
                            </button>
                        </div>

                        {{-- 18. Dark: Luxury Velvet & Gold --}}
                        <div class="col-6 preset-card-item" data-category="dark">
                            <button type="button" class="btn btn-preset w-100 p-2 text-start rounded-3 border {{ $currentPreset === 'luxury_gold' ? 'active-preset' : '' }}" data-preset="luxury_gold" data-topbar="#1C1917" data-topbar-text="#FACC15" data-primary="#EAB308" data-sidebar="#0C0A09" data-sidebar-text="#FEF08A" data-sidebar-active="#EAB308" data-sidebar-active-text="#000000" data-accent="#FACC15">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <div class="d-flex align-items-center gap-1.5">
                                        <span class="rounded-circle" style="width:12px;height:12px;background:#1C1917;border:1px solid #EAB308;"></span>
                                        <span class="rounded-circle" style="width:12px;height:12px;background:#EAB308;"></span>
                                    </div>
                                    <span class="badge bg-dark text-warning" style="font-size:0.6rem;border:1px solid #EAB308;">VIP Luxury</span>
                                </div>
                                <div class="fw-bold extra-small text-dark font-mono">Velvet Obsidian Gold</div>
                                <small class="text-muted" style="font-size:0.65rem;">Jewelry & Boutique</small>
                            </button>
                        </div>

                        {{-- 19. Dark: Oceanic Abyss --}}
                        <div class="col-6 preset-card-item" data-category="dark">
                            <button type="button" class="btn btn-preset w-100 p-2 text-start rounded-3 border {{ $currentPreset === 'abyss' ? 'active-preset' : '' }}" data-preset="abyss" data-topbar="#020617" data-topbar-text="#FFFFFF" data-primary="#38BDF8" data-sidebar="#0B1220" data-sidebar-text="#BAE6FD" data-sidebar-active="#38BDF8" data-sidebar-active-text="#000000" data-accent="#7DD3FC">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <div class="d-flex align-items-center gap-1.5">
                                        <span class="rounded-circle" style="width:12px;height:12px;background:#020617;border:1px solid #38BDF8;"></span>
                                        <span class="rounded-circle" style="width:12px;height:12px;background:#38BDF8;"></span>
                                    </div>
                                    <span class="badge bg-dark text-info" style="font-size:0.6rem;border:1px solid #38BDF8;">Abyss Blue</span>
                                </div>
                                <div class="fw-bold extra-small text-dark font-mono">Oceanic Abyss</div>
                                <small class="text-muted" style="font-size:0.65rem;">Night Shift Gadgets</small>
                            </button>
                        </div>

                        {{-- 20. Café: Royal Crimson Wine --}}
                        <div class="col-6 preset-card-item" data-category="cafe">
                            <button type="button" class="btn btn-preset w-100 p-2 text-start rounded-3 border {{ $currentPreset === 'wine_crimson' ? 'active-preset' : '' }}" data-preset="wine_crimson" data-topbar="#4C0519" data-topbar-text="#FFFFFF" data-primary="#BE123C" data-sidebar="#1C030B" data-sidebar-text="#FECDD3" data-sidebar-active="#BE123C" data-sidebar-active-text="#FFFFFF" data-accent="#FB7185">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <div class="d-flex align-items-center gap-1.5">
                                        <span class="rounded-circle" style="width:12px;height:12px;background:#4C0519;border:1px solid #fff;"></span>
                                        <span class="rounded-circle" style="width:12px;height:12px;background:#BE123C;"></span>
                                    </div>
                                    <span class="badge bg-danger-subtle text-danger" style="font-size:0.6rem;">Wine & Dine</span>
                                </div>
                                <div class="fw-bold extra-small text-dark font-mono">Royal Vintage Plum</div>
                                <small class="text-muted" style="font-size:0.65rem;">Fine Dining & Lounge</small>
                            </button>
                        </div>

                    </div>
                    <input type="hidden" name="theme_preset" id="themePresetInput" value="{{ $currentPreset }}">

                    {{-- 2. Granular Color Pickers --}}
                    <div class="pt-2 border-top">
                        <div class="row g-2 mb-2">
                            <div class="col-12"><span class="badge bg-light text-dark border font-mono extra-small"><i class="bi bi-layout-text-window-reverse me-1"></i> Topbar & Brand Palette</span></div>
                            <div class="col-6">
                                <label class="form-label extra-small fw-bold text-muted mb-1">Topbar Background</label>
                                <div class="input-group input-group-sm">
                                    <input type="color" class="form-control form-control-color p-1" id="topbarColorPicker" name="topbar_color" value="{{ $topbarColor }}" title="Choose Topbar Color">
                                    <input type="text" class="form-control font-mono extra-small" id="topbarColorText" value="{{ $topbarColor }}" readonly>
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label extra-small fw-bold text-muted mb-1">Topbar Text & Icons</label>
                                <div class="input-group input-group-sm">
                                    <input type="color" class="form-control form-control-color p-1" id="topbarTextColorPicker" name="topbar_text_color" value="{{ $topbarTextColor }}" title="Choose Topbar Text Color">
                                    <input type="text" class="form-control font-mono extra-small" id="topbarTextColorText" value="{{ $topbarTextColor }}" readonly>
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label extra-small fw-bold text-muted mb-1">Primary Button / Brand</label>
                                <div class="input-group input-group-sm">
                                    <input type="color" class="form-control form-control-color p-1" id="primaryColorPicker" name="primary_color" value="{{ $primaryColor }}" title="Choose Primary Color">
                                    <input type="text" class="form-control font-mono extra-small" id="primaryColorText" value="{{ $primaryColor }}" readonly>
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label extra-small fw-bold text-muted mb-1">Accent Dot / Highlights</label>
                                <div class="input-group input-group-sm">
                                    <input type="color" class="form-control form-control-color p-1" id="accentColorPicker" name="accent_color" value="{{ $accentColor }}" title="Choose Accent Color">
                                    <input type="text" class="form-control font-mono extra-small" id="accentColorText" value="{{ $accentColor }}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row g-2 pt-2 border-top">
                            <div class="col-12"><span class="badge bg-light text-dark border font-mono extra-small"><i class="bi bi-layout-sidebar-inset me-1"></i> Sidebar & Text Palette</span></div>
                            <div class="col-6">
                                <label class="form-label extra-small fw-bold text-muted mb-1">Sidebar Background</label>
                                <div class="input-group input-group-sm">
                                    <input type="color" class="form-control form-control-color p-1" id="sidebarColorPicker" name="sidebar_color" value="{{ $sidebarColor }}" title="Choose Sidebar Color">
                                    <input type="text" class="form-control font-mono extra-small" id="sidebarColorText" value="{{ $sidebarColor }}" readonly>
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label extra-small fw-bold text-muted mb-1">Sidebar Inactive Text</label>
                                <div class="input-group input-group-sm">
                                    <input type="color" class="form-control form-control-color p-1" id="sidebarTextColorPicker" name="sidebar_text_color" value="{{ $sidebarTextColor }}" title="Choose Inactive Text Color">
                                    <input type="text" class="form-control font-mono extra-small" id="sidebarTextColorText" value="{{ $sidebarTextColor }}" readonly>
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label extra-small fw-bold text-muted mb-1">Sidebar Active Link Bg</label>
                                <div class="input-group input-group-sm">
                                    <input type="color" class="form-control form-control-color p-1" id="sidebarActivePicker" name="sidebar_active_color" value="{{ $sidebarActive }}" title="Choose Active Item Color">
                                    <input type="text" class="form-control font-mono extra-small" id="sidebarActiveText" value="{{ $sidebarActive }}" readonly>
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label extra-small fw-bold text-muted mb-1">Sidebar Active Text</label>
                                <div class="input-group input-group-sm">
                                    <input type="color" class="form-control form-control-color p-1" id="sidebarActiveTextColorPicker" name="sidebar_active_text_color" value="{{ $sidebarActiveTextColor }}" title="Choose Active Text Color">
                                    <input type="text" class="form-control font-mono extra-small" id="sidebarActiveTextColorText" value="{{ $sidebarActiveTextColor }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Interactive Live Preview Screen (Right) --}}
                <div class="col-lg-6">
                    <label class="form-label extra-small fw-bold text-uppercase text-muted mb-2">Live UI Preview Mockup</label>
                    <div id="mockupContainer" class="rounded-4 border shadow-sm overflow-hidden bg-light" style="height: 310px; position:relative; font-family:'Plus Jakarta Sans',sans-serif;">
                        
                        {{-- Mockup Topbar --}}
                        <div id="mockTopbar" class="d-flex align-items-center justify-content-between px-3 py-2" style="background-color: {{ $topbarColor }}; color: {{ $topbarTextColor }}; transition: all 0.3s ease;">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge rounded-pill bg-white text-dark font-mono extra-small px-2 py-0.5 fw-bold">{{ $tenant->business_code ?? 'STORE-01' }}</span>
                                <span class="fw-bold extra-small font-mono text-truncate" style="max-width:140px;">{{ $tenant->business_name }}</span>
                            </div>
                            <div class="d-flex align-items-center gap-1.5">
                                <span class="badge bg-black-50 extra-small rounded-pill"><i class="bi bi-broadcast text-success me-1"></i>Sync</span>
                                <button type="button" id="mockBtnPos" class="btn btn-sm rounded-pill text-white fw-bold px-2 py-0.5" style="background-color: {{ $primaryColor }}; font-size:0.65rem; border:none; transition: all 0.3s ease;">
                                    <i class="bi bi-cart-fill me-1"></i>POS
                                </button>
                            </div>
                        </div>

                        {{-- Mockup Body with Sidebar & Content --}}
                        <div class="d-flex" style="height:calc(100% - 37px);">
                            {{-- Mockup Sidebar --}}
                            <div id="mockSidebar" class="p-2 d-flex flex-column gap-1" style="width:115px; background-color: {{ $sidebarColor }}; color: {{ $sidebarTextColor }}; transition: all 0.3s ease;">
                                <small class="extra-small text-uppercase font-mono fw-bold px-1" style="font-size:0.55rem; opacity:0.75;">MAIN</small>
                                <div id="mockActiveLink" class="p-1.5 rounded-2 d-flex align-items-center gap-1.5 fw-bold" style="background-color: {{ $sidebarActive }}; color: {{ $sidebarActiveTextColor }}; font-size:0.65rem; transition: all 0.3s ease;">
                                    <i class="bi bi-speedometer2"></i>
                                    <span>Dashboard</span>
                                </div>
                                <div class="mock-inactive-item p-1.5 rounded-2 d-flex align-items-center gap-1.5" style="font-size:0.65rem; color: {{ $sidebarTextColor }}; opacity:0.85;">
                                    <i class="bi bi-box-seam"></i>
                                    <span>Products</span>
                                </div>
                                <div class="mock-inactive-item p-1.5 rounded-2 d-flex align-items-center gap-1.5" style="font-size:0.65rem; color: {{ $sidebarTextColor }}; opacity:0.85;">
                                    <i class="bi bi-people"></i>
                                    <span>Suki CRM</span>
                                </div>
                            </div>

                            {{-- Mockup Content Area --}}
                            <div class="flex-grow-1 p-2.5 overflow-hidden bg-light">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="fw-bold extra-small text-dark font-mono">Today's Sales KPI</span>
                                    <span class="badge bg-success-subtle text-success extra-small">+14.2%</span>
                                </div>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="card p-2 border rounded-3 bg-white shadow-xs">
                                            <small class="text-muted" style="font-size:0.6rem;">Gross Revenue</small>
                                            <span class="fw-black text-dark font-mono" style="font-size:0.85rem;">₱24,850.00</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="card p-2 border rounded-3 bg-white shadow-xs">
                                            <small class="text-muted" style="font-size:0.6rem;">Cash in Drawer</small>
                                            <span class="fw-black text-success font-mono" style="font-size:0.85rem;">₱18,400.00</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-2 text-center">
                                    <button type="button" id="mockActionButton" class="btn btn-sm text-white fw-bold rounded-3 px-3 py-1 shadow-xs" style="background-color: {{ $primaryColor }}; font-size:0.7rem; border:none; transition: all 0.3s ease;">
                                        <i class="bi bi-calculator me-1"></i> Start Cashier Shift
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 2: Store Logo, Receipt Branding & CRM Loyalty Engine --}}
        <div class="row g-3">
            {{-- Left Column: Store Logo & Receipt Branding --}}
            <div class="col-lg-4">
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

            {{-- Right Column: Store Business Profile & CRM Settings --}}
            <div class="col-lg-8">
                {{-- Store Profile --}}
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-3 bg-white">
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
                </div>

                {{-- CRM & Suki Rewards Configuration --}}
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-3 bg-white">
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-people-fill text-purple fs-5" style="color:#9333EA;"></i>
                            <h6 class="fw-black text-dark mb-0 font-mono">Complete CRM & Suki Loyalty Engine</h6>
                        </div>
                        <span class="badge bg-purple-subtle text-purple font-mono extra-small" style="background:#FAF5FF;color:#9333EA;">
                            Loyalty & Credit Automation
                        </span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Points Per ₱100 Spent</label>
                            <div class="input-group">
                                <input type="number" step="0.1" name="points_per_peso" class="form-control font-mono text-dark" value="{{ old('points_per_peso', $pointsPerPeso * 100) }}">
                                <span class="input-group-text font-mono extra-small">pts / ₱100</span>
                            </div>
                            <small class="text-muted extra-small">Points awarded to registered suki customers automatically.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Default Suki Utang (Credit) Limit</label>
                            <div class="input-group">
                                <span class="input-group-text font-mono extra-small">₱</span>
                                <input type="number" step="100" name="default_credit_limit" class="form-control font-mono text-dark" value="{{ old('default_credit_limit', $defaultCreditLimit) }}">
                            </div>
                            <small class="text-muted extra-small">Default maximum allowable utang balance per customer.</small>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" role="switch" name="sms_receipt_enabled" value="1" id="smsReceiptCheck" {{ $smsReceiptEnabled ? 'checked' : '' }}>
                                <label class="form-check-label extra-small fw-bold text-dark" for="smsReceiptCheck">
                                    Send SMS E-Receipts to Customers
                                </label>
                            </div>
                            <small class="text-muted extra-small d-block ms-4">Automatically send SMS transaction receipt after checkout.</small>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" role="switch" name="sms_utang_reminder_enabled" value="1" id="smsUtangCheck" {{ $smsUtangReminderEnabled ? 'checked' : '' }}>
                                <label class="form-check-label extra-small fw-bold text-dark" for="smsUtangCheck">
                                    Automated Suki Utang SMS Reminders
                                </label>
                            </div>
                            <small class="text-muted extra-small d-block ms-4">Notify suki customers with outstanding credit balances.</small>
                        </div>
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="d-flex justify-content-end">
                    <button type="submit" id="btnSaveStoreSettings" class="btn btn-success rounded-3 px-4 py-2.5 fw-bold d-flex align-items-center gap-2 shadow-sm hover-lift" style="background:linear-gradient(135deg, #059669 0%, #047857 100%);border:none;font-size:0.9rem;">
                        <i class="bi bi-check-circle-fill fs-5"></i>
                        <span>Save Store Profile, Theme & CRM Settings</span>
                    </button>
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

<style>
    .btn-preset {
        background: #F8FAFC;
        transition: all 0.2s ease;
    }
    .btn-preset:hover {
        background: #F1F5F9;
        transform: translateY(-1px);
    }
    .active-preset {
        border-color: #059669 !important;
        background: #ECFDF5 !important;
        box-shadow: 0 0 0 2px rgba(5, 150, 105, 0.2);
    }
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Logo Preview
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

    // 2. Interactive Real-time Theme Customizer Elements
    const topbarPicker = document.getElementById('topbarColorPicker');
    const topbarText = document.getElementById('topbarColorText');
    const topbarTextColorPicker = document.getElementById('topbarTextColorPicker');
    const topbarTextColorText = document.getElementById('topbarTextColorText');

    const primaryPicker = document.getElementById('primaryColorPicker');
    const primaryText = document.getElementById('primaryColorText');
    const accentPicker = document.getElementById('accentColorPicker');
    const accentText = document.getElementById('accentColorText');

    const sidebarPicker = document.getElementById('sidebarColorPicker');
    const sidebarText = document.getElementById('sidebarColorText');
    const sidebarTextColorPicker = document.getElementById('sidebarTextColorPicker');
    const sidebarTextColorText = document.getElementById('sidebarTextColorText');

    const sidebarActivePicker = document.getElementById('sidebarActivePicker');
    const sidebarActiveText = document.getElementById('sidebarActiveText');
    const sidebarActiveTextColorPicker = document.getElementById('sidebarActiveTextColorPicker');
    const sidebarActiveTextColorText = document.getElementById('sidebarActiveTextColorText');

    const themePresetInput = document.getElementById('themePresetInput');

    // Mockup Elements
    const mockTopbar = document.getElementById('mockTopbar');
    const mockSidebar = document.getElementById('mockSidebar');
    const mockActiveLink = document.getElementById('mockActiveLink');
    const mockBtnPos = document.getElementById('mockBtnPos');
    const mockActionButton = document.getElementById('mockActionButton');

    function updateLivePreview() {
        const topbarCol = topbarPicker ? topbarPicker.value : '#064E3B';
        const topbarTextCol = topbarTextColorPicker ? topbarTextColorPicker.value : '#FFFFFF';
        const primaryCol = primaryPicker ? primaryPicker.value : '#059669';
        const accentCol = accentPicker ? accentPicker.value : '#10B981';

        const sidebarCol = sidebarPicker ? sidebarPicker.value : '#0F172A';
        const sidebarTextCol = sidebarTextColorPicker ? sidebarTextColorPicker.value : '#CBD5E1';
        const sidebarActiveCol = sidebarActivePicker ? sidebarActivePicker.value : '#059669';
        const sidebarActiveTextCol = sidebarActiveTextColorPicker ? sidebarActiveTextColorPicker.value : '#FFFFFF';

        // Update hex text displays
        if (topbarText) topbarText.value = topbarCol;
        if (topbarTextColorText) topbarTextColorText.value = topbarTextCol;
        if (primaryText) primaryText.value = primaryCol;
        if (accentText) accentText.value = accentCol;
        if (sidebarText) sidebarText.value = sidebarCol;
        if (sidebarTextColorText) sidebarTextColorText.value = sidebarTextCol;
        if (sidebarActiveText) sidebarActiveText.value = sidebarActiveCol;
        if (sidebarActiveTextColorText) sidebarActiveTextColorText.value = sidebarActiveTextCol;

        // 1. Update Preview Sandbox Mockup
        if (mockTopbar) {
            mockTopbar.style.backgroundColor = topbarCol;
            mockTopbar.style.color = topbarTextCol;
        }
        if (mockSidebar) {
            mockSidebar.style.backgroundColor = sidebarCol;
            mockSidebar.style.color = sidebarTextCol;
        }
        if (mockActiveLink) {
            mockActiveLink.style.backgroundColor = sidebarActiveCol;
            mockActiveLink.style.color = sidebarActiveTextCol;
        }
        document.querySelectorAll('.mock-inactive-item').forEach(item => {
            item.style.color = sidebarTextCol;
        });
        if (mockBtnPos) mockBtnPos.style.backgroundColor = primaryCol;
        if (mockActionButton) mockActionButton.style.backgroundColor = primaryCol;

        // 2. Real-time Live Update on the ACTUAL Application Layout (Instant Feedback)
        document.documentElement.style.setProperty('--theme-topbar', topbarCol);
        document.documentElement.style.setProperty('--theme-topbar-text', topbarTextCol);
        document.documentElement.style.setProperty('--theme-sidebar', sidebarCol);
        document.documentElement.style.setProperty('--theme-sidebar-text', sidebarTextCol);
        document.documentElement.style.setProperty('--theme-sidebar-active', sidebarActiveCol);
        document.documentElement.style.setProperty('--theme-sidebar-active-text', sidebarActiveTextCol);
        document.documentElement.style.setProperty('--theme-primary', primaryCol);
        document.documentElement.style.setProperty('--theme-accent', accentCol);

        const realSidebar = document.querySelector('.likha-sidebar') || document.querySelector('#mainSidebar');
        if (realSidebar) {
            realSidebar.style.setProperty('background-color', sidebarCol, 'important');
            realSidebar.style.setProperty('background', sidebarCol, 'important');
        }

        const realTopbar = document.querySelector('.crm-topbar') || document.querySelector('#crmTopbar') || document.querySelector('.likha-topbar') || document.querySelector('header');
        if (realTopbar) {
            realTopbar.style.setProperty('background-color', topbarCol, 'important');
            realTopbar.style.setProperty('background', topbarCol, 'important');
            realTopbar.style.setProperty('color', topbarTextCol, 'important');
            realTopbar.querySelectorAll('.crm-title-text, .crm-subtitle, .crm-store-name, .crm-store-status, a, span, i').forEach(el => {
                if (!el.classList.contains('crm-notif-badge') && !el.classList.contains('crm-pos-btn')) {
                    el.style.setProperty('color', topbarTextCol, 'important');
                }
            });
            realTopbar.querySelectorAll('.crm-icon-btn, .crm-store-pill').forEach(el => {
                el.style.setProperty('color', topbarTextCol, 'important');
            });
        }

        document.querySelectorAll('.sidebar-link:not(.active), .sidebar-link:not(.active) span, .sidebar-link:not(.active) i, .sidebar-sublink:not(.active), .sidebar-sublink:not(.active) span, .sidebar-sublink:not(.active) i').forEach(el => {
            el.style.setProperty('color', sidebarTextCol, 'important');
        });
        document.querySelectorAll('.sidebar-section-title, .sidebar-heading').forEach(title => {
            title.style.setProperty('color', sidebarTextCol, 'important');
        });
        document.querySelectorAll('.sidebar-link.active, .sidebar-link.active span, .sidebar-link.active i').forEach(el => {
            el.style.setProperty('background-color', sidebarActiveCol, 'important');
            el.style.setProperty('background', sidebarActiveCol, 'important');
            el.style.setProperty('color', sidebarActiveTextCol, 'important');
        });
        document.querySelectorAll('.sidebar-sublink.active, .sidebar-sublink.active span, .sidebar-sublink.active i').forEach(el => {
            el.style.setProperty('color', primaryCol, 'important');
        });
    }

    // Helper to calculate luminance for smart auto-contrast
    function getLuminance(hex) {
        const c = hex.replace('#', '');
        const rgb = parseInt(c, 16);
        const r = (rgb >> 16) & 0xff;
        const g = (rgb >> 8) & 0xff;
        const b = (rgb >> 0) & 0xff;
        return 0.2126 * (r / 255) + 0.7152 * (g / 255) + 0.0722 * (b / 255);
    }

    // Bind color pickers
    [
        topbarPicker, topbarTextColorPicker,
        primaryPicker, accentPicker,
        sidebarPicker, sidebarTextColorPicker,
        sidebarActivePicker, sidebarActiveTextColorPicker
    ].forEach(picker => {
        if (picker) {
            picker.addEventListener('input', () => {
                document.querySelectorAll('.btn-preset').forEach(b => b.classList.remove('active-preset'));
                if (themePresetInput) themePresetInput.value = 'custom';
                updateLivePreview();
            });
        }
    });

    // Auto-contrast when changing sidebar background
    if (sidebarPicker) {
        sidebarPicker.addEventListener('change', () => {
            const lum = getLuminance(sidebarPicker.value);
            if (lum > 0.65) {
                // Light/white sidebar -> switch text to dark slate
                if (sidebarTextColorPicker) sidebarTextColorPicker.value = '#334155';
            } else {
                // Dark sidebar -> switch text to light
                if (sidebarTextColorPicker) sidebarTextColorPicker.value = '#CBD5E1';
            }
            updateLivePreview();
        });
    }

    // Preset Button Clicks (Robust delegation)
    document.querySelectorAll('.btn-preset').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const targetBtn = this.closest('.btn-preset') || this;

            document.querySelectorAll('.btn-preset').forEach(b => b.classList.remove('active-preset'));
            targetBtn.classList.add('active-preset');

            const preset = targetBtn.dataset.preset;
            const topbar = targetBtn.dataset.topbar;
            const topbarText = targetBtn.dataset.topbarText || '#FFFFFF';
            const primary = targetBtn.dataset.primary;
            const accent = targetBtn.dataset.accent || primary;
            const sidebar = targetBtn.dataset.sidebar;
            const sidebarText = targetBtn.dataset.sidebarText || '#CBD5E1';
            const sidebarActive = targetBtn.dataset.sidebarActive;
            const sidebarActiveText = targetBtn.dataset.sidebarActiveText || '#FFFFFF';

            if (themePresetInput) themePresetInput.value = preset;
            if (topbarPicker && topbar) topbarPicker.value = topbar;
            if (topbarTextColorPicker && topbarText) topbarTextColorPicker.value = topbarText;
            if (primaryPicker && primary) primaryPicker.value = primary;
            if (accentPicker && accent) accentPicker.value = accent;
            if (sidebarPicker && sidebar) sidebarPicker.value = sidebar;
            if (sidebarTextColorPicker && sidebarText) sidebarTextColorPicker.value = sidebarText;
            if (sidebarActivePicker && sidebarActive) sidebarActivePicker.value = sidebarActive;
            if (sidebarActiveTextColorPicker && sidebarActiveText) sidebarActiveTextColorPicker.value = sidebarActiveText;

            updateLivePreview();
        });
    });

    // Category Filter Pills
    const categoryPills = document.querySelectorAll('.btn-category-pill');
    const presetItems = document.querySelectorAll('.preset-card-item');

    categoryPills.forEach(pill => {
        pill.addEventListener('click', function () {
            categoryPills.forEach(p => p.classList.remove('active-category-pill'));
            this.classList.add('active-category-pill');

            const category = this.dataset.category;

            presetItems.forEach(item => {
                if (category === 'all' || item.dataset.category === category) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });

    // 3. Receipt Preview Modal
    const btnPreviewReceipt = document.getElementById('btnPreviewReceipt');
    const receiptModal = document.getElementById('receiptPreviewModal');
    const receiptFrame = document.getElementById('receiptPreviewFrame');

    if (btnPreviewReceipt && receiptModal && receiptFrame) {
        btnPreviewReceipt.addEventListener('click', function () {
            const previewUrl = "{{ route('settings.receipt.preview') }}";
            receiptFrame.src = previewUrl;
            const bsModal = new bootstrap.Modal(receiptModal);
            bsModal.show();
        });
    }
});
</script>
<style>
    .btn-preset {
        background: #ffffff;
        transition: all 0.2s ease;
        border-color: #E2E8F0 !important;
    }
    .btn-preset:hover {
        background: #F8FAFC;
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.08);
    }
    .active-preset {
        border-color: #059669 !important;
        background: #ECFDF5 !important;
        box-shadow: 0 0 0 2px rgba(5, 150, 105, 0.3) !important;
    }
    .btn-category-pill {
        background: #F1F5F9;
        color: #475569;
        border: 1px solid #E2E8F0;
        transition: all 0.15s ease;
    }
    .btn-category-pill:hover {
        background: #E2E8F0;
        color: #1E293B;
    }
    .active-category-pill {
        background: #0F172A !important;
        color: #FFFFFF !important;
        border-color: #0F172A !important;
    }
</style>
@endpush
@endsection