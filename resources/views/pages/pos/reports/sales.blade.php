@extends('layouts.app')

@section('title', 'Sales Performance & Revenue Audit Report | LikhaPOS')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Executive Header (Matching Dashboard Header Style) --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
        <div class="d-flex align-items-center gap-2.5">
            <div class="kpi-icon-box emerald" style="width:40px;height:40px;font-size:1.15rem;">
                <i class="bi bi-graph-up-arrow"></i>
            </div>
            <div>
                <h4 class="fw-black text-dark mb-0 font-mono" style="letter-spacing:-0.4px;">Sales Performance Analytics</h4>
                <p class="text-muted extra-small mb-0">Real-time gross sales revenue, transaction velocity, and payment method audit</p>
            </div>
        </div>

        {{-- Date Filter Form (AJAX) --}}
        <form id="reportFilterForm" class="d-flex align-items-center gap-2 flex-wrap bg-white p-2 rounded-3 border shadow-xs">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-calendar-range text-success extra-small fw-bold"></i>
                <input type="date" id="startDate" name="start_date" class="form-control form-control-sm font-mono fw-bold py-1 text-dark" value="{{ $startDate }}" style="font-size:0.82rem;">
                <span class="text-muted extra-small fw-bold">to</span>
                <input type="date" id="endDate" name="end_date" class="form-control form-control-sm font-mono fw-bold py-1 text-dark" value="{{ $endDate }}" style="font-size:0.82rem;">
            </div>
            <button type="submit" class="btn btn-sm btn-success font-mono fw-bold px-3 py-1.5 shadow-xs" style="background:linear-gradient(135deg, #059669 0%, #047857 100%);border:none;">
                <i class="bi bi-filter me-1"></i> Apply Filter
            </button>
            <button type="button" onclick="window.print()" class="btn btn-sm btn-light border fw-bold px-3 py-1.5 text-dark">
                <i class="bi bi-printer me-1"></i> Print / PDF
            </button>
        </form>
    </div>

    {{-- 4 Dashboard-Style KPI Cards --}}
    <div class="row g-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="likha-kpi-card emerald h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Gross Revenue</span>
                    <div class="kpi-icon-box emerald"><i class="bi bi-currency-dollar"></i></div>
                </div>
                <div class="kpi-value font-mono text-success" id="statGrossRevenue">₱{{ number_format($totalRevenue, 2) }}</div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1">
                    <span class="text-success extra-small fw-bold"><i class="bi bi-arrow-up-right me-1"></i>Total completed sales</span>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="likha-kpi-card blue h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Completed Orders</span>
                    <div class="kpi-icon-box blue"><i class="bi bi-receipt-cutoff"></i></div>
                </div>
                <div class="kpi-value font-mono" id="statTotalOrders">{{ number_format($totalSalesCount) }}</div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1">
                    <span class="text-primary extra-small fw-bold"><i class="bi bi-check-circle-fill me-1"></i>Invoices generated</span>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="likha-kpi-card purple h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Average Order Value</span>
                    <div class="kpi-icon-box purple"><i class="bi bi-calculator"></i></div>
                </div>
                <div class="kpi-value font-mono" id="statAvgOrderValue">₱{{ number_format($avgOrderValue, 2) }}</div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1">
                    <span class="text-secondary extra-small fw-bold"><i class="bi bi-pie-chart-fill me-1"></i>Revenue per basket</span>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="likha-kpi-card amber h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Discounts Granted</span>
                    <div class="kpi-icon-box amber"><i class="bi bi-percent"></i></div>
                </div>
                <div class="kpi-value font-mono text-amber" id="statTotalDiscounts">₱{{ number_format($totalDiscounts, 2) }}</div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1">
                    <span class="text-warning extra-small fw-bold"><i class="bi bi-tag-fill me-1"></i>Promos & Senior/PWD</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Sales Ledger Table Card --}}
    <div class="card border-0 rounded-3 shadow-xs bg-white overflow-hidden">
        <div class="card-header bg-white border-bottom py-2.5 px-3 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-table text-success fw-bold"></i>
                <h6 class="fw-black text-dark mb-0 font-mono extra-small text-uppercase tracking-wider">Completed Sales Audit Ledger</h6>
            </div>
            <span class="badge bg-light text-dark font-mono fw-bold extra-small border">REAL-TIME SYNC</span>
        </div>
        <div class="card-body p-3">
            <div class="table-responsive">
                <table id="salesReportTable" class="table table-hover align-middle w-100 font-sans mb-0">
                    <thead class="bg-light text-dark extra-small text-uppercase font-mono fw-black">
                        <tr>
                            <th class="fw-black">Invoice / Sale #</th>
                            <th class="fw-black">Customer Name</th>
                            <th class="fw-black">Payment Method</th>
                            <th class="fw-black">Total Amount</th>
                            <th class="fw-black">Date & Time</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
(function () {
    function initSalesReport($) {
        var table = $('#salesReportTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('reports.sales') }}",
                data: function (d) {
                    d.start_date = $('#startDate').val();
                    d.end_date = $('#endDate').val();
                }
            },
            columns: [
                { data: 'invoice_ref', name: 'invoice_no' },
                { data: 'customer_name', name: 'customer.name' },
                { data: 'payment_badge', name: 'payment_method' },
                { data: 'net_amount', name: 'total_amount' },
                { data: 'date_formatted', name: 'sale_date' }
            ],
            order: [[4, 'desc']],
            pageLength: 25,
            responsive: true
        });

        table.on('xhr', function (e, settings, json) {
            if (json && json.stats) {
                $('#statGrossRevenue').text('₱' + json.stats.totalRevenue);
                $('#statTotalOrders').text(json.stats.totalSalesCount);
                $('#statAvgOrderValue').text('₱' + json.stats.avgOrderValue);
                $('#statTotalDiscounts').text('₱' + json.stats.totalDiscounts);
            }
        });

        $('#reportFilterForm').on('submit', function (e) {
            e.preventDefault();
            table.ajax.reload();
        });
    }

    function checkJQuery() {
        if (window.$ && window.$.fn && window.$.fn.DataTable) {
            initSalesReport(window.$);
        } else {
            setTimeout(checkJQuery, 50);
        }
    }
    checkJQuery();
})();
</script>
@endpush
