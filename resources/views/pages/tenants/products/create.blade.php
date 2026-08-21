@extends('layouts.app')
@section('title', isset($product) ? 'Edit Product' : 'Add New Product')
@section('shortText', 'Manage product details, variants, and pricing')

@push('styles')
<style>
/* ── Product Form — Premium CRM Styles ─────────────────── */
.pf-shell        { display: flex; flex-direction: column; gap: 1.25rem; padding-bottom: 100px; }
.pf-card         { background:#fff; border:1px solid #e9ecef; border-radius:16px; overflow:hidden; }
.pf-card-header  {
    display:flex; align-items:center; gap:12px;
    padding:16px 20px; border-bottom:1px solid #f1f5f9;
    background: linear-gradient(135deg,#f8fafc,#f0fdf4);
}
.pf-card-icon    {
    width:38px;height:38px;border-radius:10px;
    background:linear-gradient(135deg,#059669,#047857);
    color:#fff;display:flex;align-items:center;justify-content:center;
    font-size:1rem;flex-shrink:0;
}
.pf-card-title   { font-size:.9rem;font-weight:700;color:#0f172a;line-height:1.2; }
.pf-card-subtitle{ font-size:.73rem;color:#94a3b8; }
.pf-card-body    { padding:20px; }
.pf-card-header .badge-tag {
    margin-left:auto;font-size:.65rem;padding:3px 10px;border-radius:999px;
    background:#d1fae5;color:#059669;font-weight:700;letter-spacing:.03em;
}

/* Variant table */
.variant-table    { width:100%;border-collapse:collapse; }
.variant-table th { font-size:.72rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.04em;padding:8px 10px;border-bottom:2px solid #e9ecef;white-space:nowrap; }
.variant-table td { padding:6px 6px;vertical-align:middle; }
.variant-table tr:not(:last-child) td { border-bottom:1px solid #f1f5f9; }
.variant-table tr:hover td { background:#fafafa; }
.variant-table .form-control,.variant-table .form-select { font-size:.8rem;padding:5px 8px;height:34px; }
.variant-delete-btn { color:#ef4444;background:transparent;border:none;padding:4px 6px;border-radius:6px;cursor:pointer;transition:background .15s; }
.variant-delete-btn:hover { background:#fef2f2; }

/* Add variant btn */
.btn-add-variant {
    display:inline-flex;align-items:center;gap:6px;
    font-size:.8rem;font-weight:600;
    border:1.5px dashed #d1d5db;color:#64748b;
    background:transparent;border-radius:10px;padding:7px 16px;
    cursor:pointer;transition:all .2s;
}
.btn-add-variant:hover { border-color:#059669;color:#059669;background:#f0fdf4; }

/* Bulk mode */
.bulk-mode-panel { background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:14px 16px; }
.bulk-mode-panel .form-label { color:#065f46;font-weight:600; }

/* Import panel */
.import-panel    { background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:16px; }
.import-card     {
    display:flex;align-items:center;gap:10px;
    background:#fff;border:1px solid #e2e8f0;border-radius:10px;
    padding:10px 12px;cursor:pointer;transition:all .2s;margin-bottom:8px;
}
.import-card:hover { border-color:#059669;box-shadow:0 2px 8px rgba(5,150,105,.1); }
.import-card-img  { width:40px;height:40px;border-radius:8px;object-fit:cover;flex-shrink:0; }
.import-card-name { font-size:.82rem;font-weight:700;color:#0f172a; }
.import-card-meta { font-size:.72rem;color:#64748b; }
.import-badge     { margin-left:auto;font-size:.65rem;font-weight:700;background:#d1fae5;color:#059669;padding:2px 8px;border-radius:999px;white-space:nowrap; }

/* Scan button */
.btn-scan {
    display:inline-flex;align-items:center;gap:6px;
    background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;
    padding:6px 12px;font-size:.8rem;font-weight:600;color:#475569;
    cursor:pointer;transition:all .2s;white-space:nowrap;
}
.btn-scan:hover { background:#f0fdf4;border-color:#059669;color:#059669; }

/* Image drop zone */
.image-drop-zone {
    border:2px dashed #d1d5db;border-radius:12px;
    padding:28px 20px;text-align:center;cursor:pointer;
    transition:all .2s;background:#fafafa;position:relative;
}
.image-drop-zone.drag-over { border-color:#059669;background:#f0fdf4; }
.image-drop-zone input[type=file] { position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%; }
.image-preview { max-height:120px;border-radius:10px;object-fit:cover;margin-top:10px; }

/* Sticky footer */
.pf-footer {
    position:sticky;bottom:0;z-index:20;
    background:#fff;border-top:1px solid #e9ecef;
    padding:14px 20px;display:flex;align-items:center;justify-content:space-between;
    gap:10px;
}

/* SKU generate btn */
.btn-gen-sku {
    background:#f8fafc;border:1px solid #e2e8f0;border-radius:0 8px 8px 0;
    padding:0 12px;font-size:.8rem;color:#475569;cursor:pointer;
    transition:background .15s;white-space:nowrap;
}
.btn-gen-sku:hover { background:#f0fdf4;color:#059669; }

/* Profit badge */
.profit-badge {
    display:inline-flex;align-items:center;gap:4px;
    background:#d1fae5;color:#059669;border-radius:8px;
    font-size:.75rem;font-weight:700;padding:4px 10px;
}
.loss-badge {
    background:#fee2e2;color:#dc2626;
}

/* Variant toggle */
.variant-toggle-wrap { display:flex;align-items:center;gap:10px; }
.form-check-input:checked { background-color:#059669;border-color:#059669; }

/* Import search spinner */
.import-loading { text-align:center;padding:20px;color:#94a3b8;font-size:.82rem; }
.import-empty   { text-align:center;padding:20px;color:#94a3b8;font-size:.82rem; }
</style>
@endpush

@section('content')
@php $isEdit = isset($product); @endphp

<form
    id="productForm"
    method="POST"
    enctype="multipart/form-data"
    action="{{ $isEdit ? route('products.update', encrypt($product->id)) : route('products.store') }}"
>
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="pf-shell">

        {{-- ── SECTION 1: Basic Info ────────────────────────── --}}
        <div class="pf-card">
            <div class="pf-card-header">
                <div class="pf-card-icon"><i class="bi bi-box-seam"></i></div>
                <div>
                    <div class="pf-card-title">Product Information</div>
                    <div class="pf-card-subtitle">Basic product identity & classification</div>
                </div>
            </div>
            <div class="pf-card-body">
                <div class="row g-3">

                    {{-- Product Name (with cross-store suggestions) --}}
                    <div class="col-12 position-relative">
                        <label class="form-label fw-semibold small">Product Name <span class="text-danger">*</span></label>
                        <input
                            id="productName"
                            name="name"
                            type="text"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $product->name ?? '') }}"
                            placeholder="e.g. Premium White Rice, Sunflower Oil..."
                            autocomplete="off"
                            required
                        >
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div id="productSuggestions" class="product-suggestions d-none"></div>
                    </div>

                    {{-- Barcode --}}
                    <div class="col-lg-4 col-md-6">
                        <label class="form-label fw-semibold small">Barcode</label>
                        <div class="input-group">
                            <input
                                id="barcode"
                                name="barcode"
                                type="text"
                                class="form-control"
                                value="{{ old('barcode', $product->barcode ?? '') }}"
                                placeholder="Scan or type barcode"
                                autocomplete="off"
                            >
                            <button type="button" class="btn-scan" id="scanBarcodeBtn" title="Use camera to scan barcode">
                                <i class="bi bi-camera-fill"></i> Scan
                            </button>
                        </div>
                        <div id="scanFeedback" class="text-muted small mt-1 d-none"></div>
                    </div>

                    {{-- SKU --}}
                    <div class="col-lg-4 col-md-6">
                        <label class="form-label fw-semibold small">SKU</label>
                        <div class="input-group">
                            <input
                                id="sku"
                                name="sku"
                                type="text"
                                class="form-control"
                                value="{{ old('sku', $product->sku ?? '') }}"
                                placeholder="Stock Keeping Unit"
                            >
                            <button type="button" class="btn-gen-sku" id="genSkuBtn" title="Auto-generate SKU">
                                <i class="bi bi-magic me-1"></i>Generate
                            </button>
                        </div>
                    </div>

                    {{-- Category --}}
                    <div class="col-lg-4 col-md-6">
                        <label class="form-label fw-semibold small">Category <span class="text-danger">*</span></label>
                        <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                            <option value="">Select Category</option>
                            @foreach($categories ?? [] as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Base Unit --}}
                    <div class="col-lg-4 col-md-6">
                        <label class="form-label fw-semibold small">Base Unit <span class="text-danger">*</span></label>
                        <select id="unit_id" name="unit_id" class="form-select @error('unit_id') is-invalid @enderror">
                            <option value="">Select Unit</option>
                            @foreach($units ?? [] as $unit)
                                <option value="{{ $unit->id }}" {{ old('unit_id', $product->unit_id ?? '') == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Description --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Description</label>
                        <textarea
                            id="description"
                            name="description"
                            rows="2"
                            class="form-control"
                            placeholder="Optional product description..."
                        >{{ old('description', $product->description ?? '') }}</textarea>
                    </div>

                </div>
            </div>
        </div>

        {{-- ── SECTION 2: Pricing ──────────────────────────── --}}
        <div class="pf-card">
            <div class="pf-card-header">
                <div class="pf-card-icon" style="background:linear-gradient(135deg,#d97706,#b45309);">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div>
                    <div class="pf-card-title">Base Pricing</div>
                    <div class="pf-card-subtitle">Cost and selling price for the base unit</div>
                </div>
                {{-- Smart Bulk Mode toggle --}}
                <div class="ms-auto d-flex align-items-center gap-2">
                    <label class="form-check-label small fw-semibold text-muted" for="bulkModeToggle">Smart Bulk Mode</label>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" id="bulkModeToggle" role="switch">
                    </div>
                    <i class="bi bi-info-circle text-muted" data-bs-toggle="tooltip"
                       title="Enable to set a per-unit base cost. Variant prices will auto-calculate from qty × base cost."></i>
                </div>
            </div>
            <div class="pf-card-body">

                {{-- Bulk mode panel --}}
                <div id="bulkModePanel" class="bulk-mode-panel mb-3 d-none">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label small">Base Cost per Unit (e.g. per kg, per piece)</label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" id="baseCostPerUnit" class="form-control" step="0.01" min="0" placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="text-muted small">
                                <i class="bi bi-lightbulb-fill text-warning me-1"></i>
                                Set the cost per base unit (e.g. ₱60/kg). When you add variants with a quantity (e.g. 2.5kg pack),
                                the system will auto-calculate the cost: <strong>₱60 × 2.5 = ₱150</strong>.
                                You still set the selling price yourself.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-lg-4 col-md-6">
                        <label class="form-label fw-semibold small">Cost Price (Capital) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" id="cost_price" name="cost_price" step="0.01" min="0"
                                class="form-control @error('cost_price') is-invalid @enderror"
                                value="{{ old('cost_price', $product->cost_price ?? 0) }}" required>
                        </div>
                        @error('cost_price') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <label class="form-label fw-semibold small text-success">
                            <i class="bi bi-tag-fill me-1"></i>Retail Price (Selling Price) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-success-subtle text-success fw-bold">₱</span>
                            <input type="number" id="selling_price" name="selling_price" step="0.01" min="0"
                                class="form-control fw-bold @error('selling_price') is-invalid @enderror"
                                value="{{ old('selling_price', $product->selling_price ?? 0) }}" required>
                        </div>
                        @error('selling_price') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <label class="form-label fw-semibold small text-primary">
                            <i class="bi bi-box-seam-fill me-1"></i>Wholesale Price
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-primary-subtle text-primary fw-bold">₱</span>
                            <input type="number" id="wholesale_price" name="wholesale_price" step="0.01" min="0"
                                class="form-control fw-bold"
                                value="{{ old('wholesale_price', $product->wholesale_price ?? 0) }}"
                                placeholder="Optional (for bulk buyers)">
                        </div>
                    </div>
                    <div class="col-12">
                        <div id="profitPreview" class="d-flex flex-wrap align-items-center gap-3 pt-1"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── SECTION 3: Variants ─────────────────────────── --}}
        <div class="pf-card">
            <div class="pf-card-header">
                <div class="pf-card-icon" style="background:linear-gradient(135deg,#7c3aed,#6d28d9);">
                    <i class="bi bi-collection"></i>
                </div>
                <div>
                    <div class="pf-card-title">Product Variants</div>
                    <div class="pf-card-subtitle">Different sizes, packs, or bundles of this product</div>
                </div>
                <div class="ms-auto d-flex align-items-center gap-2">
                    <label class="form-check-label small fw-semibold text-muted" for="variantToggle">Enable Variants</label>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" id="variantToggle" role="switch"
                            {{ count($variants ?? []) > 0 ? 'checked' : '' }}>
                    </div>
                </div>
            </div>
            <div class="pf-card-body" id="variantPanel" style="{{ count($variants ?? []) == 0 ? 'display:none' : '' }}">

                <div class="text-muted small mb-3">
                    <i class="bi bi-info-circle me-1"></i>
                    Example: Product = <strong>White Rice</strong>, Variants = <em>2.5kg Pack</em>, <em>5kg Sack</em>, <em>25kg Sack</em> — each with their own barcode and price.
                </div>

                <div class="table-responsive">
                    <table class="variant-table" id="variantTable">
                        <thead>
                            <tr>
                                <th>Variant Name</th>
                                <th>Qty / Pack</th>
                                <th>Barcode</th>
                                <th>Cost Price</th>
                                <th>Selling Price</th>
                                <th>Wholesale</th>
                                <th>Unit</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="variantBody">
                            @foreach($variants ?? [] as $i => $v)
                            <tr class="variant-row" data-index="{{ $i }}">
                                <input type="hidden" name="variants[{{ $i }}][id]" value="{{ $v->id }}">
                                <td>
                                    <input type="text" name="variants[{{ $i }}][variant_name]"
                                        class="form-control variant-name" placeholder="e.g. 2.5kg Pack"
                                        value="{{ $v->variant_name }}" required>
                                </td>
                                <td>
                                    <input type="number" name="variants[{{ $i }}][qty_per_pack]"
                                        class="form-control variant-qty" step="0.0001" min="0.0001"
                                        value="{{ $v->qty_per_pack }}" required>
                                </td>
                                <td>
                                    <input type="text" name="variants[{{ $i }}][barcode]"
                                        class="form-control" placeholder="Optional"
                                        value="{{ $v->barcode }}">
                                </td>
                                <td>
                                    <div class="input-group">
                                        <span class="input-group-text px-2">₱</span>
                                        <input type="number" name="variants[{{ $i }}][cost_price]"
                                            class="form-control variant-cost" step="0.01" min="0"
                                            value="{{ $v->cost_price }}" required>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group">
                                        <span class="input-group-text px-2">₱</span>
                                        <input type="number" name="variants[{{ $i }}][selling_price]"
                                            class="form-control" step="0.01" min="0"
                                            value="{{ $v->selling_price }}" required>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group">
                                        <span class="input-group-text px-2">₱</span>
                                        <input type="number" name="variants[{{ $i }}][wholesale_price]"
                                            class="form-control" step="0.01" min="0"
                                            value="{{ $v->wholesale_price }}">
                                    </div>
                                </td>
                                <td>
                                    <select name="variants[{{ $i }}][unit_id]" class="form-select">
                                        <option value="">—</option>
                                        @foreach($units ?? [] as $unit)
                                            <option value="{{ $unit->id }}" {{ $v->unit_id == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <button type="button" class="variant-delete-btn" title="Remove variant">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <button type="button" id="addVariantBtn" class="btn-add-variant">
                        <i class="bi bi-plus-lg"></i> Add Variant
                    </button>
                </div>

                {{-- Hidden template row (cloned by JS) --}}
                <template id="variantRowTemplate">
                    <tr class="variant-row">
                        <input type="hidden" name="variants[__IDX__][id]" value="">
                        <td>
                            <input type="text" name="variants[__IDX__][variant_name]"
                                class="form-control variant-name" placeholder="e.g. 2.5kg Pack" required>
                        </td>
                        <td>
                            <input type="number" name="variants[__IDX__][qty_per_pack]"
                                class="form-control variant-qty" step="0.0001" min="0.0001" value="1" required>
                        </td>
                        <td>
                            <input type="text" name="variants[__IDX__][barcode]"
                                class="form-control" placeholder="Optional">
                        </td>
                        <td>
                            <div class="input-group">
                                <span class="input-group-text px-2">₱</span>
                                <input type="number" name="variants[__IDX__][cost_price]"
                                    class="form-control variant-cost" step="0.01" min="0" value="0" required>
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <span class="input-group-text px-2">₱</span>
                                <input type="number" name="variants[__IDX__][selling_price]"
                                    class="form-control" step="0.01" min="0" value="0" required>
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <span class="input-group-text px-2">₱</span>
                                <input type="number" name="variants[__IDX__][wholesale_price]"
                                    class="form-control" step="0.01" min="0" value="">
                            </div>
                        </td>
                        <td>
                            <select name="variants[__IDX__][unit_id]" class="form-select">
                                <option value="">—</option>
                                @foreach($units ?? [] as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <button type="button" class="variant-delete-btn" title="Remove variant">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </td>
                    </tr>
                </template>

            </div>
        </div>

        {{-- ── SECTION 4: Inventory ────────────────────────── --}}
        <div class="pf-card">
            <div class="pf-card-header">
                <div class="pf-card-icon" style="background:linear-gradient(135deg,#0ea5e9,#0284c7);">
                    <i class="bi bi-boxes"></i>
                </div>
                <div>
                    <div class="pf-card-title">Inventory Settings</div>
                    <div class="pf-card-subtitle">Stock thresholds and product image</div>
                </div>
            </div>
            <div class="pf-card-body">
                <div class="row g-3">
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label fw-semibold small">Reorder Level</label>
                        <div class="input-group">
                            <input type="number" name="reorder_level" min="0"
                                class="form-control"
                                value="{{ old('reorder_level', $product->reorder_level ?? 0) }}"
                                placeholder="0">
                            <span class="input-group-text text-muted small">units</span>
                        </div>
                        <div class="text-muted" style="font-size:.7rem;margin-top:4px;">Alert when stock drops below this</div>
                    </div>
                    <div class="col-lg-9 col-md-12">
                        <label class="form-label fw-semibold small">Product Image</label>
                        <div class="image-drop-zone" id="imageDropZone">
                            <input type="file" name="image" id="imageInput" accept="image/*">
                            <div id="imageDropContent">
                                <i class="bi bi-cloud-upload fs-2 text-muted mb-2"></i>
                                <div class="fw-semibold text-muted small">Drop image here or click to browse</div>
                                <div class="text-muted" style="font-size:.72rem;">JPG, PNG, WEBP — max 2MB</div>
                            </div>
                            @if(!empty($product?->image))
                                <img src="{{ asset('storage/'.$product->image) }}" class="image-preview" id="imagePreview" alt="Product Image">
                            @else
                                <img src="" class="image-preview d-none" id="imagePreview" alt="Product Image">
                            @endif
                        </div>
                    </div>

                    {{-- Allow Decimal / Fractional Quantities Switch --}}
                    <div class="col-12 mt-2 pt-2 border-top">
                        <div class="d-flex align-items-center justify-content-between p-3 rounded-3 bg-light border">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-3 p-2 bg-warning-subtle text-warning d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                                    <i class="bi bi-speedometer2 fs-5"></i>
                                </div>
                                <div>
                                    <label class="form-check-label fw-bold text-dark mb-0 d-block" for="allowDecimalQty" style="font-size:0.9rem;">
                                        Allow Decimal / Weighed Quantities (Tinitimbang / Loose Goods)
                                    </label>
                                    <small class="text-muted" style="font-size:0.75rem;">
                                        Enable for loose items sold by fraction or weight in POS (e.g. Rice, Sugar, Meat, Vegetables — enables 0.25kg, 0.5kg, 2.5kg).
                                    </small>
                                </div>
                            </div>
                            <div class="form-check form-switch mb-0 fs-5">
                                <input class="form-check-input" type="checkbox" name="allow_decimal_qty" id="allowDecimalQty" value="1"
                                    {{ old('allow_decimal_qty', $product->allow_decimal_qty ?? false) ? 'checked' : '' }} role="switch">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        {{-- ── STICKY FOOTER ───────────────────────────────── --}}
        <div class="pf-footer">
            <a href="{{ route('products.index') }}" class="btn btn-light border px-4">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small d-none d-md-block">All fields marked * are required</span>
                <button type="submit" class="btn btn-success px-5 fw-bold" style="background:#059669;border:none;">
                    <i class="bi bi-check-circle-fill me-1"></i>
                    {{ $isEdit ? 'Update Product' : 'Save Product' }}
                </button>
            </div>
        </div>

    </div>
</form>
@endsection

@push('scripts')
@vite('resources/js/pages/pos/product-suggestions.js')

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Tooltips ──────────────────────────────────────────
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        new bootstrap.Tooltip(el);
    });

    // ── SKU Generator ─────────────────────────────────────
    document.getElementById('genSkuBtn')?.addEventListener('click', function () {
        const name = document.getElementById('productName').value.trim();
        const prefix = name ? name.replace(/[^a-zA-Z0-9]/g,' ').trim().split(/\s+/).map(w=>w[0]).join('').toUpperCase().slice(0,4) : 'PRD';
        const rand = Math.floor(Math.random() * 90000) + 10000;
        document.getElementById('sku').value = `${prefix}-${rand}`;
    });

    // ── Profit Preview (Retail & Wholesale) ───────────────
    function updateProfitPreview() {
        const cost      = parseFloat(document.getElementById('cost_price')?.value) || 0;
        const retail    = parseFloat(document.getElementById('selling_price')?.value) || 0;
        const wholesale = parseFloat(document.getElementById('wholesale_price')?.value) || 0;
        const el        = document.getElementById('profitPreview');
        if (!el) return;

        let html = '';

        // Retail profit
        if (retail > 0 || cost > 0) {
            const retailProfit = retail - cost;
            const retailPct    = cost > 0 ? ((retailProfit / cost) * 100).toFixed(1) : 0;
            const retailCls    = retailProfit >= 0 ? 'profit-badge' : 'profit-badge loss-badge';
            const retailIcon   = retailProfit >= 0 ? 'bi-graph-up-arrow' : 'bi-graph-down-arrow';
            html += `<span class="${retailCls}"><i class="bi ${retailIcon}"></i> <strong>Retail Margin:</strong> &nbsp; ${retailProfit >= 0 ? '+' : ''}₱${retailProfit.toFixed(2)} (${retailPct}%)</span>`;
        }

        // Wholesale profit
        if (wholesale > 0) {
            const wsProfit = wholesale - cost;
            const wsPct    = cost > 0 ? ((wsProfit / cost) * 100).toFixed(1) : 0;
            const wsCls    = wsProfit >= 0 ? 'badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 rounded-3 fw-bold' : 'profit-badge loss-badge';
            const wsIcon   = wsProfit >= 0 ? 'bi-box-seam-fill' : 'bi-graph-down-arrow';
            html += `<span class="${wsCls}"><i class="bi ${wsIcon} me-1"></i> <strong>Wholesale Margin:</strong> &nbsp; ${wsProfit >= 0 ? '+' : ''}₱${wsProfit.toFixed(2)} (${wsPct}%)</span>`;
        }

        el.innerHTML = html;
    }
    document.getElementById('cost_price')?.addEventListener('input', updateProfitPreview);
    document.getElementById('selling_price')?.addEventListener('input', updateProfitPreview);
    document.getElementById('wholesale_price')?.addEventListener('input', updateProfitPreview);
    updateProfitPreview();

    // ── Bulk Mode ─────────────────────────────────────────
    const bulkToggle  = document.getElementById('bulkModeToggle');
    const bulkPanel   = document.getElementById('bulkModePanel');
    const baseCostInp = document.getElementById('baseCostPerUnit');

    bulkToggle?.addEventListener('change', function () {
        bulkPanel.classList.toggle('d-none', !this.checked);
    });

    function applyBulkCostToVariants() {
        const base = parseFloat(baseCostInp.value) || 0;
        if (base <= 0) return;
        document.querySelectorAll('.variant-row').forEach(row => {
            const qty  = parseFloat(row.querySelector('.variant-qty')?.value) || 1;
            const cost = row.querySelector('.variant-cost');
            if (cost) cost.value = (base * qty).toFixed(2);
        });
    }

    baseCostInp?.addEventListener('input', applyBulkCostToVariants);

    // ── Variants Panel Toggle ─────────────────────────────
    const variantToggle = document.getElementById('variantToggle');
    const variantPanel  = document.getElementById('variantPanel');

    variantToggle?.addEventListener('change', function () {
        variantPanel.style.display = this.checked ? '' : 'none';
        if (!this.checked) {
            // clear variant inputs so they don't submit
            document.querySelectorAll('#variantBody .variant-row').forEach(r => r.remove());
        }
    });

    // ── Add Variant Row ───────────────────────────────────
    let variantIndex = {{ count($variants ?? []) }};
    const variantBody    = document.getElementById('variantBody');
    const rowTemplate    = document.getElementById('variantRowTemplate');

    document.getElementById('addVariantBtn')?.addEventListener('click', function () {
        const clone = rowTemplate.content.cloneNode(true);
        clone.querySelectorAll('[name]').forEach(el => {
            el.name = el.name.replace('__IDX__', variantIndex);
        });
        variantBody.appendChild(clone);
        variantIndex++;
        applyBulkCostToVariants();
    });

    // ── Delete Variant Row ────────────────────────────────
    variantBody?.addEventListener('click', function (e) {
        const btn = e.target.closest('.variant-delete-btn');
        if (!btn) return;
        btn.closest('.variant-row').remove();
    });

    // ── Auto-compute variant cost from qty in Bulk Mode ──
    variantBody?.addEventListener('input', function (e) {
        if (!e.target.classList.contains('variant-qty')) return;
        const base = parseFloat(baseCostInp?.value) || 0;
        if (base <= 0 || !bulkToggle?.checked) return;
        const row  = e.target.closest('.variant-row');
        const cost = row?.querySelector('.variant-cost');
        if (cost) cost.value = (base * parseFloat(e.target.value || 1)).toFixed(2);
    });

    // ── Barcode Camera Scan ───────────────────────────────
    document.getElementById('scanBarcodeBtn')?.addEventListener('click', async function () {
        const feedback = document.getElementById('scanFeedback');
        if (!('BarcodeDetector' in window)) {
            feedback.textContent = 'Camera scan not supported in this browser. Use Chrome/Edge or plug in a USB barcode scanner.';
            feedback.classList.remove('d-none','text-success','text-danger');
            feedback.classList.add('text-warning');
            return;
        }
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
            const video  = document.createElement('video');
            video.srcObject = stream;
            video.play();

            const detector = new BarcodeDetector({ formats: ['ean_13','ean_8','code_128','code_39','upc_a','upc_e'] });

            feedback.textContent = '📷 Point camera at barcode... (scanning)';
            feedback.className   = 'text-muted small mt-1';
            feedback.classList.remove('d-none');

            let detected = false;
            const interval = setInterval(async () => {
                try {
                    const barcodes = await detector.detect(video);
                    if (barcodes.length > 0 && !detected) {
                        detected = true;
                        clearInterval(interval);
                        stream.getTracks().forEach(t => t.stop());
                        document.getElementById('barcode').value = barcodes[0].rawValue;
                        feedback.textContent = `✅ Barcode detected: ${barcodes[0].rawValue}`;
                        feedback.className = 'text-success small mt-1';
                    }
                } catch {}
            }, 200);

            // Timeout after 15s
            setTimeout(() => {
                if (!detected) {
                    clearInterval(interval);
                    stream.getTracks().forEach(t => t.stop());
                    feedback.textContent = 'Scan timed out. Try again or type the barcode manually.';
                    feedback.className = 'text-danger small mt-1';
                }
            }, 15000);

        } catch (err) {
            feedback.textContent = 'Camera access denied. Grant camera permission and try again.';
            feedback.className = 'text-danger small mt-1';
            feedback.classList.remove('d-none');
        }
    });

    // ── Image Drop Zone ───────────────────────────────────
    const dropZone  = document.getElementById('imageDropZone');
    const imgInput  = document.getElementById('imageInput');
    const imgPreview= document.getElementById('imagePreview');
    const imgContent= document.getElementById('imageDropContent');

    function showPreview(file) {
        const reader = new FileReader();
        reader.onload = e => {
            imgPreview.src = e.target.result;
            imgPreview.classList.remove('d-none');
            imgContent.style.display = 'none';
        };
        reader.readAsDataURL(file);
    }

    imgInput?.addEventListener('change', function () {
        if (this.files[0]) showPreview(this.files[0]);
    });

    dropZone?.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('drag-over'); });
    dropZone?.addEventListener('dragleave', ()  => dropZone.classList.remove('drag-over'));
    dropZone?.addEventListener('drop', e => {
        e.preventDefault();
        dropZone.classList.remove('drag-over');
        const file = e.dataTransfer.files[0];
        if (file && file.type.startsWith('image/')) {
            // Replace file input
            const dt = new DataTransfer();
            dt.items.add(file);
            imgInput.files = dt.files;
            showPreview(file);
        }
    });

});
</script>
@endpush