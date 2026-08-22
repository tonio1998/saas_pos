@extends('layouts.app')

@section('title', 'Profit & Loss (P&L) Executive Financial Report | LikhaPOS')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Executive Header --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
        <div class="d-flex align-items-center gap-2.5">
            <div class="kpi-icon-box emerald" style="width:40px;height:40px;font-size:1.15rem;">
                <i class="bi bi-bank"></i>
            </div>
            <div>
                <h4 class="fw-black text-dark mb-0 font-mono" style="letter-spacing:-0.4px;">Profit & Loss (P&L) Executive Statement</h4>
                <p class="text-muted extra-small mb-0">Financial audit of Gross Revenue, COGS, Operating Expenses, Net Margin %, and Net Profit</p>
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
                <i class="bi bi-filter me-1"></i> Filter
            </button>
            <button type="button" onclick="window.print()" class="btn btn-sm btn-light border fw-bold px-3 py-1.5 text-dark">
                <i class="bi bi-printer me-1"></i> Print / PDF
            </button>
        </form>
    </div>

    {{-- 4 Dashboard-Style KPI Cards --}}
    <div class="row g-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="likha-kpi-card blue h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Gross Sales Revenue</span>
                    <div class="kpi-icon-box blue"><i class="bi bi-cash-coin"></i></div>
                </div>
                <div class="kpi-value font-mono" id="statGrossRevenue">₱{{ number_format($grossRevenue, 2) }}</div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1">
                    <span class="text-primary extra-small fw-bold"><i class="bi bi-arrow-up-right me-1"></i>Total completed sales</span>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="likha-kpi-card amber h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Cost of Goods (COGS)</span>
                    <div class="kpi-icon-box amber"><i class="bi bi-box-seam-fill"></i></div>
                </div>
                <div class="kpi-value font-mono" id="statCogs">₱{{ number_format($cogs, 2) }}</div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1">
                    <span class="text-warning extra-small fw-bold"><i class="bi bi-dash-circle me-1"></i>Cost price of items sold</span>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="likha-kpi-card rose h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Operating Expenses</span>
                    <div class="kpi-icon-box rose"><i class="bi bi-receipt-cutoff"></i></div>
                </div>
                <div class="kpi-value font-mono text-danger" id="statExpenses">₱{{ number_format($operatingExpenses, 2) }}</div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1">
                    <span class="text-danger extra-small fw-bold"><i class="bi bi-arrow-down me-1"></i>Store payouts & overhead</span>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div id="netProfitCard" class="likha-kpi-card {{ $netProfit >= 0 ? 'emerald' : 'rose' }} h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Net Profit & Margin</span>
                    <div id="netProfitIconBox" class="kpi-icon-box {{ $netProfit >= 0 ? 'emerald' : 'rose' }}"><i class="bi bi-trophy-fill"></i></div>
                </div>
                <div class="kpi-value font-mono {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}" id="statNetProfit">₱{{ number_format($netProfit, 2) }}</div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1">
                    <span class="extra-small fw-bold {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}"><i class="bi bi-percent me-1"></i>Margin: <strong id="statMargin">{{ number_format($profitMargin, 1) }}%</strong></span>
                </div>
            </div>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="card border-0 rounded-3 shadow-xs bg-white overflow-hidden">
        <div class="card-header bg-white border-bottom py-2.5 px-3 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-table text-success fw-bold"></i>
                <h6 class="fw-black text-dark mb-0 font-mono extra-small text-uppercase tracking-wider">Sales Invoice Profit Breakdown Matrix</h6>
            </div>
            <span class="badge bg-light text-dark font-mono fw-bold extra-small border">P&L AUDIT</span>
        </div>
        <div class="card-body p-3">
            <div class="table-responsive">
                <table id="profitReportTable" class="table table-hover align-middle w-100 font-sans mb-0">
                    <thead class="bg-light text-dark extra-small text-uppercase font-mono fw-black">
                        <tr>
                            <th class="fw-black">Invoice #</th>
                            <th class="fw-black">Gross Revenue</th>
                            <th class="fw-black">Cost of Goods (COGS)</th>
                            <th class="fw-black">Estimated Profit</th>
                            <th class="fw-black">Profit Margin %</th>
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
    function initProfitReport($) {
        var table = $('#profitReportTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('reports.profit') }}",
                data: function (d) {
                    d.start_date = $('#startDate').val();
                    d.end_date = $('#endDate').val();
                }
            },
            columns: [
                { data: 'invoice_ref', name: 'invoice_no' },
                { data: 'revenue', name: 'total_amount' },
                { data: 'estimated_cogs', searchable: false },
                { data: 'estimated_profit', searchable: false },
                { data: 'profit_margin', searchable: false }
            ],
            order: [[0, 'desc']],
            pageLength: 25,
            responsive: true
        });

        table.on('xhr', function (e, settings, json) {
            if (json && json.stats) {
                $('#statGrossRevenue').text('₱' + json.stats.grossRevenue);
                $('#statCogs').text('₱' + json.stats.cogs);
                $('#statExpenses').text('₱' + json.stats.operatingExpenses);
                $('#statNetProfit').text('₱' + json.stats.netProfit);
                $('#statMargin').text(json.stats.profitMargin + '%');

                if (json.stats.isPositive) {
                    $('#netProfitCard').removeClass('rose').addClass('emerald');
                    $('#netProfitIconBox').removeClass('rose').addClass('emerald');
                    $('#statNetProfit').removeClass('text-danger').addClass('text-success');
                } else {
                    $('#netProfitCard').removeClass('emerald').addClass('rose');
                    $('#netProfitIconBox').removeClass('emerald').addClass('rose');
                    $('#statNetProfit').removeClass('text-success').addClass('text-danger');
                }
            }
        });

        $('#reportFilterForm').on('submit', function (e) {
            e.preventDefault();
            table.ajax.reload();
        });
    }

    function checkJQuery() {
        if (window.$ && window.$.fn && window.$.fn.DataTable) {
            initProfitReport(window.$);
        } else {
            setTimeout(checkJQuery, 50);
        }
    }
    checkJQuery();
})();
</script>
@endpush
