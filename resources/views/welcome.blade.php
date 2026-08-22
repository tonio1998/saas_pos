<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'LikhaPOS') }} - Cloud Minimart POS & CRM System</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons & Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --emerald-primary: #059669;
            --emerald-hover: #047857;
            --emerald-light: #d1fae5;
            --emerald-soft: #ecfdf5;
            --gold-primary: #f59e0b;
            --gold-hover: #d97706;
            --gold-soft: #fef3c7;
            --gold-text: #b45309;
            --slate-dark: #0f172a;
            --slate-body: #334155;
            --slate-muted: #64748b;
            --bg-light: #f8fafc;
            --card-bg: #ffffff;
            --border-color: #e2e8f0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-light);
            color: var(--slate-body);
            overflow-x: hidden;
        }

        /* Navbar */
        .navbar-landing {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-color);
            padding: 16px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .brand-title {
            font-weight: 800;
            font-size: 1.35rem;
            color: var(--slate-dark);
        }

        .brand-badge {
            background: var(--emerald-soft);
            color: var(--emerald-primary);
            font-size: 0.75rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            border: 1px solid var(--emerald-light);
        }

        .btn-emerald {
            background-color: var(--emerald-primary);
            color: #ffffff;
            font-weight: 700;
            padding: 10px 24px;
            border-radius: 12px;
            border: none;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(5, 150, 105, 0.25);
        }

        .btn-emerald:hover {
            background-color: var(--emerald-hover);
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(5, 150, 105, 0.35);
        }

        .btn-gold-outline {
            background-color: #ffffff;
            color: var(--slate-dark);
            font-weight: 700;
            padding: 10px 24px;
            border-radius: 12px;
            border: 2px solid var(--gold-primary);
            transition: all 0.2s ease;
        }

        .btn-gold-outline:hover {
            background-color: var(--gold-soft);
            color: var(--gold-text);
            border-color: var(--gold-hover);
        }

        /* Hero Section */
        .hero-section {
            padding: 80px 0 60px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            position: relative;
        }

        .hero-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--gold-soft);
            color: var(--gold-text);
            font-weight: 700;
            font-size: 0.85rem;
            padding: 6px 16px;
            border-radius: 50px;
            border: 1px solid rgba(245, 158, 11, 0.3);
            margin-bottom: 24px;
        }

        .hero-title {
            font-size: 3.2rem;
            font-weight: 800;
            color: var(--slate-dark);
            line-height: 1.2;
            margin-bottom: 20px;
        }

        .text-emerald-highlight {
            color: var(--emerald-primary);
        }

        .text-gold-highlight {
            color: var(--gold-primary);
        }

        .hero-subtitle {
            font-size: 1.15rem;
            color: var(--slate-muted);
            line-height: 1.6;
            margin-bottom: 36px;
            max-width: 680px;
        }

        /* Stats Cards */
        .stat-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03);
            transition: all 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
            border-color: var(--emerald-light);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 800;
            color: var(--emerald-primary);
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 0.85rem;
            color: var(--slate-muted);
            font-weight: 600;
        }

        /* Feature Card */
        .feature-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 32px;
            height: 100%;
            transition: all 0.25s ease;
        }

        .feature-card:hover {
            border-color: var(--emerald-primary);
            box-shadow: 0 12px 30px rgba(5, 150, 105, 0.08);
            transform: translateY(-4px);
        }

        .feature-icon {
            width: 54px;
            height: 54px;
            border-radius: 14px;
            background: var(--emerald-soft);
            color: var(--emerald-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 20px;
        }

        .feature-icon-gold {
            background: var(--gold-soft);
            color: var(--gold-text);
        }

        /* Calculator Box */
        .calculator-box {
            background: #ffffff;
            border: 2px solid var(--emerald-light);
            border-radius: 24px;
            padding: 36px;
            box-shadow: 0 12px 36px rgba(5, 150, 105, 0.08);
        }

        /* Comparison Table */
        .comparison-table th, .comparison-table td {
            padding: 16px;
            vertical-align: middle;
        }

        .comparison-table tbody tr:hover {
            background-color: var(--emerald-soft);
        }

        /* Pricing Card */
        .pricing-card {
            background: #ffffff;
            border: 2px solid var(--border-color);
            border-radius: 24px;
            padding: 36px 28px;
            position: relative;
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .pricing-card.featured {
            border-color: var(--gold-primary);
            box-shadow: 0 12px 36px rgba(245, 158, 11, 0.15);
        }

        .popular-badge {
            position: absolute;
            top: -14px;
            right: 28px;
            background: var(--gold-primary);
            color: #ffffff;
            font-size: 0.75rem;
            font-weight: 800;
            padding: 4px 14px;
            border-radius: 50px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .plan-title {
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--slate-dark);
            margin-bottom: 8px;
        }

        .plan-price {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--slate-dark);
            margin-bottom: 16px;
        }

        .plan-price span {
            font-size: 1rem;
            font-weight: 500;
            color: var(--slate-muted);
        }

        .plan-features {
            list-style: none;
            padding: 0;
            margin: 24px 0 32px;
            flex-grow: 1;
        }

        .plan-features li {
            padding: 8px 0;
            font-size: 0.95rem;
            color: var(--slate-body);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .plan-features i {
            color: var(--emerald-primary);
            font-size: 1.1rem;
        }

        /* Testimonial Card */
        .testimonial-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 28px;
            height: 100%;
        }

        /* FAQ Accordion */
        .accordion-button:not(.collapsed) {
            background-color: var(--emerald-soft);
            color: var(--emerald-primary);
            font-weight: 700;
        }

        /* CTA Banner */
        .cta-banner {
            background: linear-gradient(135deg, #059669, #047857);
            border-radius: 28px;
            padding: 50px 40px;
            color: #ffffff;
            position: relative;
            overflow: hidden;
        }

        .cta-banner::before {
            content: '';
            position: absolute;
            top: -60px;
            right: -60px;
            width: 260px;
            height: 260px;
            background: rgba(245, 158, 11, 0.2);
            border-radius: 50%;
        }

        /* Footer */
        .footer-landing {
            background: #ffffff;
            border-top: 1px solid var(--border-color);
            padding: 40px 0;
            font-size: 0.9rem;
            color: var(--slate-muted);
        }
    </style>
</head>
<body>

    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-landing">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="#">
                <div class="brand-title">Barya<span class="text-emerald-highlight">POS</span></div>
                <span class="brand-badge">Minimart & CRM</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarLanding">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarLanding">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 fw-semibold">
                    <li class="nav-item"><a class="nav-link text-dark px-3" href="#features">Features</a></li>
                    <li class="nav-item"><a class="nav-link text-dark px-3" href="#calculator">ROI Calculator</a></li>
                    <li class="nav-item"><a class="nav-link text-dark px-3" href="#comparison">Why Choose Us</a></li>
                    <li class="nav-item"><a class="nav-link text-dark px-3" href="#pricing">Plans & Pricing</a></li>
                    <li class="nav-item"><a class="nav-link text-dark px-3" href="#faq">FAQ</a></li>
                </ul>

                <div class="d-flex align-items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard.index') }}" class="btn btn-emerald">
                            <i class="bi bi-speedometer2 me-1"></i> Go to Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-gold-outline">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Log In
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-emerald">
                            Start Free Trial
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container text-center text-lg-start">
            <div class="row align-items-center g-5">
                <div class="col-lg-7">
                    <div class="hero-tag">
                        <i class="bi bi-star-fill text-gold-highlight"></i> Designed Specifically for PH Minimarts & Groceries
                    </div>
                    <h1 class="hero-title">
                        I-Automate ang <span class="text-emerald-highlight">Minimart Sales</span> & <span class="text-gold-highlight">Utang CRM</span> Mo!
                    </h1>
                    <p class="hero-subtitle">
                        Iwasan ang nawawalang paninda, i-track ang customer utang sa ledger, at mag-print ng BIR-ready receipts gamit ang laptop, tablet, o cellphone.
                    </p>

                    <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start mb-5">
                        <a href="{{ route('register') }}" class="btn btn-emerald btn-lg px-4 py-3">
                            <i class="bi bi-rocket-takeoff-fill me-2"></i> Subukan nang Libre sa 7 Araw
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-gold-outline btn-lg px-4 py-3">
                            <i class="bi bi-shield-lock-fill me-2 text-gold-highlight"></i> Store Admin Sign In
                        </a>
                    </div>

                    <div class="d-flex align-items-center gap-4 text-muted small justify-content-center justify-content-lg-start">
                        <div><i class="bi bi-check-circle-fill text-emerald-highlight me-1"></i> No Credit Card Required</div>
                        <div><i class="bi bi-check-circle-fill text-emerald-highlight me-1"></i> BIR Tax Compliance Ready</div>
                        <div><i class="bi bi-check-circle-fill text-emerald-highlight me-1"></i> 1-Device Session Security</div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card border-0 shadow-lg rounded-4 p-4 bg-white position-relative">
                        <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
                            <div>
                                <h5 class="fw-bold m-0 text-dark">Live Store System Metrics</h5>
                                <span class="badge bg-success-subtle text-success fw-bold">100% Operational Status</span>
                            </div>
                            <i class="bi bi-shop fs-2 text-emerald-highlight"></i>
                        </div>

                        <div class="row g-3">
                            <div class="col-6">
                                <div class="stat-card">
                                    <div class="stat-number">500+</div>
                                    <div class="stat-label">Active PH Minimarts</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card">
                                    <div class="stat-number">2.5M+</div>
                                    <div class="stat-label">Daily Items Scanned</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card">
                                    <div class="stat-number">99.9%</div>
                                    <div class="stat-label">Cloud Uptime SLA</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card">
                                    <div class="stat-number text-gold-highlight">₱299</div>
                                    <div class="stat-label">Starting Price / Mo</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Key Features Section -->
    <section id="features" class="py-5 bg-white">
        <div class="container py-4">
            <div class="text-center max-w-700 mx-auto mb-5">
                <span class="badge bg-emerald-subtle text-success fw-bold px-3 py-2 rounded-pill mb-2">Features Built for Retail Success</span>
                <h2 class="fw-extrabold text-dark display-6">Lahat ng Kailangan ng Store Mo, Nandito Na</h2>
                <p class="text-muted">Designed for maximum cashier speed, stock inventory accuracy, and instant utang tracking.</p>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="bi bi-cart-check-fill"></i></div>
                        <h4 class="fw-bold text-dark">Mabilis na Barcode Checkout</h4>
                        <p class="text-muted">Scan barcodes with any USB/Bluetooth scanner or mobile camera. Instant receipt printing & cash drawer trigger.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon feature-icon-gold"><i class="bi bi-journal-bookmark-fill"></i></div>
                        <h4 class="fw-bold text-dark">Customer Utang & Ledger CRM</h4>
                        <p class="text-muted">Wala nang nawawalang cuaderno! Record customer credit balances, payment receipts, and credit limit warnings.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="bi bi-shield-lock-fill"></i></div>
                        <h4 class="fw-bold text-dark">1-Device Active Session Protection</h4>
                        <p class="text-muted">Strict single device login per cashier account to prevent simultaneous unauthorized access and theft.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon feature-icon-gold"><i class="bi bi-box-seam-fill"></i></div>
                        <h4 class="fw-bold text-dark">Real-Time Inventory & Low Stock Alerts</h4>
                        <p class="text-muted">Automatic stock deductions per sale. Receive instant alerts when inventory falls below minimum reorder points.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="bi bi-graph-up-arrow"></i></div>
                        <h4 class="fw-bold text-dark">Daily Profit & Sales Analytics</h4>
                        <p class="text-muted">Know your daily net profit, fast-selling products, and shift sales summaries anytime on your phone or PC.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon feature-icon-gold"><i class="bi bi-receipt-cutoff"></i></div>
                        <h4 class="fw-bold text-dark">BIR Tax & Senior Discount Ready</h4>
                        <p class="text-muted">12% VAT breakdown computation, Senior Citizen / PWD discount processing, and official receipt customization.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Interactive ROI Calculator Section -->
    <section id="calculator" class="py-5 bg-light">
        <div class="container py-4">
            <div class="text-center max-w-700 mx-auto mb-5">
                <span class="hero-tag mb-2"><i class="bi bi-calculator-fill text-gold-highlight me-1"></i> Interactive Savings Calculator</span>
                <h2 class="fw-extrabold text-dark display-6">Gaano Kalaki ang Matitipid ng Store Mo?</h2>
                <p class="text-muted">Tingnan kung magkano ang maiiwasang malugi mula sa nawawalang paninda at hindi nasisingil na utang.</p>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-9">
                    <div class="calculator-box">
                        <div class="row g-4 align-items-center">
                            <div class="col-md-6">
                                <label class="fw-bold text-dark mb-2">Estimate Your Daily Store Sales (₱)</label>
                                <input type="range" class="form-range" id="salesRange" min="3000" max="50000" step="1000" value="15000" oninput="updateCalculator(this.value)">
                                <div class="d-flex justify-content-between text-muted small fw-semibold">
                                    <span>₱3,000 / day</span>
                                    <span class="text-emerald-highlight fw-extrabold fs-5" id="salesDisplay">₱15,000 / day</span>
                                    <span>₱50,000 / day</span>
                                </div>
                            </div>
                            <div class="col-md-6 border-start-md ps-md-4">
                                <div class="bg-emerald-soft p-3 rounded-4 mb-3 border border-emerald-light">
                                    <div class="small text-muted fw-semibold">Estimated Monthly Savings from Inventory & Utang Loss:</div>
                                    <div class="fs-2 fw-extrabold text-emerald-highlight" id="savingsDisplay">₱13,500 / month</div>
                                </div>
                                <div class="small text-muted">
                                    <i class="bi bi-info-circle-fill text-gold-highlight me-1"></i> Based on average 3% inventory shrinkage & uncollected utang in un-automated minimarts.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Comparison Table Section -->
    <section id="comparison" class="py-5 bg-white">
        <div class="container py-4">
            <div class="text-center max-w-700 mx-auto mb-5">
                <span class="badge bg-gold-subtle text-warning-emphasis fw-bold px-3 py-2 rounded-pill mb-2">Bakit LikhaPOS ang Pinakamagandang Desisyon?</span>
                <h2 class="fw-extrabold text-dark display-6">Sulat sa Cuaderno vs LikhaPOS Cloud</h2>
            </div>

            <div class="table-responsive shadow-sm rounded-4 border bg-white">
                <table class="table table-hover align-middle mb-0 text-center vs-table">
                    <thead>
                        <tr>
                            <th class="text-start bg-light text-muted ps-4" style="width: 30%;">Mga Tampok at Kakayahan</th>
                            <th class="bg-light text-muted" style="width: 35%;">Cuaderno / Mano-mano</th>
                            <th class="bg-emerald-primary text-white" style="width: 35%; font-size: 1.1rem;">✅ LikhaPOS Cloud</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-start ps-4 fw-semibold">Bilis ng Cashier Checkout</td>
                            <td class="text-muted">Mabagal (Manual Calculator)</td>
                            <td class="fw-bold text-success"><i class="bi bi-check-circle-fill me-1"></i> Instant Barcode Scan</td>
                        </tr>
                        <tr>
                            <td class="text-start ps-4 fw-semibold">Customer Utang Tracking</td>
                            <td class="text-muted">Madaling mawala ang sulat</td>
                            <td class="fw-bold text-success"><i class="bi bi-check-circle-fill me-1"></i> Digital Ledger CRM</td>
                        </tr>
                        <tr>
                            <td class="text-start ps-4 fw-semibold">Inventory Stock Monitoring</td>
                            <td class="text-muted">Kailangan mag-bilang nang mano-mano</td>
                            <td class="fw-bold text-success"><i class="bi bi-check-circle-fill me-1"></i> Real-time Stock Deduction</td>
                        </tr>
                        <tr>
                            <td class="text-start ps-4 fw-semibold">Security & Cashier Integrity</td>
                            <td class="text-muted">Delikado sa kupit at kupas</td>
                            <td class="fw-bold text-success"><i class="bi bi-check-circle-fill me-1"></i> 1-Device Guard & Audit Trail</td>
                        </tr>
                        <tr>
                            <td class="text-start ps-4 fw-semibold">Presyo & Hardware Requirement</td>
                            <td class="text-muted">Mura pero Malaki ang Lugi</td>
                            <td class="fw-bold text-success"><i class="bi bi-check-circle-fill me-1"></i> Starts at ₱299/mo (Gamit kahit Cellphone/PC)</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Subscription Levels Section -->
    <section id="pricing" class="py-5 bg-light">
        <div class="container py-4">
            <div class="text-center max-w-700 mx-auto mb-5">
                <span class="hero-tag mb-2">3 Flexible Scalable Tiers</span>
                <h2 class="fw-extrabold text-dark display-6">Affordable & Transparent Pricing</h2>
                <p class="text-muted">Pumili ng tamang plano para sa laki ng store mo. Walang hidden charges!</p>
            </div>

            <div class="row g-4 align-items-stretch">
                <!-- Level I: Tindahan Starter -->
                <div class="col-lg-4">
                    <div class="pricing-card">
                        <div class="plan-title">Tindahan Starter</div>
                        <p class="text-muted small mb-3">Affordable starter plan (₱10/day • Single register store)</p>
                        <div class="plan-price">₱299 <span>/ month</span></div>

                        <ul class="plan-features">
                            <li><i class="bi bi-check-circle-fill"></i> <strong>Full App & POS Terminal Access</strong></li>
                            <li><i class="bi bi-check-circle-fill"></i> <strong>Utang CRM & Customer Ledgers</strong></li>
                            <li><i class="bi bi-check-circle-fill"></i> <strong>1 Admin Account</strong></li>
                            <li><i class="bi bi-check-circle-fill"></i> <strong>1 Cashier Account</strong></li>
                            <li><i class="bi bi-check-circle-fill"></i> Up to 1,000 Products</li>
                            <li><i class="bi bi-check-circle-fill"></i> Inventory & Stock Alerts</li>
                            <li><i class="bi bi-check-circle-fill"></i> Single Device Session Guard</li>
                        </ul>

                        <a href="{{ route('register', ['plan' => 1]) }}" class="btn btn-gold-outline w-full py-3 mt-auto">
                            Choose Tindahan Starter
                        </a>
                    </div>
                </div>

                <!-- Level II: Suki Growth (Featured) -->
                <div class="col-lg-4">
                    <div class="pricing-card featured">
                        <span class="popular-badge">Most Popular</span>
                        <div class="plan-title">Suki Growth</div>
                        <p class="text-muted small mb-3">Best value growth plan (₱20/day • Multi-shift stores)</p>
                        <div class="plan-price text-emerald-highlight">₱599 <span>/ month</span></div>

                        <ul class="plan-features">
                            <li><i class="bi bi-check-circle-fill"></i> <strong>Full App & POS Terminal Access</strong></li>
                            <li><i class="bi bi-check-circle-fill"></i> <strong>Utang CRM & Customer Ledgers</strong></li>
                            <li><i class="bi bi-check-circle-fill"></i> <strong>1 Admin Account</strong></li>
                            <li><i class="bi bi-check-circle-fill"></i> <strong>3 Cashier Accounts</strong></li>
                            <li><i class="bi bi-check-circle-fill"></i> Up to 5,000 Products</li>
                            <li><i class="bi bi-check-circle-fill"></i> Profit & Sales Analytics</li>
                            <li><i class="bi bi-check-circle-fill"></i> Single Device Session Guard</li>
                        </ul>

                        <a href="{{ route('register', ['plan' => 2]) }}" class="btn btn-emerald w-full py-3 mt-auto">
                            Get Started Suki Growth
                        </a>
                    </div>
                </div>

                <!-- Level III: Negosyo Pro -->
                <div class="col-lg-4">
                    <div class="pricing-card">
                        <div class="plan-title">Negosyo Pro</div>
                        <p class="text-muted small mb-3">Enterprise plan (₱43/day • Multi-branch store chains)</p>
                        <div class="plan-price">₱1,299 <span>/ month</span></div>

                        <ul class="plan-features">
                            <li><i class="bi bi-check-circle-fill"></i> <strong>Full App & POS Terminal Access</strong></li>
                            <li><i class="bi bi-check-circle-fill"></i> <strong>Utang CRM & Customer Ledgers</strong></li>
                            <li><i class="bi bi-check-circle-fill"></i> <strong>5 Admin Accounts</strong></li>
                            <li><i class="bi bi-check-circle-fill"></i> <strong>20 Cashier Accounts</strong></li>
                            <li><i class="bi bi-check-circle-fill"></i> Up to 50,000 Products</li>
                            <li><i class="bi bi-check-circle-fill"></i> Multi-Branch Store Support</li>
                            <li><i class="bi bi-check-circle-fill"></i> BIR Receipt & Tax Reports</li>
                        </ul>

                        <a href="{{ route('register', ['plan' => 3]) }}" class="btn btn-gold-outline w-full py-3 mt-auto">
                            Choose Negosyo Pro
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Dynamic Store Reviews Section -->
    <section class="py-5 bg-white">
        <div class="container py-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-5 gap-3">
                <div>
                    <span class="badge bg-emerald-subtle text-success fw-bold px-3 py-2 rounded-pill mb-2">Verified Real Store Reviews</span>
                    <h2 class="fw-extrabold text-dark display-6 m-0">Ano ang Sinasabi ng mga Store Owners?</h2>
                </div>
                <button type="button" class="btn btn-gold-outline" data-bs-toggle="modal" data-bs-target="#reviewModal">
                    <i class="bi bi-chat-left-text-fill me-1 text-gold-highlight"></i> Submit Store Review
                </button>
            </div>

            <div class="row g-4">
                @if(isset($reviews) && count($reviews) > 0)
                    @foreach($reviews as $rev)
                        <div class="col-md-4">
                            <div class="testimonial-card">
                                <div class="text-warning mb-3">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi bi-star{{ $i <= $rev->rating ? '-fill' : '' }}"></i>
                                    @endfor
                                </div>
                                <p class="text-muted fst-italic mb-4">"{{ $rev->review_text }}"</p>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-emerald-soft text-emerald-primary fw-bold d-flex align-items-center justify-content-center" style="width:44px;height:44px;">
                                        {{ $rev->avatar_initials ?? 'ST' }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $rev->reviewer_name }}</div>
                                        <div class="small text-muted">Owner, {{ $rev->store_name }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-12 text-center py-4 text-muted">
                        Be the first store owner to leave a review!
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-5 bg-light">
        <div class="container py-4">
            <div class="text-center max-w-700 mx-auto mb-5">
                <span class="hero-tag mb-2">Got Questions?</span>
                <h2 class="fw-extrabold text-dark display-6">Frequently Asked Questions</h2>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item border-0 mb-3 rounded-3 shadow-sm">
                            <h2 class="accordion-header">
                                <button class="accordion-button rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    Kailangan ba ng mahal na POS Hardware or Touchscreen Machine?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted">
                                    Hindi! Pwedeng-pwede mong gamitin ang kahit anong existing Laptop, Desktop PC, Android Tablet, o Cellphone. Gumagana ito sa kahit anong standard USB or Bluetooth barcode scanner at thermal receipt printer.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-0 mb-3 rounded-3 shadow-sm">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    Paano kapag nawalan ng Internet Connection habang nagtitinda?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted">
                                    May built-in offline caching protection ang LikhaPOS Terminal. Makakapag-scan pa rin ang cashier at kapag nag-online ang koneksyon, kusa nitong i-sync ang benta sa cloud.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-0 mb-3 rounded-3 shadow-sm">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    Ligtas ba ang 1-Device Session Security?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted">
                                    Opo! Kapag naka-log in ang cashier sa POS terminal ng store, hindi makakapag-log in ang parehong cashier account sa ibang device. Pinipigilan nito ang kahit anong kahina-hinalang sabay na pag-access.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-0 mb-3 rounded-3 shadow-sm">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                    May kontrata ba o lock-in period?
                                </button>
                            </h2>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted">
                                    Walang lock-in contract! Pwede kang mag-subscribe monthly at mag-upgrade o mag-cancel anumang oras nang walang penalty.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Urgency Call-To-Action Banner -->
    <section class="py-5 bg-white">
        <div class="container">
            <div class="cta-banner text-center text-lg-start">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <h2 class="fw-extrabold fs-1 mb-3">Handa Ka Na Bang Palaguin ang Minimart Mo?</h2>
                        <p class="lead opacity-90 m-0">I-setup ang store mo sa loob lamang ng 3 minuto. Subukan nang libre sa loob ng 7 araw, walang credit card na kailangan!</p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <a href="{{ route('register') }}" class="btn btn-warning btn-lg px-4 py-3 fw-extrabold shadow text-dark">
                            <i class="bi bi-rocket-takeoff-fill me-2"></i> Register Store Free
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer-landing">
        <div class="container text-center text-md-start">
            <div class="row g-4 align-items-center">
                <div class="col-md-6">
                    <div class="fw-bold text-dark fs-5">Likha<span class="text-emerald-highlight">POS</span> Minimart & CRM</div>
                    <div class="small mt-1">© {{ date('Y') }} LikhaPOS SaaS Platform. All rights reserved.</div>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="{{ route('login') }}" class="btn btn-emerald btn-sm px-3">
                        <i class="bi bi-shield-lock me-1"></i> Store Admin / Cashier Login
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Submit Store Review Modal -->
    <div class="modal fade" id="reviewModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark">Submit Your LikhaPOS Store Review</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('reviews.store') }}">
                    @csrf
                    <div class="modal-body py-3">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark">Your Name / Title *</label>
                            <input type="text" name="reviewer_name" class="form-control rounded-3" placeholder="e.g. Aling Maria Santos" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark">Store / Minimart Name *</label>
                            <input type="text" name="store_name" class="form-control rounded-3" placeholder="e.g. San Jose Minimart" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark">Rating *</label>
                            <select name="rating" class="form-select rounded-3" required>
                                <option value="5">⭐⭐⭐⭐⭐ (5 Stars - Excellent)</option>
                                <option value="4">⭐⭐⭐⭐ (4 Stars - Very Good)</option>
                                <option value="3">⭐⭐⭐ (3 Stars - Good)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark">Your Honest Feedback *</label>
                            <textarea name="review_text" class="form-control rounded-3" rows="4" placeholder="Ibahagi ang inyong karanasan sa paggamit ng LikhaPOS sa inyong store..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-emerald rounded-3 px-4">Post Store Review</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateCalculator(val) {
            document.getElementById('salesDisplay').innerText = '₱' + parseInt(val).toLocaleString() + ' / day';
            // Savings estimate: 3% of monthly sales (30 days)
            const monthlySales = val * 30;
            const savings = monthlySales * 0.03;
            document.getElementById('savingsDisplay').innerText = '₱' + Math.round(savings).toLocaleString() + ' / month';
        }
    </script>
</body>
</html>
