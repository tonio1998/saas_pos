<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'LikhaPOS') }} - Cloud Minimart POS & CRM System</title>
    
    <!-- Fonts: Outfit (Headings & Badges) + Plus Jakarta Sans (Body & UI) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons & Bootstrap 5.3 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            /* Canva-Inspired Signature Color Palette */
            --canva-purple: #7d2ae8;
            --canva-violet: #5e17eb;
            --canva-cyan: #00c4cc;
            --canva-blue: #155dfc;
            --canva-royal: #0d47a1;
            --canva-pink: #ff4b6b;
            --canva-coral: #ff758c;
            --canva-amber: #ffb703;
            --canva-gold: #f59e0b;
            --canva-emerald: #10b981;
            --canva-dark: #0f172a;
            --canva-slate: #334155;
            --canva-muted: #64748b;
            --canva-light-bg: #f8fafc;
            --canva-card-bg: #ffffff;
            --canva-border: #e2e8f0;
            
            /* Gradients */
            --gradient-canva: linear-gradient(135deg, #7d2ae8 0%, #00c4cc 100%);
            --gradient-vibrant: linear-gradient(135deg, #5e17eb 0%, #7d2ae8 45%, #00c4cc 100%);
            --gradient-sunset: linear-gradient(135deg, #ff4b6b 0%, #ffb703 100%);
            --gradient-cyan-blue: linear-gradient(135deg, #00c4cc 0%, #155dfc 100%);
            --gradient-mesh: radial-gradient(at 0% 0%, rgba(125, 42, 232, 0.15) 0px, transparent 50%),
                             radial-gradient(at 100% 0%, rgba(0, 196, 204, 0.15) 0px, transparent 50%),
                             radial-gradient(at 50% 100%, rgba(255, 75, 107, 0.10) 0px, transparent 50%);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #fafbfc;
            color: var(--canva-slate);
            overflow-x: hidden;
            letter-spacing: -0.01em;
        }

        h1, h2, h3, h4, .font-heading {
            font-family: 'Outfit', sans-serif;
            letter-spacing: -0.02em;
        }

        /* Decorative Canva Floating Aurora Glow Orbs */
        .canva-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            pointer-events: none;
            z-index: 0;
            opacity: 0.55;
        }
        .orb-purple {
            width: 480px;
            height: 480px;
            background: rgba(125, 42, 232, 0.18);
            top: -120px;
            left: -100px;
        }
        .orb-cyan {
            width: 440px;
            height: 440px;
            background: rgba(0, 196, 204, 0.16);
            top: 100px;
            right: -80px;
        }
        .orb-pink {
            width: 380px;
            height: 380px;
            background: rgba(255, 75, 107, 0.12);
            bottom: 20%;
            left: 15%;
        }

        /* Gradient Text Helper */
        .gradient-text {
            background: var(--gradient-vibrant);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: inline-block;
        }
        .gradient-text-sunset {
            background: var(--gradient-sunset);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: inline-block;
        }
        .gradient-text-cyan {
            background: var(--gradient-cyan-blue);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: inline-block;
        }

        /* Canva-Style Sticky Frosted Glass Navbar */
        .navbar-canva {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            padding: 14px 0;
            position: sticky;
            top: 0;
            z-index: 1050;
            transition: all 0.3s ease;
        }
        .navbar-canva.scrolled {
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
            background: rgba(255, 255, 255, 0.96);
        }

        .brand-logo-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .brand-icon-box {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: var(--gradient-vibrant);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 1.25rem;
            box-shadow: 0 4px 14px rgba(125, 42, 232, 0.3);
            transition: transform 0.25s ease;
            flex-shrink: 0;
        }
        .brand-logo-wrap:hover .brand-icon-box {
            transform: rotate(-6deg) scale(1.05);
        }
        .brand-name {
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 1.45rem;
            color: var(--canva-dark);
            line-height: 1;
            white-space: nowrap;
        }
        .brand-badge-pill {
            background: rgba(0, 196, 204, 0.12);
            color: #00878e;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            border: 1px solid rgba(0, 196, 204, 0.25);
            letter-spacing: 0.3px;
            white-space: nowrap;
            display: inline-block;
            flex-shrink: 0;
        }

        .navbar-nav {
            flex-wrap: nowrap !important;
            gap: 2px;
        }

        .nav-link-canva {
            color: var(--canva-slate);
            font-weight: 600;
            font-size: 0.88rem;
            padding: 8px 12px !important;
            border-radius: 50px;
            transition: all 0.2s ease;
            white-space: nowrap !important;
            display: inline-block;
        }
        .nav-link-canva:hover {
            color: var(--canva-violet);
            background: rgba(125, 42, 232, 0.06);
        }

        /* Canva Signature Buttons */
        .btn-canva-primary {
            background: var(--gradient-vibrant);
            color: #ffffff;
            font-weight: 700;
            font-family: 'Outfit', sans-serif;
            padding: 9px 20px;
            font-size: 0.92rem;
            border-radius: 50px;
            border: none;
            box-shadow: 0 6px 20px rgba(125, 42, 232, 0.32);
            transition: all 0.25s cubic-bezier(0.2, 0.8, 0.2, 1);
            position: relative;
            overflow: hidden;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            white-space: nowrap !important;
            flex-shrink: 0;
        }
        .btn-canva-primary:hover {
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 10px 26px rgba(125, 42, 232, 0.42);
        }
        .btn-canva-outline {
            background: #ffffff;
            color: var(--canva-dark);
            font-weight: 700;
            font-family: 'Outfit', sans-serif;
            padding: 8px 18px;
            font-size: 0.92rem;
            border-radius: 50px;
            border: 1.5px solid #cbd5e1;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap !important;
            flex-shrink: 0;
        }
        .btn-canva-outline:hover {
            border-color: var(--canva-violet);
            color: var(--canva-violet);
            background: rgba(125, 42, 232, 0.04);
            transform: translateY(-1px);
        }

        /* Hero Section */
        .hero-section {
            padding: 70px 0 60px;
            position: relative;
            background: var(--gradient-mesh);
        }
        .hero-sticker-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #ffffff;
            padding: 6px 18px;
            border-radius: 50px;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.06);
            border: 1px solid rgba(226, 232, 240, 0.9);
            font-size: 0.86rem;
            font-weight: 700;
            color: var(--canva-dark);
            margin-bottom: 24px;
        }
        .hero-sticker-badge .sparkle {
            color: var(--canva-amber);
            font-size: 1.1rem;
        }
        .hero-title {
            font-size: 3.5rem;
            font-weight: 900;
            color: var(--canva-dark);
            line-height: 1.15;
            margin-bottom: 20px;
        }
        .hero-subtitle {
            font-size: 1.2rem;
            line-height: 1.65;
            color: var(--canva-muted);
            max-width: 600px;
            margin-bottom: 34px;
        }

        /* Canva Studio POS Interactive Canvas Mockup */
        .pos-studio-frame {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.12), 0 0 0 1px rgba(226, 232, 240, 0.9);
            position: relative;
            overflow: visible;
        }
        .canva-toolbar-header {
            background: #f8fafc;
            border-bottom: 1px solid var(--canva-border);
            padding: 10px 18px;
            border-radius: 24px 24px 0 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .canva-window-dots {
            display: flex;
            gap: 6px;
        }
        .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }
        .dot-red { background: #ff5f56; }
        .dot-yellow { background: #ffbd2e; }
        .dot-green { background: #27c93f; }

        .canva-canvas-title {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--canva-muted);
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .canva-canvas-title span {
            background: rgba(125, 42, 232, 0.1);
            color: var(--canva-violet);
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 0.7rem;
        }

        .pos-canvas-body {
            padding: 20px;
            background: #f1f5f9;
            border-radius: 0 0 24px 24px;
            position: relative;
        }

        /* Interactive POS Screen Inside Mockup */
        .pos-inner-screen {
            background: #ffffff;
            border-radius: 16px;
            border: 2px dashed #3b82f6; /* Canva active selection boundary */
            padding: 16px;
            position: relative;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
        }
        /* Selection handles */
        .selection-handle {
            position: absolute;
            width: 10px;
            height: 10px;
            background: #ffffff;
            border: 2px solid #3b82f6;
            border-radius: 50%;
            z-index: 10;
        }
        .handle-tl { top: -6px; left: -6px; }
        .handle-tr { top: -6px; right: -6px; }
        .handle-bl { bottom: -6px; left: -6px; }
        .handle-br { bottom: -6px; right: -6px; }

        .pos-cart-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 10px;
            border-radius: 10px;
            background: #f8fafc;
            margin-bottom: 8px;
            font-size: 0.86rem;
            border: 1px solid #f1f5f9;
        }

        /* Floating Canva Cursors & Labels */
        .floating-cursor {
            position: absolute;
            z-index: 20;
            pointer-events: none;
            filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.15));
            animation: cursorFloat 4s ease-in-out infinite alternate;
        }
        .cursor-purple {
            top: 25%;
            right: -25px;
        }
        .cursor-teal {
            bottom: 20px;
            left: -20px;
            animation-delay: -2s;
        }
        .cursor-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #ffffff;
            font-size: 0.76rem;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 30px;
            white-space: nowrap;
            margin-left: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .cursor-pill-purple {
            background: var(--canva-violet);
        }
        .cursor-pill-teal {
            background: #009688;
        }

        @keyframes cursorFloat {
            0% { transform: translateY(0px) rotate(0deg); }
            100% { transform: translateY(-8px) rotate(2deg); }
        }

        /* Floating Metric Cards */
        .floating-metric-card {
            position: absolute;
            background: #ffffff;
            border-radius: 16px;
            padding: 12px 16px;
            box-shadow: 0 16px 36px rgba(15, 23, 42, 0.12), 0 0 0 1px rgba(226, 232, 240, 0.8);
            z-index: 15;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: cardFloat 5s ease-in-out infinite alternate;
        }
        .metric-card-top {
            top: -24px;
            left: 20px;
        }
        .metric-card-bottom {
            bottom: -28px;
            right: 25px;
            animation-delay: -2.5s;
        }
        @keyframes cardFloat {
            0% { transform: translateY(0px); }
            100% { transform: translateY(-10px); }
        }

        /* Section Styling */
        .section-header-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 16px;
            border-radius: 50px;
            font-size: 0.82rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 12px;
        }
        .badge-purple {
            background: rgba(125, 42, 232, 0.1);
            color: var(--canva-violet);
        }
        .badge-cyan {
            background: rgba(0, 196, 204, 0.12);
            color: #008f95;
        }
        .badge-gold {
            background: rgba(255, 183, 3, 0.15);
            color: #b45309;
        }

        /* Canva Feature Cards */
        .canva-feature-card {
            background: #ffffff;
            border-radius: 22px;
            padding: 32px 28px;
            border: 1px solid rgba(226, 232, 240, 0.9);
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.03);
            transition: all 0.3s cubic-bezier(0.2, 0.8, 0.2, 1);
            height: 100%;
            position: relative;
            overflow: hidden;
        }
        .canva-feature-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 36px rgba(125, 42, 232, 0.09);
            border-color: rgba(125, 42, 232, 0.3);
        }
        .canva-feature-icon {
            width: 58px;
            height: 58px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            margin-bottom: 22px;
            transition: transform 0.25s ease;
        }
        .canva-feature-card:hover .canva-feature-icon {
            transform: scale(1.1) rotate(-4deg);
        }
        .icon-bg-violet { background: rgba(125, 42, 232, 0.12); color: var(--canva-violet); }
        .icon-bg-cyan { background: rgba(0, 196, 204, 0.14); color: #0099a0; }
        .icon-bg-coral { background: rgba(255, 75, 107, 0.12); color: var(--canva-pink); }
        .icon-bg-gold { background: rgba(255, 183, 3, 0.16); color: #d97706; }
        .icon-bg-emerald { background: rgba(16, 185, 129, 0.14); color: #059669; }
        .icon-bg-blue { background: rgba(21, 93, 252, 0.12); color: var(--canva-blue); }

        /* Interactive Feature Studio Switcher */
        .feature-studio-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
            margin-bottom: 36px;
        }
        .studio-tab-btn {
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            color: var(--canva-slate);
            font-weight: 700;
            font-size: 0.92rem;
            padding: 10px 22px;
            border-radius: 50px;
            transition: all 0.2s ease;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .studio-tab-btn:hover {
            border-color: var(--canva-violet);
            color: var(--canva-violet);
            background: rgba(125, 42, 232, 0.04);
        }
        .studio-tab-btn.active {
            background: var(--gradient-vibrant);
            color: #ffffff;
            border-color: transparent;
            box-shadow: 0 6px 18px rgba(125, 42, 232, 0.28);
        }

        .studio-preview-box {
            background: #ffffff;
            border-radius: 28px;
            border: 1px solid var(--canva-border);
            padding: 36px;
            box-shadow: 0 16px 40px rgba(15, 23, 42, 0.06);
        }

        /* Interactive ROI Calculator */
        .canva-calculator-card {
            background: #ffffff;
            border-radius: 28px;
            padding: 40px;
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.07);
            border: 1px solid rgba(226, 232, 240, 0.9);
            position: relative;
            overflow: hidden;
        }
        .canva-range-slider {
            -webkit-appearance: none;
            width: 100%;
            height: 10px;
            border-radius: 10px;
            background: #e2e8f0;
            outline: none;
            margin: 20px 0;
        }
        .canva-range-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--canva-violet);
            cursor: pointer;
            border: 4px solid #ffffff;
            box-shadow: 0 4px 14px rgba(94, 23, 235, 0.4);
            transition: transform 0.15s ease;
        }
        .canva-range-slider::-webkit-slider-thumb:hover {
            transform: scale(1.15);
        }

        /* Cuaderno vs LikhaPOS Cloud Visual Battle */
        .battle-card {
            border-radius: 24px;
            padding: 32px;
            height: 100%;
            transition: all 0.25s ease;
        }
        .battle-card-cuaderno {
            background: #fef2f2;
            border: 2px dashed #fca5a5;
        }
        .battle-card-cloud {
            background: #f0fdf4;
            border: 2px solid #86efac;
            box-shadow: 0 14px 36px rgba(16, 185, 129, 0.12);
            position: relative;
        }
        .winner-badge {
            position: absolute;
            top: -14px;
            right: 24px;
            background: var(--gradient-vibrant);
            color: #ffffff;
            font-size: 0.76rem;
            font-weight: 800;
            padding: 4px 14px;
            border-radius: 50px;
            letter-spacing: 0.5px;
        }

        /* ==========================================================================
           SUBSCRIPTION & PRICING SECTION (Matches User Reference Image)
           ========================================================================== */
        .pricing-section-container {
            padding: 80px 0;
            background: #f8fafc;
            position: relative;
        }
        
        .duration-pills-wrap {
            display: inline-flex;
            align-items: center;
            background: #f1f5f9;
            padding: 4px;
            border-radius: 50px;
            gap: 2px;
            flex-wrap: wrap;
            margin-bottom: 20px;
            border: 1px solid #e2e8f0;
        }
        .duration-pill {
            font-size: 0.8rem;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 50px;
            color: #64748b;
            background: transparent;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .duration-pill.active {
            background: #155dfc;
            color: #ffffff;
            box-shadow: 0 2px 8px rgba(21, 93, 252, 0.3);
        }
        .duration-pill:hover:not(.active) {
            color: #0f172a;
            background: rgba(255, 255, 255, 0.7);
        }

        /* Three Main Pricing Cards */
        .sub-card {
            background: #ffffff;
            border-radius: 24px;
            padding: 36px 30px;
            height: 100%;
            display: flex;
            flex-direction: column;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
            transition: all 0.3s cubic-bezier(0.2, 0.8, 0.2, 1);
            position: relative;
        }
        .sub-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 16px 36px rgba(15, 23, 42, 0.09);
        }

        /* Top Accent Line for Pro Card */
        .sub-card.card-pro {
            border-top: 4px solid #155dfc;
            border-image: linear-gradient(90deg, #155dfc 0%, #ffb703 100%) 1;
            border-radius: 24px;
        }

        /* Solid Royal Blue for Premium / Enterprise Card */
        .sub-card.card-enterprise {
            background: #0b4fc7; /* Royal electric blue */
            color: #ffffff;
            border: none;
            box-shadow: 0 16px 40px rgba(11, 79, 199, 0.35);
        }
        .sub-card.card-enterprise:hover {
            box-shadow: 0 20px 50px rgba(11, 79, 199, 0.45);
        }

        .sub-icon {
            font-size: 1.8rem;
            margin-bottom: 12px;
            display: inline-block;
        }
        .sub-title {
            font-size: 1.7rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 4px;
            font-family: 'Outfit', sans-serif;
        }
        .card-enterprise .sub-title {
            color: #ffffff;
        }

        .sub-desc {
            font-size: 0.92rem;
            color: #64748b;
            min-height: 44px;
            margin-bottom: 18px;
        }
        .card-enterprise .sub-desc {
            color: rgba(255, 255, 255, 0.85);
        }

        .sub-price-val {
            font-size: 2.8rem;
            font-weight: 900;
            color: #0f172a;
            line-height: 1;
            font-family: 'Outfit', sans-serif;
        }
        .card-enterprise .sub-price-val {
            color: #ffffff;
        }
        .sub-price-period {
            font-size: 1rem;
            font-weight: 600;
            color: #64748b;
        }
        .sub-billed-note {
            font-size: 0.84rem;
            color: #64748b;
            margin-top: 4px;
            margin-bottom: 24px;
            font-weight: 500;
        }
        .sub-billed-note strong {
            color: #0f172a;
        }

        /* Specs breakdown box for Enterprise */
        .enterprise-specs-box {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 14px;
            padding: 16px;
            margin-bottom: 24px;
        }
        .specs-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.86rem;
            padding: 6px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .specs-row:last-child {
            border-bottom: none;
        }
        .specs-row .specs-label {
            color: rgba(255, 255, 255, 0.8);
        }
        .specs-row .specs-val {
            font-weight: 700;
            color: #ffffff;
        }

        .feature-group-heading {
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #94a3b8;
            margin-bottom: 14px;
            margin-top: 10px;
        }
        .card-enterprise .feature-group-heading {
            color: rgba(255, 255, 255, 0.8);
        }

        .sub-feature-list {
            list-style: none;
            padding: 0;
            margin: 0 0 20px 0;
        }
        .sub-feature-list li {
            font-size: 0.88rem;
            padding: 6px 0;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #334155;
        }
        .card-enterprise .sub-feature-list li {
            color: #ffffff;
        }
        .sub-feature-list li.faded {
            color: #cbd5e1;
        }
        .sub-feature-list li i.check-green {
            color: #10b981;
            font-size: 1rem;
        }
        .sub-feature-list li i.check-yellow {
            color: #ffeb3b;
            font-size: 1.1rem;
        }

        .btn-sub-trial {
            background: #ffffff;
            color: #155dfc;
            border: 1.5px solid #155dfc;
            font-weight: 700;
            padding: 12px 24px;
            border-radius: 12px;
            width: 100%;
            text-align: center;
            margin-top: auto;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .btn-sub-trial:hover {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .btn-sub-pro {
            background: #155dfc;
            color: #ffffff;
            border: none;
            font-weight: 700;
            padding: 12px 24px;
            border-radius: 12px;
            width: 100%;
            text-align: center;
            margin-top: auto;
            text-decoration: none;
            box-shadow: 0 4px 14px rgba(21, 93, 252, 0.35);
            transition: all 0.2s ease;
        }
        .btn-sub-pro:hover {
            background: #1d4ed8;
            color: #ffffff;
            transform: translateY(-1px);
        }

        .btn-sub-enterprise {
            background: #fef08a; /* Bright yellow */
            color: #0f172a;
            border: none;
            font-weight: 800;
            padding: 14px 24px;
            border-radius: 12px;
            width: 100%;
            text-align: center;
            margin-top: auto;
            text-decoration: none;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15);
            transition: all 0.2s ease;
        }
        .btn-sub-enterprise:hover {
            background: #fde047;
            color: #0f172a;
            transform: translateY(-1px);
        }

        /* Testimonials Wall */
        .testimonial-masonry-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 28px;
            border: 1px solid var(--canva-border);
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);
            height: 100%;
            transition: all 0.25s ease;
        }
        .testimonial-masonry-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 30px rgba(15, 23, 42, 0.08);
            border-color: rgba(125, 42, 232, 0.25);
        }
        .avatar-initials-box {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
        }

        /* Accordion FAQ */
        .canva-accordion .accordion-item {
            border: 1px solid #e2e8f0;
            border-radius: 16px !important;
            margin-bottom: 14px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.02);
        }
        .canva-accordion .accordion-button {
            font-weight: 700;
            color: var(--canva-dark);
            padding: 20px 24px;
            background: #ffffff;
            font-size: 1rem;
        }
        .canva-accordion .accordion-button:not(.collapsed) {
            background: rgba(125, 42, 232, 0.04);
            color: var(--canva-violet);
            box-shadow: none;
        }
        .canva-accordion .accordion-body {
            padding: 0 24px 22px;
            color: var(--canva-muted);
            line-height: 1.65;
        }

        /* Canva High-Energy CTA Banner */
        .canva-cta-section {
            background: var(--gradient-vibrant);
            border-radius: 32px;
            padding: 60px 48px;
            color: #ffffff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 24px 60px rgba(94, 23, 235, 0.3);
        }
        .cta-step-chip {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 50px;
            padding: 8px 18px;
            font-size: 0.88rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        /* Footer */
        .footer-canva {
            background: #ffffff;
            border-top: 1px solid var(--canva-border);
            padding: 50px 0 30px;
        }
    </style>
</head>
<body>

    <!-- Floating Background Aura Glows -->
    <div class="canva-orb orb-purple"></div>
    <div class="canva-orb orb-cyan"></div>
    <div class="canva-orb orb-pink"></div>

    <!-- Canva Frosted Glass Navbar -->
    <nav class="navbar navbar-expand-xl navbar-canva" id="mainNav">
        <div class="container-fluid px-xl-5 px-lg-4 px-3">
            <a class="brand-logo-wrap d-inline-flex align-items-center text-nowrap" href="#">
                <div class="brand-icon-box">
                    <i class="bi bi-stars"></i>
                </div>
                <div class="d-inline-flex align-items-center text-nowrap">
                    <span class="brand-name text-nowrap">Likha<span class="gradient-text">POS</span></span>
                    <span class="brand-badge-pill ms-2 text-nowrap">Cloud Minimart & CRM</span>
                </div>
            </a>
            
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarLanding">
                <i class="bi bi-list fs-1 text-dark"></i>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarLanding">
                <ul class="navbar-nav mx-auto mb-2 mb-xl-0 fw-semibold text-nowrap">
                    <li class="nav-item text-nowrap"><a class="nav-link nav-link-canva text-nowrap" href="#features">Features</a></li>
                    <li class="nav-item text-nowrap"><a class="nav-link nav-link-canva text-nowrap" href="#studio">Interactive POS</a></li>
                    <li class="nav-item text-nowrap"><a class="nav-link nav-link-canva text-nowrap" href="#calculator">Savings Calculator</a></li>
                    <li class="nav-item text-nowrap"><a class="nav-link nav-link-canva text-nowrap" href="#comparison">Cuaderno vs Cloud</a></li>
                    <li class="nav-item text-nowrap"><a class="nav-link nav-link-canva text-nowrap" href="#pricing">Plans & Pricing</a></li>
                    <li class="nav-item text-nowrap"><a class="nav-link nav-link-canva text-nowrap" href="#reviews">Reviews</a></li>
                    <li class="nav-item text-nowrap"><a class="nav-link nav-link-canva text-nowrap" href="#faq">FAQ</a></li>
                </ul>

                <div class="d-flex align-items-center gap-2 text-nowrap flex-shrink-0 ms-xl-3">
                    @auth
                        <a href="{{ route('dashboard.index') }}" class="btn btn-canva-primary text-nowrap">
                            <i class="bi bi-speedometer2"></i> My Store Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-canva-outline text-nowrap">
                            <i class="bi bi-box-arrow-in-right"></i> Log In
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-canva-primary text-nowrap">
                            <i class="bi bi-rocket-takeoff-fill"></i> Start Free Trial
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container position-relative" style="z-index: 2;">
            <div class="row align-items-center g-5">
                <!-- Hero Left Copy -->
                <div class="col-lg-6 text-center text-lg-start">
                    <div class="hero-sticker-badge">
                        <span class="sparkle">✨</span>
                        <span>Designed Specifically for Philippine Minimarts & Groceries</span>
                    </div>

                    <h1 class="hero-title">
                        I-Automate ang <span class="gradient-text">Minimart Sales</span> & <span class="gradient-text-sunset">Utang CRM</span> Mo!
                    </h1>

                    <p class="hero-subtitle">
                        Iwasan ang nawawalang paninda, i-track ang pautang sa digital ledger, at mag-print ng thermal receipts gamit ang kahit anong laptop, tablet, o cellphone nang walang mamahaling hardware!
                    </p>

                    <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start mb-4">
                        <a href="{{ route('register') }}" class="btn btn-canva-primary px-4 py-3 fs-6">
                            <i class="bi bi-stars"></i> Subukan nang Libre sa 7 Araw
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-canva-outline px-4 py-3 fs-6">
                            <i class="bi bi-shield-check text-primary"></i> Store Admin Sign In
                        </a>
                    </div>

                    <!-- Social Proof Chips -->
                    <div class="d-flex flex-wrap align-items-center gap-4 justify-content-center justify-content-lg-start pt-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="d-flex" style="color: #ffb703;">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                            </div>
                            <span class="small fw-bold text-dark">4.9/5 Rating</span>
                        </div>
                        <div class="small text-muted fw-semibold">
                            <i class="bi bi-check-circle-fill text-success me-1"></i> No Credit Card Required
                        </div>
                        <div class="small text-muted fw-semibold">
                            <i class="bi bi-check-circle-fill text-success me-1"></i> Thermal Receipt Ready
                        </div>
                    </div>
                </div>

                <!-- Hero Right: Canva "POS Studio Canvas" Mockup -->
                <div class="col-lg-6">
                    <div class="pos-studio-frame">
                        <!-- Top Canva Window Header -->
                        <div class="canva-toolbar-header">
                            <div class="canva-window-dots">
                                <span class="dot dot-red"></span>
                                <span class="dot dot-yellow"></span>
                                <span class="dot dot-green"></span>
                            </div>
                            <div class="canva-canvas-title">
                                <i class="bi bi-layout-text-window"></i>
                                <span>LikhaPOS Terminal Canvas v2.4</span> • 100% Zoom
                            </div>
                            <div class="d-flex gap-2">
                                <span class="badge bg-success-subtle text-success fw-bold px-2 py-1" style="font-size: 0.7rem;">
                                    ● Cloud Active
                                </span>
                            </div>
                        </div>

                        <!-- Main Canvas Area -->
                        <div class="pos-canvas-body">
                            <!-- Floating Top Metric -->
                            <div class="floating-metric-card metric-card-top">
                                <div class="avatar-initials-box bg-success-subtle text-success">
                                    <i class="bi bi-graph-up-arrow fs-5"></i>
                                </div>
                                <div>
                                    <div class="small text-muted fw-semibold">Today's Store Sales</div>
                                    <div class="fw-bold text-dark fs-6">₱18,450.00 <span class="text-success small">(+24%)</span></div>
                                </div>
                            </div>

                            <!-- Floating Canva Cursor 1 (Cashier Scanning) -->
                            <div class="floating-cursor cursor-purple">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="#5e17eb">
                                    <path d="M3 3l7 18 3-7 7-3L3 3z"/>
                                </svg>
                                <span class="cursor-pill cursor-pill-purple">
                                    <i class="bi bi-upc-scan"></i> Cashier Ana: Scanned Lucky Me! ⚡
                                </span>
                            </div>

                            <!-- Inside POS Selection Screen -->
                            <div class="pos-inner-screen">
                                <span class="selection-handle handle-tl"></span>
                                <span class="selection-handle handle-tr"></span>
                                <span class="selection-handle handle-bl"></span>
                                <span class="selection-handle handle-br"></span>

                                <!-- Store Barcode Search Header -->
                                <div class="d-flex align-items-center justify-content-between pb-3 mb-2 border-bottom">
                                    <div>
                                        <div class="fw-bold text-dark font-heading">San Jose Minimart & Suki Center</div>
                                        <div class="small text-muted" style="font-size: 0.76rem;">Receipt #TRX-2026-092301 • Register 01</div>
                                    </div>
                                    <span class="badge bg-primary-subtle text-primary fw-bold">Live Counter</span>
                                </div>

                                <!-- Current Cart Items -->
                                <div class="pos-cart-item">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="badge bg-light text-dark fw-bold">2x</div>
                                        <div>
                                            <div class="fw-bold text-dark">Lucky Me! Pancit Canton (Chilimansi)</div>
                                            <div class="text-muted small" style="font-size: 0.72rem;">SKU: 4807770270014</div>
                                        </div>
                                    </div>
                                    <div class="fw-bold text-dark">₱34.00</div>
                                </div>

                                <div class="pos-cart-item">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="badge bg-light text-dark fw-bold">1x</div>
                                        <div>
                                            <div class="fw-bold text-dark">Alaska Evaporated Milk 370ml</div>
                                            <div class="text-muted small" style="font-size: 0.72rem;">SKU: 4800047820129</div>
                                        </div>
                                    </div>
                                    <div class="fw-bold text-dark">₱42.50</div>
                                </div>

                                <div class="pos-cart-item">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="badge bg-light text-dark fw-bold">1x</div>
                                        <div>
                                            <div class="fw-bold text-dark">San Miguel Pale Pilsen 330ml Can</div>
                                            <div class="text-muted small" style="font-size: 0.72rem;">SKU: 4801689201992</div>
                                        </div>
                                    </div>
                                    <div class="fw-bold text-dark">₱65.00</div>
                                </div>

                                <!-- Order Total Breakdown -->
                                <div class="bg-light p-2 rounded-3 mt-3">
                                    <div class="d-flex justify-content-between small text-muted mb-1">
                                        <span>Subtotal (4 items)</span>
                                        <span>₱141.50</span>
                                    </div>
                                    <div class="d-flex justify-content-between small text-success mb-2">
                                        <span>Senior/PWD Discount (20%)</span>
                                        <span>- ₱14.00</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                        <span class="fw-bold text-dark">Total Amount Due</span>
                                        <span class="fs-5 fw-extrabold text-primary font-heading">₱127.50</span>
                                    </div>
                                </div>

                                <!-- Quick Action Buttons -->
                                <div class="row g-2 mt-2">
                                    <div class="col-6">
                                        <button class="btn btn-sm btn-outline-secondary w-100 fw-bold rounded-3">
                                            <i class="bi bi-journal-bookmark text-warning"></i> Charge to Utang
                                        </button>
                                    </div>
                                    <div class="col-6">
                                        <button class="btn btn-sm btn-success w-100 fw-bold rounded-3">
                                            <i class="bi bi-cash-stack"></i> Cash Pay (₱127.50)
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Floating Canva Cursor 2 (Owner settling credit) -->
                            <div class="floating-cursor cursor-teal">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="#009688">
                                    <path d="M3 3l7 18 3-7 7-3L3 3z"/>
                                </svg>
                                <span class="cursor-pill cursor-pill-teal">
                                    <i class="bi bi-check2-circle"></i> Nanay Gloria: Utang Settled ₱450 ✅
                                </span>
                            </div>

                            <!-- Floating Bottom Metric Card -->
                            <div class="floating-metric-card metric-card-bottom">
                                <div class="avatar-initials-box bg-warning-subtle text-warning">
                                    <i class="bi bi-bell-fill fs-5"></i>
                                </div>
                                <div>
                                    <div class="small text-muted fw-semibold">Low Stock Radar</div>
                                    <div class="fw-bold text-dark fs-6">Silver Swan (3 pcs left) ⚠️</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Key Features Section with Canva Aesthetics -->
    <section id="features" class="py-5 bg-white">
        <div class="container py-4">
            <div class="text-center max-w-700 mx-auto mb-5">
                <span class="section-header-badge badge-purple">
                    <i class="bi bi-stars"></i> Retail Engine Built for Growth
                </span>
                <h2 class="fw-extrabold text-dark display-6">Lahat ng Kailangan ng Negosyo Mo, Nandito Na</h2>
                <p class="text-muted fs-6">Binuo para sa bilis ng cashier, inventory accuracy, at seamless na pagsingil ng utang.</p>
            </div>

            <div class="row g-4">
                <!-- Feature 1 -->
                <div class="col-md-4">
                    <div class="canva-feature-card">
                        <div class="canva-feature-icon icon-bg-violet">
                            <i class="bi bi-upc-scan"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-2 font-heading">Mabilis na Barcode Checkout</h4>
                        <p class="text-muted small lh-lg">
                            Gamitin ang kahit anong USB/Bluetooth barcode scanner o camera ng cellphone. 0.1 segundo scan speed para walang mahabang pila sa counter.
                        </p>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="col-md-4">
                    <div class="canva-feature-card">
                        <div class="canva-feature-icon icon-bg-gold">
                            <i class="bi bi-journal-bookmark-fill"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-2 font-heading">Digital Utang & CRM Ledger</h4>
                        <p class="text-muted small lh-lg">
                            Wala nang nawawalang pahina sa cuaderno! I-record ang customer credit balances, automated SMS reminders, at instant payment receipts.
                        </p>
                    </div>
                </div>

                <!-- Feature 3 (Replaced 1-Device restriction with Staff Permissions & Anti-Kupit Guard) -->
                <div class="col-md-4">
                    <div class="canva-feature-card">
                        <div class="canva-feature-icon icon-bg-emerald">
                            <i class="bi bi-shield-lock-fill"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-2 font-heading">Staff & Cashier Anti-Kupit Guard</h4>
                        <p class="text-muted small lh-lg">
                            Proteksyon sa bawat benta! Role-based permissions, shift cash drawer audits, at audit log para maiwasan ang kupit at unauthorized price discounts.
                        </p>
                    </div>
                </div>

                <!-- Feature 4 -->
                <div class="col-md-4">
                    <div class="canva-feature-card">
                        <div class="canva-feature-icon icon-bg-coral">
                            <i class="bi bi-box-seam-fill"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-2 font-heading">Real-Time Inventory & Low Stock Radar</h4>
                        <p class="text-muted small lh-lg">
                            Kusa nagbabawas ng stocks sa bawat benta. Makatanggap ng alerts bago maubusan ng paninda para makapag-restock sa supplier sa tamang oras.
                        </p>
                    </div>
                </div>

                <!-- Feature 5 -->
                <div class="col-md-4">
                    <div class="canva-feature-card">
                        <div class="canva-feature-icon icon-bg-blue">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-2 font-heading">Daily Net Profit & Shift Analytics</h4>
                        <p class="text-muted small lh-lg">
                            Alamin ang tunay na net profit araw-araw, fast-moving items, at sales breakdown kahit nasa bahay ka gamit ang iyong cellphone.
                        </p>
                    </div>
                </div>

                <!-- Feature 6 -->
                <div class="col-md-4">
                    <div class="canva-feature-card">
                        <div class="canva-feature-icon icon-bg-cyan">
                            <i class="bi bi-receipt-cutoff"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-2 font-heading">Thermal Receipts & Senior Discounts</h4>
                        <p class="text-muted small lh-lg">
                            Suporta sa standard 58mm at 80mm thermal receipt printers, 20% Senior Citizen / PWD auto-computation, at custom store logos at headers.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Interactive POS Studio Showcase Section -->
    <section id="studio" class="py-5" style="background: #f8fafc;">
        <div class="container py-4">
            <div class="text-center max-w-700 mx-auto mb-4">
                <span class="section-header-badge badge-cyan">
                    <i class="bi bi-palette-fill"></i> Interactive Live Studio
                </span>
                <h2 class="fw-extrabold text-dark display-6 font-heading">Tuklasin ang Bawat Tampok</h2>
                <p class="text-muted">Pumili ng module sa ibaba para makita kung gaano kadali gamitin ang LikhaPOS sa iyong tindahan.</p>
            </div>

            <!-- Tab Buttons -->
            <div class="feature-studio-tabs">
                <button class="studio-tab-btn active" onclick="switchStudioTab('barcode', this)">
                    <i class="bi bi-upc-scan"></i> Barcode Checkout
                </button>
                <button class="studio-tab-btn" onclick="switchStudioTab('utang', this)">
                    <i class="bi bi-journal-bookmark-fill"></i> Utang & Suki CRM
                </button>
                <button class="studio-tab-btn" onclick="switchStudioTab('inventory', this)">
                    <i class="bi bi-boxes"></i> Inventory & Stock In
                </button>
                <button class="studio-tab-btn" onclick="switchStudioTab('profit', this)">
                    <i class="bi bi-pie-chart-fill"></i> P&L Reports
                </button>
                <button class="studio-tab-btn" onclick="switchStudioTab('security', this)">
                    <i class="bi bi-shield-check"></i> Shift & Drawer Audit
                </button>
            </div>

            <!-- Tab Content Pane -->
            <div class="studio-preview-box" id="studioPreviewBox">
                <div class="row align-items-center g-4">
                    <div class="col-lg-5">
                        <div class="badge bg-primary-subtle text-primary fw-bold px-3 py-1 rounded-pill mb-2" id="tabBadge">
                            Counter Automation
                        </div>
                        <h3 class="fw-extrabold text-dark font-heading mb-3" id="tabTitle">
                            Lightning-Fast Barcode Scanning
                        </h3>
                        <p class="text-muted mb-4" id="tabDesc">
                            Bilis na tatagal kahit sa pinakamalaking holiday rush. Isaksak lang ang scanner o gamitin ang camera, instant add to cart agad ang produkto na may tamang retail price!
                        </p>
                        <ul class="list-unstyled mb-4" id="tabList">
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Compatible sa kahit anong 1D/2D USB or Bluetooth barcode scanner</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Automatic computation ng sukli at discount sa isang click</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Cash drawer auto-pop trigger sa pagtapos ng transaksyon</li>
                        </ul>
                        <a href="{{ route('register') }}" class="btn btn-canva-primary">
                            Subukan Ito Nang Libre <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="col-lg-7">
                        <div class="p-3 bg-light rounded-4 border text-center">
                            <div class="d-flex align-items-center justify-content-between bg-white p-3 rounded-3 shadow-sm mb-3">
                                <span class="fw-bold text-dark"><i class="bi bi-laptop me-2 text-primary"></i> Terminal View Simulation</span>
                                <span class="badge bg-success text-white">Live Connected</span>
                            </div>
                            <div class="row g-2 text-start">
                                <div class="col-6">
                                    <div class="bg-white p-3 rounded-3 border">
                                        <div class="small text-muted">Item Scanned</div>
                                        <div class="fw-bold text-dark">Great Taste White Twin</div>
                                        <div class="text-primary fw-bold">₱14.00</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-white p-3 rounded-3 border">
                                        <div class="small text-muted">Inventory Remaining</div>
                                        <div class="fw-bold text-dark">48 sachets</div>
                                        <div class="text-success fw-bold">In Stock</div>
                                    </div>
                                </div>
                                <div class="col-12 mt-2">
                                    <div class="bg-white p-3 rounded-3 border d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="small text-muted">Customer Name</div>
                                            <div class="fw-bold text-dark">Aling Marites (Suki VIP)</div>
                                        </div>
                                        <span class="badge bg-warning-subtle text-warning fw-bold px-3 py-2">Credit Limit: ₱3,000</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Interactive Savings & ROI Calculator -->
    <section id="calculator" class="py-5 bg-white">
        <div class="container py-4">
            <div class="text-center max-w-700 mx-auto mb-5">
                <span class="section-header-badge badge-gold">
                    <i class="bi bi-calculator-fill"></i> ROI & Loss Prevention Calculator
                </span>
                <h2 class="fw-extrabold text-dark display-6 font-heading">Gaano Kalaki ang Maiiwasang Lugi ng Store Mo?</h2>
                <p class="text-muted">Ayon sa datos, 3% hanggang 5% ng benta ay nawawala dahil sa unrecorded utang at manual counting errors.</p>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="canva-calculator-card">
                        <div class="row g-5 align-items-center">
                            <div class="col-md-6">
                                <label class="fw-bold text-dark mb-1">Araw-araw na Tinatayang Benta ng Store Mo:</label>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="fs-4 fw-extrabold text-primary font-heading" id="calcSalesDisplay">₱15,000 / araw</span>
                                    <span class="badge bg-primary-subtle text-primary">Slide to adjust</span>
                                </div>
                                <input type="range" class="canva-range-slider" id="salesRange" min="3000" max="60000" step="1000" value="15000" oninput="calculateSavings(this.value)">
                                <div class="d-flex justify-content-between text-muted small fw-semibold">
                                    <span>₱3,000 / day</span>
                                    <span>₱30,000 / day</span>
                                    <span>₱60,000 / day</span>
                                </div>
                            </div>

                            <div class="col-md-6 border-start-md ps-md-4">
                                <div class="p-4 rounded-4" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.12) 0%, rgba(0, 196, 204, 0.12) 100%); border: 1.5px solid rgba(16, 185, 129, 0.3);">
                                    <div class="small text-muted fw-bold text-uppercase mb-1">Tinatayang Matitipid Kada Buwan:</div>
                                    <div class="display-6 fw-extrabold text-success font-heading mb-2" id="calcSavingsDisplay">₱13,500 / month</div>
                                    <div class="small text-muted mb-3">
                                        Katumbas ng <strong>₱162,000</strong> na maiiwasang mawala bawat taon mula sa sirang cuaderno at kupit.
                                    </div>
                                    <div class="d-flex gap-2">
                                        <span class="badge bg-white text-dark border px-2 py-1 small">
                                            <i class="bi bi-clock-history text-primary me-1"></i> 15 hrs / wk saved
                                        </span>
                                        <span class="badge bg-white text-dark border px-2 py-1 small">
                                            <i class="bi bi-shield-check text-success me-1"></i> 100% Audit Tracked
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Comparison: Cuaderno vs LikhaPOS Cloud -->
    <section id="comparison" class="py-5" style="background: #f8fafc;">
        <div class="container py-4">
            <div class="text-center max-w-700 mx-auto mb-5">
                <span class="section-header-badge badge-purple">
                    <i class="bi bi-arrow-left-right"></i> Bakit Dapat Mag-Upgrade?
                </span>
                <h2 class="fw-extrabold text-dark display-6 font-heading">Cuaderno vs LikhaPOS Cloud</h2>
                <p class="text-muted">Tingnan ang malaking pagkakaiba ng lumang mano-manong paraan kumpara sa modernong Cloud POS.</p>
            </div>

            <div class="row g-4 align-items-stretch">
                <!-- Old Way: Cuaderno -->
                <div class="col-md-6">
                    <div class="battle-card battle-card-cuaderno">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="avatar-initials-box bg-danger-subtle text-danger fs-4">
                                <i class="bi bi-book"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold text-danger m-0 font-heading">Mano-Mano / Cuaderno</h4>
                                <div class="small text-muted">Lumang Sistema ng Tindahan</div>
                            </div>
                        </div>

                        <ul class="list-unstyled">
                            <li class="d-flex align-items-start gap-3 mb-3 text-muted">
                                <i class="bi bi-x-circle-fill text-danger fs-5 mt-1"></i>
                                <div>
                                    <strong class="text-dark">Mabagal ang Pila sa Counter:</strong> Mano-manong nagku-kwenta sa calculator habang naghihintay ang mga mamimili.
                                </div>
                            </li>
                            <li class="d-flex align-items-start gap-3 mb-3 text-muted">
                                <i class="bi bi-x-circle-fill text-danger fs-5 mt-1"></i>
                                <div>
                                    <strong class="text-dark">Nawawalang Listahan ng Utang:</strong> Napupunit ang papel, nababasa ng tubig, o nakakalimutang ilista ang pautang.
                                </div>
                            </li>
                            <li class="d-flex align-items-start gap-3 mb-3 text-muted">
                                <i class="bi bi-x-circle-fill text-danger fs-5 mt-1"></i>
                                <div>
                                    <strong class="text-dark">Walang Tunay na Imbentaryo:</strong> Hindi alam kung may nananakaw na paninda o kailan kailangan mag-order muli.
                                </div>
                            </li>
                            <li class="d-flex align-items-start gap-3 mb-3 text-muted">
                                <i class="bi bi-x-circle-fill text-danger fs-5 mt-1"></i>
                                <div>
                                    <strong class="text-dark">Madaling Makupitan ang Drawer:</strong> Walang audit trail ng bawat transaksyon at shift turnover.
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Modern Way: LikhaPOS -->
                <div class="col-md-6">
                    <div class="battle-card battle-card-cloud">
                        <span class="winner-badge">ANG PANALO 🚀</span>
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="avatar-initials-box bg-success-subtle text-success fs-4">
                                <i class="bi bi-cloud-check-fill"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold text-success m-0 font-heading">LikhaPOS Cloud System</h4>
                                <div class="small text-muted">Smart Retail Automation</div>
                            </div>
                        </div>

                        <ul class="list-unstyled">
                            <li class="d-flex align-items-start gap-3 mb-3">
                                <i class="bi bi-check-circle-fill text-success fs-5 mt-1"></i>
                                <div>
                                    <strong class="text-dark">Instant 0.1s Barcode Scan:</strong> Mabilis na checkout at automatic receipt print sa thermal printer.
                                </div>
                            </li>
                            <li class="d-flex align-items-start gap-3 mb-3">
                                <i class="bi bi-check-circle-fill text-success fs-5 mt-1"></i>
                                <div>
                                    <strong class="text-dark">Digital Suki Ledger CRM:</strong> Real-time balance ledger na may SMS reminders at credit limits kada customer.
                                </div>
                            </li>
                            <li class="d-flex align-items-start gap-3 mb-3">
                                <i class="bi bi-check-circle-fill text-success fs-5 mt-1"></i>
                                <div>
                                    <strong class="text-dark">Automatic Stock Deductions:</strong> Auto-deduct sa bawat benta na may babala kapag paubos na ang paninda.
                                </div>
                            </li>
                            <li class="d-flex align-items-start gap-3 mb-3">
                                <i class="bi bi-check-circle-fill text-success fs-5 mt-1"></i>
                                <div>
                                    <strong class="text-dark">Cashier Shifts & Anti-Kupit Guard:</strong> Malinaw na benta bawat kaha at bantay sa bawat bawas ng pera sa drawer.
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==========================================================================
         SUBSCRIPTION & PRICING SECTION (MATCHES USER REFERENCE IMAGE EXACTLY)
         ========================================================================== -->
    <section id="pricing" class="pricing-section-container">
        <div class="container">
            <div class="text-center max-w-700 mx-auto mb-5">
                <span class="section-header-badge badge-cyan">
                    <i class="bi bi-tags-fill"></i> Transparent Subscription Plans
                </span>
                <h2 class="fw-extrabold text-dark display-6 font-heading">Presyong Abot-Kaya Para sa Bawat Negosyo</h2>
                <p class="text-muted">Pumili ng plano na angkop sa laki ng inyong operasyon. Walang hidden setup fees.</p>
            </div>

            <div class="row g-4 align-items-stretch justify-content-center">
                
                <!-- CARD 1: BASIC -->
                <div class="col-lg-4 col-md-6">
                    <div class="sub-card">
                        <!-- Top Icon -->
                        <div class="sub-icon text-primary">
                            <i class="bi bi-rocket-takeoff-fill"></i>
                        </div>
                        <h3 class="sub-title">Basic</h3>
                        <p class="sub-desc">Perfect for startups and small businesses.</p>

                        <!-- Duration Selector Pills for Basic -->
                        <div class="duration-pills-wrap" id="basicPills">
                            <button class="duration-pill active" onclick="setPlanDuration('basic', 'quarterly', 300, 100, this)">Quarterly</button>
                            <button class="duration-pill" onclick="setPlanDuration('basic', '1year', 1080, 90, this)">1 Year</button>
                            <button class="duration-pill" onclick="setPlanDuration('basic', '3years', 3000, 83, this)">3 Years</button>
                            <button class="duration-pill" onclick="setPlanDuration('basic', '4years', 3800, 79, this)">4 Years</button>
                            <button class="duration-pill" onclick="setPlanDuration('basic', '6years', 5400, 75, this)">6 Years</button>
                            <button class="duration-pill" onclick="setPlanDuration('basic', '10years', 8400, 70, this)">10 Years ⭐</button>
                        </div>

                        <!-- Price Value Display -->
                        <div class="d-flex align-items-baseline gap-2">
                            <span class="sub-price-val" id="basicPrice">₱300</span>
                            <span class="sub-price-period" id="basicPeriod">/Quarterly</span>
                        </div>
                        <div class="sub-billed-note">
                            Billed as <strong id="basicMonthly">₱100/month</strong>
                        </div>

                        <!-- Included Features -->
                        <div class="feature-group-heading">INCLUDED FEATURES</div>
                        <ul class="sub-feature-list">
                            <li><i class="bi bi-check-circle-fill check-green"></i> Dashboard Analytics</li>
                            <li><i class="bi bi-check-circle-fill check-green"></i> Product Management</li>
                            <li><i class="bi bi-check-circle-fill check-green"></i> Inventory Management</li>
                            <li><i class="bi bi-check-circle-fill check-green"></i> Sales Monitoring</li>
                            <li><i class="bi bi-check-circle-fill check-green"></i> Employee Management</li>
                            <li><i class="bi bi-check-circle-fill check-green"></i> Periodic Sales Reports</li>
                        </ul>

                        <!-- Limitations -->
                        <div class="feature-group-heading">LIMITATIONS</div>
                        <ul class="sub-feature-list">
                            <li><i class="bi bi-calculator-fill text-warning"></i> <strong>1 POS Terminal Only</strong> (Single Register)</li>
                            <li><i class="bi bi-check-circle-fill check-green"></i> 1 Administrator Account</li>
                            <li><i class="bi bi-check-circle-fill check-green"></i> Up to 2 Cashier Accounts</li>
                            <li><i class="bi bi-check-circle-fill check-green"></i> 1 Branch Only</li>
                            <li class="faded"><i class="bi bi-dash-circle text-muted"></i> Multi Branch</li>
                            <li class="faded"><i class="bi bi-dash-circle text-muted"></i> Purchase Journal</li>
                            <li class="faded"><i class="bi bi-dash-circle text-muted"></i> Cash Receipt Journal</li>
                            <li class="faded"><i class="bi bi-dash-circle text-muted"></i> Cash Disbursement Journal</li>
                            <li class="faded"><i class="bi bi-dash-circle text-muted"></i> Transaction Override</li>
                            <li class="faded"><i class="bi bi-dash-circle text-muted"></i> Advanced Reports</li>
                            <li><i class="bi bi-check-circle-fill check-green"></i> Standard Technical Support</li>
                        </ul>

                        <!-- Bottom Button -->
                        <a href="{{ route('register', ['plan' => 1]) }}" class="btn-sub-trial">
                            Start Free Trial
                        </a>
                    </div>
                </div>

                <!-- CARD 2: PRO (Featured with Gradient Top Bar) -->
                <div class="col-lg-4 col-md-6">
                    <div class="sub-card card-pro">
                        <!-- Top Icon -->
                        <div class="sub-icon text-primary">
                            <i class="bi bi-award-fill" style="color: #155dfc;"></i>
                        </div>
                        <h3 class="sub-title">Pro</h3>
                        <p class="sub-desc">Ideal for growing businesses with multiple employees.</p>

                        <!-- Duration Selector Pills for Pro -->
                        <div class="duration-pills-wrap" id="proPills">
                            <button class="duration-pill active" onclick="setPlanDuration('pro', 'quarterly', 600, 200, this)">Quarterly</button>
                            <button class="duration-pill" onclick="setPlanDuration('pro', '1year', 2160, 180, this)">1 Year</button>
                            <button class="duration-pill" onclick="setPlanDuration('pro', '3years', 6000, 166, this)">3 Years</button>
                            <button class="duration-pill" onclick="setPlanDuration('pro', '4years', 7600, 158, this)">4 Years</button>
                            <button class="duration-pill" onclick="setPlanDuration('pro', '6years', 10800, 150, this)">6 Years</button>
                            <button class="duration-pill" onclick="setPlanDuration('pro', '10years', 16800, 140, this)">10 Years ⭐</button>
                        </div>

                        <!-- Price Value Display -->
                        <div class="d-flex align-items-baseline gap-2">
                            <span class="sub-price-val" id="proPrice">₱600</span>
                            <span class="sub-price-period" id="proPeriod">/Quarterly</span>
                        </div>
                        <div class="sub-billed-note">
                            Billed as <strong id="proMonthly">₱200/month</strong>
                        </div>

                        <!-- Everything in Basic Plus -->
                        <div class="feature-group-heading">EVERYTHING IN BASIC, PLUS:</div>
                        <ul class="sub-feature-list">
                            <li><i class="bi bi-check-circle-fill check-green"></i> Purchase Journal</li>
                            <li><i class="bi bi-check-circle-fill check-green"></i> Cash Receipt Journal</li>
                            <li><i class="bi bi-check-circle-fill check-green"></i> Cash Disbursement Journal</li>
                            <li><i class="bi bi-check-circle-fill check-green"></i> Sales Report Generation</li>
                            <li><i class="bi bi-check-circle-fill check-green"></i> Cashiering Management</li>
                            <li><i class="bi bi-check-circle-fill check-green"></i> Product Cart</li>
                            <li><i class="bi bi-check-circle-fill check-green"></i> Sales Transaction Override</li>
                            <li><i class="bi bi-check-circle-fill check-green"></i> Dashboard Analytics</li>
                            <li><i class="bi bi-check-circle-fill check-green"></i> Inventory Management</li>
                            <li><i class="bi bi-check-circle-fill check-green"></i> Product Management</li>
                            <li><i class="bi bi-check-circle-fill check-green"></i> Employee Management</li>
                        </ul>

                        <!-- Limitations -->
                        <div class="feature-group-heading">LIMITATIONS & QUOTAS</div>
                        <ul class="sub-feature-list">
                            <li><i class="bi bi-calculator-fill text-primary"></i> <strong class="text-primary">Up to 3 POS Terminals</strong> (Multi-Counter Ready)</li>
                            <li><i class="bi bi-check-circle-fill check-green"></i> 2 Administrator Accounts</li>
                            <li><i class="bi bi-check-circle-fill check-green"></i> Up to 6 Cashier Accounts</li>
                            <li><i class="bi bi-check-circle-fill check-green"></i> 1 Main Branch</li>
                            <li class="faded"><i class="bi bi-dash-circle text-muted"></i> Multi Branch Support</li>
                            <li class="faded"><i class="bi bi-dash-circle text-muted"></i> Custom API Integrations</li>
                            <li><i class="bi bi-check-circle-fill check-green"></i> Priority Technical Support</li>
                        </ul>

                        <!-- Bottom Button -->
                        <a href="{{ route('register', ['plan' => 2]) }}" class="btn-sub-pro">
                            Upgrade to PRO
                        </a>
                    </div>
                </div>

                <!-- CARD 3: PREMIUM / ENTERPRISE (Solid Royal Blue & Yellow Accents) -->
                <div class="col-lg-4 col-md-6">
                    <div class="sub-card card-enterprise">
                        <div class="d-flex justify-content-between align-items-start">
                            <!-- Top Icon with yellow container -->
                            <div class="sub-icon text-warning">
                                <i class="bi bi-buildings-fill fs-2"></i>
                            </div>
                            <!-- White Pill Badge -->
                            <span class="badge bg-white text-primary fw-extrabold px-3 py-2 rounded-pill" style="font-size: 0.72rem; letter-spacing: 0.6px;">
                                ENTERPRISE
                            </span>
                        </div>

                        <h3 class="sub-title">Premium</h3>
                        <p class="sub-desc">Designed for enterprises with advanced operational requirements.</p>

                        <!-- Translucent Specs Box -->
                        <div class="enterprise-specs-box">
                            <div class="specs-row">
                                <span class="specs-label">Plan Type</span>
                                <span class="specs-val">Custom Plan</span>
                            </div>
                            <div class="specs-row">
                                <span class="specs-label">Software Development</span>
                                <span class="specs-val">₱15,000–₱30,000</span>
                            </div>
                            <div class="specs-row">
                                <span class="specs-label">Monthly Maintenance</span>
                                <span class="specs-val">Starting at ₱500/mo</span>
                            </div>
                        </div>

                        <!-- Everything in Pro Plus -->
                        <div class="feature-group-heading">EVERYTHING IN PRO, PLUS:</div>
                        <ul class="sub-feature-list">
                            <li><i class="bi bi-calculator-fill text-warning"></i> <strong>10+ POS Terminals Included</strong></li>
                            <li><i class="bi bi-check-circle-fill check-yellow"></i> Multi-Branch (Up to 5 Branches)</li>
                            <li><i class="bi bi-check-circle-fill check-yellow"></i> Unlimited Administrator Accounts</li>
                            <li><i class="bi bi-check-circle-fill check-yellow"></i> Unlimited Cashier Accounts</li>
                            <li><i class="bi bi-check-circle-fill check-yellow"></i> Multi-Branch Central Dashboard</li>
                            <li><i class="bi bi-check-circle-fill check-yellow"></i> Central Warehouse & Stock Transfer</li>
                            <li><i class="bi bi-check-circle-fill check-yellow"></i> Unlimited Products</li>
                            <li><i class="bi bi-check-circle-fill check-yellow"></i> Unlimited Customers & Suppliers</li>
                            <li><i class="bi bi-check-circle-fill check-yellow"></i> Unlimited Monthly Transactions</li>
                            <li><i class="bi bi-check-circle-fill check-yellow"></i> VIP 24/7 Dedicated Support</li>
                        </ul>

                        <!-- Bottom Button: Bright Yellow -->
                        <a href="{{ route('register', ['plan' => 3]) }}" class="btn-sub-enterprise">
                            Contact Sales
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Verified Real Store Reviews -->
    <section id="reviews" class="py-5 bg-white">
        <div class="container py-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-5 gap-3">
                <div>
                    <span class="section-header-badge badge-purple">
                        <i class="bi bi-chat-heart-fill"></i> Verified Community
                    </span>
                    <h2 class="fw-extrabold text-dark display-6 m-0 font-heading">Ano ang Sinasabi ng mga Store Owners?</h2>
                </div>
                <button type="button" class="btn btn-canva-outline" data-bs-toggle="modal" data-bs-target="#reviewModal">
                    <i class="bi bi-chat-left-dots-fill text-primary"></i> Submit Store Review
                </button>
            </div>

            <div class="row g-4">
                @if(isset($reviews) && count($reviews) > 0)
                    @foreach($reviews as $rev)
                        <div class="col-md-4">
                            <div class="testimonial-masonry-card">
                                <div class="text-warning mb-3">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi bi-star{{ $i <= $rev->rating ? '-fill' : '' }}"></i>
                                    @endfor
                                </div>
                                <p class="text-muted fst-italic mb-4" style="line-height: 1.6;">"{{ $rev->review_text }}"</p>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-initials-box bg-primary-subtle text-primary">
                                        {{ $rev->avatar_initials ?? 'ST' }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $rev->reviewer_name }}</div>
                                        <div class="small text-muted">{{ $rev->store_name }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-12 text-center py-5 text-muted">
                        <i class="bi bi-stars fs-1 text-warning d-block mb-2"></i>
                        Maging unang may-ari ng tindahan na magbahagi ng inyong review!
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Frequently Asked Questions -->
    <section id="faq" class="py-5" style="background: #f8fafc;">
        <div class="container py-4">
            <div class="text-center max-w-700 mx-auto mb-5">
                <span class="section-header-badge badge-cyan">May Mga Tanong Ka Ba?</span>
                <h2 class="fw-extrabold text-dark display-6 font-heading">Frequently Asked Questions</h2>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="accordion canva-accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    Kailangan ba ng mamahaling touchscreen machine o POS terminal?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Hindi kailangan! Pwedeng gamitin ang kahit anong existing Laptop, Desktop PC, Android Tablet, o Cellphone. Gumagana ito sa standard USB o Bluetooth barcode scanners at thermal receipt printers.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    Paano kapag nawalan ng Internet Connection habang nagtitinda?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    May built-in offline caching protection ang LikhaPOS Terminal. Makakapag-scan pa rin ang inyong cashier at kapag bumalik na ang WiFi o mobile data, kusa nitong i-sync ang benta pabalik sa cloud.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    Puwede ko bang makita ang benta sa cellphone ko habang nasa counter ang cashier?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Opo! Bilang Store Owner / Administrator, maaari mong buksan ang iyong Owner Dashboard sa iyong cellphone kahit nasa bahay o nasa biyahe ka. Makikita mo ang bawat item na binabayaran sa counter real-time.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                    May kontrata ba o lock-in period?
                                </button>
                            </h2>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Walang lock-in contract! Malaya kang mag-subscribe sa term na gusto mo (Quarterly o Annual) at mag-upgrade o mag-cancel anumang oras nang walang penalty.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Canva High-Energy Urgency CTA Banner -->
    <section class="py-5 bg-white">
        <div class="container">
            <div class="canva-cta-section text-center text-lg-start">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span class="cta-step-chip">1. Register in 2 Mins</span>
                            <span class="cta-step-chip">2. Add Products</span>
                            <span class="cta-step-chip">3. Start Ringing Sales</span>
                        </div>
                        <h2 class="fw-extrabold display-5 mb-3 font-heading">Handa Ka Na Bang Palaguin ang Minimart Mo?</h2>
                        <p class="lead opacity-90 m-0">I-setup ang store mo sa loob lamang ng ilang minuto. Subukan nang libre sa loob ng 7 araw, walang credit card na kailangan!</p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <a href="{{ route('register') }}" class="btn btn-warning btn-lg px-4 py-3 fw-extrabold rounded-pill shadow-lg text-dark font-heading">
                            <i class="bi bi-rocket-takeoff-fill me-2"></i> Register Store Free
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modern Canva Footer -->
    <footer class="footer-canva">
        <div class="container text-center text-md-start">
            <div class="row g-4 align-items-center">
                <div class="col-md-6">
                    <div class="fw-bold text-dark fs-5 font-heading">
                        Likha<span class="gradient-text">POS</span> Minimart & CRM
                    </div>
                    <div class="small text-muted mt-1">© {{ date('Y') }} LikhaPOS SaaS Platform. All rights reserved.</div>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="{{ route('login') }}" class="btn btn-canva-primary btn-sm px-3">
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
                    <h5 class="modal-title fw-bold text-dark font-heading">Submit Your LikhaPOS Store Review</h5>
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
                        <button type="submit" class="btn btn-canva-primary rounded-3 px-4">Post Store Review</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const nav = document.getElementById('mainNav');
            if (window.scrollY > 20) {
                nav.classList.add('scrolled');
            } else {
                nav.classList.remove('scrolled');
            }
        });

        // Interactive Feature Studio Tab Switcher
        const studioData = {
            barcode: {
                badge: "Counter Automation",
                title: "Lightning-Fast Barcode Scanning",
                desc: "Bilis na tatagal kahit sa pinakamalaking holiday rush. Isaksak lang ang scanner o gamitin ang camera, instant add to cart agad ang produkto na may tamang retail price!",
                list: [
                    "Compatible sa kahit anong 1D/2D USB or Bluetooth barcode scanner",
                    "Automatic computation ng sukli at discount sa isang click",
                    "Cash drawer auto-pop trigger sa pagtapos ng transaksyon"
                ]
            },
            utang: {
                badge: "Customer CRM",
                title: "Digital Utang & Suki Credit Ledger",
                desc: "Wala nang nawawalang cuaderno! I-record ang pautang sa pangalan ng suki. May automated credit limit warnings para hindi lumagpas sa pinapayagang halaga.",
                list: [
                    "Searchable ledger records ng bawat utang at partial payments",
                    "Printable thermal acknowledgement receipts ng bayad",
                    "Kusang nag-aalert kapag lumampas sa itinakdang credit limit"
                ]
            },
            inventory: {
                badge: "Stock Radar",
                title: "Real-Time Inventory & Auto-Deductions",
                desc: "Bawat scan sa cashier, kusa nang nababawas sa stock. Hindi mo na kailangang mag-mano-manong bilang ng laman ng estante gabi-gabi.",
                list: [
                    "Visual color-coded stock alerts kapag paubos na ang paninda",
                    "Purchase order at supplier delivery tracking sa isang pindot",
                    "Stock adjustment log para mabantayan ang tapon o sirang items"
                ]
            },
            profit: {
                badge: "Financial Intelligence",
                title: "Daily Net Profit & Shift Audits",
                desc: "Hindi lang kabuuang benta ang nakikita kundi ang tunay na kinita matapos ibawas ang puhunan at operating expenses ng tindahan.",
                list: [
                    "Breakdown ng gross sales, gross profit, at net profit per shift",
                    "Top 10 fast-moving products na may pinakamalaking kita",
                    "Exportable Excel and PDF summary para sa bookkeeping"
                ]
            },
            security: {
                badge: "Loss Prevention",
                title: "Staff & Cashier Anti-Kupit Guard",
                desc: "Seguridad sa bawat sentimo. May sariling cashier account ang staff na may restricted permissions para bawal magbura ng benta o mag-override nang walang admin PIN.",
                list: [
                    "Drawer cash-in at cash-out audit logs bawat turnover ng shift",
                    "Admin PIN requirement para sa item void o custom discounts",
                    "Multi-device access para sa may-ari habang gamit sa counter ng cashier"
                ]
            }
        };

        function switchStudioTab(key, btn) {
            document.querySelectorAll('.studio-tab-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const d = studioData[key];
            if (!d) return;

            document.getElementById('tabBadge').innerText = d.badge;
            document.getElementById('tabTitle').innerText = d.title;
            document.getElementById('tabDesc').innerText = d.desc;

            const listEl = document.getElementById('tabList');
            listEl.innerHTML = d.list.map(item => `<li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> ${item}</li>`).join('');
        }

        // Interactive ROI Calculator
        function calculateSavings(val) {
            document.getElementById('calcSalesDisplay').innerText = '₱' + parseInt(val).toLocaleString() + ' / araw';
            const monthlySales = val * 30;
            // 3% average loss prevention (shrinkage + uncollected credit)
            const savings = monthlySales * 0.03;
            document.getElementById('calcSavingsDisplay').innerText = '₱' + Math.round(savings).toLocaleString() + ' / month';
        }

        // Pricing Term Switcher
        function setPlanDuration(plan, term, totalVal, monthlyVal, btn) {
            // Update active pill
            const pillsWrap = btn.parentElement;
            pillsWrap.querySelectorAll('.duration-pill').forEach(p => p.classList.remove('active'));
            btn.classList.add('active');

            // Format period label
            let periodText = '/Quarterly';
            if (term === '1year') periodText = '/Year';
            else if (term === '3years') periodText = '/3 Years';
            else if (term === '4years') periodText = '/4 Years';
            else if (term === '6years') periodText = '/6 Years';
            else if (term === '10years') periodText = '/10 Years';

            if (plan === 'basic') {
                document.getElementById('basicPrice').innerText = '₱' + totalVal.toLocaleString();
                document.getElementById('basicPeriod').innerText = periodText;
                document.getElementById('basicMonthly').innerText = '₱' + monthlyVal + '/month';
            } else if (plan === 'pro') {
                document.getElementById('proPrice').innerText = '₱' + totalVal.toLocaleString();
                document.getElementById('proPeriod').innerText = periodText;
                document.getElementById('proMonthly').innerText = '₱' + monthlyVal + '/month';
            }
        }
    </script>
</body>
</html>
