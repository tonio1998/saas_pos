@extends('layouts.app')
@section('title', 'Business Dashboard')
@section('shortText', 'Executive POS & CRM Analytics, Inventory Health, and Financial Monitoring')

@push('styles')
<style>
    .kpi-growth-positive { color: #059669; font-weight: 800; font-size: 0.72rem; }
    .kpi-growth-negative { color: #e11d48; font-weight: 800; font-size: 0.72rem; }
    .kpi-growth-neutral { color: #64748b; font-weight: 800; font-size: 0.72rem; }
    .chart-container-wrapper { height: 240px; position: relative; overflow: hidden; }
    .skeleton-box { min-height: 22px; border-radius: 6px; background: #e2e8f0; animation: pulse 1.5s infinite; }
    @keyframes pulse { 0%, 100% { opacity: 0.6; } 50% { opacity: 0.25; } }
</style>
@endpush

@section('content')
<div class="container-fluid px-0">

    {{-- Semi-Compact Executive Welcome Banner with Proper Gaps --}}
    <div class="barya-welcome-banner d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-3 p-3.5">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1.5 flex-wrap" id="shiftBannerStatus">
                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2.5 py-1 fw-bold" style="font-size:0.7rem;">
                    <span class="spinner-border spinner-border-sm me-1" style="width:0.65rem;height:0.65rem;"></span> Checking Shift...
                </span>
                <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 font-mono fw-bold" style="font-size:0.7rem;">
                    Store Code: {{ session('tenant_code', 'STORE-01') }}
                </span>
                <span class="text-muted extra-small fw-semibold"><i class="bi bi-cloud-check-fill text-success me-1"></i>Live Sync</span>
            </div>
            <h4 class="fw-black text-dark mb-1 font-mono" style="letter-spacing:-0.4px;font-size:1.15rem;">
                Welcome back, <span class="text-success">{{ optional(auth()->user())->name ?? 'Store Owner' }}</span>! 👋
            </h4>
            <p class="text-muted extra-small mb-0">
                Real-time executive POS analytics and customer CRM summary for <strong>{{ session('tenant_name', 'LikhaPOS Minimart') }}</strong>.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap flex-shrink-0">
            <a href="{{ route('terminal.index') }}" class="btn btn-success fw-bold px-3.5 py-2 rounded-3 shadow-xs d-flex align-items-center gap-2 hover-lift" style="background:linear-gradient(135deg, #059669 0%, #047857 100%);border:none;font-size:0.82rem;">
                <i class="bi bi-calculator-fill fs-6"></i>
                <span>Open POS</span>
            </a>
            <a href="{{ route('products.create') }}" class="btn btn-light border fw-bold px-3 py-2 rounded-3 text-dark d-flex align-items-center gap-1.5 hover-lift" style="font-size:0.82rem;">
                <i class="bi bi-plus-circle-fill text-success" style="font-size:0.8rem;"></i>
                <span>Add Product</span>
            </a>
            <a href="{{ route('customers.index') }}" class="btn btn-light border fw-bold px-3 py-2 rounded-3 text-dark d-flex align-items-center gap-1.5 hover-lift" style="font-size:0.82rem;">
                <i class="bi bi-people-fill text-primary" style="font-size:0.8rem;"></i>
                <span>Customer CRM</span>
            </a>
        </div>
    </div>

    {{-- 6 Semi-Compact Real-Time KPI Cards (With Proper Spacing & Gaps) --}}
    <div class="row g-3 mb-3">
        {{-- 1. Today's Sales --}}
        <div class="col-6 col-md-4 col-xl-2">
            <div class="barya-kpi-card emerald h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Today's Sales</span>
                    <div class="kpi-icon-box emerald"><i class="bi bi-graph-up-arrow"></i></div>
                </div>
                <div class="kpi-value font-mono" id="kpiTodaySales"><div class="skeleton-box my-1"></div></div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1" id="kpiTodaySalesSub">
                    <span class="text-muted extra-small">Loading...</span>
                </div>
            </div>
        </div>

        {{-- 2. This Month's Sales --}}
        <div class="col-6 col-md-4 col-xl-2">
            <div class="barya-kpi-card blue h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Month's Revenue</span>
                    <div class="kpi-icon-box blue"><i class="bi bi-calendar-check-fill"></i></div>
                </div>
                <div class="kpi-value font-mono" id="kpiMonthSales"><div class="skeleton-box my-1"></div></div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1" id="kpiMonthSalesSub">
                    <span class="text-muted extra-small">Loading...</span>
                </div>
            </div>
        </div>

        {{-- 3. Today's Gross Profit --}}
        <div class="col-6 col-md-4 col-xl-2">
            <div class="barya-kpi-card emerald h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Today's Profit</span>
                    <div class="kpi-icon-box emerald"><i class="bi bi-wallet2"></i></div>
                </div>
                <div class="kpi-value font-mono text-success" id="kpiTodayProfit"><div class="skeleton-box my-1"></div></div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1" id="kpiTodayProfitSub">
                    <span class="text-muted extra-small">Loading...</span>
                </div>
            </div>
        </div>

        {{-- 4. Cash in Drawer --}}
        <div class="col-6 col-md-4 col-xl-2">
            <div class="barya-kpi-card amber h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Drawer Balance</span>
                    <div class="kpi-icon-box amber"><i class="bi bi-cash-stack"></i></div>
                </div>
                <div class="kpi-value font-mono" id="kpiDrawerBalance"><div class="skeleton-box my-1"></div></div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1" id="kpiDrawerBalanceSub">
                    <span class="text-muted extra-small">Loading...</span>
                </div>
            </div>
        </div>

        {{-- 5. Credit / Receivables --}}
        <div class="col-6 col-md-4 col-xl-2">
            <div class="barya-kpi-card rose h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Credit Receivables</span>
                    <div class="kpi-icon-box rose"><i class="bi bi-book-half"></i></div>
                </div>
                <div class="kpi-value font-mono text-danger" id="kpiUtangBalance"><div class="skeleton-box my-1"></div></div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1" id="kpiUtangBalanceSub">
                    <span class="text-muted extra-small">Loading...</span>
                </div>
            </div>
        </div>

        {{-- 6. Total Inventory Valuation --}}
        <div class="col-6 col-md-4 col-xl-2">
            <div class="barya-kpi-card purple h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Stock Valuation</span>
                    <div class="kpi-icon-box purple"><i class="bi bi-box-seam"></i></div>
                </div>
                <div class="kpi-value font-mono" id="kpiStockValuation"><div class="skeleton-box my-1"></div></div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1" id="kpiStockValuationSub">
                    <span class="text-muted extra-small">Loading...</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Row: Interactive Sales Trend Chart & Payment Methods / CRM Breakdown --}}
    <div class="row g-3 mb-3">
        {{-- Dynamic Sales Trends Chart (Async Pulled via /dashboard/analytics) --}}
        <div class="col-xl-8">
            <div class="modern-card h-100 bg-white p-3.5">
                <div class="d-flex align-items-center justify-content-between mb-2.5">
                    <div>
                        <h5 class="section-title font-mono mb-0">Revenue & Sales Trends</h5>
                        <div class="section-subtitle">Daily transaction totals and gross revenue trends</div>
                    </div>
                    <div class="btn-group btn-group-sm rounded-pill p-0.5 bg-light border">
                        <button type="button" class="btn btn-sm chart-filter-btn active rounded-pill px-3 py-1 fw-bold" data-period="7days" style="background:#059669;color:#fff;border:none;font-size:0.75rem;">Last 7 Days</button>
                        <button type="button" class="btn btn-sm chart-filter-btn rounded-pill px-3 py-1 fw-bold btn-light border-0" data-period="30days" style="font-size:0.75rem;">Last 30 Days</button>
                    </div>
                </div>

                <div class="chart-container-wrapper" id="chartWrapper" style="position: relative; min-height: 270px; width: 100%;">
                    <div class="text-muted" id="chartLoader" style="display: flex; align-items: center; justify-content: center; position: absolute; inset: 0; background: rgba(255, 255, 255, 0.9); z-index: 5; border-radius: 8px;">
                        <div class="spinner-border spinner-border-sm text-success" role="status"></div>
                        <span class="ms-2 fw-semibold extra-small">Fetching sales analytics...</span>
                    </div>
                    <div id="salesTrendsChart" style="min-height: 270px;"></div>
                </div>
            </div>
        </div>

        {{-- Payment Methods & Customer CRM Snapshot (Apex Donut Chart) --}}
        <div class="col-xl-4">
            <div class="modern-card h-100 bg-white p-3.5 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div>
                            <h5 class="section-title font-mono mb-0">Payment Channel Share</h5>
                            <div class="section-subtitle">30-day payment method distribution</div>
                        </div>
                        <span class="badge bg-light text-dark border extra-small fw-bold">Live DB</span>
                    </div>

                    {{-- Apex Donut Chart --}}
                    <div id="paymentDonutChart" style="min-height: 185px;"></div>

                    {{-- 4 Channel Chips --}}
                    <div class="row g-2 mt-1 mb-2.5" id="paymentBadgesGrid">
                        <div class="col-6">
                            <div class="channel-stat-chip cash">
                                <span class="chip-label"><i class="bi bi-cash"></i>Cash</span>
                                <span class="chip-val" id="txtCashAmount">--</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="channel-stat-chip gcash">
                                <span class="chip-label"><i class="bi bi-phone"></i>GCash</span>
                                <span class="chip-val" id="txtGcashAmount">--</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="channel-stat-chip maya">
                                <span class="chip-label"><i class="bi bi-wallet2"></i>Maya</span>
                                <span class="chip-val" id="txtMayaAmount">--</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="channel-stat-chip credit">
                                <span class="chip-label"><i class="bi bi-book"></i>Credit</span>
                                <span class="chip-val" id="txtCreditAmount">--</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Loyalty Points & Suki Summary Bar --}}
                <div class="suki-summary-bar mt-2">
                    <div class="suki-stat-col">
                        <div class="suki-icon-box loyalty">
                            <i class="bi bi-star-fill"></i>
                        </div>
                        <div>
                            <div class="suki-meta-label">Loyalty Points</div>
                            <div class="suki-meta-value text-success" id="valSukiPoints">-- pts</div>
                        </div>
                    </div>
                    <div class="suki-divider"></div>
                    <div class="suki-stat-col text-end">
                        <div>
                            <div class="suki-meta-label">Active Suki</div>
                            <div class="suki-meta-value text-dark" id="valTotalCustomers">-- cust</div>
                        </div>
                        <div class="suki-icon-box users">
                            <i class="bi bi-people-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Action Command Bar --}}
    <div class="modern-card bg-white p-3.5 mb-3">
        <div class="d-flex align-items-center justify-content-between mb-2.5">
            <div>
                <h5 class="section-title font-mono mb-0">Quick POS Commands & Management</h5>
                <div class="section-subtitle">Fast shortcuts to essential POS, inventory, and cashiering operations</div>
            </div>
        </div>
        <div class="quick-grid">
            <a href="{{ route('terminal.index') }}" class="quick-action-tile emerald">
                <div class="quick-tile-icon"><i class="bi bi-calculator-fill"></i></div>
                <div class="quick-tile-title">Open POS</div>
            </a>
            <a href="{{ route('products.create') }}" class="quick-action-tile blue">
                <div class="quick-tile-icon"><i class="bi bi-plus-circle-fill"></i></div>
                <div class="quick-tile-title">Add Item</div>
            </a>
            <a href="{{ route('products.index') }}" class="quick-action-tile cyan">
                <div class="quick-tile-icon"><i class="bi bi-box-seam-fill"></i></div>
                <div class="quick-tile-title">Masterlist</div>
            </a>
            <a href="{{ route('customers.index') }}" class="quick-action-tile rose">
                <div class="quick-tile-icon"><i class="bi bi-people-fill"></i></div>
                <div class="quick-tile-title">Suki CRM</div>
            </a>
            <a href="{{ route('reports.sales') }}" class="quick-action-tile amber">
                <div class="quick-tile-icon"><i class="bi bi-bar-chart-line-fill"></i></div>
                <div class="quick-tile-title">Reports</div>
            </a>
            <a href="{{ route('cashiering.cash-shifts.index') }}" class="quick-action-tile purple">
                <div class="quick-tile-icon"><i class="bi bi-clock-history"></i></div>
                <div class="quick-tile-title">Shift Logs</div>
            </a>
            <a href="{{ route('cashiering.cash-transactions.index') }}" class="quick-action-tile emerald">
                <div class="quick-tile-icon"><i class="bi bi-arrow-left-right"></i></div>
                <div class="quick-tile-title">Cash In/Out</div>
            </a>
            <a href="{{ route('settings.index') }}" class="quick-action-tile slate">
                <div class="quick-tile-icon"><i class="bi bi-gear-fill"></i></div>
                <div class="quick-tile-title">Settings</div>
            </a>
        </div>
    </div>

    {{-- Real DB Data Grids: Recent Transactions & Low Stock Alert --}}
    <div class="row g-3 mb-3">
        {{-- Recent Real Store Transactions (Async Pulled via /dashboard/recent-sales) --}}
        <div class="col-xl-6">
            <div class="modern-card h-100 bg-white p-3.5">
                <div class="d-flex align-items-center justify-content-between mb-2.5">
                    <div>
                        <h5 class="section-title font-mono mb-0">Live Store Transactions</h5>
                        <div class="section-subtitle">Real-time checkout stream recorded across terminals</div>
                    </div>
                    <a href="{{ route('sales.index') }}" class="btn btn-xs btn-light border fw-bold text-success rounded-pill px-2.5 py-1" style="font-size:0.75rem;">View All &rarr;</a>
                </div>

                <div class="table-responsive rounded-3 border overflow-hidden">
                    <table class="barya-data-table">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Customer</th>
                                <th class="text-center">Payment</th>
                                <th class="text-end">Amount</th>
                                <th class="text-end">Time</th>
                            </tr>
                        </thead>
                        <tbody id="recentSalesTbody">
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <div class="spinner-border spinner-border-sm text-success me-1"></div>
                                    Loading live transactions...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Low Stock Inventory Alert (Async Pulled via /dashboard/inventory-alerts) --}}
        <div class="col-xl-6">
            <div class="modern-card h-100 bg-white p-3.5">
                <div class="d-flex align-items-center justify-content-between mb-2.5">
                    <div>
                        <h5 class="section-title font-mono mb-0">Critical Low Stock Alerts</h5>
                        <div class="section-subtitle">SKUs requiring immediate purchase order or replenishment</div>
                    </div>
                    <a href="{{ route('products.index') }}" class="btn btn-xs btn-light border fw-bold text-warning rounded-pill px-2.5 py-1" style="font-size:0.75rem;">Masterlist &rarr;</a>
                </div>

                <div class="table-responsive rounded-3 border overflow-hidden">
                    <table class="barya-data-table">
                        <thead>
                            <tr>
                                <th>Product Item</th>
                                <th>Category</th>
                                <th class="text-center">Stock Level</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody id="inventoryAlertsTbody">
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <div class="spinner-border spinner-border-sm text-warning me-1"></div>
                                    Analyzing inventory health...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Top Customers & Loyalty Leaderboard (Async Pulled via /dashboard/top-suki) --}}
    <div class="modern-card bg-white p-3.5 mb-3">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h5 class="section-title font-mono mb-0">Top Customers & Loyalty Leaderboard</h5>
                <div class="section-subtitle">Store VIP accounts, accumulated rewards, and active receivables</div>
            </div>
            <a href="{{ route('customers.index') }}" class="btn btn-xs btn-light border fw-bold text-primary rounded-pill px-2.5 py-1" style="font-size:0.75rem;">Customer CRM &rarr;</a>
        </div>

        <div class="row g-3" id="topSukiContainer">
            <div class="col-12 text-center py-4 text-muted">
                <div class="spinner-border spinner-border-sm text-primary me-1"></div>
                Loading CRM leaderboard...
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/apexcharts.min.js') }}"></script>
<script>
    let apexChartInstance = null;

    // Start all requests immediately
    pullKpis();
    pullAnalytics('7days');
    pullRecentSales();
    pullInventoryAlerts();
    pullTopSuki();

    // Chart filter toggle
    document.querySelectorAll('.chart-filter-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.chart-filter-btn').forEach(b => {
                b.classList.remove('active');
                b.style.background = '';
                b.style.color = '';
                b.classList.add('btn-light');
            });

            this.classList.add('active');
            this.classList.remove('btn-light');
            this.style.background = '#059669';
            this.style.color = '#ffffff';

            const period = this.getAttribute('data-period');
            pullAnalytics(period);
        });
    });

    /**
     * 1. Pull Core KPIs & Shift Status
     */
    async function pullKpis() {
        try {
            const res = await fetch('/dashboard/kpis');
            if (!res.ok) return;
            const d = await res.json();

            // Shift Banner
            const banner = document.getElementById('shiftBannerStatus');
            if (banner) {
                if (d.active_shift) {
                    banner.innerHTML = `
                        <span class="badge bg-success text-white border-0 rounded-pill px-2.5 py-1 fw-bold shadow-xs" style="font-size:0.7rem;">
                            <span class="live-dot me-1"></span> Active Shift #${d.active_shift.shift_code} (${d.active_shift.cashier_name})
                        </span>
                        <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 font-mono fw-bold" style="font-size:0.7rem;">
                            Store Code: {{ session('tenant_code', 'STORE-01') }}
                        </span>
                        <span class="text-muted extra-small fw-semibold ms-1"><i class="bi bi-cloud-check-fill text-success me-1"></i>Live Sync</span>
                    `;
                } else {
                    banner.innerHTML = `
                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2.5 py-1 fw-bold" style="font-size:0.7rem;">
                            <i class="bi bi-pause-circle me-1"></i> No Active Shift
                        </span>
                        <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 font-mono fw-bold" style="font-size:0.7rem;">
                            Store Code: {{ session('tenant_code', 'STORE-01') }}
                        </span>
                        <span class="text-muted extra-small fw-semibold ms-1"><i class="bi bi-cloud-check-fill text-success me-1"></i>Live Sync</span>
                    `;
                }
            }

            // Today's Sales
            const s1 = document.getElementById('kpiTodaySales');
            const s1sub = document.getElementById('kpiTodaySalesSub');
            if (s1) s1.textContent = d.today_sales_formatted;
            if (s1sub) s1sub.innerHTML = `
                <span class="${d.sales_growth >= 0 ? 'kpi-growth-positive' : 'kpi-growth-negative'} text-truncate">
                    <i class="bi ${d.sales_growth >= 0 ? 'bi-arrow-up-right' : 'bi-arrow-down-right'} me-0.5"></i>${d.sales_growth >= 0 ? '+' : ''}${d.sales_growth}%
                </span>
                <span class="text-muted extra-small font-mono fw-bold flex-shrink-0 ms-auto">${d.today_orders_count} orders</span>
            `;

            // Month's Revenue
            const s2 = document.getElementById('kpiMonthSales');
            const s2sub = document.getElementById('kpiMonthSalesSub');
            if (s2) s2.textContent = d.month_sales_formatted;
            if (s2sub) s2sub.innerHTML = `
                <span class="${d.month_growth >= 0 ? 'kpi-growth-positive' : 'kpi-growth-negative'} text-truncate">
                    <i class="bi ${d.month_growth >= 0 ? 'bi-arrow-up-right' : 'bi-arrow-down-right'} me-0.5"></i>${d.month_growth >= 0 ? '+' : ''}${d.month_growth}%
                </span>
                <span class="text-muted extra-small font-mono fw-bold flex-shrink-0 ms-auto">MTD</span>
            `;

            // Today's Profit
            const s3 = document.getElementById('kpiTodayProfit');
            const s3sub = document.getElementById('kpiTodayProfitSub');
            if (s3) s3.textContent = d.today_gross_profit_formatted;
            if (s3sub) s3sub.innerHTML = `
                <span class="text-success extra-small fw-bold font-mono text-truncate">
                    <i class="bi bi-pie-chart-fill me-1"></i>${d.today_profit_margin}% Margin
                </span>
            `;

            // Drawer Balance
            const s4 = document.getElementById('kpiDrawerBalance');
            const s4sub = document.getElementById('kpiDrawerBalanceSub');
            if (s4) s4.textContent = d.current_drawer_balance_formatted;
            if (s4sub) s4sub.innerHTML = `
                <span class="text-muted extra-small font-mono text-truncate">Float: ${d.drawer_starting_cash_formatted}</span>
                <span class="badge bg-warning-subtle text-warning-emphasis extra-small fw-bold flex-shrink-0 ms-auto" style="font-size:0.62rem;padding:2px 6px;">Open</span>
            `;

            // Credit Receivables
            const s5 = document.getElementById('kpiUtangBalance');
            const s5sub = document.getElementById('kpiUtangBalanceSub');
            if (s5) s5.textContent = d.total_utang_receivables_formatted;
            if (s5sub) s5sub.innerHTML = `
                <span class="text-danger extra-small fw-bold text-truncate">
                    <i class="bi bi-people-fill me-1"></i>${d.customers_with_utang_count} Accounts
                </span>
                <a href="{{ route('customers.index') }}" class="extra-small text-danger fw-bold text-decoration-none flex-shrink-0 ms-auto">View &rarr;</a>
            `;

            // Stock Valuation
            const s6 = document.getElementById('kpiStockValuation');
            const s6sub = document.getElementById('kpiStockValuationSub');
            if (s6) s6.textContent = d.total_inventory_cost_formatted;
            if (s6sub) s6sub.innerHTML = `
                <span class="text-warning extra-small fw-bold text-truncate">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>${d.low_stock_total} Low
                </span>
                <span class="text-muted extra-small font-mono flex-shrink-0 ms-auto">${d.total_products_count} SKUs</span>
            `;
        } catch (e) {
            console.error('KPIs fetch error:', e);
        }
    }

    /**
     * 2. Pull Sales Trends Chart & Payment Channels
     */
    async function pullAnalytics(period) {
        const loader = document.getElementById('chartLoader');
        if (loader) loader.style.setProperty('display', 'flex', 'important');

        try {
            const res = await fetch('/dashboard/analytics?period=' + period);
            if (!res.ok) throw new Error('Analytics error: ' + res.status);
            const d = await res.json();

            // Render Payment Channels Donut & Badges
            if (d.payment_percentages && d.payment_stats) {
                renderPaymentDonut(d.payment_percentages, d.payment_stats);
            }

            // Hide overlay loader
            if (loader) loader.style.setProperty('display', 'none', 'important');

            renderChart(d.labels, d.revenues, d.orders);
        } catch (e) {
            console.error('pullAnalytics error:', e);
            if (loader) {
                loader.style.setProperty('display', 'flex', 'important');
                loader.innerHTML = `
                    <div class="text-danger extra-small py-2 text-center px-3">
                        <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Analytics Error:</div>
                        <code class="d-block bg-danger-subtle text-danger p-2 rounded mb-2 text-start font-mono" style="font-size:0.75rem; word-break:break-all;">
                            ${e.message || e}
                        </code>
                        <button type="button" class="btn btn-xs btn-outline-danger px-3 py-1 rounded-pill fw-bold" onclick="pullAnalytics('${period}')">
                            <i class="bi bi-arrow-clockwise me-1"></i> Retry
                        </button>
                    </div>
                `;
            }
        }
    }

    let paymentDonutInstance = null;

    /**
     * Render Payment Channels Apex Donut Chart
     */
    function renderPaymentDonut(p, s) {
        const el = document.querySelector("#paymentDonutChart");
        if (!el || typeof ApexCharts === 'undefined') return;

        if (paymentDonutInstance) {
            try { paymentDonutInstance.destroy(); } catch(e){}
            paymentDonutInstance = null;
        }

        const cash = Number(s.cash) || 0;
        const gcash = Number(s.gcash) || 0;
        const maya = Number(s.maya) || 0;
        const credit = Number(s.credit) || 0;
        const total = cash + gcash + maya + credit;

        // Update badge amounts
        const cEl = document.getElementById('txtCashAmount');
        const gEl = document.getElementById('txtGcashAmount');
        const mEl = document.getElementById('txtMayaAmount');
        const crEl = document.getElementById('txtCreditAmount');
        if (cEl) cEl.textContent = '₱' + (cash >= 1000 ? (cash/1000).toFixed(1) + 'k' : cash.toFixed(0));
        if (gEl) gEl.textContent = '₱' + (gcash >= 1000 ? (gcash/1000).toFixed(1) + 'k' : gcash.toFixed(0));
        if (mEl) mEl.textContent = '₱' + (maya >= 1000 ? (maya/1000).toFixed(1) + 'k' : maya.toFixed(0));
        if (crEl) crEl.textContent = '₱' + (credit >= 1000 ? (credit/1000).toFixed(1) + 'k' : credit.toFixed(0));

        const options = {
            series: [cash, gcash, maya, credit],
            labels: ['Cash', 'GCash', 'Maya', 'Credit / Utang'],
            chart: {
                type: 'donut',
                height: 185,
                fontFamily: 'Plus Jakarta Sans, sans-serif'
            },
            colors: ['#059669', '#2563eb', '#06b6d4', '#e11d48'],
            plotOptions: {
                pie: {
                    donut: {
                        size: '72%',
                        labels: {
                            show: true,
                            name: {
                                show: true,
                                fontSize: '11px',
                                fontWeight: 700,
                                color: '#64748b',
                                offsetY: -3
                            },
                            value: {
                                show: true,
                                fontSize: '13.5px',
                                fontWeight: 800,
                                color: '#0f172a',
                                formatter: function (val) {
                                    return '₱' + Number(val).toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 });
                                },
                                offsetY: 3
                            },
                            total: {
                                show: true,
                                label: 'Total Paid',
                                fontSize: '10.5px',
                                fontWeight: 700,
                                color: '#64748b',
                                formatter: function (w) {
                                    const sum = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                    return '₱' + (sum >= 1000 ? (sum / 1000).toFixed(1) + 'k' : sum.toFixed(0));
                                }
                            }
                        }
                    }
                }
            },
            stroke: {
                width: 2,
                colors: ['#ffffff']
            },
            dataLabels: {
                enabled: false
            },
            legend: {
                show: false
            },
            tooltip: {
                theme: 'dark',
                y: {
                    formatter: function (val) {
                        const pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                        return '₱' + Number(val).toLocaleString(undefined, { minimumFractionDigits: 2 }) + ` (${pct}%)`;
                    }
                }
            }
        };

        try {
            paymentDonutInstance = new ApexCharts(el, options);
            paymentDonutInstance.render();
        } catch(err) {
            console.error('Payment donut render error:', err);
        }
    }

    /**
     * Render Interactive ApexCharts Area & Column Visualization
     */
    function renderChart(labels, revenues, orders) {
        const chartElement = document.querySelector("#salesTrendsChart");
        if (!chartElement) return;

        if (typeof ApexCharts === 'undefined') {
            const loader = document.getElementById('chartLoader');
            if (loader) {
                loader.style.setProperty('display', 'flex', 'important');
                loader.innerHTML = `
                    <div class="text-danger extra-small py-2 text-center px-3">
                        <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Chart Library Error:</div>
                        <p class="mb-0 text-muted">ApexCharts library is not loaded. Please verify apexcharts.min.js.</p>
                    </div>
                `;
            }
            return;
        }

        if (apexChartInstance) {
            try {
                apexChartInstance.destroy();
            } catch (e) {}
            apexChartInstance = null;
        }

        const formattedRevenues = revenues.map(v => Number(v) || 0);
        const formattedOrders = orders.map(v => Number(v) || 0);
        const maxOrders = Math.max(...formattedOrders, 3);

        const options = {
            series: [
                {
                    name: 'Gross Revenue (₱)',
                    type: 'area',
                    data: formattedRevenues
                },
                {
                    name: 'Orders Count',
                    type: 'column',
                    data: formattedOrders
                }
            ],
            chart: {
                height: 270,
                type: 'line',
                toolbar: {
                    show: false
                },
                zoom: {
                    enabled: false
                },
                fontFamily: 'Plus Jakarta Sans, sans-serif',
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 450
                }
            },
            stroke: {
                curve: 'smooth',
                width: [2.8, 0]
            },
            colors: ['#059669', '#93c5fd'],
            fill: {
                type: ['gradient', 'solid'],
                gradient: {
                    shade: 'light',
                    type: 'vertical',
                    shadeIntensity: 0.5,
                    gradientToColors: ['#34d399'],
                    inverseColors: false,
                    opacityFrom: 0.32,
                    opacityTo: 0.02,
                    stops: [0, 90, 100]
                }
            },
            plotOptions: {
                bar: {
                    columnWidth: '32%',
                    borderRadius: 4,
                    borderRadiusApplication: 'end'
                }
            },
            dataLabels: {
                enabled: false
            },
            labels: labels,
            xaxis: {
                type: 'category',
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                },
                labels: {
                    style: {
                        colors: '#64748b',
                        fontSize: '10.5px',
                        fontWeight: 600
                    },
                    rotate: 0,
                    hideOverlappingLabels: true
                }
            },
            yaxis: [
                {
                    title: {
                        text: 'Revenue',
                        style: { color: '#059669', fontSize: '10.5px', fontWeight: 700 }
                    },
                    min: 0,
                    labels: {
                        style: {
                            colors: '#64748b',
                            fontSize: '10.5px',
                            fontWeight: 600
                        },
                        formatter: function (val) {
                            return '₱' + (val >= 1000 ? (val / 1000).toFixed(0) + 'k' : (val ? val.toLocaleString() : '0'));
                        }
                    }
                },
                {
                    opposite: true,
                    title: {
                        text: 'Orders',
                        style: { color: '#3b82f6', fontSize: '10.5px', fontWeight: 700 }
                    },
                    min: 0,
                    max: maxOrders * 2.5,
                    labels: {
                        style: {
                            colors: '#3b82f6',
                            fontSize: '10.5px',
                            fontWeight: 600
                        },
                        formatter: function (val) {
                            return Math.round(val);
                        }
                    }
                }
            ],
            grid: {
                borderColor: '#f1f5f9',
                strokeDashArray: 3,
                xaxis: {
                    lines: { show: false }
                },
                yaxis: {
                    lines: { show: true }
                },
                padding: {
                    top: 0,
                    right: 15,
                    bottom: 0,
                    left: 10
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'center',
                fontSize: '11.5px',
                fontWeight: 700,
                markers: {
                    radius: 12
                },
                itemMargin: {
                    horizontal: 12,
                    vertical: 4
                }
            },
            tooltip: {
                shared: true,
                intersect: false,
                theme: 'dark',
                style: {
                    fontSize: '11.5px'
                },
                y: {
                    formatter: function (y, { seriesIndex }) {
                        if (typeof y !== "undefined") {
                            if (seriesIndex === 0) {
                                return '₱' + Number(y).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                            }
                            return y + ' tx';
                        }
                        return y;
                    }
                }
            }
        };

        try {
            apexChartInstance = new ApexCharts(chartElement, options);
            apexChartInstance.render();
        } catch (err) {
            console.error('ApexCharts render error:', err);
        }
    }

    /**
     * 3. Pull Live Store Sales Feed
     */
    async function pullRecentSales() {
        try {
            const res = await fetch('/dashboard/recent-sales');
            if (!res.ok) return;
            const d = await res.json();

            const tbody = document.getElementById('recentSalesTbody');
            if (!tbody) return;

            if (!d.sales || d.sales.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-muted">No sales recorded yet.</td></tr>`;
                return;
            }

            tbody.innerHTML = d.sales.map(s => {
                const initials = s.customer_name ? s.customer_name.split(' ').map(n=>n[0]).slice(0,2).join('').toUpperCase() : 'GS';
                const methodBadge = s.payment_method === 'cash' 
                    ? '<span class="badge bg-success-subtle text-success border border-success-subtle fw-bold" style="font-size:0.68rem;padding:3px 7px;"><i class="bi bi-cash me-1"></i>Cash</span>'
                    : (s.payment_method === 'credit'
                        ? '<span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-bold" style="font-size:0.68rem;padding:3px 7px;"><i class="bi bi-book me-1"></i>Credit</span>'
                        : (s.payment_method === 'gcash'
                            ? '<span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-bold" style="font-size:0.68rem;padding:3px 7px;"><i class="bi bi-phone me-1"></i>GCash</span>'
                            : '<span class="badge bg-info-subtle text-info border border-info-subtle fw-bold" style="font-size:0.68rem;padding:3px 7px;"><i class="bi bi-wallet2 me-1"></i>Maya</span>'
                        )
                    );

                return `
                    <tr>
                        <td class="py-2.5 px-3">
                            <a href="${s.details_url}" class="badge bg-light text-dark border font-mono fw-bold text-decoration-none hover-primary" style="font-size:0.72rem;">
                                ${s.invoice_no}
                            </a>
                        </td>
                        <td class="py-2.5 px-3">
                            <div class="d-flex align-items-center gap-2">
                                <span class="avatar-initials" style="background:#475569;">${initials}</span>
                                <span class="fw-bold text-dark text-truncate" style="max-width:130px;">${s.customer_name}</span>
                            </div>
                        </td>
                        <td class="py-2.5 px-3 text-center">
                            ${methodBadge}
                        </td>
                        <td class="py-2.5 px-3 text-end font-mono fw-black text-dark" style="font-size:0.85rem;">
                            ${s.total_amount_formatted}
                        </td>
                        <td class="py-2.5 px-3 text-end text-muted font-mono extra-small">
                            ${s.time_formatted}
                        </td>
                    </tr>
                `;
            }).join('');
        } catch (e) {
            console.error('Recent sales error:', e);
        }
    }

    /**
     * 4. Pull Low Stock Alerts
     */
    async function pullInventoryAlerts() {
        try {
            const res = await fetch('/dashboard/inventory-alerts');
            if (!res.ok) return;
            const d = await res.json();

            const tbody = document.getElementById('inventoryAlertsTbody');
            if (!tbody) return;

            if (!d.products || d.products.length === 0) {
                tbody.innerHTML = `<tr><td colspan="4" class="text-center py-4 text-success fw-bold"><i class="bi bi-check-circle-fill me-1"></i> All products have healthy inventory!</td></tr>`;
                return;
            }

            tbody.innerHTML = d.products.map(p => {
                const stockBadge = p.is_out_of_stock 
                    ? '<span class="badge bg-danger text-white fw-bold font-mono" style="font-size:0.68rem;padding:3px 7px;"><i class="bi bi-x-circle-fill me-1"></i>0 Left</span>'
                    : `<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle fw-bold font-mono" style="font-size:0.68rem;padding:3px 7px;"><i class="bi bi-exclamation-circle-fill me-1"></i>${p.stock} ${p.unit}</span>`;

                return `
                    <tr>
                        <td class="py-2.5 px-3">
                            <div class="fw-bold text-dark text-truncate mb-0.5" style="max-width:160px;" title="${p.name}">
                                ${p.name}
                            </div>
                            <span class="badge bg-light text-muted border font-mono" style="font-size:0.62rem;">${p.barcode}</span>
                        </td>
                        <td class="py-2.5 px-3">
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle" style="font-size:0.68rem;">
                                ${p.category}
                            </span>
                        </td>
                        <td class="py-2.5 px-3 text-center">
                            ${stockBadge}
                        </td>
                        <td class="py-2.5 px-3 text-end">
                            <a href="${p.restock_url}" class="btn btn-xs btn-outline-success fw-bold rounded-pill px-2.5 py-1 hover-lift" style="font-size:0.72rem;">
                                <i class="bi bi-plus-lg me-0.5"></i> Restock
                            </a>
                        </td>
                    </tr>
                `;
            }).join('');
        } catch (e) {
            console.error('Inventory alerts error:', e);
        }
    }

    /**
     * 5. Pull Top Customers CRM Leaderboard
     */
    async function pullTopSuki() {
        try {
            const res = await fetch('/dashboard/top-suki');
            if (!res.ok) return;
            const d = await res.json();

            // Update Suki numbers
            const sukiPts = document.getElementById('valSukiPoints');
            const totalCust = document.getElementById('valTotalCustomers');
            if (sukiPts && d.total_suki_points) sukiPts.textContent = d.total_suki_points;
            if (totalCust && d.total_customers) totalCust.textContent = d.total_customers + ' customers';

            const container = document.getElementById('topSukiContainer');
            if (!container) return;

            if (!d.customers || d.customers.length === 0) {
                container.innerHTML = `<div class="col-12 text-center py-4 text-muted">No customer data recorded yet.</div>`;
                return;
            }

            container.innerHTML = d.customers.map((c, index) => {
                const rankCards = [
                    { rank: '👑 #1 VIP', cls: 'rank-1', badge: 'bg-warning text-dark' },
                    { rank: '🥈 #2 Top', cls: 'rank-2', badge: 'bg-secondary text-white' },
                    { rank: '🥉 #3 Top', cls: 'rank-3', badge: 'bg-amber-600 text-white' },
                    { rank: `#${index+1}`, cls: '', badge: 'bg-light text-dark border' },
                    { rank: `#${index+1}`, cls: '', badge: 'bg-light text-dark border' }
                ];
                const r = rankCards[index] || { rank: `#${index+1}`, cls: '', badge: 'bg-light text-muted border' };
                const initials = c.name ? c.name.split(' ').map(n=>n[0]).slice(0,2).join('').toUpperCase() : 'SK';
                const avatarBg = index === 0 ? '#d97706' : (index === 1 ? '#475569' : (index === 2 ? '#b45309' : '#059669'));

                return `
                    <div class="col-12 col-md-6 col-xl">
                        <div class="crm-leaderboard-card ${r.cls} h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge ${r.badge} fw-black" style="font-size:0.65rem;padding:3px 7px;">${r.rank}</span>
                                    <span class="badge ${c.type === 'vip' ? 'bg-warning-subtle text-warning-emphasis' : (c.type === 'senior' ? 'bg-info-subtle text-info-emphasis' : 'bg-primary-subtle text-primary')} extra-small fw-bold text-uppercase" style="font-size:0.65rem;padding:2px 6px;">
                                        ${c.type}
                                    </span>
                                </div>
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="avatar-initials" style="background:${avatarBg};width:32px;height:32px;font-size:0.75rem;">${initials}</span>
                                    <div class="overflow-hidden">
                                        <h6 class="fw-bold text-dark mb-0 text-truncate" style="font-size:0.84rem;" title="${c.name}">${c.name}</h6>
                                        <div class="text-muted extra-small font-mono" style="font-size:0.68rem;">${c.phone}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="p-2 rounded-2 bg-white border d-flex align-items-center justify-content-between gap-1 mt-1 shadow-xs">
                                <div>
                                    <div class="text-muted extra-small text-uppercase fw-bold" style="font-size:0.6rem;">Points</div>
                                    <div class="font-mono fw-black text-success" style="font-size:0.82rem;">${c.points_formatted}</div>
                                </div>
                                <div class="text-end">
                                    <div class="text-muted extra-small text-uppercase fw-bold" style="font-size:0.6rem;">Utang Bal</div>
                                    <div class="font-mono fw-black ${c.balance > 0 ? 'text-danger' : 'text-dark'}" style="font-size:0.82rem;">
                                        ${c.balance_formatted}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        } catch (e) {
            console.error('Top suki error:', e);
        }
    }
</script>
@endpush
@endsection
