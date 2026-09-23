<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Store Login & POS Terminal - {{ config('app.name', 'LikhaPOS') }}</title>
    
    <!-- Google Fonts: Outfit & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons & Bootstrap 5.3 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --canva-violet: #7d2ae8;
            --canva-purple: #5e17eb;
            --canva-cyan: #00c4cc;
            --canva-blue: #155dfc;
            --canva-dark: #0f172a;
            --canva-slate: #475569;
            --canva-border: #e2e8f0;
            --gradient-primary: linear-gradient(135deg, #7d2ae8 0%, #5e17eb 50%, #155dfc 100%);
            --gradient-mesh: radial-gradient(at 0% 0%, rgba(125, 42, 232, 0.12) 0px, transparent 50%),
                             radial-gradient(at 100% 0%, rgba(0, 196, 204, 0.15) 0px, transparent 50%),
                             radial-gradient(at 100% 100%, rgba(21, 93, 252, 0.1) 0px, transparent 50%),
                             radial-gradient(at 0% 100%, rgba(247, 37, 133, 0.08) 0px, transparent 50%);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #fafbfe;
            background-image: var(--gradient-mesh);
            min-height: 100vh;
            color: var(--canva-dark);
            margin: 0;
            padding: 0;
            position: relative;
            overflow-x: hidden;
        }

        /* Ambient glowing orbs */
        .canva-orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(90px);
            z-index: 0;
            pointer-events: none;
            opacity: 0.6;
            animation: orbFloat 18s ease-in-out infinite alternate;
        }
        .orb-purple { width: 450px; height: 450px; background: rgba(125, 42, 232, 0.18); top: -80px; left: -100px; }
        .orb-cyan { width: 420px; height: 420px; background: rgba(0, 196, 204, 0.16); bottom: -60px; right: -80px; }
        .orb-pink { width: 350px; height: 350px; background: rgba(247, 37, 133, 0.12); top: 35%; right: 15%; }

        @keyframes orbFloat {
            0% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(30px, 40px) scale(1.08); }
            100% { transform: translate(-20px, 20px) scale(0.96); }
        }

        .auth-container {
            position: relative;
            z-index: 10;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 16px;
        }

        .auth-card-wrapper {
            width: 100%;
            max-width: 1000px;
            background: #ffffff;
            border-radius: 32px;
            border: 1px solid rgba(226, 232, 240, 0.9);
            box-shadow: 0 25px 70px rgba(15, 23, 42, 0.08), 0 0 0 1px rgba(255, 255, 255, 0.8) inset;
            overflow: hidden;
        }

        /* Left Showcase Column (Canva Hero Style) */
        .showcase-pane {
            background: linear-gradient(150deg, #0f172a 0%, #1e1b4b 55%, #31104b 100%);
            color: #ffffff;
            padding: 50px 44px;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .showcase-pane::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: 
                radial-gradient(circle at 20% 20%, rgba(125, 42, 232, 0.35) 0%, transparent 40%),
                radial-gradient(circle at 80% 80%, rgba(0, 196, 204, 0.25) 0%, transparent 40%);
            pointer-events: none;
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            position: relative;
            z-index: 2;
        }

        .brand-icon-box {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 1.35rem;
            box-shadow: 0 4px 18px rgba(125, 42, 232, 0.4);
        }

        .brand-name {
            font-family: 'Outfit', sans-serif;
            font-weight: 900;
            font-size: 1.6rem;
            color: #ffffff;
            line-height: 1;
        }

        .showcase-headline {
            font-family: 'Outfit', sans-serif;
            font-size: 2.15rem;
            font-weight: 800;
            line-height: 1.25;
            letter-spacing: -0.02em;
            margin: 36px 0 16px;
            position: relative;
            z-index: 2;
        }

        .showcase-subtext {
            color: rgba(255, 255, 255, 0.78);
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 30px;
            position: relative;
            z-index: 2;
        }

        /* Perks List */
        .perks-list {
            list-style: none;
            padding: 0;
            margin: 0 0 36px;
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .perk-item {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.92rem;
            color: rgba(255, 255, 255, 0.92);
        }

        .perk-icon-wrap {
            width: 30px;
            height: 30px;
            border-radius: 10px;
            background: rgba(0, 196, 204, 0.2);
            color: #22d3ee;
            border: 1px solid rgba(0, 196, 204, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        /* Live Shift Security Badge */
        .showcase-status-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 20px;
            padding: 18px 20px;
            position: relative;
            z-index: 2;
        }

        /* Right Form Pane */
        .form-pane {
            padding: 50px 48px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        @media (max-width: 991.98px) {
            .form-pane {
                padding: 36px 24px;
            }
            .showcase-pane {
                padding: 36px 24px;
            }
        }

        .form-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.95rem;
            font-weight: 800;
            color: var(--canva-dark);
            margin-bottom: 6px;
            letter-spacing: -0.02em;
        }

        .form-subtitle {
            color: var(--canva-slate);
            font-size: 0.92rem;
            margin-bottom: 24px;
        }

        /* Custom Form Inputs */
        .input-group-canva {
            position: relative;
            margin-bottom: 18px;
        }

        .input-label-canva {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.84rem;
            font-weight: 700;
            color: #334155;
            margin-bottom: 6px;
        }

        .input-box-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-box-wrap i.field-icon {
            position: absolute;
            left: 16px;
            color: #94a3b8;
            font-size: 1.1rem;
            pointer-events: none;
            transition: color 0.2s ease;
        }

        .form-control-canva {
            width: 100%;
            height: 52px;
            padding: 10px 16px 10px 46px;
            font-size: 0.95rem;
            font-weight: 500;
            color: #0f172a;
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 14px;
            transition: all 0.2s cubic-bezier(0.2, 0.8, 0.2, 1);
            outline: none;
        }

        .form-control-canva:focus {
            border-color: var(--canva-violet);
            box-shadow: 0 0 0 4px rgba(125, 42, 232, 0.12);
            background: #ffffff;
        }

        .input-box-wrap:focus-within i.field-icon {
            color: var(--canva-violet);
        }

        .toggle-pw-btn {
            position: absolute;
            right: 14px;
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 4px;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            transition: color 0.2s ease;
        }

        .toggle-pw-btn:hover {
            color: var(--canva-violet);
        }

        /* Canva Gradient Submit Button */
        .btn-login-submit {
            width: 100%;
            height: 52px;
            background: var(--gradient-primary);
            color: #ffffff;
            border: none;
            border-radius: 50px;
            font-family: 'Outfit', sans-serif;
            font-size: 1.05rem;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
            box-shadow: 0 10px 25px rgba(125, 42, 232, 0.35);
            transition: all 0.25s cubic-bezier(0.2, 0.8, 0.2, 1);
            margin-top: 10px;
            text-decoration: none;
        }

        .btn-login-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 30px rgba(125, 42, 232, 0.45);
            color: #ffffff;
        }

        /* Google OAuth Button */
        .btn-google-signin {
            width: 100%;
            height: 48px;
            background: #ffffff;
            border: 1.5px solid #cbd5e1;
            border-radius: 50px;
            color: #334155;
            font-weight: 700;
            font-size: 0.92rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            transition: all 0.2s ease;
            text-decoration: none;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04);
            margin-bottom: 20px;
        }

        .btn-google-signin:hover {
            border-color: #94a3b8;
            background: #f8fafc;
            color: #0f172a;
            transform: translateY(-1px);
        }

        .divider-or {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 20px 0;
            color: #94a3b8;
            font-size: 0.76rem;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .divider-or::before,
        .divider-or::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e2e8f0;
        }

        .divider-or span {
            padding: 0 12px;
        }

        .back-nav-bar {
            margin-bottom: 18px;
        }

        .back-link {
            color: var(--canva-slate);
            font-size: 0.86rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: color 0.15s ease;
        }

        .back-link:hover {
            color: var(--canva-violet);
        }
    </style>
</head>
<body>

    <!-- Ambient Glow Orbs -->
    <div class="canva-orb orb-purple"></div>
    <div class="canva-orb orb-cyan"></div>
    <div class="canva-orb orb-pink"></div>

    <div class="auth-container">
        <div class="auth-card-wrapper">
            <div class="row g-0">
                
                <!-- Left Column: Canva Brand Showcase -->
                <div class="col-lg-5 showcase-pane d-none d-lg-flex">
                    <div>
                        <!-- Brand Logo -->
                        <a href="{{ route('home') }}" class="brand-badge">
                            <div class="brand-icon-box">
                                <i class="bi bi-stars"></i>
                            </div>
                            <span class="brand-name">Likha<span style="color: #67e8f9;">POS</span></span>
                        </a>

                        <div class="showcase-headline">
                            Mag-Sign In sa Iyong Cloud POS Terminal
                        </div>
                        <p class="showcase-subtext">
                            I-access ang live store cashier counter, customer utang ledger, real-time stock inventory, at daily sales reports.
                        </p>

                        <!-- Key Features -->
                        <ul class="perks-list">
                            <li class="perk-item">
                                <div class="perk-icon-wrap"><i class="bi bi-upc-scan"></i></div>
                                <div><strong>Mabilis na Barcode Checkout:</strong> Subok sa mabilisang rush hour at thermal printing.</div>
                            </li>
                            <li class="perk-item">
                                <div class="perk-icon-wrap"><i class="bi bi-journal-bookmark"></i></div>
                                <div><strong>Digital Suki Utang Ledger:</strong> Auto-record ng pautang at hulog ng bawat customer.</div>
                            </li>
                            <li class="perk-item">
                                <div class="perk-icon-wrap"><i class="bi bi-shield-lock"></i></div>
                                <div><strong>Cashier & Drawer Audit Guard:</strong> Iwas kupit at may detalyadong shift reconciliations.</div>
                            </li>
                            <li class="perk-item">
                                <div class="perk-icon-wrap"><i class="bi bi-cloud-check"></i></div>
                                <div><strong>Multi-Device Cloud Sync:</strong> Gumagana sa tablet, laptop, o kahit anong Android phone.</div>
                            </li>
                        </ul>
                    </div>

                    <!-- Shift Status Box -->
                    <div class="showcase-status-card">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 fw-bold">
                                <i class="bi bi-circle-fill me-1" style="font-size: 7px;"></i> Terminal Ready
                            </span>
                            <span class="small text-white-50">Cloud Active</span>
                        </div>
                        <p class="small text-white-50 mb-0">
                            Handa ang inyong tindahan para sa bagong shift. I-enter ang credentials para magsimula.
                        </p>
                    </div>
                </div>

                <!-- Right Column: Login Form -->
                <div class="col-lg-7 form-pane">
                    
                    <div class="back-nav-bar d-flex justify-content-between align-items-center">
                        <a href="{{ route('home') }}" class="back-link">
                            <i class="bi bi-arrow-left"></i> Bumalik sa LikhaPOS Home
                        </a>
                        <span class="d-lg-none brand-name" style="font-size: 1.2rem; color: #0f172a;">
                            Likha<span style="color: var(--canva-violet);">POS</span>
                        </span>
                    </div>

                    <h1 class="form-title">Store Sign In</h1>
                    <p class="form-subtitle">I-enter ang inyong email o username para buksan ang POS terminal.</p>

                    @if(isset($errors) && $errors->any())
                        <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4" role="alert">
                            <div class="d-flex align-items-center gap-2 mb-1 fw-bold">
                                <i class="bi bi-exclamation-triangle-fill"></i> Hindi matagumpay ang sign in:
                            </div>
                            <ul class="mb-0 ps-3 small">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4" role="alert">
                            <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
                        </div>
                    @endif

                    <!-- Google Sign In Button -->
                    <a href="{{ route('google.redirect') }}" class="btn-google-signin">
                        <svg width="18" height="18" viewBox="0 0 18 18">
                            <path fill="#4285F4" d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.259h2.908c1.702-1.567 2.684-3.874 2.684-6.617z"/>
                            <path fill="#34A853" d="M9 18c2.43 0 4.467-.806 5.956-2.18l-2.908-2.259c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332A8.997 8.997 0 0 0 9 18z"/>
                            <path fill="#FBBC05" d="M3.964 10.71A5.41 5.41 0 0 1 3.682 9c0-.593.102-1.17.282-1.71V4.958H.957A8.996 8.996 0 0 0 0 9c0 1.452.348 2.827.957 4.042l3.007-2.332z"/>
                            <path fill="#EA4335" d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0A8.997 8.997 0 0 0 .957 4.958L3.964 7.29C4.672 5.163 6.656 3.58 9 3.58z"/>
                        </svg>
                        <span>Mag-Sign In gamit ang Google</span>
                    </a>

                    <div class="divider-or">
                        <span>O mag-login gamit ang Email</span>
                    </div>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Email or Username Input -->
                        <div class="input-group-canva">
                            <label class="input-label-canva" for="login">
                                <span>Email Address o Username *</span>
                            </label>
                            <div class="input-box-wrap">
                                <i class="bi bi-envelope field-icon"></i>
                                <input type="text" 
                                       id="login" 
                                       name="login" 
                                       class="form-control-canva" 
                                       placeholder="owner@gmail.com o username" 
                                       value="{{ old('login') }}" 
                                       required 
                                       autofocus>
                            </div>
                        </div>

                        <!-- Password Input -->
                        <div class="input-group-canva mb-2">
                            <label class="input-label-canva" for="password">
                                <span>Password *</span>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="text-decoration-none small" style="color: var(--canva-violet);">
                                        Nakalimutan ang password?
                                    </a>
                                @endif
                            </label>
                            <div class="input-box-wrap">
                                <i class="bi bi-lock field-icon"></i>
                                <input type="password" 
                                       id="password" 
                                       name="password" 
                                       class="form-control-canva" 
                                       placeholder="••••••••" 
                                       required>
                                <button type="button" class="toggle-pw-btn" onclick="togglePassword('password', this)" title="Show/Hide Password">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Remember Device Checkbox -->
                        <div class="d-flex align-items-center justify-content-between mb-4 mt-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label small text-muted" for="remember">
                                    Tandaan ang device na ito
                                </label>
                            </div>
                            <span class="small text-muted d-flex align-items-center gap-1">
                                <i class="bi bi-shield-check text-success"></i> 256-bit Secure
                            </span>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn-login-submit">
                            <span>Buksan ang POS Terminal</span>
                            <i class="bi bi-arrow-right-short fs-4"></i>
                        </button>
                    </form>

                    <!-- Sign Up Promo Footer -->
                    <div class="text-center mt-4 pt-2">
                        <span class="text-muted small">Wala ka pang LikhaPOS store account?</span>
                        <a href="{{ route('register') }}" class="fw-bold text-decoration-none ms-1" style="color: var(--canva-violet);">
                            Mag-rehistro Nang Libre (5-Day Trial) &rarr;
                        </a>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <!-- Password Toggle Script -->
    <script>
        function togglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }
    </script>
</body>
</html>
