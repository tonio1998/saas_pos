<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        @yield('title', 'System Error')
    </title>

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <style>

        :root{

            --primary:#004D1A;

            --primary-light:#0F7A35;

            --bg:#f4f7f5;

            --card:#ffffff;

            --text:#17212b;

            --muted:#6b7280;

            --border:#e5e7eb;
        }

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        body{

            font-family:'Inter',sans-serif;

            min-height:100vh;

            background:
                linear-gradient(
                    180deg,
                    #f8fafc 0%,
                    #eef6f0 100%
                );

            display:flex;
            align-items:center;
            justify-content:center;

            padding:24px;

            color:var(--text);
        }

        .error-wrapper{

            width:100%;
            max-width:640px;
        }

        .error-card{

            background:var(--card);

            border:
                1px solid var(--border);

            border-radius:28px;

            padding:56px 48px;

            text-align:center;

            box-shadow:
                0 10px 40px rgba(15,23,42,.06);
        }

        .error-badge{

            width:86px;
            height:86px;

            margin:0 auto 24px;

            border-radius:24px;

            display:flex;
            align-items:center;
            justify-content:center;

            background:
                linear-gradient(
                    135deg,
                    rgba(0,77,26,.12),
                    rgba(15,122,53,.08)
                );

            color:var(--primary);

            font-size:40px;
        }

        .error-code{

            font-size:88px;
            font-weight:800;
            line-height:1;

            color:var(--primary);

            margin-bottom:12px;
        }

        .error-title{

            font-size:30px;
            font-weight:700;

            margin-bottom:14px;
        }

        .error-description{

            max-width:460px;

            margin:0 auto 36px;

            font-size:15px;
            line-height:1.8;

            color:var(--muted);
        }

        .error-actions{

            display:flex;
            align-items:center;
            justify-content:center;
            gap:14px;

            flex-wrap:wrap;
        }

        .btn{

            border:none;

            text-decoration:none;

            min-width:170px;
            height:50px;

            padding:0 18px;

            border-radius:14px;

            display:inline-flex;
            align-items:center;
            justify-content:center;

            font-size:14px;
            font-weight:600;

            transition:.2s ease;

            cursor:pointer;
        }

        .btn-primary{

            background:var(--primary);

            color:#fff;
        }

        .btn-primary:hover{

            background:var(--primary-light);

            transform:translateY(-2px);
        }

        .btn-secondary{

            background:#fff;

            color:var(--text);

            border:
                1px solid var(--border);
        }

        .btn-secondary:hover{

            background:#f9fafb;

            transform:translateY(-2px);
        }

        @media (max-width:640px){

            .error-card{

                padding:40px 24px;

                border-radius:22px;
            }

            .error-code{

                font-size:70px;
            }

            .error-title{

                font-size:24px;
            }

            .btn{

                width:100%;
            }
        }

    </style>
</head>
<body>

<div class="error-wrapper">

    <div class="error-card">

        <div class="error-badge">
            @yield('icon', '⚠️')
        </div>

        <div class="error-code">
            @yield('code')
        </div>

        <div class="error-title">
            @yield('heading')
        </div>

        <div class="error-description">
            @yield('message')
        </div>

        <div class="error-actions">

            <a
                href="{{ url()->previous() }}"
                class="btn btn-secondary"
            >
                Go Back
            </a>

            <a
                href="{{ route('dashboard.index') }}"
                class="btn btn-primary"
            >
                Dashboard
            </a>

        </div>

    </div>

</div>

</body>
</html>
