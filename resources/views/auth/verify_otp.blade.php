<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Your Email - {{ config('app.name', 'LikhaPOS') }}</title>
    
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
            padding: 24px 16px;
            color: #334155;
        }

        .verify-card {
            background: #ffffff;
            border-radius: 28px;
            padding: 48px 40px;
            max-width: 520px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.08), 0 0 0 1px rgba(226, 232, 240, 0.8);
            text-align: center;
            position: relative;
        }

        .verify-title {
            font-family: 'Outfit', serif, sans-serif;
            font-size: 2.35rem;
            font-weight: 800;
            color: #1e3a8a; /* Deep navy blue matching user reference image */
            margin-bottom: 12px;
            letter-spacing: -0.02em;
        }

        .verify-subtitle {
            font-size: 0.96rem;
            color: #64748b;
            margin-bottom: 16px;
            line-height: 1.5;
        }

        .verify-email-highlight {
            color: #155dfc;
            font-weight: 700;
        }

        .verify-alert-sent {
            color: #16a34a;
            font-size: 0.92rem;
            font-weight: 600;
            margin-bottom: 28px;
        }

        /* 6-Digit Individual Boxes */
        .otp-inputs-wrap {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-bottom: 28px;
        }

        .otp-digit-input {
            width: 54px;
            height: 64px;
            text-align: center;
            font-size: 2rem;
            font-weight: 800;
            font-family: 'Outfit', sans-serif;
            color: #1e3a8a;
            border: 2px solid #cbd5e1;
            border-radius: 14px;
            background: #ffffff;
            transition: all 0.2s cubic-bezier(0.2, 0.8, 0.2, 1);
            outline: none;
        }

        .otp-digit-input:focus {
            border-color: #155dfc;
            box-shadow: 0 0 0 4px rgba(21, 93, 252, 0.15);
            transform: translateY(-2px);
            background: #f8faff;
        }

        .otp-expiry-note {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 0.88rem;
            color: #155dfc;
            font-weight: 600;
            margin-bottom: 24px;
        }

        .btn-verify-submit {
            background: #155dfc;
            color: #ffffff;
            border: none;
            font-family: 'Outfit', sans-serif;
            font-size: 1.1rem;
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
        }

        .btn-verify-submit:hover {
            background: #1d4ed8;
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 12px 28px rgba(21, 93, 252, 0.45);
        }

        /* Divider & Resend */
        .divider-wrap {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 30px 0 20px;
        }
        .divider-line {
            flex: 1;
            border-bottom: 1.5px solid #38bdf8;
            opacity: 0.7;
        }
        .divider-text {
            padding: 0 16px;
            color: #0284c7;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .btn-resend-link {
            color: #0284c7;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: transparent;
            border: none;
            cursor: pointer;
            transition: color 0.2s ease;
        }
        .btn-resend-link:hover {
            color: #0369a1;
            text-decoration: underline;
        }
        .btn-resend-link:disabled {
            color: #94a3b8;
            cursor: not-allowed;
            text-decoration: none;
        }

        /* Dev Mode Helper Banner */
        .dev-otp-pill {
            background: #fef3c7;
            color: #92400e;
            border: 1px dashed #f59e0b;
            border-radius: 10px;
            padding: 8px 14px;
            font-size: 0.82rem;
            margin-bottom: 20px;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <div class="verify-card">
        <!-- Brand Link -->
        <a href="{{ route('home') }}" class="text-decoration-none d-inline-block mb-3">
            <span class="fw-bold text-dark fs-5 font-heading">Likha<span class="text-primary">POS</span></span>
        </a>

        <!-- Main Heading -->
        <h1 class="verify-title">Verify Your Email</h1>

        <!-- Subtitle with Masked Email -->
        <p class="verify-subtitle">
            We sent a 6 digit verification code to<br>
            <span class="verify-email-highlight">{{ $maskedEmail }}</span>
        </p>

        <!-- Status / Sent Alert -->
        @if(session('success'))
            <div class="verify-alert-sent">
                {{ session('success') }}
            </div>
        @else
            <div class="verify-alert-sent">
                We sent a verification code to your email.
            </div>
        @endif

        @if($errors->has('otp'))
            <div class="alert alert-danger rounded-3 py-2 small fw-bold mb-3">
                <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $errors->first('otp') }}
            </div>
        @endif

        @if(session('dev_otp') || config('app.debug'))
            <div class="dev-otp-pill">
                <i class="bi bi-info-circle-fill me-1"></i> <strong>Dev Test Code:</strong> 
                <span class="badge bg-warning text-dark fs-6">{{ session('dev_otp', $user->otp_code ?? '691507') }}</span>
                <span class="small d-block mt-1 text-muted">(Autofills automatically for convenience)</span>
            </div>
        @endif

        <!-- Form with 6 Digit Inputs -->
        <form method="POST" action="{{ route('verification.verify') }}" id="verifyForm">
            @csrf
            <input type="hidden" name="otp" id="hiddenOtp">

            <div class="otp-inputs-wrap">
                <input type="text" maxlength="1" class="otp-digit-input" id="digit-1" inputmode="numeric" autocomplete="one-time-code" autofocus>
                <input type="text" maxlength="1" class="otp-digit-input" id="digit-2" inputmode="numeric">
                <input type="text" maxlength="1" class="otp-digit-input" id="digit-3" inputmode="numeric">
                <input type="text" maxlength="1" class="otp-digit-input" id="digit-4" inputmode="numeric">
                <input type="text" maxlength="1" class="otp-digit-input" id="digit-5" inputmode="numeric">
                <input type="text" maxlength="1" class="otp-digit-input" id="digit-6" inputmode="numeric">
            </div>

            <!-- Expiry Note -->
            <div class="otp-expiry-note">
                <i class="bi bi-shield-fill-check"></i>
                <span>This code will expire in 10 minutes.</span>
            </div>

            <!-- Verify Button -->
            <button type="submit" class="btn-verify-submit" id="btnSubmit">
                <i class="bi bi-shield-check"></i> Verify Code
            </button>
        </form>

        <!-- Divider -->
        <div class="divider-wrap">
            <span class="divider-line"></span>
            <span class="divider-text">Didn't receive the code?</span>
            <span class="divider-line"></span>
        </div>

        <!-- Resend Code Form -->
        <form method="POST" action="{{ route('verification.resend') }}" id="resendForm">
            @csrf
            <button type="submit" class="btn-resend-link" id="btnResend">
                <i class="bi bi-arrow-clockwise"></i> Resend Code
            </button>
            <div class="small text-muted mt-2" id="cooldownText" style="display: none;"></div>
        </form>

        <div class="mt-4 pt-2">
            <a href="{{ route('login') }}" class="small text-muted text-decoration-none">
                <i class="bi bi-arrow-left me-1"></i> Back to Store Login
            </a>
        </div>
    </div>

    <!-- Interactive Auto-advance and Paste JavaScript -->
    <script>
        const digits = [
            document.getElementById('digit-1'),
            document.getElementById('digit-2'),
            document.getElementById('digit-3'),
            document.getElementById('digit-4'),
            document.getElementById('digit-5'),
            document.getElementById('digit-6')
        ];
        const hiddenOtp = document.getElementById('hiddenOtp');
        const form = document.getElementById('verifyForm');

        digits.forEach((input, index) => {
            // Typing handler
            input.addEventListener('input', function(e) {
                const val = this.value.replace(/[^0-9]/g, '');
                this.value = val;

                if (val.length === 1 && index < 5) {
                    digits[index + 1].focus();
                }
                updateHiddenOtp();
            });

            // Backspace handler
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && !this.value && index > 0) {
                    digits[index - 1].focus();
                }
            });

            // Paste handler
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const pasteData = (e.clipboardData || window.clipboardData).getData('text').trim();
                const cleanDigits = pasteData.replace(/[^0-9]/g, '').slice(0, 6);
                
                for (let i = 0; i < cleanDigits.length; i++) {
                    if (digits[i]) {
                        digits[i].value = cleanDigits[i];
                    }
                }

                if (cleanDigits.length === 6) {
                    digits[5].focus();
                } else if (cleanDigits.length > 0) {
                    digits[Math.min(cleanDigits.length, 5)].focus();
                }
                updateHiddenOtp();
            });
        });

        function updateHiddenOtp() {
            const code = digits.map(d => d.value).join('');
            hiddenOtp.value = code;
        }

        form.addEventListener('submit', function(e) {
            updateHiddenOtp();
            if (hiddenOtp.value.length < 6) {
                e.preventDefault();
                alert('Mangyaring ilagay ang buong 6-digit verification code.');
            }
        });

        // Autofill dev code if available
        @if(session('dev_otp') || (!empty($user->otp_code) && config('app.debug')))
            const devCode = "{{ session('dev_otp', $user->otp_code ?? '') }}";
            if (devCode && devCode.length === 6) {
                for (let i = 0; i < 6; i++) {
                    digits[i].value = devCode[i];
                }
                updateHiddenOtp();
            }
        @endif

        // Resend Cooldown
        let timeLeft = 60;
        const btnResend = document.getElementById('btnResend');
        const cooldownText = document.getElementById('cooldownText');

        // Optional local 60s cooldown if just resent
        @if(session('just_resent'))
            btnResend.disabled = true;
            cooldownText.style.display = 'block';
            const interval = setInterval(() => {
                timeLeft--;
                cooldownText.innerText = `Puwede muling mag-resend sa loob ng ${timeLeft} segundo.`;
                if (timeLeft <= 0) {
                    clearInterval(interval);
                    btnResend.disabled = false;
                    cooldownText.style.display = 'none';
                }
            }, 1000);
        @endif
    </script>
</body>
</html>
