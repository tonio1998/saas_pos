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
        'title' => 'POS Terminals',
        'items' => [

            [
                'type'   => 'collapse',
                'id'     => 'terminalMenu',
                'label'  => 'POS Terminals',
                'icon'   => 'bi bi-calculator-fill',
                'active' => [
                    'terminal.*',
                ],
                'children' => [

                    [
                        'label'  => 'Open POS Terminal',
                        'route'  => 'terminal.index',
                        'icon'   => 'bi bi-play-circle-fill',
                        'active' => 'terminal.index',
                    ],
                    [
                        'label'  => 'Add POS Terminal',
                        'route'  => 'terminal.create',
                        'icon'   => 'bi bi-plus-circle-fill',
                        'active' => 'terminal.create',
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

                    [
                        'label'  => 'Price History',
                        'route'  => 'products.price-history.index',
                        'icon'   => 'bi bi-graph-up-arrow',
                        'active' => 'products.price-history.*',
                    ],

                    [
                        'label'  => 'Print Barcode Tags',
                        'route'  => 'products.barcode-labels.index',
                        'icon'   => 'bi bi-upc-scan',
                        'active' => 'products.barcode-labels.*',
                    ],

                ],
            ],

            [
                'type'   => 'link',
                'label'  => 'Promotions & Deals',
                'icon'   => 'bi bi-ticket-perforated-fill',
                'route'  => 'promotions.index',
                'active' => 'promotions.*',
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
                'icon'   => 'bi bi-wallet2',
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
                        'label'  => 'Expense Report',
                        'route'  => 'reports.expenses',
                        'icon'   => 'bi bi-receipt',
                        'active' => 'reports.expenses',
                    ],

                    [
                        'label'  => 'Profit & Loss (P&L)',
                        'route'  => 'reports.profit',
                        'icon'   => 'bi bi-currency-dollar',
                        'active' => 'reports.profit',
                    ],

                ],
            ],

        ],
    ],

    [
        'title' => 'Settings',
        'items' => [

            [
                'type'   => 'collapse',
                'id'     => 'settingsMenu',
                'label'  => 'Settings',
                'icon'   => 'bi bi-gear-fill',
                'active' => [
                    'settings.*',
                    'users.*',
                ],
                'children' => [

                    [
                        'label'  => 'Store Settings',
                        'route'  => 'settings.index',
                        'icon'   => 'bi bi-shop',
                        'active' => 'settings.*',
                    ],

                    [
                        'label'  => 'Users & Staff',
                        'route'  => 'users.index',
                        'icon'   => 'bi bi-people-fill',
                        'active' => 'users.*',
                    ],

                ],
            ],

        ],
    ],

];
