@extends('layouts.app')

@section('title', 'Stock Adjustments & Physical Count Audits | LikhaPOS')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <div class="rounded-3 p-2 bg-warning bg-opacity-10 text-warning-emphasis d-flex align-items-center justify-content-center" style="width:38px;height:38px;">
                    <i class="bi bi-sliders fs-5"></i>
                </div>
                <div>
                    <h4 class="fw-black text-dark mb-0 font-mono" style="letter-spacing:-0.5px;">Stock Adjustments & Physical Count Audits</h4>
                </div>
            </div>
            <p class="text-muted small mb-0 ms-5 ps-1">
                Record stock reconciliations from physical inventory counts, shrinkage, damage, spoilage, or expired stock.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('stocks.index') }}" class="btn btn-white border rounded-3 px-3 py-2 fw-bold text-dark extra-small shadow-xs hover-lift d-flex align-items-center gap-1.5">
                <i class="bi bi-boxes text-success"></i>
                <span>Current Stocks</span>
            </a>

            <button type="button" class="btn btn-warning fw-bold px-3.5 py-2 rounded-3 shadow-xs d-flex align-items-center gap-2 hover-lift text-dark" data-bs-toggle="modal" data-bs-target="#newAdjustmentModal" style="background:linear-gradient(135deg, #f59e0b 0%, #d97706 100%);border:none;color:#fff !important;font-size:0.82rem;">
                <i class="bi bi-plus-circle-fill fs-6"></i>
                <span>Record Stock Adjustment</span>
            </button>
        </div>
    </div>

    @php
        $totalAdjCount = $adjustments->count();
        $posAdjCount = $adjustments->filter(fn($a) => $a->stock_after > $a->stock_before)->count();
        $negAdjCount = $adjustments->filter(fn($a) => $a->stock_after < $a->stock_before)->count();
        $totalShrinkageVal = $adjustments->filter(fn($a) => $a->stock_after < $a->stock_before)->sum(fn($a) => abs($a->stock_after - $a->stock_before) * (float)($a->unit_cost ?: $a->product?->cost_price ?? 0));
    @endphp

    {{-- Top 4 KPI Metrics Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="modern-card h-100 p-3 bg-white border rounded-4 shadow-xs">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label text-muted extra-small fw-extrabold text-uppercase">Total Adjustments</span>
                    <div class="kpi-icon-box rounded-3 p-1.5" style="background:#fef3c7;color:#d97706;"><i class="bi bi-sliders fs-6"></i></div>
                </div>
                <div class="kpi-value font-mono fs-4 fw-black text-dark mb-0">{{ number_format($totalAdjCount) }} <span class="fs-6 text-muted fw-normal">records</span></div>
                <div class="text-muted extra-small mt-1 fw-semibold">Audit Logs</div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="modern-card h-100 p-3 bg-white border rounded-4 shadow-xs">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label text-muted extra-small fw-extrabold text-uppercase">Positive Reconciliations</span>
                    <div class="kpi-icon-box rounded-3 p-1.5" style="background:#ecfdf5;color:#059669;"><i class="bi bi-arrow-up-circle-fill fs-6"></i></div>
                </div>
                <div class="kpi-value font-mono fs-4 fw-black text-success mb-0">+{{ number_format($posAdjCount) }} <span class="fs-6 text-muted fw-normal">found</span></div>
                <div class="text-muted extra-small mt-1 fw-semibold">Count Surplus</div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="modern-card h-100 p-3 bg-white border rounded-4 shadow-xs">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label text-muted extra-small fw-extrabold text-uppercase">Negative Shrinkage</span>
                    <div class="kpi-icon-box rounded-3 p-1.5" style="background:#fff1f2;color:#e11d48;"><i class="bi bi-arrow-down-circle-fill fs-6"></i></div>
                </div>
                <div class="kpi-value font-mono fs-4 fw-black text-danger mb-0">-{{ number_format($negAdjCount) }} <span class="fs-6 text-muted fw-normal">damaged/lost</span></div>
                <div class="text-muted extra-small mt-1 fw-semibold">Count Deficits</div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="modern-card h-100 p-3 bg-white border rounded-4 shadow-xs">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label text-muted extra-small fw-extrabold text-uppercase">Financial Shrinkage</span>
                    <div class="kpi-icon-box rounded-3 p-1.5" style="background:#eff6ff;color:#2563eb;"><i class="bi bi-shield-x fs-6"></i></div>
                </div>
                <div class="kpi-value font-mono fs-4 fw-black text-danger mb-0">₱{{ number_format($totalShrinkageVal, 2) }}</div>
                <div class="text-muted extra-small mt-1 fw-semibold">Net Loss Valuation</div>
            </div>
        </div>
    </div>

    {{-- Main Adjustments Table Card --}}
    <div class="card border rounded-4 shadow-xs bg-white overflow-hidden">
        <div class="card-header bg-white border-bottom p-3.5 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-journal-text text-warning-emphasis fs-5"></i>
                <h5 class="fw-bold text-dark mb-0 fs-6">Physical Stock Adjustments History</h5>
            </div>
            <button type="button" class="btn btn-warning btn-sm font-mono fw-bold px-3 py-1.5 rounded-3 extra-small shadow-xs text-white" style="background:#d97706;border:none;" data-bs-toggle="modal" data-bs-target="#newAdjustmentModal">
                <i class="bi bi-plus-lg me-1"></i> Record Adjustment
            </button>
        </div>

        <div class="card-body p-3">
            <div class="table-responsive rounded-3 border overflow-hidden">
                <table id="adjustmentsTable" class="likha-data-table align-middle w-100">
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>Product Name</th>
                            <th class="text-center">Stock Transition</th>
                            <th class="text-end">Variance Qty</th>
                            <th class="text-end">Unit Cost</th>
                            <th class="text-end">Value Impact</th>
                            <th>Reason</th>
                            <th>Audit Remarks</th>
                            <th>Adjusted By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($adjustments as $adj)
                            @php
                                $before = (float) $adj->stock_before;
                                $after = (float) $adj->stock_after;
                                $diff = $after - $before;
                                $costVal = (float) ($adj->unit_cost ?: $adj->product?->cost_price ?? 0);
                                $valImpact = abs($diff) * $costVal;
                            @endphp
                            <tr>
                                <td class="font-mono text-dark fw-semibold" style="font-size:0.8rem;">
                                    <i class="bi bi-clock me-1 text-muted"></i>{{ $adj->created_at?->format('M d, Y h:i A') }}
                                </td>
                                <td>
                                    @if($adj->product)
                                        <a href="{{ route('products.stock.history', encrypt($adj->product->id)) }}" class="fw-bold text-dark text-decoration-none hover-primary d-block" style="font-size:0.88rem;">
                                            {{ $adj->product->name }}
                                        </a>
                                        <div class="extra-small text-muted font-mono">SKU: {{ $adj->product->sku ?: 'N/A' }}</div>
                                    @else
                                        <span class="text-muted italic">Deleted Product</span>
                                    @endif
                                </td>
                                <td class="text-center font-mono extra-small">
                                    <span class="text-muted">{{ number_format($before) }}</span>
                                    <i class="bi bi-arrow-right mx-1 text-muted"></i>
                                    <strong class="text-dark fw-black">{{ number_format($after) }}</strong>
                                </td>
                                <td class="text-end font-mono fw-bold {{ $diff < 0 ? 'text-danger' : 'text-success' }}">
                                    {{ $diff > 0 ? '+' : '' }}{{ number_format($diff) }}
                                </td>
                                <td class="text-end font-mono fw-semibold text-dark">
                                    ₱{{ number_format($costVal, 2) }}
                                </td>
                                <td class="text-end font-mono fw-bold {{ $diff < 0 ? 'text-danger' : 'text-success' }}">
                                    ₱{{ number_format($valImpact, 2) }}
                                </td>
                                <td>
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1 fw-bold extra-small">
                                        {{ Str::limit($adj->remarks ? explode(']', $adj->remarks)[0] . ']' : 'Physical Count', 30) }}
                                    </span>
                                </td>
                                <td class="small text-muted">
                                    {{ $adj->remarks ?: '-' }}
                                </td>
                                <td class="small fw-semibold text-dark">
                                    <div class="d-flex align-items-center gap-1.5">
                                        <div class="rounded-circle bg-secondary bg-opacity-20 text-secondary d-flex align-items-center justify-content-center fw-bold extra-small" style="width:22px;height:22px;font-size:0.65rem;">
                                            {{ strtoupper(substr($adj->creator?->name ?? 'S', 0, 1)) }}
                                        </div>
                                        <span>{{ $adj->creator?->name ?: 'System' }}</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    No stock adjustment records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Record New Stock Adjustment Modal --}}
<div class="modal fade" id="newAdjustmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header text-white p-3.5" style="background:linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-sliders fs-5"></i>
                    <h5 class="modal-title fw-bold fs-6 mb-0">Record Physical Stock Adjustment</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('stocks.adjustments.store') }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="product_id" class="form-label fw-bold small text-dark">Search & Select Target Product <span class="text-danger">*</span></label>
                        <select name="product_id" id="product_id" class="form-select w-100" required>
                            <option value="">Type Product Name, Barcode, or SKU...</option>
                        </select>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold small text-dark">Current System Stock</label>
                            <input type="text" id="displayCurrentStock" class="form-control bg-light font-mono fw-bold" value="0" readonly>
                        </div>
                        <div class="col-6">
                            <label for="actual_stock" class="form-label fw-bold small text-dark">Actual Physical Count <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" name="actual_stock" id="actual_stock" class="form-control font-mono fw-bold fs-5 text-dark" placeholder="0.00" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="reason" class="form-label fw-bold small text-dark">Adjustment Reason <span class="text-danger">*</span></label>
                        <select name="reason" id="reason" class="form-select fw-semibold" required>
                            <option value="Physical Count Discrepancy">Physical Count Discrepancy (Audit)</option>
                            <option value="Damaged Goods">Damaged / Broken Items</option>
                            <option value="Expired Product">Expired / Spoiled Inventory</option>
                            <option value="Shrinkage / Theft">Inventory Shrinkage / Missing</option>
                            <option value="Sample / Promo Use">Sample / Promotional Distribution</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="remarks" class="form-label fw-bold small text-dark">Audit Remarks / Notes</label>
                        <textarea name="remarks" id="remarks" rows="2" class="form-control" placeholder="Optional delivery slip, damaged box reference..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-light border fw-bold px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning fw-bold px-4 text-white" style="background:#d97706;border:none;">Save Adjustment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    function initAdjustments($) {
        const $modal = $('#newAdjustmentModal');
        const $select = $('#product_id');
        const displayStock = document.getElementById('displayCurrentStock');

        // Modal shown event handler for modal-embedded Select2
        $modal.on('shown.bs.modal', function () {
            if ($select.length && !$select.hasClass("select2-hidden-accessible")) {
                $select.select2({
                    theme: 'bootstrap-5',
                    dropdownParent: $modal,
                    placeholder: 'Type Product Name, Barcode, or SKU...',
                    allowClear: true,
                    width: '100%',
                    ajax: {
                        url: "{{ route('select2.products') }}",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params.term
                            };
                        },
                        processResults: function (data) {
                            return {
                                results: data.results
                            };
                        },
                        cache: true
                    }
                }).on('select2:select', function (e) {
                    const data = e.params.data;
                    if (data && data.stock !== undefined) {
                        displayStock.value = parseFloat(data.stock).toLocaleString();
                    }
                }).on('select2:clear', function() {
                    displayStock.value = '0';
                });
            }
        });

        if ($('#adjustmentsTable').length) {
            $('#adjustmentsTable').DataTable({
                responsive: true,
                order: [[0, 'desc']],
                pageLength: 25
            });
        }
    }

    function checkJQuery() {
        if (window.$ && window.$.fn && window.$.fn.select2) {
            initAdjustments(window.$);
        } else {
            setTimeout(checkJQuery, 50);
        }
    }

    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        checkJQuery();
    } else {
        document.addEventListener('DOMContentLoaded', checkJQuery);
    }
})();
</script>
@endpush
