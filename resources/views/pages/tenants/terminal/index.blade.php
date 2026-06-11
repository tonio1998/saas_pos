@extends('layouts.pos')

@section('title', 'POS Terminal')

@section('content')

    <div class="pos-shell">

        <div class="pos-topbar">

            <div class="pos-search">

                <div class="input-group input-group-lg">

                <span class="input-group-text">
                    <i class="bi bi-upc-scan"></i>
                </span>

                    <input
                        type="text"
                        id="barcodeSearch"
                        class="form-control"
                        placeholder="Scan barcode or search product..."
                        autofocus
                    >

                </div>

            </div>

            <div class="pos-meta">

                <div class="meta-card">
                    <small>Terminal</small>
                    <strong>POS-01</strong>
                </div>

                <div class="meta-card">
                    <small>Cashier</small>
                    <strong>{{ auth()->user()->name }}</strong>
                </div>

                <div class="meta-card">
                    <small>Transaction</small>
                    <strong>#000001</strong>
                </div>

            </div>

        </div>

        <div class="pos-body">

            <aside class="pos-sidebar">

                <div class="sidebar-section">

                    <div class="section-title">
                        Categories
                    </div>

                    <div class="category-list">

                        <button class="category-pill active">
                            All
                        </button>

                        <button class="category-pill">
                            Beverages
                        </button>

                        <button class="category-pill">
                            Snacks
                        </button>

                        <button class="category-pill">
                            Frozen
                        </button>

                        <button class="category-pill">
                            Pharmacy
                        </button>

                        <button class="category-pill">
                            Personal Care
                        </button>

                    </div>

                </div>

                <div class="sidebar-section">

                    <div class="section-title">
                        Quick Actions
                    </div>

                    <div class="d-grid gap-2">

                        <button class="btn btn-light">
                            F2 Inquiry
                        </button>

                        <button class="btn btn-light">
                            F4 Discount
                        </button>

                        <button class="btn btn-light">
                            F5 Hold
                        </button>

                        <button class="btn btn-light">
                            F6 Recall
                        </button>

                        <button class="btn btn-light">
                            F7 Customer
                        </button>

                        <button class="btn btn-light">
                            F8 Refund
                        </button>

                    </div>

                </div>

            </aside>

            <main class="pos-products">

                <div class="products-header">

                    <div>
                        <h5 class="mb-0">
                            Products
                        </h5>

                        <small class="text-muted">
                            Available Items
                        </small>
                    </div>

                    <div class="text-muted">
                        1,245 Products
                    </div>

                </div>

                <div class="products-grid">
                    @foreach($products as $product)
                        <button
                            class="product-card"
                            data-id="{{ $product->id }}"
                            data-barcode="{{ $product->barcode }}"
                            data-name="{{ $product->name }}"
                            data-price="{{ $product->selling_price }}"
                            data-image="{{ $product->image }}"
                        >

                            <div class="product-image">

                                <img
                                    src="{{
            $product->image
                ? Storage::url($product->image)
                : asset('images/no_image.jpg')
        }}"
                                    alt="{{ $product->name }}"
                                >

                            </div>

                            <div class="product-name">
                                {{ $product->name }}
                            </div>

                            <div class="product-price">
                                {{ $product->selling_price }}
                            </div>

                            <div class="product-stock">
                                Stock: 125
                            </div>

                        </button>
                    @endforeach
                </div>

            </main>

            <aside class="pos-cart">

                <div class="cart-header">

                    <div>

                        <h5 class="mb-0">
                            Current Sale
                        </h5>

                        <small class="text-muted">
                            3 Items
                        </small>

                    </div>

                    <button class="btn btn-sm btn-danger">
                        Clear
                    </button>

                </div>

                <div class="cart-items">

                    <div class="cart-item">

                        <div class="cart-item-top">

                            <div>

                                <div class="cart-name">
                                    Coke 1.5L
                                </div>

                                <div class="cart-meta">
                                    Barcode: 480001
                                </div>

                            </div>

                            <button class="btn btn-sm btn-danger">
                                ×
                            </button>

                        </div>

                        <div class="cart-item-bottom">

                            <div class="qty-box">

                                <button>-</button>

                                <span>2</span>

                                <button>+</button>

                            </div>

                            <div class="cart-total">
                                ₱150.00
                            </div>

                        </div>

                    </div>

                </div>

                <div class="cart-summary">

                    <div class="summary-row">
                        <span>Subtotal</span>
                        <strong id="subtotalAmount">
                            ₱0.00
                        </strong>
                    </div>

                    <div class="summary-row">
                        <span>Discount</span>
                        <strong id="discountAmount">
                            ₱0.00
                        </strong>
                    </div>

                    <div class="summary-row">
                        <span>VAT</span>
                        <strong id="vatAmount">
                            ₱0.00
                        </strong>
                    </div>

                </div>

                <div class="grand-total">

                    <small>
                        TOTAL DUE
                    </small>

                    <div
                        class="amount"
                        id="grandTotal"
                    >
                        ₱0.00
                    </div>

                </div>

                <div class="payment-actions">

                    <button class="btn-pay">
                        PAY NOW
                    </button>

                    <div class="secondary-actions">

                        <button class="btn-hold">
                            HOLD
                        </button>

                        <button class="btn-void">
                            VOID
                        </button>

                    </div>

                </div>

            </aside>

        </div>

    </div>

@endsection
