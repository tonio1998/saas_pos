@if($isSalePaid ?? false)
    <div class="border-bottom px-3 py-2 d-flex align-items-center justify-content-between flex-wrap gap-2" style="background:#fffbeb;border-color:#fef3c7;flex-shrink:0;">
        <div class="d-flex align-items-center gap-2">
            <div class="rounded-circle bg-warning p-1 text-white d-flex align-items-center justify-content-center" style="width:26px;height:26px;">
                <i class="bi bi-lock-fill small"></i>
            </div>
            <div>
                <strong class="text-dark font-mono small">Viewing Past Paid Transaction #{{ $sale->sale_code }}</strong>
                <span class="text-muted extra-small d-block">Items and products are locked in view-only mode.</span>
            </div>
        </div>
        <a href="{{ route('sales.create', [encryptId(session('sale_id')), 'q=new']) }}" class="btn btn-sm btn-success rounded-pill fw-bold extra-small px-3 shadow-xs" style="background:#059669;border:none;">
            <i class="bi bi-plus-lg me-1"></i> Start New Sale
        </a>
    </div>
@endif

<div class="d-flex align-items-center justify-content-between px-3 py-2 bg-white border-bottom shadow-xs gap-2" style="min-height: 48px; flex-shrink: 0;">
    <!-- Category Filter Chips -->
    <div class="category-section d-flex align-items-center gap-1.5 overflow-x-auto py-1" id="categoryContainer" style="scrollbar-width: none;">
        <button class="category-chip active btn btn-sm btn-light border extra-small fw-bold px-3 py-1 rounded-pill" data-category="">All</button>
        @if(isset($categories))
            @foreach($categories as $category)
                <button class="category-chip btn btn-sm btn-light border extra-small fw-bold px-3 py-1 rounded-pill" data-category="{{ $category->id }}">{{ $category->name }}</button>
            @endforeach
        @endif
    </div>

    <!-- View Mode Switcher Toggle (Grid vs Table) -->
    <div class="d-flex align-items-center gap-1 flex-shrink-0 bg-light border p-1 rounded-pill shadow-xs ms-2">
        <button type="button" class="btn btn-sm py-1 px-2.5 rounded-pill extra-small fw-bold view-mode-btn active" data-mode="table" id="btnViewTable" title="Table View">
            <i class="bi bi-table me-1"></i>Table
        </button>
        <button type="button" class="btn btn-sm py-1 px-2.5 rounded-pill extra-small fw-bold text-muted view-mode-btn" data-mode="grid" id="btnViewGrid" title="Card Grid View">
            <i class="bi bi-grid-3x3-gap-fill me-1"></i>Grid
        </button>
    </div>
</div>

<div id="productContainer" class="products-table-wrapper flex-grow-1 overflow-y-auto"></div>
