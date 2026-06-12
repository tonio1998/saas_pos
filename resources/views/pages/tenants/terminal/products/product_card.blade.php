<div
    class="product-card"
    data-id="{{ $product->id }}"
    data-name="{{ $product->name }}"
    data-price="{{ $product->selling_price }}"
    data-stock="{{ $product->stock_on_hand }}"
    data-barcode="{{ $product->barcode }}"
>
    <div class="product-image">
        <img
            src="{{ $product->image ? Storage::url($product->image) : asset('images/no_image.jpg') }}"
            alt="{{ $product->name }}"
        >
    </div>

    <div class="product-info">

        <div class="product-name">
            {{ $product->name }}
        </div>

        @php
            $stock = $product->stock_on_hand ?? 0;
        @endphp

        <div class="product-bottom">

            <span class="product-price">
                ₱{{ number_format($product->selling_price, 2) }} / {{ $stock }}
            </span>
            <span class="stock-badge out">
                    0
                </span>
            @if($stock <= 0)

                <span class="stock-badge out">
                    0
                </span>

            @elseif($stock <= 10)

                <span class="stock-badge low">
                    {{ $stock }}
                </span>

            @else

                <span class="stock-badge in">
                    {{ $stock }}
                </span>

            @endif

        </div>

    </div>
</div>
