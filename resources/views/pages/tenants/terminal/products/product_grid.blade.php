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
