<div class="sidebar d-flex flex-column">

    <div class="border-bottom px-4 pb-3">

        <a
            href="{{ route('dashboard.index') }}"
            class="d-flex align-items-center text-decoration-none"
        >

            <img
                src="{{ asset('images/logo.png') }}"
                alt="POS Logo"
                class="me-3"
                style="width:52px;height:52px;object-fit:contain;"
            >

            <div>

                <h5 class="fw-bold text-dark mb-0">
                    RetailPOS
                </h5>

                <small class="text-muted">
                    Business Management
                </small>

            </div>

        </a>

    </div>

    <div class="sidebar-scroll flex-grow-1 py-3">

        <div class="px-3 mb-2">

            <small class="text-uppercase text-muted fw-semibold">
                Main
            </small>

        </div>

        <ul class="sidebar-menu">

            <li class="sidebar-item">

                <a
                    href="{{ route('dashboard.index') }}"
                    class="sidebar-link justify-content-start {{ request()->routeIs('dashboard.*') ? 'active' : '' }}"
                >

                    <i class="bi bi-grid-1x2-fill sidebar-icon"></i>

                    <span>
                        Dashboard
                    </span>

                </a>

            </li>

        </ul>

        <div class="px-3 mt-3 mb-2">

            <small class="text-uppercase text-muted fw-semibold">
                Sales
            </small>

        </div>

        <ul class="sidebar-menu">

            <li class="sidebar-item">

                <a
                    class="sidebar-link d-flex justify-content-between align-items-center {{ request()->routeIs('sales.*') || request()->routeIs('returns.*') || request()->routeIs('payments.*') ? '' : 'collapsed' }}"
                    data-bs-toggle="collapse"
                    href="#salesMenu"
                    role="button"
                    aria-expanded="{{ request()->routeIs('sales.*') || request()->routeIs('returns.*') || request()->routeIs('payments.*') ? 'true' : 'false' }}"
                >

                    <div class="d-flex align-items-center">

                        <i class="bi bi-cart-check-fill sidebar-icon"></i>

                        <span>
                            Sales
                        </span>

                    </div>

                    <i class="bi bi-chevron-down dropdown-icon"></i>

                </a>

                <div
                    class="collapse sidebar-dropdown {{ request()->routeIs('sales.*') || request()->routeIs('returns.*') || request()->routeIs('payments.*') ? 'show' : '' }}"
                    id="salesMenu"
                >

                    <a
                        href="{{ route('sales.create1') }}"
                        class="sidebar-sublink {{ request()->routeIs('sales.create1') ? 'active' : '' }}"
                    >

                        <i class="bi bi-plus-circle-fill sidebar-subicon"></i>

                        <span>
                            New Sale
                        </span>

                    </a>

                    <a
                        href="{{ route('sales.index') }}"
                        class="sidebar-sublink {{ request()->routeIs('sales.index') ? 'active' : '' }}"
                    >

                        <i class="bi bi-receipt-cutoff sidebar-subicon"></i>

                        <span>
                            Sales History
                        </span>

                    </a>

                    <a
                        href="{{ route('returns.index') }}"
                        class="sidebar-sublink {{ request()->routeIs('returns.*') ? 'active' : '' }}"
                    >

                        <i class="bi bi-arrow-return-left sidebar-subicon"></i>

                        <span>
                            Returns
                        </span>

                    </a>

                    <a
                        href="{{ route('payments.index') }}"
                        class="sidebar-sublink {{ request()->routeIs('payments.*') ? 'active' : '' }}"
                    >

                        <i class="bi bi-cash-stack sidebar-subicon"></i>

                        <span>
                            Payments
                        </span>

                    </a>

                </div>

            </li>

        </ul>

        <div class="px-3 mt-3 mb-2">

            <small class="text-uppercase text-muted fw-semibold">
                Catalog
            </small>

        </div>

        <ul class="sidebar-menu">

            <li class="sidebar-item">

                <a
                    class="sidebar-link d-flex justify-content-between align-items-center {{ request()->routeIs('products.*') ? '' : 'collapsed' }}"
                    data-bs-toggle="collapse"
                    href="#productsMenu"
                    role="button"
                    aria-expanded="{{ request()->routeIs('products.*') ? 'true' : 'false' }}"
                >

                    <div class="d-flex align-items-center">

                        <i class="bi bi-box-seam-fill sidebar-icon"></i>

                        <span>
                            Products
                        </span>

                    </div>

                    <i class="bi bi-chevron-down dropdown-icon"></i>

                </a>

                <div
                    class="collapse sidebar-dropdown {{ request()->routeIs('products.*') ? 'show' : '' }}"
                    id="productsMenu"
                >

                    <a
                        href="{{ route('products.index') }}"
                        class="sidebar-sublink {{ request()->routeIs('products.index') ? 'active' : '' }}"
                    >

                        <i class="bi bi-box-fill sidebar-subicon"></i>

                        <span>
                            Product List
                        </span>

                    </a>

                    <a
                        href="{{ route('products.categories.index') }}"
                        class="sidebar-sublink {{ request()->routeIs('products.categories.*') ? 'active' : '' }}"
                    >

                        <i class="bi bi-tags-fill sidebar-subicon"></i>

                        <span>
                            Categories
                        </span>

                    </a>

                    <a
                        href="{{ route('products.units.index') }}"
                        class="sidebar-sublink {{ request()->routeIs('products.units.*') ? 'active' : '' }}"
                    >

                        <i class="bi bi-rulers sidebar-subicon"></i>

                        <span>
                            Units
                        </span>

                    </a>

                </div>

            </li>

        </ul>

        <div class="px-3 mt-3 mb-2">

            <small class="text-uppercase text-muted fw-semibold">
                Inventory
            </small>

        </div>

        <ul class="sidebar-menu">
            <li class="sidebar-item">

                <a
                    class="sidebar-link d-flex justify-content-between align-items-center {{ request()->routeIs('stocks.*') || request()->routeIs('inventory-movements.*') ? '' : 'collapsed' }}"
                    data-bs-toggle="collapse"
                    href="#inventoryMenu"
                    role="button"
                    aria-expanded="{{ request()->routeIs('stocks.*') || request()->routeIs('inventory-movements.*') ? 'true' : 'false' }}"
                >

                    <div class="d-flex align-items-center">

                        <i class="bi bi-boxes sidebar-icon"></i>

                        <span>
                            Inventory
                        </span>

                    </div>

                    <i class="bi bi-chevron-down dropdown-icon"></i>

                </a>

                <div
                    class="collapse sidebar-dropdown {{ request()->routeIs('stocks.*') || request()->routeIs('inventory-movements.*') ? 'show' : '' }}"
                    id="inventoryMenu"
                >

                    <a
                        href="{{ route('stocks.index') }}"
                        class="sidebar-sublink {{ request()->routeIs('stocks.index') ? 'active' : '' }}"
                    >

                        <i class="bi bi-boxes sidebar-subicon"></i>

                        <span>
                            Current Stocks
                        </span>

                    </a>

                    <a
                        href="{{ route('inventory-movements.index') }}"
                        class="sidebar-sublink {{ request()->routeIs('inventory-movements.*') ? 'active' : '' }}"
                    >

                        <i class="bi bi-arrow-left-right sidebar-subicon"></i>

                        <span>
                            Stock Movements
                        </span>

                    </a>

                    <a
                        href="{{ route('stocks.adjustments.index') }}"
                        class="sidebar-sublink {{ request()->routeIs('stocks.adjustments.*') ? 'active' : '' }}"
                    >

                        <i class="bi bi-sliders sidebar-subicon"></i>

                        <span>
                            Stock Adjustments
                        </span>

                    </a>

                    <a
                        href="{{ route('stocks.low-stocks.index') }}"
                        class="sidebar-sublink {{ request()->routeIs('stocks.low-stocks.*') ? 'active' : '' }}"
                    >

                        <i class="bi bi-exclamation-triangle-fill sidebar-subicon"></i>

                        <span>
                            Low Stocks
                        </span>

                    </a>

                </div>

            </li>

            <li class="sidebar-item">

                <a
                    class="sidebar-link d-flex justify-content-between align-items-center {{ request()->routeIs('purchases.*') || request()->routeIs('suppliers.*') ? '' : 'collapsed' }}"
                    data-bs-toggle="collapse"
                    href="#purchasingMenu"
                    role="button"
                    aria-expanded="{{ request()->routeIs('purchases.*') || request()->routeIs('suppliers.*') ? 'true' : 'false' }}"
                >

                    <div class="d-flex align-items-center">

                        <i class="bi bi-cart-plus-fill sidebar-icon"></i>

                        <span>
                            Purchasing
                        </span>

                    </div>

                    <i class="bi bi-chevron-down dropdown-icon"></i>

                </a>

                <div
                    class="collapse sidebar-dropdown {{ request()->routeIs('purchases.*') || request()->routeIs('suppliers.*') ? 'show' : '' }}"
                    id="purchasingMenu"
                >

                    <a
                        href="{{ route('purchases.index') }}"
                        class="sidebar-sublink {{ request()->routeIs('purchases.*') ? 'active' : '' }}"
                    >

                        <i class="bi bi-bag-check-fill sidebar-subicon"></i>

                        <span>
                            Purchases
                        </span>

                    </a>

                    <a
                        href="{{ route('suppliers.index') }}"
                        class="sidebar-sublink {{ request()->routeIs('suppliers.*') ? 'active' : '' }}"
                    >

                        <i class="bi bi-truck sidebar-subicon"></i>

                        <span>
                            Suppliers
                        </span>

                    </a>

                </div>

            </li>

        </ul>

        <div class="px-3 mt-3 mb-2">

            <small class="text-uppercase text-muted fw-semibold">
                CRM
            </small>

        </div>

        <ul class="sidebar-menu">

            <li class="sidebar-item">

                <a
                    class="sidebar-link d-flex justify-content-between align-items-center {{ request()->routeIs('customers.*') ? '' : 'collapsed' }}"
                    data-bs-toggle="collapse"
                    href="#customersMenu"
                    role="button"
                    aria-expanded="{{ request()->routeIs('customers.*') ? 'true' : 'false' }}"
                >

                    <div class="d-flex align-items-center">

                        <i class="bi bi-people-fill sidebar-icon"></i>

                        <span>
                            Customers
                        </span>

                    </div>

                    <i class="bi bi-chevron-down dropdown-icon"></i>

                </a>

                <div
                    class="collapse sidebar-dropdown {{ request()->routeIs('customers.*') ? 'show' : '' }}"
                    id="customersMenu"
                >

                    <a
                        href="{{ route('customers.index') }}"
                        class="sidebar-sublink {{ request()->routeIs('customers.index') ? 'active' : '' }}"
                    >

                        <i class="bi bi-person-lines-fill sidebar-subicon"></i>

                        <span>
                            Customer List
                        </span>

                    </a>

                    <a
                        href="{{ route('customers.credit.index') }}"
                        class="sidebar-sublink {{ request()->routeIs('customers.credit.*') ? 'active' : '' }}"
                    >

                        <i class="bi bi-wallet2 sidebar-subicon"></i>

                        <span>
                            Credit Accounts
                        </span>

                    </a>

                    <a
                        href="{{ route('customers.collections.index') }}"
                        class="sidebar-sublink {{ request()->routeIs('customers.collections.*') ? 'active' : '' }}"
                    >

                        <i class="bi bi-cash-coin sidebar-subicon"></i>

                        <span>
                            Collections
                        </span>

                    </a>

                </div>

            </li>

            <li class="sidebar-item">

                <a
                    class="sidebar-link d-flex justify-content-between align-items-center {{ request()->routeIs('cashiering.*') ? '' : 'collapsed' }}"
                    data-bs-toggle="collapse"
                    href="#cashierMenu"
                    role="button"
                    aria-expanded="{{ request()->routeIs('cashiering.*') ? 'true' : 'false' }}"
                >

                    <div class="d-flex align-items-center">

                        <i class="bi bi-safe-fill sidebar-icon"></i>

                        <span>
                            Cashiering
                        </span>

                    </div>

                    <i class="bi bi-chevron-down dropdown-icon"></i>

                </a>

                <div
                    class="collapse sidebar-dropdown {{ request()->routeIs('cashiering.*') ? 'show' : '' }}"
                    id="cashierMenu"
                >
                    <a
                        href="{{ route('cashiering.cash-drawers.index') }}"
                        class="sidebar-sublink {{ request()->routeIs('cashiering.cash-drawers.*') ? 'active' : '' }}"
                    >

                        <i class="bi bi-safe sidebar-subicon"></i>

                        <span>
                            Cash Drawers
                        </span>

                    </a>

                    <a
                        href="{{ route('cashiering.cash-transactions.index') }}"
                        class="sidebar-sublink {{ request()->routeIs('cashiering.cash-transactions.*') ? 'active' : '' }}"
                    >

                        <i class="bi bi-cash-stack sidebar-subicon"></i>

                        <span>
                            Cash In / Out
                        </span>

                    </a>

                    <a
                        href="{{ route('cashiering.cash-shifts.index') }}"
                        class="sidebar-sublink {{ request()->routeIs('cashiering.cash-shifts.*') ? 'active' : '' }}"
                    >

                        <i class="bi bi-clock-history sidebar-subicon"></i>

                        <span>
                            Shift History
                        </span>

                    </a>

                </div>

            </li>

            <li class="sidebar-item">

                <a
                    href="{{ route('expenses.index') }}"
                    class="sidebar-link justify-content-start {{ request()->routeIs('expenses.*') ? 'active' : '' }}"
                >

                    <i class="bi bi-receipt sidebar-icon"></i>

                    <span>
                        Expenses
                    </span>

                </a>

            </li>

        </ul>

        <div class="px-3 mt-3 mb-2">

            <small class="text-uppercase text-muted fw-semibold">
                Reports
            </small>

        </div>

        <ul class="sidebar-menu">

            <li class="sidebar-item">

                <a
                    class="sidebar-link d-flex justify-content-between align-items-center {{ request()->routeIs('reports.*') ? '' : 'collapsed' }}"
                    data-bs-toggle="collapse"
                    href="#reportsMenu"
                    role="button"
                    aria-expanded="{{ request()->routeIs('reports.*') ? 'true' : 'false' }}"
                >

                    <div class="d-flex align-items-center">

                        <i class="bi bi-bar-chart-fill sidebar-icon"></i>

                        <span>
                            Reports
                        </span>

                    </div>

                    <i class="bi bi-chevron-down dropdown-icon"></i>

                </a>

                <div
                    class="collapse sidebar-dropdown {{ request()->routeIs('reports.*') ? 'show' : '' }}"
                    id="reportsMenu"
                >

                    <a
                        href="{{ route('reports.sales') }}"
                        class="sidebar-sublink {{ request()->routeIs('reports.sales') ? 'active' : '' }}"
                    >

                        <i class="bi bi-graph-up sidebar-subicon"></i>

                        <span>
                            Sales Report
                        </span>

                    </a>

                    <a
                        href="{{ route('reports.inventory') }}"
                        class="sidebar-sublink {{ request()->routeIs('reports.inventory') ? 'active' : '' }}"
                    >

                        <i class="bi bi-boxes sidebar-subicon"></i>

                        <span>
                            Inventory Report
                        </span>

                    </a>

                    <a
                        href="{{ route('reports.purchases') }}"
                        class="sidebar-sublink {{ request()->routeIs('reports.purchases') ? 'active' : '' }}"
                    >

                        <i class="bi bi-bag-check-fill sidebar-subicon"></i>

                        <span>
                            Purchase Report
                        </span>

                    </a>

                    <a
                        href="{{ route('reports.expenses') }}"
                        class="sidebar-sublink {{ request()->routeIs('reports.expenses') ? 'active' : '' }}"
                    >

                        <i class="bi bi-receipt sidebar-subicon"></i>

                        <span>
                            Expense Report
                        </span>

                    </a>

                    <a
                        href="{{ route('reports.profit') }}"
                        class="sidebar-sublink {{ request()->routeIs('reports.profit') ? 'active' : '' }}"
                    >

                        <i class="bi bi-currency-dollar sidebar-subicon"></i>

                        <span>
                            Profit Report
                        </span>

                    </a>

                </div>

            </li>

        </ul>



    </div>

</div>
