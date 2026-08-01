<div class="search-section">
    <div class="d-flex align-items-center gap-4 p-3 pb-0 pt-0">
        <a href="{{ route('dashboard.index') }}"
           class="sale-btn sale-btn-secondary d-flex align-items-center justify-content-center"
           title="Home">

            <i class="bi bi-house-door-fill fs-4"></i>

        </a>

        <div class="brand-area">
            <div>
                <img src="{{ asset('images/logo.png') }}" class="img-fluid"
                     style="width:220px;height:100px;object-fit:contain;">
            </div>
        </div>

        <div class="flex-grow-1 d-flex align-items-center gap-3">
            <input
                id="barcodeSearch"
                class="form-control form-control-lg search-input"
                placeholder="Search product, barcode or SKU..."
                autocomplete="off">
            <div class="theme-toggle">
                <span>Light</span>
                <label class="theme-switch">
                    <input
                        type="checkbox"
                        id="themeToggle">
                    <span class="theme-slider">
                        <i class="bi bi-sun-fill"></i>
                        <i class="bi bi-moon-stars-fill"></i>
                    </span>
                </label>
                <span>Dark</span>
            </div>
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

            <a href="{{ route('sales.create', [encryptId(session('sale_id')), 'q=new']) }}"
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

    <div class="saleStatusContainer"></div>

</div>
