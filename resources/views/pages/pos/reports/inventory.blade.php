@extends('layouts.app')

@section('title', 'Inventory Valuation & Asset Valuation Audit Report | LikhaPOS')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Executive Header --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
        <div class="d-flex align-items-center gap-2.5">
            <div class="kpi-icon-box emerald" style="width:40px;height:40px;font-size:1.15rem;">
                <i class="bi bi-boxes"></i>
            </div>
            <div>
                <h4 class="fw-black text-dark mb-0 font-mono" style="letter-spacing:-0.4px;">Inventory Valuation & Asset Report</h4>
                <p class="text-muted extra-small mb-0">Real-time stock quantities, asset valuation costs, and potential retail worth</p>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <button type="button" onclick="window.print()" class="btn btn-sm btn-light border fw-bold px-3 py-1.5 text-dark">
                <i class="bi bi-printer me-1"></i> Print / PDF
            </button>
        </div>
    </div>

    {{-- 4 Dashboard-Style KPI Cards --}}
    <div class="row g-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="likha-kpi-card blue h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Master Catalog SKUs</span>
                    <div class="kpi-icon-box blue"><i class="bi bi-box-seam"></i></div>
                </div>
                <div class="kpi-value font-mono">{{ number_format($totalProducts) }}</div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1">
                    <span class="text-primary extra-small fw-bold"><i class="bi bi-grid-fill me-1"></i>Active product items</span>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="likha-kpi-card purple h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Physical Units</span>
                    <div class="kpi-icon-box purple"><i class="bi bi-layers-half"></i></div>
                </div>
                <div class="kpi-value font-mono">{{ number_format($totalStockOnHand) }}</div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1">
                    <span class="text-secondary extra-small fw-bold"><i class="bi bi-box me-1"></i>Total in-stock balance</span>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="likha-kpi-card emerald h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Asset Cost Value</span>
                    <div class="kpi-icon-box emerald"><i class="bi bi-safe-fill"></i></div>
                </div>
                <div class="kpi-value font-mono text-success">₱{{ number_format($totalCostValue, 2) }}</div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1">
                    <span class="text-success extra-small fw-bold"><i class="bi bi-coin me-1"></i>Capital investment value</span>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="likha-kpi-card amber h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Retail Sales Worth</span>
                    <div class="kpi-icon-box amber"><i class="bi bi-tag-fill"></i></div>
                </div>
                <div class="kpi-value font-mono">₱{{ number_format($totalRetailValue, 2) }}</div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1">
                    <span class="text-warning extra-small fw-bold"><i class="bi bi-graph-up me-1"></i>Potential retail value</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="card border-0 rounded-3 shadow-xs bg-white overflow-hidden">
        <div class="card-header bg-white border-bottom py-2.5 px-3 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-table text-success fw-bold"></i>
                <h6 class="fw-black text-dark mb-0 font-mono extra-small text-uppercase tracking-wider">Inventory Stock Valuation Matrix</h6>
            </div>
            <span class="badge bg-light text-dark font-mono fw-bold extra-small border">AUTOMATED AUDIT</span>
        </div>
        <div class="card-body p-3">
            <div class="table-responsive">
                <table id="inventoryReportTable" class="table table-hover align-middle w-100 font-sans mb-0">
                    <thead class="bg-light text-dark extra-small text-uppercase font-mono fw-black">
                        <tr>
                            <th class="fw-black">Product & Code</th>
                            <th class="fw-black">Stock Status</th>
                            <th class="fw-black">Total Cost Value</th>
                            <th class="fw-black">Total Retail Value</th>
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
    function initInventoryReport($) {
        $('#inventoryReportTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('reports.inventory') }}",
            columns: [
                { data: 'product_name', name: 'name' },
                { data: 'stock_badge', name: 'stock_on_hand' },
                { data: 'cost_val', name: 'cost_price', searchable: false },
                { data: 'retail_val', name: 'selling_price', searchable: false }
            ],
            order: [[0, 'asc']],
            pageLength: 25,
            responsive: true
        });
    }

    function checkJQuery() {
        if (window.$ && window.$.fn && window.$.fn.DataTable) {
            initInventoryReport(window.$);
        } else {
            setTimeout(checkJQuery, 50);
        }
    }
    checkJQuery();
})();
</script>
@endpush
