<div class="search-section">
    <div class="d-flex align-items-center gap-4 p-3">
        <div class="brand-area">
            <div class="brand-logo">
                @if(!empty($settings->app_logo))
                    <img src="{{ asset('storage/' . $settings->app_logo) }}" class="img-fluid">
                @else
                    <i class="bi bi-shop"></i>
                @endif
            </div>
            <div>
                <h4 class="brand-title mb-0">
                    {{ $settings->app_name ?? config('app.name') }}
                </h4>
                <span class="badge app-badge">
                    {{ $settings->business_type ?? 'Retail POS' }}
                </span>
            </div>
        </div>

        <div class="flex-grow-1">
            <input
                id="barcodeSearch"
                class="form-control form-control-lg search-input"
                placeholder="Search product, barcode or SKU..."
                autocomplete="off"
            >
        </div>

        <div class="sale-switcher">

            @if($previousSale)
                <a href="{{ route('sales.create', encryptId($previousSale->id)) }}"
                   class="sale-btn sale-btn-primary d-flex align-items-center justify-content-center">
                    <i class="bi bi-chevron-left fs-2"></i>
                </a>
            @else
                <button
                    class="sale-btn sale-btn-primary d-flex align-items-center justify-content-center"
                    disabled>
                    <i class="bi bi-chevron-left fs-2"></i>
                </button>
            @endif

            <div class="sale-card">
                <small>CURRENT SALE</small>
                <strong>#{{ $sale->sale_code }}</strong>
            </div>

            <a href="{{ route('sales.create1') }}"
               class="sale-btn sale-btn-primary d-flex align-items-center justify-content-center">
                <i class="bi bi-plus-lg fs-2"></i>
            </a>

            @if($nextSale)
                <a href="{{ route('sales.create', encryptId($nextSale->id)) }}"
                   class="sale-btn sale-btn-primary d-flex align-items-center justify-content-center">
                    <i class="bi bi-chevron-right fs-2"></i>
                </a>
            @endif

        </div>
    </div>
</div>
