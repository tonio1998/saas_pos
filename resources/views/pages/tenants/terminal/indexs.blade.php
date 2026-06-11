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

                <input
                    type="hidden"
                    id="customer_id"
                    name="customer_id"
                >

                <div class="cart-header">

                    <div>

                        <small class="text-muted">
                            Customer
                        </small>

                        <div
                            id="selectedCustomerName"
                            class="fw-semibold"
                        >
                            Walk-in Customer
                        </div>

                    </div>

                    <button
                        class="btn btn-sm btn-primary"
                        data-bs-toggle="offcanvas"
                        data-bs-target="#customerDrawer"
                    >
                        F7 Customer
                    </button>

                </div>

                <div class="cart-items">

                </div>

            </aside>

        </div>

    </div>

    <div
        class="offcanvas offcanvas-end"
        tabindex="-1"
        id="customerDrawer"
        style="width:420px"
    >

        <div class="offcanvas-header">

            <h5 class="offcanvas-title">
                Customer Selection
            </h5>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="offcanvas"
            ></button>

        </div>

        <div class="offcanvas-body p-3">

            <div class="mb-3">

                <div class="input-group">

                <span class="input-group-text">
                    <i class="bi bi-search"></i>
                </span>

                    <input
                        type="text"
                        id="customerSearch"
                        class="form-control"
                        placeholder="Search customer..."
                    >

                </div>

            </div>

            <div
                id="customerResults"
                class="list-group mb-4"
                style="
                max-height:300px;
                overflow:auto;
            "
            ></div>

            <div
                id="noCustomerFound"
                style="display:none;"
            >

                <div
                    class="
                    border
                    rounded
                    p-3
                    bg-light
                "
                >

                    <div class="fw-semibold mb-3">
                        Quick Create Customer
                    </div>

                    <div class="row">

                        <div class="col-6">

                            <input
                                type="text"
                                id="newFirstName"
                                class="form-control"
                                placeholder="First Name"
                            >

                        </div>

                        <div class="col-6">

                            <input
                                type="text"
                                id="newLastName"
                                class="form-control"
                                placeholder="Last Name"
                            >

                        </div>

                    </div>

                    <button
                        class="btn btn-primary w-100 mt-3"
                        id="btnCreateCustomer"
                    >
                        Create Customer
                    </button>

                </div>

            </div>

        </div>

    </div>
@endsection
