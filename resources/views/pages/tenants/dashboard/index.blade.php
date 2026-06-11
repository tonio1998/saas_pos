@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    <x-page-header
        title="Business Dashboard"
        subtitle="Sales, inventory, profit, and business performance overview"
    />

    <div class="row g-3 mb-4">

        <div class="col-12 col-sm-6 col-xl-2">
            <div class="dashboard-card success">
                <div class="card-label">TODAY'S SALES</div>
                <div class="card-value">₱125,450</div>
                <div class="card-meta">
                    <i class="bi bi-arrow-up-right"></i>
                    +12.8% vs Yesterday
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-2">
            <div class="dashboard-card primary">
                <div class="card-label">GROSS PROFIT</div>
                <div class="card-value">₱37,820</div>
                <div class="card-meta">
                    30.15% Margin
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-2">
            <div class="dashboard-card warning">
                <div class="card-label">TRANSACTIONS</div>
                <div class="card-value">326</div>
                <div class="card-meta">
                    Completed Today
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-2">
            <div class="dashboard-card info">
                <div class="card-label">CASH ON HAND</div>
                <div class="card-value">₱52,450</div>
                <div class="card-meta">
                    Current Drawer
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-2">
            <div class="dashboard-card danger">
                <div class="card-label">RECEIVABLES</div>
                <div class="card-value">₱18,250</div>
                <div class="card-meta">
                    Outstanding Credits
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-2">
            <div class="dashboard-card dark">
                <div class="card-label">INVENTORY VALUE</div>
                <div class="card-value">₱1.25M</div>
                <div class="card-meta">
                    Current Stocks
                </div>
            </div>
        </div>

    </div>

    <div class="row g-4 mb-4">

        <div class="col-xl-8">

            <div class="modern-card h-100">

                <div class="section-header">
                    <h5 class="section-title">
                        Sales Trend
                    </h5>

                    <div class="section-subtitle">
                        Revenue performance overview
                    </div>
                </div>

                <div class="btn-group mb-3">
                    <button class="btn btn-success chart-filter active" data-type="daily">
                        Daily
                    </button>

                    <button class="btn btn-outline-success chart-filter" data-type="monthly">
                        Monthly
                    </button>

                    <button class="btn btn-outline-success chart-filter" data-type="annual">
                        Annual
                    </button>
                </div>

                <div style="height:350px">
                    <canvas id="salesChart"></canvas>
                </div>

            </div>

        </div>

        <div class="col-xl-4">

            <div class="modern-card h-100">

                <div class="section-header">
                    <h5 class="section-title">
                        Business Summary
                    </h5>

                    <div class="section-subtitle">
                        Current business statistics
                    </div>
                </div>

                <div class="summary-grid">

                    <div class="summary-box">
                        <span>Products</span>
                        <strong>1,245</strong>
                    </div>

                    <div class="summary-box">
                        <span>Customers</span>
                        <strong>325</strong>
                    </div>

                    <div class="summary-box">
                        <span>Suppliers</span>
                        <strong>48</strong>
                    </div>

                    <div class="summary-box">
                        <span>Employees</span>
                        <strong>15</strong>
                    </div>

                    <div class="summary-box">
                        <span>Branches</span>
                        <strong>3</strong>
                    </div>

                    <div class="summary-box">
                        <span>Low Stocks</span>
                        <strong class="text-warning">18</strong>
                    </div>

                    <div class="summary-box">
                        <span>Out Of Stocks</span>
                        <strong class="text-danger">5</strong>
                    </div>

                    <div class="summary-box">
                        <span>Pending Credits</span>
                        <strong>12</strong>
                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="modern-card mb-4">

        <div class="section-header">

            <h5 class="section-title">
                Quick Actions
            </h5>

            <div class="section-subtitle">
                Frequently used operations
            </div>

        </div>

        <div class="quick-grid">

            <a href="#" class="quick-action">
                <i class="bi bi-cart-plus-fill"></i>
                <span>New Sale</span>
            </a>

            <a href="#" class="quick-action">
                <i class="bi bi-box-seam-fill"></i>
                <span>Add Product</span>
            </a>

            <a href="#" class="quick-action">
                <i class="bi bi-bag-plus-fill"></i>
                <span>New Purchase</span>
            </a>

            <a href="#" class="quick-action">
                <i class="bi bi-person-plus-fill"></i>
                <span>Add Customer</span>
            </a>

            <a href="#" class="quick-action">
                <i class="bi bi-wallet2"></i>
                <span>Receive Payment</span>
            </a>

            <a href="#" class="quick-action">
                <i class="bi bi-sliders"></i>
                <span>Stock Adjustment</span>
            </a>

            <a href="#" class="quick-action">
                <i class="bi bi-cash-stack"></i>
                <span>Cash In / Out</span>
            </a>

            <a href="#" class="quick-action">
                <i class="bi bi-bar-chart-fill"></i>
                <span>Reports</span>
            </a>

        </div>

    </div>

    <div class="row g-4 mb-4">

        <div class="col-xl-4">

            <div class="modern-card">

                <div class="section-header">
                    <h5 class="section-title">
                        Low Stock Products
                    </h5>
                </div>

                <table class="table table-sm align-middle">

                    <tbody>

                    <tr>
                        <td>Coke 500ml</td>
                        <td class="text-end text-warning fw-bold">3</td>
                    </tr>

                    <tr>
                        <td>Piattos Cheese</td>
                        <td class="text-end text-warning fw-bold">2</td>
                    </tr>

                    <tr>
                        <td>Alaska Milk</td>
                        <td class="text-end text-warning fw-bold">1</td>
                    </tr>

                    <tr>
                        <td>Nescafe Stick</td>
                        <td class="text-end text-warning fw-bold">5</td>
                    </tr>

                    </tbody>

                </table>

            </div>

        </div>

        <div class="col-xl-4">

            <div class="modern-card">

                <div class="section-header">
                    <h5 class="section-title">
                        Out Of Stock
                    </h5>
                </div>

                <ul class="list-group">

                    <li class="list-group-item">
                        Sprite 1.5L
                    </li>

                    <li class="list-group-item">
                        Century Tuna
                    </li>

                    <li class="list-group-item">
                        Milo Sachet
                    </li>

                </ul>

            </div>

        </div>

        <div class="col-xl-4">

            <div class="modern-card">

                <div class="section-header">
                    <h5 class="section-title">
                        Top Selling Products
                    </h5>
                </div>

                <table class="table table-sm">

                    <tbody>

                    <tr>
                        <td>Coke 500ml</td>
                        <td class="text-end">125 Sold</td>
                    </tr>

                    <tr>
                        <td>Lucky Me Beef</td>
                        <td class="text-end">98 Sold</td>
                    </tr>

                    <tr>
                        <td>Nescafe Stick</td>
                        <td class="text-end">75 Sold</td>
                    </tr>

                    <tr>
                        <td>Piattos</td>
                        <td class="text-end">58 Sold</td>
                    </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </div>
    <div class="row g-4 mb-4">

        <div class="col-xl-4">

            <div class="modern-card h-100">

                <div class="section-header">
                    <h5 class="section-title">
                        Financial Overview
                    </h5>

                    <div class="section-subtitle">
                        Today's financial snapshot
                    </div>
                </div>

                <div class="finance-list">

                    <div class="finance-item">

                        <span>Revenue</span>

                        <strong class="text-success">
                            ₱125,450.00
                        </strong>

                    </div>

                    <div class="finance-item">

                        <span>Expenses</span>

                        <strong class="text-danger">
                            ₱8,250.00
                        </strong>

                    </div>

                    <div class="finance-item">

                        <span>Purchases</span>

                        <strong>
                            ₱15,800.00
                        </strong>

                    </div>

                    <div class="finance-item">

                        <span>Net Profit</span>

                        <strong class="text-primary">
                            ₱37,820.00
                        </strong>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-xl-8">

            <div class="modern-card h-100">

                <div class="section-header">

                    <h5 class="section-title">
                        Customer Receivables
                    </h5>

                    <div class="section-subtitle">
                        Outstanding balances
                    </div>

                </div>

                <div class="table-responsive">

                    <table class="table align-middle">

                        <thead>

                        <tr>
                            <th>Customer</th>
                            <th>Type</th>
                            <th class="text-end">Balance</th>
                        </tr>

                        </thead>

                        <tbody>

                        <tr>
                            <td>ABC Store</td>
                            <td>Wholesale</td>
                            <td class="text-end fw-bold text-danger">
                                ₱5,000.00
                            </td>
                        </tr>

                        <tr>
                            <td>Juan Dela Cruz</td>
                            <td>Retail</td>
                            <td class="text-end fw-bold text-danger">
                                ₱3,500.00
                            </td>
                        </tr>

                        <tr>
                            <td>Maria Store</td>
                            <td>Wholesale</td>
                            <td class="text-end fw-bold text-danger">
                                ₱2,000.00
                            </td>
                        </tr>

                        <tr>
                            <td>XYZ Trading</td>
                            <td>Wholesale</td>
                            <td class="text-end fw-bold text-danger">
                                ₱1,500.00
                            </td>
                        </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

    <div class="row g-4 mb-4">

        <div class="col-xl-6">

            <div class="modern-card">

                <div class="section-header">

                    <h5 class="section-title">
                        Recent Sales
                    </h5>

                    <div class="section-subtitle">
                        Latest completed transactions
                    </div>

                </div>

                <div class="table-responsive">

                    <table class="table align-middle">

                        <thead>

                        <tr>
                            <th>Invoice</th>
                            <th>Customer</th>
                            <th class="text-end">Amount</th>
                        </tr>

                        </thead>

                        <tbody>

                        <tr>
                            <td>INV-2026-00125</td>
                            <td>Walk-In</td>
                            <td class="text-end">₱1,250.00</td>
                        </tr>

                        <tr>
                            <td>INV-2026-00126</td>
                            <td>Juan Store</td>
                            <td class="text-end">₱980.00</td>
                        </tr>

                        <tr>
                            <td>INV-2026-00127</td>
                            <td>Walk-In</td>
                            <td class="text-end">₱560.00</td>
                        </tr>

                        <tr>
                            <td>INV-2026-00128</td>
                            <td>ABC Store</td>
                            <td class="text-end">₱2,450.00</td>
                        </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

        <div class="col-xl-6">

            <div class="modern-card">

                <div class="section-header">

                    <h5 class="section-title">
                        Recent Purchases
                    </h5>

                    <div class="section-subtitle">
                        Latest stock replenishments
                    </div>

                </div>

                <div class="table-responsive">

                    <table class="table align-middle">

                        <thead>

                        <tr>
                            <th>PO No.</th>
                            <th>Supplier</th>
                            <th class="text-end">Amount</th>
                        </tr>

                        </thead>

                        <tbody>

                        <tr>
                            <td>PO-1001</td>
                            <td>Coca-Cola</td>
                            <td class="text-end">₱12,000.00</td>
                        </tr>

                        <tr>
                            <td>PO-1002</td>
                            <td>Nestlé</td>
                            <td class="text-end">₱8,500.00</td>
                        </tr>

                        <tr>
                            <td>PO-1003</td>
                            <td>URC</td>
                            <td class="text-end">₱6,200.00</td>
                        </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

    <div class="row g-4 mb-4">

        <div class="col-xl-5">

            <div class="modern-card h-100">

                <div class="section-header">

                    <h5 class="section-title">
                        Cash Drawer Status
                    </h5>

                    <div class="section-subtitle">
                        Current shift monitoring
                    </div>

                </div>

                <div class="finance-list">

                    <div class="finance-item">
                        <span>Opening Cash</span>
                        <strong>₱10,000.00</strong>
                    </div>

                    <div class="finance-item">
                        <span>Cash Sales</span>
                        <strong>₱42,500.00</strong>
                    </div>

                    <div class="finance-item">
                        <span>Cash In</span>
                        <strong class="text-success">
                            ₱5,000.00
                        </strong>
                    </div>

                    <div class="finance-item">
                        <span>Cash Out</span>
                        <strong class="text-danger">
                            ₱2,500.00
                        </strong>
                    </div>

                    <div class="finance-item">
                        <span>Expected Cash</span>
                        <strong class="text-primary">
                            ₱55,000.00
                        </strong>
                    </div>

                </div>

            </div>

        </div>

        <div class="col-xl-7">

            <div class="modern-card h-100">

                <div class="section-header">

                    <h5 class="section-title">
                        Branch Performance
                    </h5>

                    <div class="section-subtitle">
                        Sales comparison
                    </div>

                </div>

                <table class="table align-middle">

                    <thead>

                    <tr>
                        <th>Branch</th>
                        <th class="text-end">Sales</th>
                    </tr>

                    </thead>

                    <tbody>

                    <tr>
                        <td>Main Branch</td>
                        <td class="text-end fw-bold">
                            ₱125,450.00
                        </td>
                    </tr>

                    <tr>
                        <td>Branch 2</td>
                        <td class="text-end fw-bold">
                            ₱87,200.00
                        </td>
                    </tr>

                    <tr>
                        <td>Branch 3</td>
                        <td class="text-end fw-bold">
                            ₱65,300.00
                        </td>
                    </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    <div class="row g-4">

        <div class="col-xl-8">

            <div class="modern-card">

                <div class="section-header">

                    <h5 class="section-title">
                        Recent Activities
                    </h5>

                    <div class="section-subtitle">
                        Latest system activities
                    </div>

                </div>

                <div class="timeline">

                    <div class="timeline-item">
                        <strong>10:01 AM</strong>
                        <div>Sale INV-00125 completed</div>
                    </div>

                    <div class="timeline-item">
                        <strong>10:05 AM</strong>
                        <div>Purchase PO-1001 created</div>
                    </div>

                    <div class="timeline-item">
                        <strong>10:12 AM</strong>
                        <div>Cash In transaction recorded</div>
                    </div>

                    <div class="timeline-item">
                        <strong>10:18 AM</strong>
                        <div>Stock adjustment approved</div>
                    </div>

                    <div class="timeline-item">
                        <strong>10:25 AM</strong>
                        <div>Customer payment received</div>
                    </div>

                </div>

            </div>

        </div>

        <div class="col-xl-4">

            <div class="modern-card">

                <div class="section-header">

                    <h5 class="section-title">
                        Subscription
                    </h5>

                    <div class="section-subtitle">
                        SaaS account information
                    </div>

                </div>

                <div class="finance-list">

                    <div class="finance-item">
                        <span>Plan</span>
                        <strong>Business Plan</strong>
                    </div>

                    <div class="finance-item">
                        <span>Branches</span>
                        <strong>3 / 5</strong>
                    </div>

                    <div class="finance-item">
                        <span>Users</span>
                        <strong>12 / 20</strong>
                    </div>

                    <div class="finance-item">
                        <span>Products</span>
                        <strong>1,245</strong>
                    </div>

                    <div class="finance-item">
                        <span>Renewal</span>
                        <strong>Dec 31, 2026</strong>
                    </div>

                </div>

            </div>

        </div>

    </div>
@endsection
