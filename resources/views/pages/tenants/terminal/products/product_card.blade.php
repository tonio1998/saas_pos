<div
    class="product-card"
    data-id="{{ $product->id }}"
    data-name="{{ $product->name }}"
    data-price="{{ $product->selling_price }}"
    data-stock="{{ $product->stock_on_hand }}"
    data-barcode="{{ $product->barcode }}"
>
    @php
        $stock = $product->stock_on_hand ?? 0;
        $hasImage = !empty($product->image) && file_exists(public_path('storage/' . $product->image));
    @endphp

    <div class="product-image">
        @if($hasImage)
            <img src="{{ Storage::url($product->image) }}" alt="" loading="lazy">
        @else
            <div class="product-no-image d-flex flex-column align-items-center justify-content-center h-100 text-muted">
                <i class="bi bi-box-seam fs-2 text-secondary opacity-50"></i>
                <span class="extra-small text-muted opacity-75 fw-semibold mt-1">Item</span>
            </div>
        @endif

        @if($stock <= 0)
            <div class="product-stock-tag out">Out of Stock</div>
        @elseif($stock <= 10)
            <div class="product-stock-tag low">Low: {{ $stock }}</div>
        @else
            <div class="product-stock-tag in">{{ $stock }} in stock</div>
        @endif
    </div>

    <div class="product-info">
        <div class="product-name" title="{{ $product->name }}">
            {{ $product->name }}
        </div>

        <div class="product-bottom">
            <span class="product-price">
                ₱{{ number_format($product->selling_price, 2) }}
            </span>
            <span class="product-add-badge">
                <i class="bi bi-plus-lg"></i>
            </span>
        </div>
    </div>
</div>
