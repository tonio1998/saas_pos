@extends('layouts.app')

@section('title', 'Receive Stock | ' . $product->name)

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('products.index') }}" class="btn btn-white border rounded-3 p-2 text-dark shadow-xs hover-lift d-inline-flex align-items-center justify-content-center" style="width:38px;height:38px;">
                    <i class="bi bi-arrow-left fs-6"></i>
                </a>
                <div>
                    <h4 class="fw-black text-dark mb-0 font-mono" style="letter-spacing:-0.5px;">Receive Inventory / Stock In</h4>
                </div>
            </div>
            <p class="text-muted small mb-0 ms-5 ps-1">
                Record supplier inventory delivery for <strong>{{ $product->name }}</strong>, calculate weighted average unit cost, and update stock levels.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('products.stock.history', encrypt($product->id)) }}" class="btn btn-white border rounded-3 px-3 py-2 fw-bold text-dark extra-small shadow-xs hover-lift d-flex align-items-center gap-1.5">
                <i class="bi bi-clock-history text-primary"></i>
                <span>Stock History</span>
            </a>

            <a href="{{ route('products.edit', encrypt($product->id)) }}" class="btn btn-white border rounded-3 px-3 py-2 fw-bold text-dark extra-small shadow-xs hover-lift d-flex align-items-center gap-1.5">
                <i class="bi bi-pencil-square text-secondary"></i>
                <span>Edit Product Master</span>
            </a>

            <a href="{{ route('products.index') }}" class="btn btn-white border rounded-3 px-3 py-2 fw-bold text-dark extra-small shadow-xs hover-lift">
                <i class="bi bi-x-circle me-1"></i> Cancel & Exit
            </a>
        </div>
    </div>

    {{-- Top 4 KPI Metrics Summary Cards --}}
    @php
        $totalStockDisplay = ($product->variants && $product->variants->count() > 0)
            ? $product->variants->sum('stock_on_hand')
            : $product->stock_on_hand;
    @endphp
    <div class="row g-3 mb-4">
        {{-- 1. Current Stock --}}
        <div class="col-6 col-md-3">
            <div class="modern-card h-100 p-3 bg-white border rounded-4 shadow-xs">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label text-muted extra-small fw-extrabold text-uppercase">Current On-Hand</span>
                    <div class="kpi-icon-box rounded-3 p-1.5" style="background:#ecfdf5;color:#059669;"><i class="bi bi-box-seam fs-6"></i></div>
                </div>
                <div class="kpi-value font-mono fs-4 fw-black text-dark mb-0">{{ number_format($totalStockDisplay) }} <span class="fs-6 text-muted fw-normal">units</span></div>
                <div class="mt-1">
                    @if($totalStockDisplay <= 0)
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-0.5 extra-small fw-bold">Out of Stock</span>
                    @elseif($totalStockDisplay <= ($product->reorder_level ?? 10))
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-0.5 extra-small fw-bold">Low Stock Warning</span>
                    @else
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-0.5 extra-small fw-bold">In Stock</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- 2. Cost Price --}}
        <div class="col-6 col-md-3">
            <div class="modern-card h-100 p-3 bg-white border rounded-4 shadow-xs">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label text-muted extra-small fw-extrabold text-uppercase">Current Cost Price</span>
                    <div class="kpi-icon-box rounded-3 p-1.5" style="background:#eff6ff;color:#2563eb;"><i class="bi bi-tag-fill fs-6"></i></div>
                </div>
                <div class="kpi-value font-mono fs-4 fw-black text-dark mb-0">₱{{ number_format($product->cost_price, 2) }}</div>
                <div class="text-muted extra-small mt-1 fw-semibold">Unit Purchase Cost</div>
            </div>
        </div>

        {{-- 3. Selling Price & Margin --}}
        <div class="col-6 col-md-3">
            <div class="modern-card h-100 p-3 bg-white border rounded-4 shadow-xs">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label text-muted extra-small fw-extrabold text-uppercase">Selling Price</span>
                    <div class="kpi-icon-box rounded-3 p-1.5" style="background:#fef3c7;color:#d97706;"><i class="bi bi-cash-stack fs-6"></i></div>
                </div>
                <div class="kpi-value font-mono fs-4 fw-black text-dark mb-0">₱{{ number_format($product->selling_price, 2) }}</div>
                @php
                    $margin = $product->selling_price > 0 ? round((($product->selling_price - $product->cost_price) / $product->selling_price) * 100, 1) : 0;
                @endphp
                <div class="text-success extra-small mt-1 fw-bold">
                    <i class="bi bi-graph-up me-0.5"></i> {{ $margin }}% Profit Margin
                </div>
            </div>
        </div>

        {{-- 4. Stock Valuation --}}
        <div class="col-6 col-md-3">
            <div class="modern-card h-100 p-3 bg-white border rounded-4 shadow-xs">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label text-muted extra-small fw-extrabold text-uppercase">Current Valuation</span>
                    <div class="kpi-icon-box rounded-3 p-1.5" style="background:#f5f3ff;color:#7c3aed;"><i class="bi bi-wallet2 fs-6"></i></div>
                </div>
                <div class="kpi-value font-mono fs-4 fw-black text-purple mb-0" style="color:#7c3aed;">₱{{ number_format($totalStockDisplay * $product->cost_price, 2) }}</div>
                <div class="text-muted extra-small mt-1 fw-semibold">On-Hand Total Value</div>
            </div>
        </div>
    </div>

    {{-- Main Form & Sidebar Container --}}
    <div class="row g-4">
        {{-- Left Form Panel (8 cols) --}}
        <div class="col-lg-8">
            <div class="card border rounded-4 shadow-xs bg-white overflow-hidden">
                <div class="card-header bg-white border-bottom p-3.5 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2.5">
                        <div class="rounded-3 p-2 d-flex align-items-center justify-content-center text-white" style="background:linear-gradient(135deg, #059669 0%, #047857 100%);width:36px;height:36px;">
                            <i class="bi bi-box-arrow-in-down fs-5"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-0 fs-6">Stock Receiving Form</h5>
                            <small class="text-muted">Enter batch quantity and unit purchase cost</small>
                        </div>
                    </div>
                    <span class="badge bg-light text-dark border font-mono fw-bold px-2.5 py-1">Item ID: #{{ $product->id }}</span>
                </div>

                <div class="card-body p-4">
                    <form method="POST" action="{{ route('products.stock.receive.store', encrypt($product->id)) }}" id="receiveStockForm">
                        @csrf

                        {{-- Hidden Reference Specs for Calculation --}}
                        <input type="hidden" id="current_stock_val" value="{{ (float) $totalStockDisplay }}">
                        <input type="hidden" id="current_cost_val" value="{{ (float) $product->cost_price }}">
                        <input type="hidden" id="selling_price_val" value="{{ (float) $product->selling_price }}">

                        {{-- Product Overview Bar --}}
                        <div class="bg-light p-3 rounded-3 border mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div>
                                <div class="extra-small text-muted text-uppercase fw-extrabold font-mono">Target Product</div>
                                <div class="fw-bold text-dark fs-6">{{ $product->name }}</div>
                                <div class="extra-small text-muted font-mono mt-0.5">
                                    SKU: <span class="fw-bold text-dark me-3">{{ $product->sku ?: 'N/A' }}</span>
                                    Barcode: <span class="fw-bold text-dark">{{ $product->barcode ?: 'N/A' }}</span>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="extra-small text-muted text-uppercase fw-extrabold font-mono">Category</div>
                                <span class="badge bg-white border text-dark fw-bold px-3 py-1 mt-1" style="font-size:0.75rem;">
                                    {{ $product->category->name ?? 'Uncategorized' }}
                                </span>
                            </div>
                        </div>

                        {{-- Variant Selection Card (if product has variants) --}}
                        @if($product->variants && $product->variants->count() > 0)
                        <div class="card border-primary border-opacity-25 bg-primary bg-opacity-10 p-3.5 mb-4 rounded-3 shadow-xs">
                            <label for="variant_id" class="form-label fw-bold text-dark mb-1.5 d-flex align-items-center gap-1.5">
                                <i class="bi bi-layers-fill text-primary fs-5"></i>
                                <span>Select Variant to Receive Stock For</span>
                                <span class="text-danger">*</span>
                            </label>
                            <select name="variant_id" id="variant_id" class="form-select font-mono fw-bold fs-6 border-primary border-opacity-50 py-2" style="background-color:#ffffff;" required>
                                <option value="" disabled selected>-- Choose Product Variant --</option>
                                @foreach($product->variants as $v)
                                    <option value="{{ $v->id }}" 
                                        data-stock="{{ (float) $v->stock_on_hand }}" 
                                        data-cost="{{ (float) ($v->cost_price ?? $product->cost_price) }}" 
                                        data-price="{{ (float) ($v->selling_price ?? $product->selling_price) }}"
                                        data-name="{{ $v->variant_name }}"
                                    >
                                        {{ $v->variant_name }} (SKU: {{ $v->sku ?: 'N/A' }} | Current Stock: {{ number_format($v->stock_on_hand) }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text extra-small text-primary mt-1.5">
                                <i class="bi bi-info-circle me-1"></i> Selecting a variant will automatically load that variant's stock balance and purchase unit cost.
                            </div>
                        </div>
                        @endif

                        {{-- Quantity & Unit Cost Grid --}}
                        <div class="row g-3 mb-4">
                            {{-- Quantity Received --}}
                            <div class="col-md-6">
                                <label for="quantity" class="form-label fw-bold text-dark small mb-1">
                                    Quantity Received <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-box-seam"></i></span>
                                    <input type="number" step="0.01" min="0.01" name="quantity" id="quantity" class="form-control border-start-0 font-mono fw-bold fs-5 text-dark" placeholder="e.g. 50" required autofocus>
                                </div>
                                <div class="d-flex gap-1.5 mt-2">
                                    <button type="button" class="btn btn-xs btn-outline-secondary rounded-2 py-0.5 px-2 font-mono extra-small btn-quick-qty" data-qty="5">+5</button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary rounded-2 py-0.5 px-2 font-mono extra-small btn-quick-qty" data-qty="10">+10</button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary rounded-2 py-0.5 px-2 font-mono extra-small btn-quick-qty" data-qty="50">+50</button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary rounded-2 py-0.5 px-2 font-mono extra-small btn-quick-qty" data-qty="100">+100</button>
                                </div>
                            </div>

                            {{-- Purchase Unit Cost --}}
                            <div class="col-md-6">
                                <label for="unit_cost" class="form-label fw-bold text-dark small mb-1">
                                    Purchase Unit Cost (₱)
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted fw-bold">₱</span>
                                    <input type="number" step="0.01" min="0" name="unit_cost" id="unit_cost" class="form-control border-start-0 font-mono fw-bold fs-5 text-dark" value="{{ number_format($product->cost_price, 2, '.', '') }}" placeholder="0.00">
                                </div>
                                <div class="form-text extra-small text-muted mt-1" id="cost_help_text">
                                    Defaulted to current cost price (₱{{ number_format($product->cost_price, 2) }}).
                                </div>
                            </div>
                        </div>

                        {{-- Live Weighted Average Cost Card --}}
                        <div class="p-3.5 rounded-3 mb-4" style="background:#0f172a; color:#ffffff;">
                            <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2" style="border-color:#1e293b !important;">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-calculator-fill text-success fs-5"></i>
                                    <span class="fw-bold text-white small text-uppercase font-mono" style="letter-spacing:0.5px;">Live Weighted Average Cost Preview</span>
                                </div>
                                <span class="badge bg-success bg-opacity-20 text-success border border-success border-opacity-20 font-mono">Auto-Calculated</span>
                            </div>

                            <div class="row g-3 text-center">
                                <div class="col-4 border-end" style="border-color:#1e293b !important;">
                                    <div class="text-secondary extra-small fw-semibold text-uppercase font-mono" style="color:#94a3b8 !important;">New Total Stock</div>
                                    <div id="previewNewStock" class="font-mono fw-black fs-4 text-white mt-1">{{ number_format($totalStockDisplay) }} units</div>
                                </div>

                                <div class="col-4 border-end" style="border-color:#1e293b !important;">
                                    <div class="text-secondary extra-small fw-semibold text-uppercase font-mono" style="color:#94a3b8 !important;">New Unit Cost</div>
                                    <div id="previewNewCost" class="font-mono fw-black fs-4 text-warning mt-1">₱{{ number_format($product->cost_price, 2) }}</div>
                                </div>

                                <div class="col-4">
                                    <div class="text-secondary extra-small fw-semibold text-uppercase font-mono" style="color:#94a3b8 !important;">New Profit Margin</div>
                                    <div id="previewNewMargin" class="font-mono fw-black fs-4 text-success mt-1">{{ $margin }}%</div>
                                </div>
                            </div>
                        </div>

                        {{-- Checkbox: Update Cost Price --}}
                        <div class="form-check p-3 rounded-3 border bg-light mb-4 d-flex align-items-center gap-2">
                            <input class="form-check-input ms-0 mt-0 fs-5" type="checkbox" name="update_cost_price" id="update_cost_price" value="1" checked style="cursor:pointer;">
                            <label class="form-check-label text-dark fw-bold small ms-1" for="update_cost_price" style="cursor:pointer;">
                                Update Cost Price in Masterlist using Weighted Average Cost
                            </label>
                        </div>

                        {{-- Reference No & Remarks --}}
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="reference_no" class="form-label fw-bold text-dark small mb-1">
                                    Reference No. / PO Number
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-hash"></i></span>
                                    <input type="text" name="reference_no" id="reference_no" class="form-control border-start-0 font-mono" placeholder="e.g. PO-2026-0801">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="remarks" class="form-label fw-bold text-dark small mb-1">
                                    Delivery Remarks / Supplier Notes
                                </label>
                                <input type="text" name="remarks" id="remarks" class="form-control" placeholder="e.g. Received 5 boxes from San Miguel Corp.">
                            </div>
                        </div>

                        {{-- Form Action Buttons --}}
                        <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                            <a href="{{ route('products.index') }}" class="btn btn-light border fw-bold px-4 py-2 text-dark rounded-3 hover-lift">
                                Cancel
                            </a>

                            <button type="submit" class="btn btn-success fw-bold px-4 py-2.5 rounded-3 shadow-sm d-flex align-items-center gap-2 hover-lift" style="background:linear-gradient(135deg, #059669 0%, #047857 100%);border:none;">
                                <i class="bi bi-box-arrow-in-down fs-5"></i>
                                <span>Receive Stock & Post Ledger</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Right Side Details Panel (4 cols) --}}
        <div class="col-lg-4">
            <div class="card border rounded-4 shadow-xs bg-white mb-4">
                <div class="card-header bg-white border-bottom p-3">
                    <h6 class="fw-bold text-dark mb-0 font-mono">Product Master Profile</h6>
                </div>
                <div class="card-body p-3">
                    <table class="table table-sm table-borderless mb-0 align-middle">
                        <tbody>
                            <tr class="border-bottom">
                                <th class="text-muted small fw-semibold py-2">Item Name</th>
                                <td class="text-end fw-bold text-dark py-2">{{ $product->name }}</td>
                            </tr>
                            <tr class="border-bottom">
                                <th class="text-muted small fw-semibold py-2">Barcode</th>
                                <td class="text-end font-mono fw-bold text-dark py-2">{{ $product->barcode ?: '-' }}</td>
                            </tr>
                            <tr class="border-bottom">
                                <th class="text-muted small fw-semibold py-2">SKU</th>
                                <td class="text-end font-mono fw-bold text-dark py-2">{{ $product->sku ?: '-' }}</td>
                            </tr>
                            <tr class="border-bottom">
                                <th class="text-muted small fw-semibold py-2">Current Unit Cost</th>
                                <td class="text-end font-mono fw-bold text-dark py-2">₱{{ number_format($product->cost_price, 2) }}</td>
                            </tr>
                            <tr class="border-bottom">
                                <th class="text-muted small fw-semibold py-2">Selling Price</th>
                                <td class="text-end font-mono fw-bold text-success py-2">₱{{ number_format($product->selling_price, 2) }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted small fw-semibold py-2">Reorder Point</th>
                                <td class="text-end font-mono fw-bold text-warning py-2">{{ $product->reorder_level ?? 10 }} units</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top:90px;">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="fw-bold text-dark mb-0 font-mono" style="font-size:1.05rem;">
                        <i class="bi bi-pie-chart-fill text-success me-2"></i>Batch Valuation Summary
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center pb-2.5 border-bottom">
                        <span class="text-muted extra-small font-mono text-uppercase">Est. Total Investment</span>
                        <span id="batchTotalCost" class="font-mono fw-bold text-dark fs-6">₱0.00</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center py-2.5 border-bottom">
                        <span class="text-muted extra-small font-mono text-uppercase">Est. Gross Revenue</span>
                        <span id="batchExpectedRevenue" class="font-mono fw-bold text-dark fs-6">₱0.00</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center p-2.5 rounded-3 bg-success bg-opacity-10 border border-success border-opacity-20 mt-3">
                        <span class="text-success fw-bold extra-small text-uppercase">Est. Batch Gross Profit</span>
                        <span id="batchEstimatedProfit" class="font-mono fw-black text-success fs-5">₱0.00</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const qtyInput = document.getElementById('quantity');
    const unitCostInput = document.getElementById('unit_cost');
    const variantSelect = document.getElementById('variant_id');
    const currentStockInput = document.getElementById('current_stock_val');
    const currentCostInput = document.getElementById('current_cost_val');
    const sellingPriceInput = document.getElementById('selling_price_val');

    const previewNewStock = document.getElementById('previewNewStock');
    const previewNewCost = document.getElementById('previewNewCost');
    const previewNewMargin = document.getElementById('previewNewMargin');

    const batchTotalCost = document.getElementById('batchTotalCost');
    const batchExpectedRevenue = document.getElementById('batchExpectedRevenue');
    const batchEstimatedProfit = document.getElementById('batchEstimatedProfit');
    const costHelpText = document.getElementById('cost_help_text');

    if (variantSelect) {
        variantSelect.addEventListener('change', function () {
            const opt = this.options[this.selectedIndex];
            if (!opt || !opt.dataset) return;

            const stock = parseFloat(opt.dataset.stock) || 0;
            const cost = parseFloat(opt.dataset.cost) || 0;
            const price = parseFloat(opt.dataset.price) || 0;

            currentStockInput.value = stock;
            currentCostInput.value = cost;
            sellingPriceInput.value = price;
            unitCostInput.value = cost.toFixed(2);

            if (costHelpText) {
                costHelpText.textContent = `Defaulted to variant cost price (₱${cost.toFixed(2)}).`;
            }

            calculateLiveImpact();
        });
    }

    function calculateLiveImpact() {
        const currentStockVal = parseFloat(currentStockInput.value) || 0;
        const currentCostVal = parseFloat(currentCostInput.value) || 0;
        const sellingPriceVal = parseFloat(sellingPriceInput.value) || 0;

        const qtyReceived = parseFloat(qtyInput.value) || 0;
        const purchaseCost = parseFloat(unitCostInput.value) || 0;

        const newStock = currentStockVal + qtyReceived;
        let newCost = currentCostVal;

        if (qtyReceived > 0) {
            if (currentStockVal > 0) {
                newCost = ((currentStockVal * currentCostVal) + (qtyReceived * purchaseCost)) / newStock;
            } else {
                newCost = purchaseCost;
            }
        }

        const newMargin = sellingPriceVal > 0 ? (((sellingPriceVal - newCost) / sellingPriceVal) * 100).toFixed(1) : 0;
        const batchCost = qtyReceived * purchaseCost;
        const batchRevenue = qtyReceived * sellingPriceVal;
        const batchProfit = batchRevenue - batchCost;

        previewNewStock.textContent = newStock.toLocaleString() + ' units';
        previewNewCost.textContent = '₱' + newCost.toFixed(2);
        previewNewMargin.textContent = newMargin + '%';

        batchTotalCost.textContent = '₱' + batchCost.toFixed(2);
        batchExpectedRevenue.textContent = '₱' + batchRevenue.toFixed(2);
        batchEstimatedProfit.textContent = '₱' + (batchProfit >= 0 ? batchProfit.toFixed(2) : '0.00');
    }

    qtyInput.addEventListener('input', calculateLiveImpact);
    unitCostInput.addEventListener('input', calculateLiveImpact);

    document.querySelectorAll('.btn-quick-qty').forEach(btn => {
        btn.addEventListener('click', function () {
            const addQty = parseFloat(this.getAttribute('data-qty')) || 0;
            const currentQty = parseFloat(qtyInput.value) || 0;
            qtyInput.value = (currentQty + addQty).toFixed(2);
            calculateLiveImpact();
        });
    });

    calculateLiveImpact();
});
</script>
@endpush
