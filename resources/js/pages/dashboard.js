import Chart from 'chart.js/auto';

let salesChartInstance = null;

function initDashboard() {
    // 1. Pull analytics (7 days default)
    pullAnalytics('7days');

    // 2. Staggered async pulls for other data
    pullKpis();
    pullRecentSales();
    pullInventoryAlerts();
    pullTopSuki();

    // Chart filter period buttons
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
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initDashboard);
} else {
    initDashboard();
}

/**
 * 1. Pull Core KPIs & Shift Status
 */
async function pullKpis() {
    try {
        const res = await fetch('/dashboard/kpis');
        if (!res.ok) throw new Error('KPIs error');
        const d = await res.json();

        // Shift Status Banner
        const banner = document.getElementById('shiftBannerStatus');
        if (banner) {
            const tenantCode = window.TENANT_CODE || 'STORE-01';
            if (d.active_shift) {
                banner.innerHTML = `
                    <span class="badge bg-success text-white border-0 rounded-pill px-2.5 py-1 fw-bold shadow-xs" style="font-size:0.7rem;">
                        <span class="live-dot me-1"></span> Active Shift #${d.active_shift.shift_code} (${d.active_shift.cashier_name})
                    </span>
                    <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 font-mono fw-bold" style="font-size:0.7rem;">
                        Store Code: ${tenantCode}
                    </span>
                    <span class="text-muted extra-small fw-semibold ms-1"><i class="bi bi-cloud-check-fill text-success me-1"></i>Live Sync</span>
                `;
            } else {
                banner.innerHTML = `
                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2.5 py-1 fw-bold" style="font-size:0.7rem;">
                        <i class="bi bi-pause-circle me-1"></i> No Active Shift
                    </span>
                    <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 font-mono fw-bold" style="font-size:0.7rem;">
                        Store Code: ${tenantCode}
                    </span>
                    <span class="text-muted extra-small fw-semibold ms-1"><i class="bi bi-cloud-check-fill text-success me-1"></i>Live Sync</span>
                `;
            }
        }

        // Today's Sales
        const todaySalesEl = document.getElementById('kpiTodaySales');
        const todaySalesSub = document.getElementById('kpiTodaySalesSub');
        if (todaySalesEl) todaySalesEl.textContent = d.today_sales_formatted;
        if (todaySalesSub) {
            todaySalesSub.innerHTML = `
                <span class="${d.sales_growth >= 0 ? 'kpi-growth-positive' : 'kpi-growth-negative'} text-truncate">
                    <i class="bi ${d.sales_growth >= 0 ? 'bi-arrow-up-right' : 'bi-arrow-down-right'} me-0.5"></i>${d.sales_growth >= 0 ? '+' : ''}${d.sales_growth}% vs Yesterday
                </span>
                <span class="text-muted extra-small font-mono fw-bold flex-shrink-0 ms-auto">${d.today_orders_count} orders</span>
            `;
        }

        // Month's Revenue
        const monthSalesEl = document.getElementById('kpiMonthSales');
        const monthSalesSub = document.getElementById('kpiMonthSalesSub');
        if (monthSalesEl) monthSalesEl.textContent = d.month_sales_formatted;
        if (monthSalesSub) {
            monthSalesSub.innerHTML = `
                <span class="${d.month_growth >= 0 ? 'kpi-growth-positive' : 'kpi-growth-negative'} text-truncate">
                    <i class="bi ${d.month_growth >= 0 ? 'bi-arrow-up-right' : 'bi-arrow-down-right'} me-0.5"></i>${d.month_growth >= 0 ? '+' : ''}${d.month_growth}% vs Last Month
                </span>
                <span class="text-muted extra-small font-mono fw-bold flex-shrink-0 ms-auto">MTD</span>
            `;
        }

        // Today's Profit
        const profitEl = document.getElementById('kpiTodayProfit');
        const profitSub = document.getElementById('kpiTodayProfitSub');
        if (profitEl) profitEl.textContent = d.today_gross_profit_formatted;
        if (profitSub) {
            profitSub.innerHTML = `
                <span class="text-success extra-small fw-bold font-mono text-truncate">
                    <i class="bi bi-pie-chart-fill me-1"></i>${d.today_profit_margin}% Margin
                </span>
            `;
        }

        // Drawer Balance
        const drawerEl = document.getElementById('kpiDrawerBalance');
        const drawerSub = document.getElementById('kpiDrawerBalanceSub');
        if (drawerEl) drawerEl.textContent = d.current_drawer_balance_formatted;
        if (drawerSub) {
            drawerSub.innerHTML = `
                <span class="text-muted extra-small font-mono text-truncate">Float: ${d.drawer_starting_cash_formatted}</span>
                <span class="badge bg-warning-subtle text-warning-emphasis extra-small fw-bold flex-shrink-0 ms-auto" style="font-size:0.62rem;padding:2px 6px;">Open</span>
            `;
        }

        // Credit CRM
        const utangEl = document.getElementById('kpiUtangBalance');
        const utangSub = document.getElementById('kpiUtangBalanceSub');
        if (utangEl) utangEl.textContent = d.total_utang_receivables_formatted;
        if (utangSub) {
            utangSub.innerHTML = `
                <span class="text-danger extra-small fw-bold text-truncate">
                    <i class="bi bi-people-fill me-1"></i>${d.customers_with_utang_count} Accounts
                </span>
                <a href="/customers" class="extra-small text-danger fw-bold text-decoration-none flex-shrink-0 ms-auto">View &rarr;</a>
            `;
        }

        // Stock Valuation
        const stockEl = document.getElementById('kpiStockValuation');
        const stockSub = document.getElementById('kpiStockValuationSub');
        if (stockEl) stockEl.textContent = d.total_inventory_cost_formatted;
        if (stockSub) {
            stockSub.innerHTML = `
                <span class="text-warning extra-small fw-bold text-truncate">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>${d.low_stock_total} Low
                </span>
                <span class="text-muted extra-small font-mono flex-shrink-0 ms-auto">${d.total_products_count} SKUs</span>
            `;
        }
    } catch (e) {
        console.error('Error pulling KPIs:', e);
    }
}

/**
 * 2. Pull Sales Trends Chart & Payment Channels (AJAX for toggle)
 */
async function pullAnalytics(period) {
    const loader = document.getElementById('chartLoader');
    const canvas = document.getElementById('salesTrendsChart');
    if (loader) loader.style.display = 'flex';

    try {
        const res = await fetch(`/dashboard/analytics?period=${period}`);
        if (!res.ok) throw new Error('Analytics fetch error: ' + res.status);
        const d = await res.json();

        // Render Payment Channels
        if (d.payment_percentages && d.payment_stats) {
            renderPaymentChannels(d.payment_percentages, d.payment_stats);
        }

        // Hide overlay loader
        if (loader) loader.style.display = 'none';

        renderChart(canvas, d.labels, d.revenues, d.orders);
    } catch (e) {
        console.error('Error pulling analytics:', e);
        if (loader) {
            loader.style.display = 'flex';
            loader.innerHTML = `
                <div class="text-danger extra-small py-2 text-center px-3">
                    <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Analytics Error:</div>
                    <code class="d-block bg-danger-subtle text-danger p-2 rounded mb-2 text-start font-mono" style="font-size:0.75rem; word-break:break-all;">
                        ${e.message || e}
                    </code>
                    <button type="button" class="btn btn-xs btn-outline-danger px-3 py-1 rounded-pill fw-bold" id="retryAnalyticsBtn">
                        <i class="bi bi-arrow-clockwise me-1"></i> Retry
                    </button>
                </div>
            `;
            const retryBtn = document.getElementById('retryAnalyticsBtn');
            if (retryBtn) retryBtn.addEventListener('click', () => pullAnalytics(period));
        }
    }
}

function renderPaymentChannels(p, s) {
    const container = document.getElementById('paymentChannelsContainer');
    if (!container) return;

    container.innerHTML = `
        <div>
            <div class="d-flex align-items-center justify-content-between extra-small mb-1 gap-2">
                <span class="fw-bold text-dark"><i class="bi bi-cash me-1 text-success"></i>Cash</span>
                <span class="font-mono fw-bold text-dark ms-auto">₱${Number(s.cash).toLocaleString(undefined, { minimumFractionDigits: 2 })} <small class="text-muted">(${p.cash}%)</small></span>
            </div>
            <div class="progress rounded-pill" style="height:6px;">
                <div class="progress-bar bg-success" style="width: ${p.cash}%"></div>
            </div>
        </div>

        <div>
            <div class="d-flex align-items-center justify-content-between extra-small mb-1 gap-2">
                <span class="fw-bold text-dark"><i class="bi bi-phone me-1 text-primary"></i>GCash</span>
                <span class="font-mono fw-bold text-dark ms-auto">₱${Number(s.gcash).toLocaleString(undefined, { minimumFractionDigits: 2 })} <small class="text-muted">(${p.gcash}%)</small></span>
            </div>
            <div class="progress rounded-pill" style="height:6px;">
                <div class="progress-bar bg-primary" style="width: ${p.gcash}%"></div>
            </div>
        </div>

        <div>
            <div class="d-flex align-items-center justify-content-between extra-small mb-1 gap-2">
                <span class="fw-bold text-dark"><i class="bi bi-wallet2 me-1 text-info"></i>Maya</span>
                <span class="font-mono fw-bold text-dark ms-auto">₱${Number(s.maya).toLocaleString(undefined, { minimumFractionDigits: 2 })} <small class="text-muted">(${p.maya}%)</small></span>
            </div>
            <div class="progress rounded-pill" style="height:6px;">
                <div class="progress-bar bg-info" style="width: ${p.maya}%"></div>
            </div>
        </div>

        <div>
            <div class="d-flex align-items-center justify-content-between extra-small mb-1 gap-2">
                <span class="fw-bold text-dark"><i class="bi bi-book me-1 text-danger"></i>Credit / Receivables</span>
                <span class="font-mono fw-bold text-dark ms-auto">₱${Number(s.credit).toLocaleString(undefined, { minimumFractionDigits: 2 })} <small class="text-muted">(${p.credit}%)</small></span>
            </div>
            <div class="progress rounded-pill" style="height:6px;">
                <div class="progress-bar bg-danger" style="width: ${p.credit}%"></div>
            </div>
        </div>
    `;
}

function renderChart(canvas, labels, revenues, orders) {
    if (!canvas) return;

    const existingChart = Chart.getChart(canvas);
    if (existingChart) {
        existingChart.destroy();
    }
    if (salesChartInstance) {
        try { salesChartInstance.destroy(); } catch (e) { }
        salesChartInstance = null;
    }

    const ctx = canvas.getContext('2d');
    const gradient = ctx.createLinearGradient(0, 0, 0, 220);
    gradient.addColorStop(0, 'rgba(5, 150, 105, 0.22)');
    gradient.addColorStop(1, 'rgba(5, 150, 105, 0.0)');

    salesChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Gross Revenue (₱)',
                    data: revenues,
                    borderColor: '#059669',
                    borderWidth: 2.5,
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#059669',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 3,
                    pointHoverRadius: 4.5,
                    yAxisID: 'y'
                },
                {
                    label: 'Orders Count',
                    data: orders,
                    type: 'bar',
                    backgroundColor: 'rgba(59, 130, 246, 0.12)',
                    borderColor: 'rgba(59, 130, 246, 0.35)',
                    borderWidth: 1,
                    borderRadius: 3,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        font: { family: 'Plus Jakarta Sans', weight: '700', size: 10.5 },
                        color: '#0f172a',
                        boxWidth: 10,
                        padding: 8
                    }
                },
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleColor: '#ffffff',
                    bodyColor: '#34d399',
                    padding: 8,
                    cornerRadius: 8,
                    callbacks: {
                        label: function (context) {
                            if (context.dataset.label.includes('Revenue')) {
                                return ' Revenue: ₱' + Number(context.raw).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                            }
                            return ' Orders: ' + context.raw + ' tx';
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: '#64748b', font: { family: 'Plus Jakarta Sans', weight: '600', size: 9.5 } }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    grid: { color: '#f1f5f9' },
                    ticks: {
                        color: '#64748b',
                        font: { family: 'Plus Jakarta Sans', weight: '600', size: 9.5 },
                        callback: function (value) { return '₱' + (value >= 1000 ? (value / 1000).toFixed(0) + 'k' : value); }
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: { drawOnChartArea: false },
                    ticks: {
                        color: '#3b82f6',
                        stepSize: 1,
                        font: { family: 'Plus Jakarta Sans', weight: '600', size: 9.5 }
                    }
                }
            }
        }
    });
}

/**
 * 3. Pull Live Store Sales Feed
 */
async function pullRecentSales() {
    try {
        const res = await fetch('/dashboard/recent-sales');
        if (!res.ok) throw new Error('Recent sales error');
        const d = await res.json();

        const tbody = document.getElementById('recentSalesTbody');
        if (!tbody) return;

        if (!d.sales || d.sales.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center py-3 text-muted">No sales recorded yet.</td></tr>`;
            return;
        }

        tbody.innerHTML = d.sales.map(s => `
            <tr>
                <td class="py-2 px-2.5 fw-bold font-mono text-dark">
                    <a href="${s.details_url}" class="text-decoration-none text-dark hover-primary">
                        ${s.invoice_no}
                    </a>
                </td>
                <td class="py-2 px-2.5">
                    <div class="fw-bold text-dark text-truncate" style="max-width:130px;">
                        ${s.customer_name}
                    </div>
                </td>
                <td class="py-2 px-2.5 text-center">
                    <span class="badge ${s.payment_method === 'cash' ? 'bg-light text-dark border' : (s.payment_method === 'credit' ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-primary-subtle text-primary border border-primary-subtle')} extra-small fw-bold text-uppercase" style="font-size:0.65rem;padding:2px 6px;">
                        ${s.payment_method}
                    </span>
                </td>
                <td class="py-2 px-2.5 text-end font-mono fw-bold text-success">
                    ${s.total_amount_formatted}
                </td>
                <td class="py-2 px-2.5 text-end text-muted font-mono extra-small" style="font-size:0.68rem;">
                    ${s.time_formatted}
                </td>
            </tr>
        `).join('');
    } catch (e) {
        console.error('Error pulling recent sales:', e);
    }
}

/**
 * 4. Pull Low Stock Alerts
 */
async function pullInventoryAlerts() {
    try {
        const res = await fetch('/dashboard/inventory-alerts');
        if (!res.ok) throw new Error('Inventory alerts error');
        const d = await res.json();

        const tbody = document.getElementById('inventoryAlertsTbody');
        if (!tbody) return;

        if (!d.products || d.products.length === 0) {
            tbody.innerHTML = `<tr><td colspan="4" class="text-center py-3 text-success fw-bold"><i class="bi bi-check-circle-fill me-1"></i> All products have healthy inventory!</td></tr>`;
            return;
        }

        tbody.innerHTML = d.products.map(p => `
            <tr>
                <td class="py-2 px-2.5">
                    <div class="fw-bold text-dark text-truncate" style="max-width:160px;" title="${p.name}">
                        ${p.name}
                    </div>
                    <div class="text-muted extra-small font-mono" style="font-size:0.65rem;">${p.barcode}</div>
                </td>
                <td class="py-2 px-2.5">
                    <span class="badge bg-light text-dark border extra-small" style="font-size:0.65rem;">
                        ${p.category}
                    </span>
                </td>
                <td class="py-2 px-2.5 text-center">
                    ${p.is_out_of_stock
                ? '<span class="badge bg-danger text-white fw-bold font-mono" style="font-size:0.65rem;padding:2px 6px;">Out of Stock</span>'
                : `<span class="badge bg-warning text-dark fw-bold font-mono" style="font-size:0.65rem;padding:2px 6px;">${p.stock} ${p.unit}</span>`}
                </td>
                <td class="py-2 px-2.5 text-end">
                    <a href="${p.restock_url}" class="btn btn-xs btn-outline-success fw-bold rounded-pill px-2 py-0.5" style="font-size:0.68rem;">
                        <i class="bi bi-box-arrow-in-down me-0.5"></i> Restock
                    </a>
                </td>
            </tr>
        `).join('');
    } catch (e) {
        console.error('Error pulling inventory alerts:', e);
    }
}

/**
 * 5. Pull Top Customers CRM Leaderboard
 */
async function pullTopSuki() {
    try {
        const res = await fetch('/dashboard/top-suki');
        if (!res.ok) throw new Error('Top suki error');
        const d = await res.json();

        // Update Suki container numbers
        const sukiPts = document.getElementById('valSukiPoints');
        const totalCust = document.getElementById('valTotalCustomers');
        if (sukiPts && d.total_suki_points) sukiPts.textContent = d.total_suki_points;
        if (totalCust && d.total_customers) totalCust.textContent = d.total_customers + ' customers';

        const container = document.getElementById('topSukiContainer');
        if (!container) return;

        if (!d.customers || d.customers.length === 0) {
            container.innerHTML = `<div class="col-12 text-center py-3 text-muted">No customer data recorded yet.</div>`;
            return;
        }

        container.innerHTML = d.customers.map((c, index) => {
            const rankBadges = [
                '<span class="badge bg-warning text-dark fw-bold" style="font-size:0.62rem;"><i class="bi bi-trophy-fill me-0.5"></i>#1 VIP</span>',
                '<span class="badge bg-secondary text-white fw-bold" style="font-size:0.62rem;">#2 Top</span>',
                '<span class="badge bg-light text-dark border fw-bold" style="font-size:0.62rem;">#3 Top</span>',
                '<span class="badge bg-light text-muted border fw-semibold" style="font-size:0.62rem;">#4</span>',
                '<span class="badge bg-light text-muted border fw-semibold" style="font-size:0.62rem;">#5</span>',
            ];
            const rankBadge = rankBadges[index] || `<span class="badge bg-light text-muted border fw-semibold" style="font-size:0.62rem;">#${index + 1}</span>`;

            return `
                <div class="col-12 col-md-6 col-xl">
                    <div class="p-3 rounded-3 bg-light border h-100 d-flex flex-column justify-content-between hover-lift shadow-xs">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-1.5 gap-2">
                                ${rankBadge}
                                <span class="badge ${c.type === 'vip' ? 'bg-warning-subtle text-warning-emphasis' : (c.type === 'senior' ? 'bg-info-subtle text-info-emphasis' : 'bg-primary-subtle text-primary')} extra-small fw-bold text-uppercase ms-auto" style="font-size:0.65rem;padding:2px 6px;">
                                    ${c.type}
                                </span>
                            </div>
                            <h6 class="fw-bold text-dark mb-0.5 text-truncate" style="font-size:0.85rem;" title="${c.name}">${c.name}</h6>
                            <div class="extra-small text-muted font-mono mb-1.5" style="font-size:0.68rem;"><i class="bi bi-telephone me-1"></i>${c.phone}</div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between gap-2 pt-2 mt-1 border-top">
                            <div>
                                <span class="extra-small text-muted" style="font-size:0.65rem;">Loyalty:</span>
                                <div class="font-mono fw-bold text-success" style="font-size:0.8rem;">${c.points_formatted}</div>
                            </div>
                            <div class="text-end ms-auto">
                                <span class="extra-small text-muted" style="font-size:0.65rem;">Credit Bal:</span>
                                <div class="font-mono fw-bold ${c.balance > 0 ? 'text-danger' : 'text-dark'}" style="font-size:0.8rem;">
                                    ${c.balance_formatted}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    } catch (e) {
        console.error('Error pulling top suki:', e);
    }
}
