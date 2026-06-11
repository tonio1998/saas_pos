<div class="sidebar d-flex flex-column">

    <div class="sidebar-scroll flex-grow-1">
        <ul class="sidebar-menu">

    <li class="sidebar-item">
        <a
            href="{{ route('dashboard.index') }}"
            class="sidebar-link {{ request()->routeIs('dashboard.*') ? 'active' : '' }}"
        >
            <i class="bi bi-grid-1x2-fill sidebar-icon"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <li class="sidebar-item">
        <a
            class="sidebar-link"
            data-bs-toggle="collapse"
            href="#salesMenu"
            role="button"
        >
            <i class="bi bi-cart-check-fill sidebar-icon"></i>
            <span>Sales</span>
            <i class="bi bi-chevron-down dropdown-icon"></i>
        </a>

        <div class="collapse sidebar-dropdown" id="salesMenu">

            <a
                href="{{ route('sales.create') }}"
                class="sidebar-sublink"
            >
                <i class="bi bi-plus-circle-fill sidebar-subicon"></i>
                <span>New Sale</span>
            </a>

            <a
                href="{{ route('sales.index') }}"
                class="sidebar-sublink"
            >
                <i class="bi bi-receipt-cutoff sidebar-subicon"></i>
                <span>Sales History</span>
            </a>

            <a
                href="{{ route('returns.index') }}"
                class="sidebar-sublink"
            >
                <i class="bi bi-arrow-return-left sidebar-subicon"></i>
                <span>Returns</span>
            </a>

            <a
                href="{{ route('payments.index') }}"
                class="sidebar-sublink"
            >
                <i class="bi bi-cash-stack sidebar-subicon"></i>
                <span>Payments</span>
            </a>

        </div>
    </li>

    <li class="sidebar-item">

        <a
            class="sidebar-link  {{ request()->routeIs('products.*') ? 'active' : '' }}"
            data-bs-toggle="collapse"
            href="#productsMenu"
            role="button"
        >
            <i class="bi bi-box-seam-fill sidebar-icon"></i>
            <span>Products</span>
            <i class="bi bi-chevron-down dropdown-icon"></i>
        </a>

        <div class="collapse sidebar-dropdown" id="productsMenu">

            <a
                href="{{ route('products.index') }}"
                class="sidebar-sublink"
            >
                <i class="bi bi-box-fill sidebar-subicon"></i>
                <span>Product List</span>
            </a>

            <a
                href="{{ route('products.categories.index') }}"
                class="sidebar-sublink"
            >
                <i class="bi bi-tags-fill sidebar-subicon"></i>
                <span>Categories</span>
            </a>

            <a
                href="{{ route('products.units.index') }}"
                class="sidebar-sublink"
            >
                <i class="bi bi-rulers sidebar-subicon"></i>
                <span>Units</span>
            </a>

        </div>

    </li>

    <li class="sidebar-item">

        <a
            class="sidebar-link"
            data-bs-toggle="collapse"
            href="#inventoryMenu"
            role="button"
        >
            <i class="bi bi-boxes sidebar-icon"></i>
            <span>Inventory</span>
            <i class="bi bi-chevron-down dropdown-icon"></i>
        </a>

        <div class="collapse sidebar-dropdown" id="inventoryMenu">

            <a
                href="{{ route('stocks.index') }}"
                class="sidebar-sublink"
            >
                <i class="bi bi-boxes sidebar-subicon"></i>
                <span>Current Stocks</span>
            </a>

            <a
                href="{{ route('inventory-movements.index') }}"
                class="sidebar-sublink"
            >
                <i class="bi bi-arrow-left-right sidebar-subicon"></i>
                <span>Stock Movements</span>
            </a>

            <a
                href="{{ route('stocks.adjustments.index') }}"
                class="sidebar-sublink"
            >
                <i class="bi bi-sliders sidebar-subicon"></i>
                <span>Stock Adjustments</span>
            </a>

            <a
                href="{{ route('stocks.low-stocks.index') }}"
                class="sidebar-sublink"
            >
                <i class="bi bi-exclamation-triangle-fill sidebar-subicon"></i>
                <span>Low Stocks</span>
            </a>

        </div>

    </li>

    <li class="sidebar-item">

        <a
            class="sidebar-link"
            data-bs-toggle="collapse"
            href="#purchasingMenu"
            role="button"
        >
            <i class="bi bi-cart-plus-fill sidebar-icon"></i>
            <span>Purchasing</span>
            <i class="bi bi-chevron-down dropdown-icon"></i>
        </a>

        <div class="collapse sidebar-dropdown" id="purchasingMenu">

            <a
                href="{{ route('purchases.index') }}"
                class="sidebar-sublink"
            >
                <i class="bi bi-bag-check-fill sidebar-subicon"></i>
                <span>Purchases</span>
            </a>

            <a
                href="{{ route('suppliers.index') }}"
                class="sidebar-sublink"
            >
                <i class="bi bi-truck sidebar-subicon"></i>
                <span>Suppliers</span>
            </a>

        </div>

    </li>

    <li class="sidebar-item">

        <a
            class="sidebar-link"
            data-bs-toggle="collapse"
            href="#customersMenu"
            role="button"
        >
            <i class="bi bi-people-fill sidebar-icon"></i>
            <span>Customers</span>
            <i class="bi bi-chevron-down dropdown-icon"></i>
        </a>

        <div class="collapse sidebar-dropdown" id="customersMenu">

            <a
                href="{{ route('customers.index') }}"
                class="sidebar-sublink"
            >
                <i class="bi bi-person-lines-fill sidebar-subicon"></i>
                <span>Customer List</span>
            </a>

            <a
                href="{{ route('customers.credit.index') }}"
                class="sidebar-sublink"
            >
                <i class="bi bi-wallet2 sidebar-subicon"></i>
                <span>Credit Accounts</span>
            </a>

            <a
                href="{{ route('customers.collections.index') }}"
                class="sidebar-sublink"
            >
                <i class="bi bi-cash-coin sidebar-subicon"></i>
                <span>Collections</span>
            </a>

        </div>

    </li>

    <li class="sidebar-item">

        <a
            class="sidebar-link"
            data-bs-toggle="collapse"
            href="#cashierMenu"
            role="button"
        >
            <i class="bi bi-safe-fill sidebar-icon"></i>
            <span>Cashiering</span>
            <i class="bi bi-chevron-down dropdown-icon"></i>
        </a>

        <div class="collapse sidebar-dropdown" id="cashierMenu">

            <a
                href="{{ route('cashiering.cash-drawers.index') }}"
                class="sidebar-sublink"
            >
                <i class="bi bi-safe sidebar-subicon"></i>
                <span>Cash Drawers</span>
            </a>

            <a
                href="{{ route('cashiering.cash-transactions.index') }}"
                class="sidebar-sublink"
            >
                <i class="bi bi-cash-stack sidebar-subicon"></i>
                <span>Cash In/Out</span>
            </a>

            <a
                href="{{ route('cashiering.ash-shifts.index') }}"
                class="sidebar-sublink"
            >
                <i class="bi bi-clock-fill sidebar-subicon"></i>
                <span>Shift History</span>
            </a>

        </div>

    </li>

    <li class="sidebar-item">
        <a
            href="{{ route('expenses.index') }}"
            class="sidebar-link"
        >
            <i class="bi bi-receipt sidebar-icon"></i>
            <span>Expenses</span>
        </a>
    </li>

    <li class="sidebar-item">

        <a
            class="sidebar-link"
            data-bs-toggle="collapse"
            href="#reportsMenu"
            role="button"
        >
            <i class="bi bi-bar-chart-fill sidebar-icon"></i>
            <span>Reports</span>
            <i class="bi bi-chevron-down dropdown-icon"></i>
        </a>

        <div class="collapse sidebar-dropdown" id="reportsMenu">

            <a href="{{ route('reports.sales') }}" class="sidebar-sublink">
                <i class="bi bi-graph-up sidebar-subicon"></i>
                <span>Sales Report</span>
            </a>

            <a href="{{ route('reports.inventory') }}" class="sidebar-sublink">
                <i class="bi bi-boxes sidebar-subicon"></i>
                <span>Inventory Report</span>
            </a>

            <a href="{{ route('reports.purchases') }}" class="sidebar-sublink">
                <i class="bi bi-bag-check-fill sidebar-subicon"></i>
                <span>Purchase Report</span>
            </a>

            <a href="{{ route('reports.expenses') }}" class="sidebar-sublink">
                <i class="bi bi-receipt sidebar-subicon"></i>
                <span>Expense Report</span>
            </a>

            <a href="{{ route('reports.profit') }}" class="sidebar-sublink">
                <i class="bi bi-currency-dollar sidebar-subicon"></i>
                <span>Profit Report</span>
            </a>

        </div>

    </li>

    <li class="sidebar-item">

        <a
            class="sidebar-link"
            data-bs-toggle="collapse"
            href="#usersMenu"
            role="button"
        >
            <i class="bi bi-person-gear sidebar-icon"></i>
            <span>User Management</span>
            <i class="bi bi-chevron-down dropdown-icon"></i>
        </a>

        <div class="collapse sidebar-dropdown" id="usersMenu">

            <a href="{{ route('users.index') }}" class="sidebar-sublink">
                <i class="bi bi-people-fill sidebar-subicon"></i>
                <span>Employees</span>
            </a>

            <a href="{{ route('roles.index') }}" class="sidebar-sublink">
                <i class="bi bi-person-workspace sidebar-subicon"></i>
                <span>Roles</span>
            </a>

            <a href="{{ route('permissions.index') }}" class="sidebar-sublink">
                <i class="bi bi-key-fill sidebar-subicon"></i>
                <span>Permissions</span>
            </a>

        </div>

    </li>

</ul>

    </div>
</div>
