@extends('layouts.app')
@section('title', isset($product) ? 'Edit Product' : 'Add New Product')
@section('shortText', 'Manage product identity, pricing, inventory health, and variants')

@push('styles')
<style>
    /* Clean Product Form Styles */
    .pf-shell { display: flex; flex-direction: column; gap: 1.25rem; padding-bottom: 2rem; }

    /* Sticky Footer */
    .pf-footer {
        position: sticky; bottom: 1rem; z-index: 1020;
        background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px);
        border: 1px solid #e2e8f0; border-radius: 0.75rem;
        padding: 0.85rem 1.5rem; display: flex; align-items: center; justify-content: space-between;
        box-shadow: 0 4px 16px -2px rgba(0, 0, 0, 0.08);
        margin-top: 0.5rem;
    }
    
    .card-pf { 
        background: #ffffff; 
        border: 1px solid #e2e8f0; 
        border-radius: 0.75rem; 
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04); 
        overflow: hidden; 
    }
    
    .card-pf-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 0.85rem 1.25rem; border-bottom: 1px solid #f1f5f9;
        background: #ffffff;
    }
    
    .card-pf-icon {
        width: 34px; height: 34px; border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem; flex-shrink: 0;
    }
    .card-pf-icon.emerald { background: #d1fae5; color: #059669; }
    .card-pf-icon.amber   { background: #fef3c7; color: #d97706; }
    .card-pf-icon.purple  { background: #ede9fe; color: #7c3aed; }
    .card-pf-icon.sky     { background: #e0f2fe; color: #0284c7; }
    .card-pf-icon.rose    { background: #ffe4e6; color: #e11d48; }
    
    .card-pf-title { font-size: 0.9rem; font-weight: 800; color: #0f172a; line-height: 1.2; }
    .card-pf-subtitle { font-size: 0.72rem; color: #64748b; margin-top: 2px; }
    .card-pf-body { padding: 1.25rem; }

    /* Inputs */
    .form-control, .form-select {
        border-radius: 0.5rem;
        border: 1px solid #cbd5e1;
        padding: 0.45rem 0.75rem;
        font-size: 0.85rem;
    }
    .form-control:focus, .form-select:focus {
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.12);
    }
    .input-group-text {
        background-color: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 0.8rem;
        border: 1px solid #cbd5e1;
    }

    /* Seamless Connected Input Groups */
    .input-group {
        display: flex !important;
        flex-wrap: nowrap !important;
    }
    .input-group > .form-control,
    .input-group > .input-group-text,
    .input-group > .btn {
        border-radius: 0 !important;
    }
    .input-group > :first-child {
        border-top-left-radius: 0.5rem !important;
        border-bottom-left-radius: 0.5rem !important;
    }
    .input-group > :last-child {
        border-top-right-radius: 0.5rem !important;
        border-bottom-right-radius: 0.5rem !important;
    }
    .input-group > :not(:first-child) {
        margin-left: -1px !important;
    }

    /* Variant Table */
    .table-variants {
        width: 100%;
        margin-bottom: 0;
        border-collapse: collapse;
    }
    .table-variants th {
        background-color: #f8fafc;
        color: #475569;
        font-size: 0.72rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        padding: 8px 6px;
        border: 1px solid #e2e8f0;
        vertical-align: middle;
    }
    .table-variants td {
        padding: 6px 4px;
        border: 1px solid #e2e8f0;
        vertical-align: middle;
    }
    .table-variants .form-control,
    .table-variants .form-select {
        font-size: 0.82rem;
        padding: 4px 6px;
        height: 32px;
        border-radius: 4px;
    }

    .variant-delete-btn { 
        color: #ef4444; background: #fef2f2; border: 1px solid #fee2e2; 
        width: 28px; height: 28px; border-radius: 6px; 
        display: inline-flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all 0.15s; font-size: 0.75rem;
    }
    .variant-delete-btn:hover { background: #dc2626; color: #ffffff; border-color: #dc2626; }

    .btn-add-variant {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 0.8rem; font-weight: 700;
        border: 1.5px dashed #cbd5e1; color: #334155;
        background: #ffffff; border-radius: 6px; padding: 6px 14px;
        cursor: pointer; transition: all 0.15s ease;
    }
    .btn-add-variant:hover { border-color: #10b981; color: #10b981; background: #ecfdf5; }

    .bulk-mode-panel { background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 0.5rem; padding: 0.85rem; }

    /* Image Dropzone */
    .image-drop-zone {
        border: 2px dashed #cbd5e1; border-radius: 0.5rem;
        padding: 1.25rem 1rem; text-align: center; cursor: pointer;
        transition: all 0.15s ease; background: #f8fafc; position: relative; overflow: hidden;
    }
    .image-drop-zone:hover, .image-drop-zone.drag-over { border-color: #10b981; background: #ecfdf5; }
    .image-drop-zone input[type=file] { position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%; z-index: 5; }
    .image-preview { max-height: 120px; border-radius: 0.5rem; object-fit: cover; margin-top: 6px; box-shadow: 0 2px 6px rgba(0,0,0,0.08); }

    .profit-badge {
        display: inline-flex; align-items: center; gap: 4px;
        background: #d1fae5; color: #065f46; border-radius: 6px;
        font-size: 0.75rem; font-weight: 700; padding: 4px 8px; border: 1px solid #a7f3d0;
    }
    .profit-badge.loss-badge { background: #fee2e2; color: #991b1b; border-color: #fca5a5; }
</style>
@endpush

@section('content')
@php $isEdit = isset($product); @endphp

<div class="container-fluid px-0">
    
    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
        <div>
            <div class="d-flex align-items-center gap-2.5 mb-1">
                <a href="{{ route('products.index') }}" class="btn btn-white border rounded-3 p-2 text-dark shadow-xs hover-lift d-inline-flex align-items-center justify-content-center" style="width:38px;height:38px;">
                    <i class="bi bi-arrow-left fs-5"></i>
                </a>
                <div>
                    <h4 class="fw-black text-dark mb-0 font-mono" style="letter-spacing:-0.5px;">
                        {{ $isEdit ? 'Edit Product Master' : 'Add New Product SKU' }}
                    </h4>
                </div>
            </div>
            <p class="text-muted extra-small mb-0 ms-5 ps-2">
                @if($isEdit)
                    Modify SKU identity, pricing structures, inventory reorder levels, and variant specifications for <strong>{{ $product->name }}</strong>.
                @else
                    Register a new SKU item in your LikhaPOS store catalog with automated pricing calculations and inventory tracking.
                @endif
            </p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            @if($isEdit)
                <a href="{{ route('products.stock.receive', encrypt($product->id)) }}" class="btn btn-success fw-bold px-3 py-2 rounded-3 shadow-xs d-flex align-items-center gap-1.5 hover-lift" style="background:linear-gradient(135deg, #059669 0%, #047857 100%);border:none;font-size:0.82rem;">
                    <i class="bi bi-box-arrow-in-down fs-6"></i>
                    <span>Stock In Receive</span>
                </a>

                <a href="{{ route('products.stock.history', encrypt($product->id)) }}" class="btn btn-white border rounded-3 px-3 py-2 fw-bold text-dark extra-small shadow-xs hover-lift d-flex align-items-center gap-1.5">
                    <i class="bi bi-clock-history text-primary"></i>
                    <span>Stock Audit Logs</span>
                </a>
            @endif

            <a href="{{ route('products.index') }}" class="btn btn-white border rounded-3 px-3 py-2 fw-bold text-dark extra-small shadow-xs hover-lift">
                <i class="bi bi-grid-fill text-secondary me-1"></i> Catalog Masterlist
            </a>
        </div>
    </div>

    @if($isEdit)
        {{-- KPI Metrics Banner for Edit Mode --}}
        <div class="row g-3 mb-3">
            <div class="col-6 col-md-3">
                <div class="card-pf p-3 h-100 bg-white">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="text-muted extra-small fw-bold text-uppercase">On-Hand Stock</span>
                        <div class="rounded-3 p-1.5 bg-emerald bg-opacity-10 text-emerald d-flex align-items-center justify-content-center" style="width:30px;height:30px;">
                            <i class="bi bi-box-seam-fill text-emerald fs-6"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-1.5">
                        <h4 class="fw-black text-dark font-mono mb-0">{{ number_format($product->stock_on_hand) }}</h4>
                        <span class="text-muted extra-small">units</span>
                    </div>
                    <div class="mt-1.5">
                        @if($product->stock_on_hand <= 0)
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-0.5 extra-small fw-bold"><i class="bi bi-x-circle-fill me-1"></i>Depleted (0)</span>
                        @elseif($product->stock_on_hand <= ($product->reorder_level ?? 10))
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-0.5 extra-small fw-bold"><i class="bi bi-exclamation-triangle-fill me-1"></i>Low Stock</span>
                        @else
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5 extra-small fw-bold"><i class="bi bi-check-circle-fill me-1"></i>Healthy</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card-pf p-3 h-100 bg-white">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="text-muted extra-small fw-bold text-uppercase">Capital Cost</span>
                        <div class="rounded-3 p-1.5 bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width:30px;height:30px;">
                            <i class="bi bi-tag-fill text-primary fs-6"></i>
                        </div>
                    </div>
                    <h4 class="fw-black text-dark font-mono mb-0">₱{{ number_format($product->cost_price, 2) }}</h4>
                    <div class="text-muted extra-small mt-1.5 font-mono">Base unit cost</div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card-pf p-3 h-100 bg-white">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="text-muted extra-small fw-bold text-uppercase">Selling Price</span>
                        <div class="rounded-3 p-1.5 bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center" style="width:30px;height:30px;">
                            <i class="bi bi-cash-stack text-warning fs-6"></i>
                        </div>
                    </div>
                    <h4 class="fw-black text-success font-mono mb-0">₱{{ number_format($product->selling_price, 2) }}</h4>
                    @php
                        $margin = $product->selling_price > 0 ? round((($product->selling_price - $product->cost_price) / $product->selling_price) * 100, 1) : 0;
                    @endphp
                    <div class="text-success extra-small mt-1.5 fw-bold font-mono">
                        <i class="bi bi-graph-up-arrow me-1"></i>{{ $margin }}% Margin
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card-pf p-3 h-100 bg-white">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="text-muted extra-small fw-bold text-uppercase">Asset Valuation</span>
                        <div class="rounded-3 p-1.5 bg-purple bg-opacity-10 text-purple d-flex align-items-center justify-content-center" style="width:30px;height:30px;">
                            <i class="bi bi-wallet2 text-purple fs-6"></i>
                        </div>
                    </div>
                    <h4 class="fw-black text-purple font-mono mb-0">₱{{ number_format($product->stock_on_hand * $product->cost_price, 2) }}</h4>
                    <div class="text-muted extra-small mt-1.5 font-mono">Total inventory value</div>
                </div>
            </div>
        </div>
    @endif

    {{-- Main Product Form --}}
    <form
        id="productForm"
        method="POST"
        enctype="multipart/form-data"
        action="{{ $isEdit ? route('products.update', encrypt($product->id)) : route('products.store') }}"
    >
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div class="pf-shell">
            
            <div class="row g-3">
                
                {{-- LEFT COLUMN: Product Info & Pricing --}}
                <div class="col-lg-8">
                    <div class="d-flex flex-column gap-3">
                        
                        {{-- SECTION 1: Product Master Information --}}
                        <div class="card-pf">
                            <div class="card-pf-header">
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="card-pf-icon emerald"><i class="bi bi-box-seam-fill"></i></div>
                                    <div>
                                        <div class="card-pf-title">Product Identity & Categorization</div>
                                        <div class="card-pf-subtitle">Basic details, barcodes, and master unit classification</div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-pf-body">
                                <div class="row g-3">
                                    
                                    {{-- Product Name --}}
                                    <div class="col-12 position-relative">
                                        <label class="form-label extra-small fw-bold text-dark text-uppercase mb-1">Product Name <span class="text-danger">*</span></label>
                                        <input
                                            id="productName"
                                            name="name"
                                            type="text"
                                            class="form-control fw-bold @error('name') is-invalid @enderror"
                                            value="{{ old('name', $product->name ?? '') }}"
                                            placeholder="e.g. Premium White Rice, Sunflower Cooking Oil..."
                                            autocomplete="off"
                                            required
                                        >
                                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        <div id="productSuggestions" class="product-suggestions d-none"></div>
                                    </div>

                                    {{-- Barcode --}}
                                    <div class="col-md-6">
                                        <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">Barcode / EAN</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-barcode"></i></span>
                                            <input
                                                id="barcode"
                                                name="barcode"
                                                type="text"
                                                class="form-control font-mono"
                                                value="{{ old('barcode', $product->barcode ?? '') }}"
                                                placeholder="Scan or enter barcode"
                                                autocomplete="off"
                                            >
                                            <button type="button" class="btn btn-light border px-3 extra-small fw-bold text-dark" id="scanBarcodeBtn" title="Scan barcode">
                                                <i class="bi bi-camera-fill text-primary me-1"></i> Scan
                                            </button>
                                        </div>
                                        <div id="scanFeedback" class="text-muted extra-small mt-1 d-none"></div>
                                    </div>

                                    {{-- SKU --}}
                                    <div class="col-md-6">
                                        <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">SKU Code</label>
                                        <div class="input-group">
                                            <input
                                                id="sku"
                                                name="sku"
                                                type="text"
                                                class="form-control font-mono"
                                                value="{{ old('sku', $product->sku ?? '') }}"
                                                placeholder="Stock Keeping Unit"
                                            >
                                            <button type="button" class="btn btn-light border px-3 extra-small fw-bold text-dark" id="genSkuBtn" title="Auto-generate SKU">
                                                <i class="bi bi-magic text-warning me-1"></i> Auto
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Category --}}
                                    <div class="col-md-6">
                                        <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">Category <span class="text-danger">*</span></label>
                                        <select id="category_id" name="category_id" class="form-select select2 @error('category_id') is-invalid @enderror" data-placeholder="Select Category" required>
                                            <option value="">Select Category</option>
                                            @foreach($categories ?? [] as $cat)
                                                <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                                                    {{ $cat->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    {{-- Base Unit --}}
                                    <div class="col-md-6">
                                        <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">Base Unit <span class="text-danger">*</span></label>
                                        <select id="unit_id" name="unit_id" class="form-select select2 @error('unit_id') is-invalid @enderror" data-placeholder="Select Unit (e.g. pcs, kg, pack)" required>
                                            <option value="">Select Unit (e.g. pcs, kg, pack)</option>
                                            @foreach($units ?? [] as $unit)
                                                <option value="{{ $unit->id }}" {{ old('unit_id', $product->unit_id ?? '') == $unit->id ? 'selected' : '' }}>
                                                    {{ $unit->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('unit_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    {{-- Description --}}
                                    <div class="col-12">
                                        <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">Product Description</label>
                                        <textarea
                                            id="description"
                                            name="description"
                                            rows="2"
                                            class="form-control"
                                            placeholder="Optional product specifications, details, or brand info..."
                                        >{{ old('description', $product->description ?? '') }}</textarea>
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- SECTION 2: Pricing Structure --}}
                        <div class="card-pf">
                            <div class="card-pf-header">
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="card-pf-icon amber"><i class="bi bi-cash-coin"></i></div>
                                    <div>
                                        <div class="card-pf-title">Base Pricing & Profit Margins</div>
                                        <div class="card-pf-subtitle">Cost capital, retail price, and wholesale tier</div>
                                    </div>
                                </div>
                                
                                {{-- Smart Bulk Mode Toggle --}}
                                <div class="d-flex align-items-center gap-2 bg-light border px-2.5 py-1 rounded-pill">
                                    <label class="form-check-label extra-small fw-bold text-dark mb-0" for="bulkModeToggle">Smart Bulk Mode</label>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" id="bulkModeToggle" role="switch">
                                    </div>
                                </div>
                            </div>
                            <div class="card-pf-body">

                                {{-- Bulk mode panel --}}
                                <div id="bulkModePanel" class="bulk-mode-panel mb-3 d-none">
                                    <div class="row g-3 align-items-center">
                                        <div class="col-md-5">
                                            <label class="form-label extra-small fw-bold text-emerald text-uppercase mb-1">Base Cost per Unit (e.g. per kg)</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white">₱</span>
                                                <input type="number" id="baseCostPerUnit" class="form-control font-mono fw-bold" step="0.01" min="0" placeholder="0.00">
                                            </div>
                                        </div>
                                        <div class="col-md-7">
                                            <div class="text-emerald extra-small lh-sm">
                                                <i class="bi bi-lightbulb-fill text-warning me-1"></i>
                                                Auto-computes variant cost based on packaging quantity (Qty × Base Cost).
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    {{-- Cost Price --}}
                                    <div class="col-md-4">
                                        <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">Capital Cost <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">₱</span>
                                            <input type="number" id="cost_price" name="cost_price" step="0.01" min="0"
                                                class="form-control font-mono @error('cost_price') is-invalid @enderror"
                                                value="{{ old('cost_price', $product->cost_price ?? 0) }}" required>
                                        </div>
                                        @error('cost_price') <div class="text-danger extra-small mt-1">{{ $message }}</div> @enderror
                                    </div>

                                    {{-- Retail Price --}}
                                    <div class="col-md-4">
                                        <label class="form-label extra-small fw-bold text-success text-uppercase mb-1">
                                            Retail Price (POS Selling) <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-success bg-opacity-10 text-success fw-bold">₱</span>
                                            <input type="number" id="selling_price" name="selling_price" step="0.01" min="0"
                                                class="form-control font-mono fw-black text-success @error('selling_price') is-invalid @enderror"
                                                value="{{ old('selling_price', $product->selling_price ?? 0) }}" required>
                                        </div>
                                        @error('selling_price') <div class="text-danger extra-small mt-1">{{ $message }}</div> @enderror
                                    </div>

                                    {{-- Wholesale Price --}}
                                    <div class="col-md-4">
                                        <label class="form-label extra-small fw-bold text-primary text-uppercase mb-1">
                                            Wholesale Price
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-primary bg-opacity-10 text-primary fw-bold">₱</span>
                                            <input type="number" id="wholesale_price" name="wholesale_price" step="0.01" min="0"
                                                class="form-control font-mono fw-bold"
                                                value="{{ old('wholesale_price', $product->wholesale_price ?? 0) }}"
                                                placeholder="Optional">
                                        </div>
                                    </div>

                                    {{-- Profit Margin Real-time Preview --}}
                                    <div class="col-12">
                                        <div id="profitPreview" class="d-flex flex-wrap align-items-center gap-2 pt-1"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- RIGHT COLUMN: Media & Inventory Settings --}}
                <div class="col-lg-4">
                    <div class="d-flex flex-column gap-3">
                        
                        {{-- Image Card --}}
                        <div class="card-pf">
                            <div class="card-pf-header">
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="card-pf-icon sky"><i class="bi bi-image-fill"></i></div>
                                    <div>
                                        <div class="card-pf-title">Product Image</div>
                                        <div class="card-pf-subtitle">POS terminal display photo</div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-pf-body">
                                <div class="image-drop-zone" id="imageDropZone">
                                    <input type="file" name="image" id="imageInput" accept="image/*">
                                    <div id="imageDropContent" style="{{ !empty($product?->image) ? 'display:none' : '' }}">
                                        <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center p-2.5 mb-1.5">
                                            <i class="bi bi-cloud-arrow-up-fill fs-3 text-primary"></i>
                                        </div>
                                        <div class="fw-bold text-dark extra-small">Drag & drop photo here</div>
                                        <div class="text-muted extra-small">or click to browse</div>
                                        <span class="badge bg-light text-muted border font-mono mt-1.5" style="font-size:0.65rem;">JPG, PNG, WEBP &le; 2MB</span>
                                    </div>
                                    @if(!empty($product?->image))
                                        <img src="{{ asset('storage/'.$product->image) }}" class="image-preview" id="imagePreview" alt="Product Image">
                                    @else
                                        <img src="" class="image-preview d-none" id="imagePreview" alt="Product Image">
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Reorder Alert --}}
                        <div class="card-pf">
                            <div class="card-pf-header">
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="card-pf-icon rose"><i class="bi bi-shield-exclamation"></i></div>
                                    <div>
                                        <div class="card-pf-title">Reorder Level Alert</div>
                                        <div class="card-pf-subtitle">Automated low-stock warnings</div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-pf-body">
                                <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">Reorder Level Quantity</label>
                                <div class="input-group">
                                    <input type="number" name="reorder_level" min="0"
                                        class="form-control font-mono fw-bold"
                                        value="{{ old('reorder_level', $product->reorder_level ?? 10) }}"
                                        placeholder="10">
                                    <span class="input-group-text">units</span>
                                </div>
                                <div class="text-muted extra-small mt-1.5">
                                    <i class="bi bi-info-circle me-1"></i>Triggers warning when stock reaches this level.
                                </div>
                            </div>
                        </div>

                        {{-- Fractional Item Switch --}}
                        <div class="card-pf p-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <label class="form-check-label fw-bold text-dark mb-0 extra-small" for="allowDecimalQty">
                                        Weighed / Fractional Item
                                    </label>
                                    <div class="text-muted extra-small">Enable decimals in POS (e.g. 0.25kg, 1.5kg)</div>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" name="allow_decimal_qty" id="allowDecimalQty" value="1"
                                        {{ old('allow_decimal_qty', $product->allow_decimal_qty ?? false) ? 'checked' : '' }} role="switch">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- SECTION 3: Multi-Variants & Pack Sizes (FULL-WIDTH GRID-12) --}}
                <div class="col-12">
                    <div class="card-pf">
                        <div class="card-pf-header">
                            <div class="d-flex align-items-center gap-2.5">
                                <div class="card-pf-icon purple"><i class="bi bi-boxes"></i></div>
                                <div>
                                    <div class="card-pf-title">Multi-Variants & Pack Sizes</div>
                                    <div class="card-pf-subtitle">Configure different sizes, bundles, or packaging with custom barcodes and prices</div>
                                </div>
                            </div>
                            
                            {{-- Variant Enable Switch --}}
                            <div class="d-flex align-items-center gap-2 bg-light border px-2.5 py-1 rounded-pill">
                                <label class="form-check-label extra-small fw-bold text-dark mb-0" for="variantToggle">Enable Variants</label>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" id="variantToggle" role="switch"
                                        {{ count($variants ?? []) > 0 ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>
                        <div class="card-pf-body" id="variantPanel" style="{{ count($variants ?? []) == 0 ? 'display:none' : '' }}">

                            <div class="alert alert-purple bg-purple-subtle border-0 rounded-3 p-2.5 mb-2.5 d-flex align-items-center gap-2">
                                <i class="bi bi-info-circle-fill text-purple fs-5"></i>
                                <span class="extra-small text-dark">
                                    Example: Product = <strong>White Rice</strong> &rarr; Variants = <em>1kg Pack</em>, <em>5kg Sack</em>, <em>25kg Sack</em> — each with dedicated barcode and custom price.
                                </span>
                            </div>

                            <div class="table-responsive">
                                <table class="table-variants" id="variantTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 20%;">Variant Name</th>
                                            <th style="width: 10%;">Stock</th>
                                            <th style="width: 8%;">Qty/Pack</th>
                                            <th style="width: 14%;">Barcode</th>
                                            <th style="width: 13%;">Cost (₱)</th>
                                            <th style="width: 13%;">Selling (₱)</th>
                                            <th style="width: 13%;">Wholesale (₱)</th>
                                            <th style="width: 5%;">Unit</th>
                                            <th style="width: 4%; text-align: center;">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="variantBody">
                                        @foreach($variants ?? [] as $i => $v)
                                        @php $hasRecords = isset($variantsWithRecords) && $variantsWithRecords->contains($v->id); @endphp
                                        <tr class="variant-row {{ ($v->status ?? 'active') === 'inactive' ? 'opacity-50' : '' }}" data-index="{{ $i }}" data-existing="1">
                                            <td>
                                                <input type="hidden" name="variants[{{ $i }}][id]" value="{{ $v->id }}">
                                                <input type="text" name="variants[{{ $i }}][variant_name]"
                                                    class="form-control variant-name" placeholder="e.g. 2.5kg Pack"
                                                    value="{{ $v->variant_name }}" required>
                                            </td>
                                            <td>
                                                <input type="number" name="variants[{{ $i }}][stock_on_hand]"
                                                    class="form-control font-mono fw-bold text-primary" step="any" min="0"
                                                    value="{{ $v->stock_on_hand !== null ? floatval($v->stock_on_hand) : 0 }}" placeholder="0">
                                            </td>
                                            <td>
                                                <input type="number" name="variants[{{ $i }}][qty_per_pack]"
                                                    class="form-control variant-qty font-mono" step="any" min="0.0001"
                                                    value="{{ $v->qty_per_pack !== null ? floatval($v->qty_per_pack) : 1 }}" required>
                                            </td>
                                            <td>
                                                <input type="text" name="variants[{{ $i }}][barcode]"
                                                    class="form-control font-mono" placeholder="Optional"
                                                    value="{{ $v->barcode }}">
                                            </td>
                                            <td>
                                                <input type="number" name="variants[{{ $i }}][cost_price]"
                                                    class="form-control variant-cost font-mono" step="any" min="0"
                                                    value="{{ $v->cost_price !== null ? floatval($v->cost_price) : 0 }}" placeholder="0.00" required>
                                            </td>
                                            <td>
                                                <input type="number" name="variants[{{ $i }}][selling_price]"
                                                    class="form-control font-mono fw-bold text-success" step="any" min="0"
                                                    value="{{ $v->selling_price !== null ? floatval($v->selling_price) : 0 }}" placeholder="0.00" required>
                                            </td>
                                            <td>
                                                <input type="number" name="variants[{{ $i }}][wholesale_price]"
                                                    class="form-control font-mono" step="any" min="0"
                                                    value="{{ $v->wholesale_price !== null ? floatval($v->wholesale_price) : '' }}" placeholder="0.00">
                                            </td>
                                            <td>
                                                <select name="variants[{{ $i }}][unit_id]" class="form-select">
                                                    <option value="">—</option>
                                                    @foreach($units ?? [] as $unit)
                                                        <option value="{{ $unit->id }}" {{ $v->unit_id == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="text-center">
                                                <input type="hidden" name="variants[{{ $i }}][status]" value="{{ $v->status ?? 'active' }}" class="variant-status-input">
                                                @php $isActive = ($v->status ?? 'active') === 'active'; @endphp
                                                <button type="button"
                                                    class="variant-status-btn btn btn-sm rounded-pill px-2.5 py-1 extra-small fw-bold {{ $isActive ? 'btn-success' : 'btn-secondary' }}"
                                                    data-status="{{ $isActive ? 'active' : 'inactive' }}"
                                                    title="Toggle Active / Inactive (existing variants preserved to safeguard records)">
                                                    <i class="bi {{ $isActive ? 'bi-check-circle-fill' : 'bi-pause-circle-fill' }} me-1"></i>
                                                    {{ $isActive ? 'Active' : 'Off' }}
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-2.5">
                                <button type="button" id="addVariantBtn" class="btn-add-variant">
                                    <i class="bi bi-plus-circle-fill text-success fs-6"></i> Add Variant Row
                                </button>
                            </div>

                            {{-- Hidden template row (cloned by JS) --}}
                            <template id="variantRowTemplate">
                                <tr class="variant-row" data-existing="0">
                                    <td>
                                        <input type="hidden" name="variants[__IDX__][id]" value="">
                                        <input type="text" name="variants[__IDX__][variant_name]"
                                            class="form-control variant-name" placeholder="e.g. 2.5kg Pack" required>
                                    </td>
                                    <td>
                                        <input type="number" name="variants[__IDX__][stock_on_hand]"
                                            class="form-control font-mono fw-bold text-primary" step="0.0001" min="0" value="0" placeholder="0">
                                    </td>
                                    <td>
                                        <input type="number" name="variants[__IDX__][qty_per_pack]"
                                            class="form-control variant-qty font-mono" step="0.0001" min="0.0001" value="1" required>
                                    </td>
                                    <td>
                                        <input type="text" name="variants[__IDX__][barcode]"
                                            class="form-control font-mono" placeholder="Optional">
                                    </td>
                                    <td>
                                        <input type="number" name="variants[__IDX__][cost_price]"
                                            class="form-control variant-cost font-mono" step="0.01" min="0" value="0" placeholder="0.00" required>
                                    </td>
                                    <td>
                                        <input type="number" name="variants[__IDX__][selling_price]"
                                            class="form-control font-mono fw-bold text-success" step="0.01" min="0" value="0" placeholder="0.00" required>
                                    </td>
                                    <td>
                                        <input type="number" name="variants[__IDX__][wholesale_price]"
                                            class="form-control font-mono" step="0.01" min="0" value="" placeholder="0.00">
                                    </td>
                                    <td>
                                        <select name="variants[__IDX__][unit_id]" class="form-select">
                                            <option value="">—</option>
                                            @foreach($units ?? [] as $unit)
                                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    {{-- NEW (unsaved) variant: allow delete since no records yet --}}
                                    <td class="text-center">
                                        <input type="hidden" name="variants[__IDX__][status]" value="active">
                                        <button type="button" class="variant-delete-btn" title="Remove this new variant">
                                            <i class="bi bi-trash3-fill"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>

                        </div>
                    </div>
                </div>

            </div>

        </div>

        {{-- Sticky Action Footer --}}
        <div class="pf-footer">
            <a href="{{ route('products.index') }}" class="btn btn-light border px-4 py-2 rounded-3 fw-bold text-dark hover-lift">
                <i class="bi bi-arrow-left me-1.5"></i> Cancel & Back
            </a>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted extra-small d-none d-md-block">Required fields are marked with <span class="text-danger">*</span></span>
                <button type="submit" id="submitBtn" class="btn btn-success px-4 py-2 rounded-3 fw-black text-white shadow-sm hover-lift" style="background:linear-gradient(135deg, #059669 0%, #047857 100%);border:none;font-size:0.875rem;">
                    <i class="bi bi-check-circle-fill me-1.5" id="submitBtnIcon"></i>
                    <span id="submitBtnText">{{ $isEdit ? 'Update Product SKU' : 'Save Product SKU' }}</span>
                </button>
            </div>
        </div>

    </form>
</div>
@endsection

@push('scripts')
@vite('resources/js/pages/pos/product-suggestions.js')

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Select2 Initialization for Category & Unit ────────
    if (window.$ && $.fn && $.fn.select2) {
        $('.select2').each(function () {
            const ph = $(this).attr('data-placeholder') || 'Select option';
            $(this).select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: ph,
                allowClear: true
            });
        });
    }

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
            const wsCls    = wsProfit >= 0 ? 'badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1.5 rounded-3 fw-bold font-mono' : 'profit-badge loss-badge';
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
        if (this.checked && variantBody && variantBody.children.length === 0) {
            document.getElementById('addVariantBtn')?.click();
        } else if (!this.checked) {
            document.querySelectorAll('#variantBody .variant-row').forEach(r => r.remove());
        }
    });

    // ── Add Variant Row ───────────────────────────────────
    let variantIndex = {{ count($variants ?? []) }};
    const variantBody    = document.getElementById('variantBody');
    const rowTemplate    = document.getElementById('variantRowTemplate');

    document.getElementById('addVariantBtn')?.addEventListener('click', function () {
        if (!rowTemplate || !variantBody) return;
        const html = rowTemplate.innerHTML.replaceAll('__IDX__', variantIndex);
        const tempContainer = document.createElement('tbody');
        tempContainer.innerHTML = html;
        const newRow = tempContainer.firstElementChild;
        if (newRow) {
            variantBody.appendChild(newRow);
            variantIndex++;
            if (typeof applyBulkCostToVariants === 'function') {
                applyBulkCostToVariants();
            }
        }
    });

    // ── Delete Variant Row ────────────────────────────────
    variantBody?.addEventListener('click', function (e) {
        const btn = e.target.closest('.variant-delete-btn');
        if (!btn) return;
        btn.closest('.variant-row').remove();
    });

    // ── Toggle Variant Status ─────────────────────────────
    variantBody?.addEventListener('click', function (e) {
        const btn = e.target.closest('.variant-status-btn');
        if (!btn) return;
        const row = btn.closest('.variant-row');
        const input = row.querySelector('.variant-status-input');
        const currentStatus = btn.dataset.status;
        const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
        
        btn.dataset.status = newStatus;
        if (input) input.value = newStatus;

        if (newStatus === 'active') {
            btn.className = 'variant-status-btn btn btn-sm rounded-pill px-2 py-0.5 extra-small fw-bold btn-success';
            btn.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>Active';
            row.classList.remove('opacity-50');
        } else {
            btn.className = 'variant-status-btn btn btn-sm rounded-pill px-2 py-0.5 extra-small fw-bold btn-secondary';
            btn.innerHTML = '<i class="bi bi-pause-circle-fill me-1"></i>Off';
            row.classList.add('opacity-50');
        }
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
            const dt = new DataTransfer();
            dt.items.add(file);
            imgInput.files = dt.files;
            showPreview(file);
        }
    });

    // ── AJAX Form Submission ──────────────────────────────
    const productForm = document.getElementById('productForm');
    const submitBtn = document.getElementById('submitBtn');
    const submitBtnIcon = document.getElementById('submitBtnIcon');
    const submitBtnText = document.getElementById('submitBtnText');

    productForm?.addEventListener('submit', async function (e) {
        e.preventDefault();

        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback-custom').forEach(el => el.remove());

        const originalText = submitBtnText.textContent;
        submitBtn.disabled = true;
        if (submitBtnIcon) submitBtnIcon.className = 'spinner-border spinner-border-sm me-2';
        submitBtnText.textContent = 'Saving Product SKU...';

        const formData = new FormData(this);

        try {
            const response = await fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (response.ok && (data.status === 'success' || response.status === 200)) {
                if (submitBtnIcon) submitBtnIcon.className = 'bi bi-check-circle-fill me-2';
                submitBtnText.textContent = 'Saved! Redirecting...';

                if (typeof appAlert === 'function') {
                    await appAlert({
                        title: 'Product Saved!',
                        text: data.message || 'Product SKU saved successfully.',
                        type: 'success',
                        confirmText: 'Redirecting...'
                    });
                }

                setTimeout(() => {
                    window.location.href = data.redirect_url || "{{ route('products.index') }}";
                }, 1500);
            } else {
                submitBtn.disabled = false;
                if (submitBtnIcon) submitBtnIcon.className = 'bi bi-check-circle-fill me-1.5';
                submitBtnText.textContent = originalText;

                if (response.status === 422 && data.errors) {
                    let errorList = [];
                    for (const [field, messages] of Object.entries(data.errors)) {
                        const input = document.querySelector(`[name="${field}"]`) || document.querySelector(`[name="${field}[]"]`);
                        if (input) {
                            input.classList.add('is-invalid');
                        }
                        errorList.push(...messages);
                    }
                    
                    if (typeof appAlert === 'function') {
                        appAlert({
                            title: 'Validation Failed',
                            text: errorList.join('\n'),
                            type: 'error'
                        });
                    } else {
                        alert(errorList.join('\n'));
                    }
                } else {
                    const errorMsg = data.message || 'An error occurred while saving product.';
                    if (typeof appAlert === 'function') {
                        appAlert({ title: 'Save Failed', text: errorMsg, type: 'error' });
                    } else {
                        alert(errorMsg);
                    }
                }
            }
        } catch (err) {
            console.error('AJAX product save error:', err);
            submitBtn.disabled = false;
            if (submitBtnIcon) submitBtnIcon.className = 'bi bi-check-circle-fill me-1.5';
            submitBtnText.textContent = originalText;
            
            if (typeof appAlert === 'function') {
                appAlert({ title: 'Network Error', text: 'Failed to connect to server. Please check your network.', type: 'error' });
            } else {
                alert('Network Error. Failed to submit form.');
            }
        }
    });

});
</script>
@endpush