<?php

return [

    [
        'title' => 'Main',
        'items' => [

            [
                'type'   => 'link',
                'label'  => 'Dashboard',
                'icon'   => 'bi bi-grid-1x2-fill',
                'route'  => 'dashboard.index',
                'active' => 'dashboard.*',
            ],

        ],
    ],

    [
        'title' => 'Sales',
        'items' => [

            [
                'type'   => 'collapse',
                'id'     => 'salesMenu',
                'label'  => 'Sales',
                'icon'   => 'bi bi-cart-check-fill',
                'active' => [
                    'sales.*',
                    'returns.*',
                    'payments.*',
                ],
                'children' => [
                    [
                        'label'  => 'Sales History',
                        'route'  => 'sales.index',
                        'icon'   => 'bi bi-receipt-cutoff',
                        'active' => 'sales.index',
                    ],

                    [
                        'label'  => 'Returns',
                        'route'  => 'returns.index',
                        'icon'   => 'bi bi-arrow-return-left',
                        'active' => 'returns.*',
                    ],

                    [
                        'label'  => 'Payments',
                        'route'  => 'payments.index',
                        'icon'   => 'bi bi-cash-stack',
                        'active' => 'payments.*',
                    ],

                ],
            ],

        ],
    ],
    [
        'title' => 'POS Devices',
        'items' => [

            [
                'type'   => 'collapse',
                'id'     => 'terminalMenu',
                'label'  => 'POS Devices',
                'icon'   => 'bi bi-tablet-landscape',
                'active' => [
                    'terminal.*',
                    'sales.*',
                    'returns.*',
                    'payments.*',
                    'cashiering.cash-shifts.*',
                ],
                'children' => [

                    [
                        'label'  => 'Open POS Device',
                        'route'  => 'terminal.index',
                        'icon'   => 'bi bi-play-circle-fill',
                        'active' => 'terminal.index',
                    ],
                    [
                        'label'  => 'Add POS Device',
                        'route'  => 'terminal.create',
                        'icon'   => 'bi bi-plus-circle-fill',
                        'active' => 'terminal.create',
                    ],
                    [
                        'label'  => 'Sales History',
                        'route'  => 'sales.index',
                        'icon'   => 'bi bi-receipt-cutoff',
                        'active' => 'sales.index',
                    ],

                    [
                        'label'  => 'Returns',
                        'route'  => 'returns.index',
                        'icon'   => 'bi bi-arrow-return-left',
                        'active' => 'returns.*',
                    ],

                    [
                        'label'  => 'Payments',
                        'route'  => 'payments.index',
                        'icon'   => 'bi bi-cash-stack',
                        'active' => 'payments.*',
                    ],

                    [
                        'label'  => 'Cash Shifts',
                        'route'  => 'cashiering.cash-shifts.index',
                        'icon'   => 'bi bi-clock-history',
                        'active' => 'cashiering.cash-shifts.*',
                    ],

                ],
            ],

        ],
    ],

    [
        'title' => 'Catalog',
        'items' => [

            [
                'type'   => 'collapse',
                'id'     => 'productsMenu',
                'label'  => 'Products',
                'icon'   => 'bi bi-box-seam-fill',
                'active' => [
                    'products.*',
                ],
                'children' => [

                    [
                        'label'  => 'Product List',
                        'route'  => 'products.index',
                        'icon'   => 'bi bi-box-fill',
                        'active' => 'products.index',
                    ],

                    [
                        'label'  => 'Categories',
                        'route'  => 'products.categories.index',
                        'icon'   => 'bi bi-tags-fill',
                        'active' => 'products.categories.*',
                    ],

                    [
                        'label'  => 'Units',
                        'route'  => 'products.units.index',
                        'icon'   => 'bi bi-rulers',
                        'active' => 'products.units.*',
                    ],

                ],
            ],

        ],
    ],

    [
        'title' => 'Inventory',
        'items' => [

            [
                'type'   => 'collapse',
                'id'     => 'inventoryMenu',
                'label'  => 'Inventory',
                'icon'   => 'bi bi-boxes',
                'active' => [
                    'stocks.*',
                    'inventory-movements.*',
                ],
                'children' => [

                    [
                        'label'  => 'Current Stocks',
                        'route'  => 'stocks.index',
                        'icon'   => 'bi bi-boxes',
                        'active' => 'stocks.index',
                    ],

                    [
                        'label'  => 'Stock Movements',
                        'route'  => 'inventory-movements.index',
                        'icon'   => 'bi bi-arrow-left-right',
                        'active' => 'inventory-movements.*',
                    ],

                    [
                        'label'  => 'Stock Adjustments',
                        'route'  => 'stocks.adjustments.index',
                        'icon'   => 'bi bi-sliders',
                        'active' => 'stocks.adjustments.*',
                    ],

                    [
                        'label'  => 'Low Stocks',
                        'route'  => 'stocks.low-stocks.index',
                        'icon'   => 'bi bi-exclamation-triangle-fill',
                        'active' => 'stocks.low-stocks.*',
                    ],

                ],
            ],

            [
                'type'   => 'collapse',
                'id'     => 'purchasingMenu',
                'label'  => 'Purchasing',
                'icon'   => 'bi bi-cart-plus-fill',
                'active' => [
                    'purchases.*',
                    'suppliers.*',
                ],
                'children' => [

                    [
                        'label'  => 'Purchases',
                        'route'  => 'purchases.index',
                        'icon'   => 'bi bi-bag-check-fill',
                        'active' => 'purchases.*',
                    ],

                    [
                        'label'  => 'Suppliers',
                        'route'  => 'suppliers.index',
                        'icon'   => 'bi bi-truck',
                        'active' => 'suppliers.*',
                    ],

                ],
            ],

        ],
    ],

    [
        'title' => 'CRM',
        'items' => [

            [
                'type'   => 'collapse',
                'id'     => 'customersMenu',
                'label'  => 'Customers',
                'icon'   => 'bi bi-people-fill',
                'active' => [
                    'customers.*',
                ],
                'children' => [

                    [
                        'label'  => 'Customer List',
                        'route'  => 'customers.index',
                        'icon'   => 'bi bi-person-lines-fill',
                        'active' => 'customers.index',
                    ],

                    [
                        'label'  => 'Credit Accounts',
                        'route'  => 'customers.credit.index',
                        'icon'   => 'bi bi-wallet2',
                        'active' => 'customers.credit.*',
                    ],

                    [
                        'label'  => 'Collections',
                        'route'  => 'customers.collections.index',
                        'icon'   => 'bi bi-cash-coin',
                        'active' => 'customers.collections.*',
                    ],

                ],
            ],

            [
                'type'   => 'collapse',
                'id'     => 'cashierMenu',
                'label'  => 'Cashiering',
                'icon'   => 'bi bi-safe-fill',
                'active' => [
                    'cashiering.*',
                ],
                'children' => [

                    [
                        'label'  => 'Cash Drawers',
                        'route'  => 'cashiering.cash-drawers.index',
                        'icon'   => 'bi bi-safe',
                        'active' => 'cash-drawers.cash-drawers.*',
                    ],

                    [
                        'label'  => 'Cash In / Out',
                        'route'  => 'cashiering.cash-transactions.index',
                        'icon'   => 'bi bi-cash-stack',
                        'active' => 'cash-drawers.cash-transactions.*',
                    ],

                    [
                        'label'  => 'Shift History',
                        'route'  => 'cashiering.cash-shifts.index',
                        'icon'   => 'bi bi-clock-history',
                        'active' => 'cash-drawers.cash-shifts.*',
                    ],

                ],
            ],

            [
                'type'   => 'link',
                'label'  => 'Expenses',
                'icon'   => 'bi bi-receipt',
                'route'  => 'expenses.index',
                'active' => 'expenses.*',
            ],

        ],
    ],

    [
        'title' => 'Reports',
        'items' => [

            [
                'type'   => 'collapse',
                'id'     => 'reportsMenu',
                'label'  => 'Reports',
                'icon'   => 'bi bi-bar-chart-fill',
                'active' => [
                    'reports.*',
                ],
                'children' => [

                    [
                        'label'  => 'Sales Report',
                        'route'  => 'reports.sales',
                        'icon'   => 'bi bi-graph-up',
                        'active' => 'reports.sales',
                    ],

                    [
                        'label'  => 'Inventory Report',
                        'route'  => 'reports.inventory',
                        'icon'   => 'bi bi-boxes',
                        'active' => 'reports.inventory',
                    ],

                    [
                        'label'  => 'Purchase Report',
                        'route'  => 'reports.purchases',
                        'icon'   => 'bi bi-bag-check-fill',
                        'active' => 'reports.purchases',
                    ],

                    [
                        'label'  => 'Expense Report',
                        'route'  => 'reports.expenses',
                        'icon'   => 'bi bi-receipt',
                        'active' => 'reports.expenses',
                    ],

                    [
                        'label'  => 'Profit Report',
                        'route'  => 'reports.profit',
                        'icon'   => 'bi bi-currency-dollar',
                        'active' => 'reports.profit',
                    ],

                ],
            ],

        ],
    ],

];
