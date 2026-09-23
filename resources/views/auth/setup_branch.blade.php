<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Set Up Your Main Branch - {{ config('app.name', 'LikhaPOS') }}</title>
    
    <!-- Fonts: Outfit & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons & Bootstrap 5.3 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 50%, #f0fdf4 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 16px;
            color: #334155;
        }

        .branch-setup-card {
            background: #ffffff;
            border-radius: 28px;
            padding: 44px 40px;
            max-width: 640px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.08), 0 0 0 1px rgba(226, 232, 240, 0.8);
            position: relative;
        }

        .step-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(21, 93, 252, 0.1);
            color: #155dfc;
            font-size: 0.78rem;
            font-weight: 700;
            padding: 4px 14px;
            border-radius: 50px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }

        .setup-title {
            font-family: 'Outfit', sans-serif;
            font-size: 2.1rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 8px;
            letter-spacing: -0.02em;
        }

        .setup-subtitle {
            font-size: 0.95rem;
            color: #64748b;
            margin-bottom: 24px;
            line-height: 1.5;
        }

        /* Multi-branch info banner */
        .multi-branch-notice {
            background: linear-gradient(135deg, #eff6ff 0%, #f0fdf4 100%);
            border: 1.5px solid #bfdbfe;
            border-radius: 16px;
            padding: 16px 20px;
            margin-bottom: 28px;
            display: flex;
            gap: 14px;
            align-items: flex-start;
        }
        .multi-branch-notice i {
            color: #155dfc;
            font-size: 1.4rem;
            margin-top: 2px;
        }
        .multi-branch-notice-title {
            font-weight: 700;
            color: #1e3a8a;
            font-size: 0.94rem;
            margin-bottom: 3px;
        }
        .multi-branch-notice-text {
            font-size: 0.84rem;
            color: #475569;
            line-height: 1.45;
            margin: 0;
        }

        .form-label {
            font-weight: 700;
            font-size: 0.88rem;
            color: #1e293b;
            margin-bottom: 6px;
        }
        .form-control {
            border-radius: 12px;
            padding: 12px 16px;
            border: 1.5px solid #cbd5e1;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }
        .form-control:focus {
            border-color: #155dfc;
            box-shadow: 0 0 0 4px rgba(21, 93, 252, 0.12);
        }

        .btn-launch-store {
            background: linear-gradient(135deg, #155dfc 0%, #5e17eb 100%);
            color: #ffffff;
            border: none;
            font-family: 'Outfit', sans-serif;
            font-size: 1.15rem;
            font-weight: 700;
            padding: 14px 28px;
            border-radius: 14px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 8px 24px rgba(21, 93, 252, 0.35);
            transition: all 0.2s ease;
            margin-top: 10px;
        }
        .btn-launch-store:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(21, 93, 252, 0.45);
            color: #ffffff;
        }
    </style>
</head>
<body>

    <div class="branch-setup-card">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="step-pill">
                <i class="bi bi-stars"></i> Step 2 of 2: Main Branch
            </span>
            <span class="small text-muted fw-bold">100% Verified ✅</span>
        </div>

        <h1 class="setup-title">I-setup ang Iyong Main Branch</h1>
        <p class="setup-subtitle">
            I-configure ang inyong pangunahing branch o tindahan para makapagsimula nang mag-cashier, mag-scan ng barcode, at mag-monitor ng imbentaryo.
        </p>

        <!-- Multi-Branch Ready Banner -->
        <div class="multi-branch-notice">
            <i class="bi bi-buildings-fill"></i>
            <div>
                <div class="multi-branch-notice-title">Multi-Branch Ready Architecture</div>
                <p class="multi-branch-notice-text">
                    Naka-setup ang inyong account sa <strong>1 Main Branch</strong> muna. Sa inyong Store Settings o kapag nag-upgrade sa Negosyo Pro, maaari kang magdagdag ng maraming branches at i-manage ang lahat ng tindahan sa iisang dashboard.
                </p>
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger rounded-3 py-2 small fw-bold mb-3">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('onboarding.branch.store') }}">
            @csrf

            <div class="row g-3 mb-3">
                <div class="col-md-8">
                    <label class="form-label">Pangalan ng Main Branch *</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-shop"></i></span>
                        <input type="text" name="branch_name" class="form-control border-start-0 ps-0" value="{{ old('branch_name', ($tenant->business_name ?? 'Likha Store') . ' - Main Branch') }}" placeholder="e.g. San Jose Minimart - Main" required>
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Branch Code *</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-upc"></i></span>
                        <input type="text" name="branch_code" class="form-control border-start-0 ps-0 text-uppercase fw-bold font-monospace" value="{{ old('branch_code', 'MAIN') }}" placeholder="e.g. MAIN" required>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Kumpletong Lokasyon / Address ng Tindahan *</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-geo-alt"></i></span>
                    <input type="text" name="address" class="form-control border-start-0 ps-0" value="{{ old('address', $tenant->address ?? '') }}" placeholder="e.g. Purok 4, Poblacion, Surigao City" required>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">Contact / Phone Number *</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-telephone"></i></span>
                        <input type="text" name="phone" class="form-control border-start-0 ps-0" value="{{ old('phone', $tenant->phone ?? '0912 345 6789') }}" placeholder="e.g. 0912 345 6789" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Branch In-Charge / Manager</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-person"></i></span>
                        <input type="text" name="manager_name" class="form-control border-start-0 ps-0" value="{{ old('manager_name', auth()->user()->name ?? 'Store Owner') }}" placeholder="Pangalan ng Manager">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-launch-store">
                <i class="bi bi-rocket-takeoff-fill"></i> I-save ang Main Branch at Buksan ang Store
            </button>
        </form>
    </div>

</body>
</html>
