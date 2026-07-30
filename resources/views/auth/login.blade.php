@extends('layouts.auth')

@section('title', 'RetailPOS')

@section('content')

    <div class="login-page">

        {{-- LEFT SIDE --}}
        <div class="hero-content">

            <div class="dashboard-preview">

                <div class="preview-sidebar">

                    <div class="sidebar-logo">

                        <img
                            src="{{ asset('images/LOGO.png') }}"
                            alt="RetailPOS"
                        >

                    </div>

                    <div class="sidebar-item active">
                        <i class="bi bi-grid"></i>
                    </div>

                    <div class="sidebar-item">
                        <i class="bi bi-cart"></i>
                    </div>

                    <div class="sidebar-item">
                        <i class="bi bi-box-seam"></i>
                    </div>

                    <div class="sidebar-item">
                        <i class="bi bi-people"></i>
                    </div>

                    <div class="sidebar-item">
                        <i class="bi bi-graph-up"></i>
                    </div>

                    <div class="sidebar-item">
                        <i class="bi bi-gear"></i>
                    </div>

                </div>

                <div class="preview-content">

                    <div class="preview-header">

                        <div>

                            <h5>
                                Business Dashboard
                            </h5>

                            <small>
                                Welcome back, Admin
                            </small>

                        </div>

                        <span class="status-live">

                        ● Live

                    </span>

                    </div>

                    <div class="preview-cards">

                        <div class="preview-card">

                            <small>
                                Today's Sales
                            </small>

                            <h3>

                                $8,420

                            </h3>

                            <span>

                            +18.5%

                        </span>

                        </div>

                        <div class="preview-card">

                            <small>

                                Orders

                            </small>

                            <h3>

                                328

                            </h3>

                            <span>

                            Today

                        </span>

                        </div>

                        <div class="preview-card">

                            <small>

                                Products

                            </small>

                            <h3>

                                1,284

                            </h3>

                            <span>

                            Active

                        </span>

                        </div>

                    </div>

                    <div class="preview-chart">

                        <div class="chart-title">

                            Sales Overview

                        </div>

                        <div class="chart-bars">

                            <span style="height:45%"></span>
                            <span style="height:60%"></span>
                            <span style="height:55%"></span>
                            <span style="height:90%"></span>
                            <span style="height:70%"></span>
                            <span style="height:100%"></span>
                            <span style="height:82%"></span>

                        </div>

                    </div>

                    <div class="preview-table">

                        <div class="table-title">

                            Recent Transactions

                        </div>

                        <div class="table-row">

                            <span>INV-100234</span>

                            <span>Wireless Mouse</span>

                            <strong>$22.00</strong>

                        </div>

                        <div class="table-row">

                            <span>INV-100235</span>

                            <span>Printer Ink</span>

                            <strong>$35.50</strong>

                        </div>

                        <div class="table-row">

                            <span>INV-100236</span>

                            <span>USB Flash Drive</span>

                            <strong>$15.00</strong>

                        </div>

                    </div>

                </div>

            </div>

            <span class="hero-badge">

            Cloud-Based Retail & Wholesale POS

        </span>

            <h1 class="hero-title">

                RetailPOS

            </h1>

            <p class="hero-subtitle">

                Manage sales, inventory, barcode scanning,
                customers, suppliers, and business analytics
                from one modern platform.

            </p>

            <div class="hero-stats">

                <div>

                    <strong>

                        500+

                    </strong>

                    <span>

                    Businesses

                </span>

                </div>

                <div>

                    <strong>

                        2M+

                    </strong>

                    <span>

                    Transactions

                </span>

                </div>

                <div>

                    <strong>

                        99.9%

                    </strong>

                    <span>

                    Uptime

                </span>

                </div>

            </div>

            <div class="hero-features">

            <span>

                <i class="bi bi-upc-scan"></i>

                Barcode Scanner

            </span>

                <span>

                <i class="bi bi-box-seam"></i>

                Inventory

            </span>

                <span>

                <i class="bi bi-shop"></i>

                Multi-Branch

            </span>

                <span>

                <i class="bi bi-bar-chart"></i>

                Analytics

            </span>

                <span>

                <i class="bi bi-cloud-check"></i>

                Cloud Sync

            </span>

                <span>

                <i class="bi bi-wifi-off"></i>

                Offline Ready

            </span>

            </div>

        </div>

        {{-- RIGHT SIDE --}}
        <div class="login-card">

            <div class="login-header">

                <h2>

                    Welcome Back 👋

                </h2>

                <p>

                    Sign in to access your business dashboard.

                </p>

            </div>

            <form
                method="POST"
                action="{{ route('login') }}"
            >

                @csrf

                <div class="mb-3">

                    <label class="form-label">

                        Email Address

                    </label>

                    <div class="input-group custom-group">

                    <span class="input-group-text">

                        <i class="bi bi-envelope"></i>

                    </span>

                        <input
                            type="email"
                            name="email"
                            class="form-control auth-input"
                            placeholder="you@company.com"
                        >

                    </div>

                </div>

                <div class="mb-4">

                    <label class="form-label">

                        Password

                    </label>

                    <div class="input-group custom-group">

                    <span class="input-group-text">

                        <i class="bi bi-lock"></i>

                    </span>

                        <input
                            type="password"
                            name="password"
                            class="form-control auth-input"
                            placeholder="Enter your password"
                        >

                    </div>

                </div>

                <button
                    class="btn login-btn"
                    type="submit"
                >

                    Sign In

                    <i class="bi bi-arrow-right ms-2"></i>

                </button>

            </form>

            <div class="divider">

            <span>

                OR

            </span>

            </div>

            <a
                href="{{ route('google.redirect') }}"
                class="google-btn"
            >

                <i class="bi bi-google"></i>

                Continue with Google

            </a>

        </div>

    </div>

@endsection
