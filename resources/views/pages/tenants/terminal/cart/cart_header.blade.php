<div class="cart-header">
    <div class="cart-header-top">

        <div>

            <h4 id="cartCustomerName">
                {{ $sale->customer?->CustomerName ?? 'Current Order' }}
            </h4>

            <span id="cartCustomerAddress">
                {{ $sale->customer?->CustomerAddress ?? 'Walk-in Customer' }}
            </span>

        </div>

        <button
            type="button"
            class="btn-customer"
            data-bs-toggle="modal"
            data-bs-target="#customerModal"
        >
            <i class="bi bi-person-plus"></i>
            Customer
        </button>

    </div>
</div>

<div
    class="modal fade"
    id="customerModal"
    tabindex="-1"
    aria-hidden="true"
>
    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content border-0 shadow rounded-4">

            <div class="modal-header border-0">

                <div>

                    <h5 class="modal-title fw-bold mb-1">
                        <i class="bi bi-people-fill text-primary me-2"></i>
                        Select Customer
                    </h5>

                    <small class="text-muted">
                        Search an existing customer or create a new one.
                    </small>

                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>

            <div class="modal-body">
                <div class="mb-4">
                    <x-form.group
                        name="Customer"
                        label="Customer"
                        class="col-12"
                    >

                        <x-form.select
                            name="customer_id"
                            id="customer_id"
                            ajax="{{ route('select2.customers') }}"
                            value="{{ old('customer_id','') }}"
                            placeholder="Search Customer"
                            dropdownParent="#customerModal"
                        />

                    </x-form.group>

                    <small class="text-muted">
                        Search by customer code, name, or mobile number.
                    </small>

                </div>

                <div class="text-center">

                    <span class="text-muted">
                        Can't find the customer?
                    </span>

                    <button
                        type="button"
                        class="btn btn-link text-decoration-none"
                        data-bs-toggle="collapse"
                        data-bs-target="#newCustomerCollapse"
                    >
                        <i class="bi bi-person-plus"></i>
                        Add New Customer
                    </button>

                </div>

                <div class="collapse mt-4" id="newCustomerCollapse">
                    <form id="newCustomerForm">
                        <div class="card border-0 bg-light rounded-4">
                            <div class="card-header bg-transparent border-0 pb-0">
                                <h6 class="fw-bold mb-1">
                                    <i class="bi bi-person-plus me-2 text-primary"></i>
                                    New Customer
                                </h6>
                                <small class="text-muted">
                                    Enter the basic customer information.
                                </small>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <x-form.group name="customer_name" label="Customer Name" required>
                                            <x-form.input
                                                name="customer_name"
                                                placeholder="Enter customer name"
                                                autocomplete="off"
                                            />
                                        </x-form.group>
                                    </div>
                                    <div class="col-12">
                                        <x-form.group name="customer_address" label="Customer Address">
                                            <x-form.input
                                                name="customer_address"
                                                rows="2"
                                                placeholder="Enter customer address"
                                            />
                                        </x-form.group>

                                        <input type="hidden" name="sale_id" value="{{ $sale->id }}">
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer bg-transparent border-0 text-end">

                                <button
                                    type="button"
                                    class="btn btn-outline-secondary"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#newCustomerCollapse"
                                >
                                    Cancel
                                </button>

                                <button
                                    type="button"
                                    class="btn btn-link text-decoration-none"
                                    id="btnSaveCustomer"
                                >
                                    <i class="bi bi-person-plus"></i>
                                    Create & Select Customer
                                </button>

                            </div>

                        </div>

                    </form>

                </div>

            </div>

            <div class="modal-footer border-0">

                <button
                    type="button"
                    class="btn btn-light"
                    data-bs-dismiss="modal"
                >
                    Cancel
                </button>

                <button
                    type="button"
                    class="btn btn-primary"
                    id="btnSelectCustomer"
                    data-sale="{{ $sale->id }}"
                >
                    Select Customer
                </button>

            </div>

        </div>

    </div>
</div>
