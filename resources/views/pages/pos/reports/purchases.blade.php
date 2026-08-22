@extends('layouts.app')

@section('title', 'Purchase Orders & Stock Receive Audit Report | LikhaPOS')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Executive Header --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
        <div class="d-flex align-items-center gap-2.5">
            <div class="kpi-icon-box blue" style="width:40px;height:40px;font-size:1.15rem;">
                <i class="bi bi-truck"></i>
            </div>
            <div>
                <h4 class="fw-black text-dark mb-0 font-mono" style="letter-spacing:-0.4px;">Purchase & Stock Receiving Report</h4>
                <p class="text-muted extra-small mb-0">Track supplier shipments, received inventory batches, and procurement costs</p>
            </div>
        </div>

        {{-- Date Filter Form (AJAX) --}}
        <form id="reportFilterForm" class="d-flex align-items-center gap-2 flex-wrap bg-white p-2 rounded-3 border shadow-xs">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-calendar-range text-primary extra-small fw-bold"></i>
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

    {{-- 3 Dashboard-Style KPI Cards --}}
    <div class="row g-3 mb-3">
        <div class="col-6 col-md-4">
            <div class="likha-kpi-card blue h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Batches Received</span>
                    <div class="kpi-icon-box blue"><i class="bi bi-box-arrow-in-down"></i></div>
                </div>
                <div class="kpi-value font-mono" id="statBatches">{{ number_format($totalBatchesCount) }}</div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1">
                    <span class="text-primary extra-small fw-bold"><i class="bi bi-check-all me-1"></i>Shipments restocked</span>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4">
            <div class="likha-kpi-card emerald h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Total Units Added</span>
                    <div class="kpi-icon-box emerald"><i class="bi bi-plus-circle-fill"></i></div>
                </div>
                <div class="kpi-value font-mono text-success" id="statUnits">+{{ number_format($totalReceivedQty) }}</div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1">
                    <span class="text-success extra-small fw-bold"><i class="bi bi-arrow-up-right me-1"></i>Stock additions</span>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-4">
            <div class="likha-kpi-card purple h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Procurement Cost</span>
                    <div class="kpi-icon-box purple"><i class="bi bi-wallet2"></i></div>
                </div>
                <div class="kpi-value font-mono" id="statCost">₱{{ number_format($totalInvestmentValue, 2) }}</div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1">
                    <span class="text-secondary extra-small fw-bold"><i class="bi bi-coin me-1"></i>Cost of goods acquired</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="card border-0 rounded-3 shadow-xs bg-white overflow-hidden">
        <div class="card-header bg-white border-bottom py-2.5 px-3 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-table text-primary fw-bold"></i>
                <h6 class="fw-black text-dark mb-0 font-mono extra-small text-uppercase tracking-wider">Stock Receiving & Purchase Audit Ledger</h6>
            </div>
            <span class="badge bg-light text-dark font-mono fw-bold extra-small border">SUPPLIER AUDIT</span>
        </div>
        <div class="card-body p-3">
            <div class="table-responsive">
                <table id="purchasesReportTable" class="table table-hover align-middle w-100 font-sans mb-0">
                    <thead class="bg-light text-dark extra-small text-uppercase font-mono fw-black">
                        <tr>
                            <th class="fw-black">Product Name & SKU</th>
                            <th class="fw-black">Quantity Received</th>
                            <th class="fw-black">Investment Value</th>
                            <th class="fw-black">Received By</th>
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
    function initPurchasesReport($) {
        var table = $('#purchasesReportTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('reports.purchases') }}",
                data: function (d) {
                    d.start_date = $('#startDate').val();
                    d.end_date = $('#endDate').val();
                }
            },
            columns: [
                { data: 'product_name', name: 'product.name' },
                { data: 'received_qty', name: 'qty' },
                { data: 'total_cost', searchable: false },
                { data: 'received_by', name: 'creator.name' },
                { data: 'date_formatted', name: 'created_at' }
            ],
            order: [[4, 'desc']],
            pageLength: 25,
            responsive: true
        });

        table.on('xhr', function (e, settings, json) {
            if (json && json.stats) {
                $('#statBatches').text(json.stats.totalBatchesCount);
                $('#statUnits').text('+' + json.stats.totalReceivedQty);
                $('#statCost').text('₱' + json.stats.totalInvestmentValue);
            }
        });

        $('#reportFilterForm').on('submit', function (e) {
            e.preventDefault();
            table.ajax.reload();
        });
    }

    function checkJQuery() {
        if (window.$ && window.$.fn && window.$.fn.DataTable) {
            initPurchasesReport(window.$);
        } else {
            setTimeout(checkJQuery, 50);
        }
    }
    checkJQuery();
})();
</script>
@endpush
