@extends('layouts.app')

@section('title', 'Stock Adjustment')

@section('content')
<div class="container-fluid px-3 px-md-4 py-2.5">

    {{-- Header Bar --}}
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-0.5">
                <div class="rounded-3 p-1.5 bg-warning bg-opacity-10 text-warning-emphasis d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                    <i class="bi bi-sliders text-warning-emphasis fs-6"></i>
                </div>
                <h4 class="fw-black text-dark mb-0 font-mono" style="letter-spacing:-0.3px;">Stock Adjustment & Inventory Reconciliation</h4>
            </div>
            <p class="text-muted extra-small mb-0 ms-1">Audit physical stock counts, log inventory shrinkage, damage, expiration, or stock recovery</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('products.stock.history', encrypt($product->id)) }}" class="btn btn-white border rounded-3 px-2.5 py-1.5 fw-bold text-dark extra-small shadow-xs hover-lift d-flex align-items-center gap-1.5">
                <i class="bi bi-clock-history text-secondary"></i> Stock History Log
            </a>
            <a href="{{ route('products.index') }}" class="btn btn-light border rounded-3 px-2.5 py-1.5 fw-bold extra-small text-muted shadow-xs hover-lift d-flex align-items-center gap-1.5">
                <i class="bi bi-arrow-left me-1"></i> Product List
            </a>
        </div>
    </div>

    {{-- Hero Product Banner (Fixed Image Handling) --}}
    @php
        $imageUrl = asset('images/no_image.jpg');
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            $imageUrl = Storage::url($product->image);
        }
    @endphp
    <div class="card border-0 shadow-sm rounded-3 p-3 mb-3 bg-white">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <div class="rounded-3 border shadow-xs d-flex align-items-center justify-content-center bg-light flex-shrink-0 overflow-hidden" style="width:64px;height:64px;">
                <img src="{{ $imageUrl }}" class="rounded-3 object-fit-cover w-100 h-100" alt="" onerror="this.onerror=null;this.src='{{ asset('images/no_image.jpg') }}';">
            </div>
            <div class="min-w-0 flex-grow-1">
                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                    <span class="badge extra-small font-mono fw-bold" style="background:#e0f2fe;color:#0369a1;border:1px solid #7dd3fc;">
                        {{ $product->category?->name ?? 'Uncategorized' }}
                    </span>
                    <span class="badge extra-small font-mono fw-bold" style="background:#f1f5f9;color:#334155;border:1px solid #cbd5e1;">
                        Unit: {{ $product->unit?->name ?? 'pcs' }}
                    </span>
                    @if($product->stock_on_hand <= 0)
                        <span class="badge extra-small fw-bold" style="background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;">
                            Out of Stock
                        </span>
                    @elseif($product->stock_on_hand <= $product->reorder_level)
                        <span class="badge extra-small fw-bold" style="background:#fef3c7;color:#92400e;border:1px solid #fcd34d;">
                            Low Stock Alert
                        </span>
                    @else
                        <span class="badge extra-small fw-bold" style="background:#dcfce7;color:#166534;border:1px solid #86efac;">
                            Healthy Stock
                        </span>
                    @endif
                </div>
                <h4 class="fw-black text-dark mb-0 lh-sm text-truncate" title="{{ $product->name }}">{{ $product->name }}</h4>
                <div class="font-mono extra-small mt-0.5" style="color:#64748b;">
                    @if($product->barcode)
                        <i class="bi bi-barcode me-1"></i><strong>{{ $product->barcode }}</strong>
                    @endif
                    @if($product->sku)
                        <span class="ms-2">| SKU: <strong>{{ $product->sku }}</strong></span>
                    @endif
                </div>
            </div>
            <div class="d-flex align-items-center gap-3 ms-auto pt-2 pt-md-0">
                <div class="text-end border-end pe-3">
                    <span class="extra-small font-mono text-uppercase fw-bold" style="color:#64748b;font-size:0.7rem;">Capital Cost</span>
                    <div class="font-mono fw-bold text-dark fs-6">₱{{ number_format($product->cost_price ?? 0, 2) }}</div>
                </div>
                <div class="text-end">
                    <span class="extra-small font-mono text-uppercase fw-bold" style="color:#64748b;font-size:0.7rem;">Total On-Hand Stock</span>
                    <div class="font-mono fw-black text-primary fs-5">{{ number_format($product->stock_on_hand, 2) }} <small class="fs-6 font-mono text-muted">{{ $product->unit?->name ?? 'pcs' }}</small></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">

        {{-- Form Column --}}
        <div class="col-lg-7 col-xl-8">
            <div class="card border-0 shadow-sm rounded-3 p-3.5 bg-white">

                <form method="POST" action="{{ route('products.stock.adjustment.store', encrypt($product->id)) }}" id="adjustmentForm">
                    @csrf

                    {{-- Target Item Selector (Base or Variant) --}}
                    @if($product->variants && $product->variants->count() > 0)
                        <div class="mb-3.5">
                            <label class="form-label extra-small fw-bold text-uppercase mb-1.5" style="color:#334155;font-size:0.725rem;">
                                Target Item to Adjust
                            </label>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <input type="radio" class="btn-check target-item-radio" name="variant_id" id="target_base" value="" checked data-stock="{{ (float)$product->stock_on_hand }}" data-cost="{{ (float)($product->cost_price ?? 0) }}">
                                    <label class="btn btn-outline-secondary text-start w-100 p-2.5 rounded-3 d-flex align-items-center justify-content-between cursor-pointer" for="target_base">
                                        <div>
                                            <div class="fw-bold extra-small" style="color:#0f172a;"><i class="bi bi-box-seam me-1 text-primary"></i>Base Product</div>
                                            <small class="font-mono extra-small text-muted d-block mt-0.5">Main inventory stock</small>
                                        </div>
                                        <span class="badge extra-small font-mono fw-bold" style="background:#e0f2fe;color:#0369a1;border:1px solid #7dd3fc;">
                                            {{ number_format($product->stock_on_hand, 2) }} {{ $product->unit?->name ?? 'pcs' }}
                                        </span>
                                    </label>
                                </div>

                                @foreach($product->variants as $variant)
                                    <div class="col-md-6">
                                        <input type="radio" class="btn-check target-item-radio" name="variant_id" id="target_variant_{{ $variant->id }}" value="{{ $variant->id }}" data-stock="{{ (float)$variant->stock_on_hand }}" data-cost="{{ (float)($variant->cost_price ?? $product->cost_price ?? 0) }}">
                                        <label class="btn btn-outline-secondary text-start w-100 p-2.5 rounded-3 d-flex align-items-center justify-content-between cursor-pointer" for="target_variant_{{ $variant->id }}">
                                            <div>
                                                <div class="fw-bold extra-small" style="color:#0f172a;"><i class="bi bi-tag-fill me-1 text-purple"></i>{{ $variant->variant_name }}</div>
                                                <small class="font-mono extra-small text-muted d-block mt-0.5">Variant stock</small>
                                            </div>
                                            <span class="badge extra-small font-mono fw-bold" style="background:#f3e8ff;color:#6b21a8;border:1px solid #d8b4fe;">
                                                {{ number_format($variant->stock_on_hand, 2) }}
                                            </span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Stock Adjustment Matrix --}}
                    <div class="p-3 rounded-3 mb-3.5 border" style="background:#f8fafc;border-color:#cbd5e1!important;">
                        <div class="row g-3 align-items-center">

                            {{-- Current System Stock --}}
                            <div class="col-md-4">
                                <label class="form-label extra-small fw-bold text-uppercase mb-1" style="color:#475569;font-size:0.7rem;">
                                    Current System Stock
                                </label>
                                <div class="input-group">
                                    <input type="number" id="display_current_stock" class="form-control fw-black font-mono bg-white border text-dark" value="{{ (float)$product->stock_on_hand }}" readonly style="font-size:1rem;">
                                    <span class="input-group-text font-mono extra-small bg-white text-muted border-start-0">{{ $product->unit?->name ?? 'pcs' }}</span>
                                </div>
                            </div>

                            {{-- Actual Physical Count (Unified Input Group) --}}
                            <div class="col-md-5">
                                <label class="form-label extra-small fw-bold text-uppercase mb-1" style="color:#0f172a;font-size:0.7rem;">
                                    Actual Physical Count <span class="text-danger">*</span>
                                </label>
                                <div class="input-group shadow-xs">
                                    <button type="button" class="btn btn-light border text-dark fw-bold px-3" id="btnStepDown" style="font-size:1.1rem;line-height:1;">&minus;</button>
                                    <input type="number" step="0.01" min="0" name="actual_stock" id="actual_stock" class="form-control text-center fw-black font-mono @error('actual_stock') is-invalid @enderror" value="{{ old('actual_stock', (float)$product->stock_on_hand) }}" required style="font-size:1.1rem;color:#0f172a;background-color:#fff;">
                                    <button type="button" class="btn btn-light border text-dark fw-bold px-3" id="btnStepUp" style="font-size:1.1rem;line-height:1;">&plus;</button>
                                    <span class="input-group-text font-mono extra-small bg-light text-muted border-start-0">{{ $product->unit?->name ?? 'pcs' }}</span>
                                </div>
                                @error('actual_stock')
                                    <div class="invalid-feedback d-block extra-small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Variance Display --}}
                            <div class="col-md-3">
                                <label class="form-label extra-small fw-bold text-uppercase mb-1" style="color:#475569;font-size:0.7rem;">
                                    Stock Variance
                                </label>
                                <div class="p-2 rounded-3 border bg-white text-center shadow-xs" id="varianceResultBox">
                                    <span class="fw-black font-mono fs-5 d-block lh-1 mb-0.5" id="varianceQtyDisplay">0.00</span>
                                    <span class="extra-small font-mono fw-bold" id="varianceStatusBadge" style="color:#64748b;font-size:0.675rem;">No Adjustment</span>
                                </div>
                            </div>

                        </div>

                        {{-- Financial Impact --}}
                        <div class="mt-3 pt-2 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <span class="extra-small font-mono fw-bold" style="color:#64748b;">
                                Estimated Inventory Value Impact:
                            </span>
                            <span class="font-mono fw-black extra-small" id="financialImpactDisplay" style="color:#334155;">
                                ₱0.00 Cost Impact
                            </span>
                        </div>
                    </div>

                    {{-- Adjustment Reason Select & Quick Preset Badges --}}
                    <div class="mb-3.5">
                        <label class="form-label extra-small fw-bold text-uppercase mb-1" style="color:#334155;font-size:0.725rem;">
                            Adjustment Reason <span class="text-danger">*</span>
                        </label>
                        
                        <div class="d-flex align-items-center gap-1.5 flex-wrap mb-2">
                            <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 extra-small fw-bold reason-preset-btn active" data-value="Physical Count Audit" style="background:#e0f2fe;color:#0369a1;border-color:#7dd3fc!important;">
                                <i class="bi bi-box-seam me-1"></i>Physical Audit
                            </button>
                            <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 extra-small fw-bold reason-preset-btn" data-value="Damaged Item" style="background:#f1f5f9;color:#334155;border-color:#cbd5e1!important;">
                                <i class="bi bi-x-circle me-1 text-danger"></i>Damaged / Broken
                            </button>
                            <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 extra-small fw-bold reason-preset-btn" data-value="Expired Item" style="background:#f1f5f9;color:#334155;border-color:#cbd5e1!important;">
                                <i class="bi bi-hourglass-bottom me-1 text-warning"></i>Expired Item
                            </button>
                            <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 extra-small fw-bold reason-preset-btn" data-value="Lost / Stolen Item" style="background:#f1f5f9;color:#334155;border-color:#cbd5e1!important;">
                                <i class="bi bi-question-circle me-1 text-danger"></i>Lost / Shrinkage
                            </button>
                            <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 extra-small fw-bold reason-preset-btn" data-value="System Correction" style="background:#f1f5f9;color:#334155;border-color:#cbd5e1!important;">
                                <i class="bi bi-tools me-1 text-info"></i>Data Correction
                            </button>
                        </div>

                        <select name="reason" id="reason_select" class="form-select form-select-sm fw-bold @error('reason') is-invalid @enderror" required style="color:#0f172a;background-color:#fff;font-size:0.85rem;">
                            <option value="Physical Count Audit" @selected(old('reason', 'Physical Count Audit') === 'Physical Count Audit')>Physical Count Audit (Routine inventory count)</option>
                            <option value="Damaged Item" @selected(old('reason') === 'Damaged Item')>Damaged Item (Spill, transit damage, broken unit)</option>
                            <option value="Expired Item" @selected(old('reason') === 'Expired Item')>Expired Item (Perished, past shelf life)</option>
                            <option value="Lost / Stolen Item" @selected(old('reason') === 'Lost / Stolen Item')>Lost / Stolen Item (Unaccounted shrinkage loss)</option>
                            <option value="System Correction" @selected(old('reason') === 'System Correction')>System Correction (Data entry error fix)</option>
                        </select>

                        @error('reason')
                            <div class="invalid-feedback d-block extra-small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Remarks / Notes --}}
                    <div class="mb-4">
                        <label class="form-label extra-small fw-bold text-uppercase mb-1" style="color:#334155;font-size:0.725rem;">
                            Audit Remarks & Notes
                        </label>
                        <textarea name="remarks" rows="3" class="form-control extra-small @error('remarks') is-invalid @enderror" placeholder="Add optional audit notes regarding this inventory adjustment..." style="color:#0f172a;background-color:#fff;">{{ old('remarks') }}</textarea>
                        @error('remarks')
                            <div class="invalid-feedback d-block extra-small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Form Footer Actions --}}
                    <div class="d-flex align-items-center justify-content-between pt-3 border-top gap-2">
                        <a href="{{ route('products.stock.history', encrypt($product->id)) }}" class="btn btn-light border py-2 px-3 rounded-3 fw-bold extra-small" style="color:#475569;">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-warning py-2 px-4 rounded-3 fw-bold extra-small text-dark shadow-xs hover-lift" style="background:#f59e0b;border:none;">
                            <i class="bi bi-check-circle-fill me-1"></i> Save Stock Adjustment
                        </button>
                    </div>

                </form>
            </div>
        </div>

        {{-- Audit Log Sidebar Column --}}
        <div class="col-lg-5 col-xl-4">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-clock-history text-warning-emphasis fs-5"></i>
                        <h6 class="fw-black text-dark mb-0 font-mono fs-6">Recent Stock Adjustments</h6>
                    </div>
                    <span class="badge extra-small font-mono fw-bold" style="background:#fef3c7;color:#92400e;border:1px solid #fcd34d;">Audit Log</span>
                </div>

                @if(isset($recentAdjustments) && $recentAdjustments->count() > 0)
                    <div class="d-flex flex-column gap-2">
                        @foreach($recentAdjustments as $adj)
                            @php
                                $diff = (float)$adj->stock_after - (float)$adj->stock_before;
                                $isPositive = $diff > 0;
                            @endphp
                            <div class="p-2.5 rounded-3 bg-light border d-flex flex-column gap-1">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-1.5">
                                        <span class="badge extra-small fw-bold" style="{{ $isPositive ? 'background:#dcfce7;color:#166534;border:1px solid #86efac;' : 'background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;' }}">
                                            {{ $isPositive ? '+' : '' }}{{ number_format($diff, 2) }}
                                        </span>
                                        @if($adj->variant)
                                            <span class="badge extra-small font-mono fw-bold" style="background:#f3e8ff;color:#6b21a8;border:1px solid #d8b4fe;font-size:0.675rem;">
                                                {{ $adj->variant->variant_name }}
                                            </span>
                                        @else
                                            <span class="badge extra-small font-mono fw-bold" style="background:#e0f2fe;color:#0369a1;border:1px solid #7dd3fc;font-size:0.675rem;">
                                                Base Item
                                            </span>
                                        @endif
                                    </div>
                                    <small class="font-mono extra-small" style="color:#64748b;">
                                        {{ $adj->created_at ? $adj->created_at->format('M d, h:i A') : '' }}
                                    </small>
                                </div>

                                <div class="extra-small font-mono mt-1" style="color:#334155;">
                                    Stock: {{ number_format($adj->stock_before, 2) }} &rarr; <strong style="color:#0f172a;">{{ number_format($adj->stock_after, 2) }}</strong>
                                </div>

                                @if($adj->remarks)
                                    <small class="extra-small font-mono text-truncate mt-0.5" style="color:#64748b;" title="{{ $adj->remarks }}">
                                        <i class="bi bi-info-circle me-1"></i>{{ $adj->remarks }}
                                    </small>
                                @endif

                                <div class="extra-small font-mono text-end" style="color:#94a3b8;font-size:0.675rem;">
                                    By: {{ $adj->createdBy?->name ?? 'System Admin' }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4" style="color:#64748b;">
                        <i class="bi bi-clipboard-check fs-2 opacity-50"></i>
                        <p class="extra-small font-mono mt-2 mb-0">No recent physical stock adjustments found for this item.</p>
                    </div>
                @endif
            </div>
        </div>

    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const currentStockInput = document.getElementById('display_current_stock');
    const actualStockInput  = document.getElementById('actual_stock');
    const varianceDisplay   = document.getElementById('varianceQtyDisplay');
    const statusBadge       = document.getElementById('varianceStatusBadge');
    const financialDisplay  = document.getElementById('financialImpactDisplay');
    const reasonSelect      = document.getElementById('reason_select');
    const presetBtns        = document.querySelectorAll('.reason-preset-btn');

    let currentCost = {{ (float)($product->cost_price ?? 0) }};

    function recalculateVariance() {
        let selectedRadio = document.querySelector('.target-item-radio:checked');
        let currentStock  = selectedRadio ? parseFloat(selectedRadio.getAttribute('data-stock') || 0) : {{ (float)$product->stock_on_hand }};
        currentCost       = selectedRadio ? parseFloat(selectedRadio.getAttribute('data-cost') || 0) : {{ (float)($product->cost_price ?? 0) }};

        currentStockInput.value = currentStock.toFixed(2);

        let actualCount = parseFloat(actualStockInput.value || 0);
        let variance    = actualCount - currentStock;
        let financialVal = Math.abs(variance * currentCost);

        varianceDisplay.innerText = (variance > 0 ? '+' : '') + variance.toFixed(2);

        if (variance < 0) {
            varianceDisplay.style.color = '#b91c1c';
            statusBadge.innerText = '⚠️ Stock Shrinkage / Reduction';
            statusBadge.style.color = '#b91c1c';
            financialDisplay.innerHTML = `<span style="color:#b91c1c;">-₱${financialVal.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})} Shrinkage Cost</span>`;
        } else if (variance > 0) {
            varianceDisplay.style.color = '#166534';
            statusBadge.innerText = '📈 Stock Surplus / Addition';
            statusBadge.style.color = '#166534';
            financialDisplay.innerHTML = `<span style="color:#166534;">+₱${financialVal.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2})} Value Recovery</span>`;
        } else {
            varianceDisplay.style.color = '#64748b';
            statusBadge.innerText = 'No Adjustment';
            statusBadge.style.color = '#64748b';
            financialDisplay.innerHTML = `<span style="color:#475569;">₱0.00 Cost Impact</span>`;
        }
    }

    // Reason Preset Badges sync with Select
    presetBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const val = this.getAttribute('data-value');
            if (reasonSelect) {
                reasonSelect.value = val;
            }
            presetBtns.forEach(b => {
                b.style.background = '#f1f5f9';
                b.style.color = '#334155';
                b.style.borderColor = '#cbd5e1';
            });
            this.style.background = '#e0f2fe';
            this.style.color = '#0369a1';
            this.style.borderColor = '#7dd3fc';
        });
    });

    reasonSelect?.addEventListener('change', function () {
        const val = this.value;
        presetBtns.forEach(b => {
            if (b.getAttribute('data-value') === val) {
                b.style.background = '#e0f2fe';
                b.style.color = '#0369a1';
                b.style.borderColor = '#7dd3fc';
            } else {
                b.style.background = '#f1f5f9';
                b.style.color = '#334155';
                b.style.borderColor = '#cbd5e1';
            }
        });
    });

    actualStockInput?.addEventListener('input', recalculateVariance);

    document.querySelectorAll('.target-item-radio').forEach(radio => {
        radio.addEventListener('change', function () {
            let newStock = parseFloat(this.getAttribute('data-stock') || 0);
            actualStockInput.value = newStock;
            recalculateVariance();
        });
    });

    document.getElementById('btnStepDown')?.addEventListener('click', function () {
        let val = parseFloat(actualStockInput.value || 0);
        if (val > 0) {
            actualStockInput.value = Math.max(0, val - 1);
            recalculateVariance();
        }
    });

    document.getElementById('btnStepUp')?.addEventListener('click', function () {
        let val = parseFloat(actualStockInput.value || 0);
        actualStockInput.value = val + 1;
        recalculateVariance();
    });

    recalculateVariance();
});
</script>
@endpush
@endsection
