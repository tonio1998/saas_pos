<style>

    :root{

        --primary:
            var(--theme-bg);

        --primary-dark:
            color-mix(
                in srgb,
                var(--theme-bg) 70%,
                black 30%
            );

        --panel:
            rgba(10,12,14,.92);

        --glass:
            rgba(255,255,255,.05);

        --border:
            rgba(255,255,255,.06);

        --text-soft:
            rgba(255,255,255,.64);

        --yellow:
            #facc15;

        --green:
            #00ff7f;
    }

    *{
        margin:0;
        padding:0;
        box-sizing:border-box;
    }

    html,
    body{
        width:100%;
        height:100%;
    }
    html,
    body,
    .wrapper,
    .content-wrapper,
    .main-content,
    .app,
    .container-fluid{
        background:
            linear-gradient(
                135deg,
                #07110f 0%,
                #040707 45%,
                #020303 100%
            ) !important;

        color:#fff !important;
    }
    .status-bar{
        position:fixed;

        top:24px;
        left:24px;

        min-width:340px;

        height:78px;

        display:flex;
        align-items:center;
        gap:16px;

        padding:
            0
            24px;

        border-radius:24px;

        backdrop-filter:blur(18px);

        border:
            1px solid rgba(255,255,255,.06);

        z-index:1000;

        box-shadow:
            0 20px 50px rgba(0,0,0,.28);

        transition:.25s ease;
    }

    .status-bar.idle{
        background:
            linear-gradient(
                135deg,
                #065f46,
                #10b981
            );
    }

    .status-bar.success{
        background:
            linear-gradient(
                135deg,
                #15803d,
                #22c55e
            );
    }

    .status-bar.out{
        background:
            linear-gradient(
                135deg,
                #2563eb,
                #38bdf8
            );
    }

    .status-bar.error{
        background:
            linear-gradient(
                135deg,
                #b91c1c,
                #ef4444
            );
    }

    .status-pulse{
        width:14px;
        height:14px;

        border-radius:50%;

        background:#fff;

        box-shadow:
            0 0 20px rgba(255,255,255,.8);

        animation:pulse 1.2s infinite;
    }

    .status-title{
        font-size:22px;
        font-weight:1000;

        letter-spacing:1px;
    }

    .status-subtitle{
        margin-top:4px;

        font-size:12px;
        font-weight:600;

        color:rgba(255,255,255,.84);
    }
    #scan-input{
        position:fixed;

        top:-1000px;
        left:-1000px;

        opacity:0;

        pointer-events:none;
    }

    body{
        overflow:hidden;

        font-family:
            Inter,
            system-ui,
            sans-serif;

        color:#fff;

        background:
            radial-gradient(
                circle,
                rgba(16,185,129,.28),
                transparent 72%
            ) !important;

        position:relative;
    }

    body::before{
        content:'';

        position:fixed;
        inset:0;

        background-image:
            linear-gradient(
                rgba(255,255,255,.018) 1px,
                transparent 1px
            ),
            linear-gradient(
                90deg,
                rgba(255,255,255,.018) 1px,
                transparent 1px
            ) !important;

        background-size:40px 40px;

        mask-image:
            radial-gradient(
                circle at center,
                black,
                transparent 85%
            );

        pointer-events:none;
    }

    body::after{
        content:'';

        position:fixed;

        width:38vw;
        height:38vw;

        top:-12%;
        left:-8%;

        border-radius:50%;

        background:
            radial-gradient(
                circle,
                color-mix(
                    in srgb,
                    var(--theme-bg) 25%,
                    transparent
                ),
                transparent 72%
            );

        filter:blur(90px);

        pointer-events:none;
    }

    .scanner-core{
        position:relative;

        width:100%;
        height:100vh;

        display:grid;

        grid-template-columns:
        minmax(280px, 32%)
        minmax(420px, 1fr)
        minmax(240px, 300px);

        align-items:center;

        gap:
            clamp(24px, 3vw, 70px);

        padding:
            clamp(20px, 2vw, 40px)
            clamp(20px, 3vw, 50px)
            120px;

        z-index:2;
    }

    .left-zone{
        width:100%;
        height:100%;

        display:flex;
        align-items:center;
        justify-content:center;
    }

    .identity-stage{
        position:relative;

        width:100%;
        height:100%;

        display:flex;
        align-items:center;
        justify-content:center;
    }

    .identity-glow{
        position:absolute;

        width:min(32vw, 420px);
        height:min(32vw, 420px);

        border-radius:50%;

        background:
            radial-gradient(
                circle,
                color-mix(
                    in srgb,
                    var(--theme-bg) 30%,
                    transparent
                ),
                transparent 72%
            );

        filter:blur(80px);

        opacity:.95;
    }

    .scanner-ring{
        position:relative;

        width:
            clamp(240px, 24vw, 430px);

        height:
            clamp(240px, 24vw, 430px);

        display:flex;
        align-items:center;
        justify-content:center;
    }

    .scanner-ring::before{
        content:'';

        position:absolute;
        inset:0;

        border-radius:50%;

        border:
            4px solid rgba(255,255,255,.08);

        animation:rotate 16s linear infinite;
    }

    .scanner-ring-2{
        position:absolute;

        inset:12%;

        border-radius:50%;

        border:
            3px dashed rgba(255,255,255,.08);

        animation:rotateReverse 18s linear infinite;
    }

    .avatar-shell{
        position:relative;

        width:
            clamp(180px, 18vw, 320px);

        height:
            clamp(180px, 18vw, 320px);

        border-radius:50%;

        overflow:hidden;

        background:#111827;

        border:
            clamp(6px,.5vw,10px)
            solid
            var(--yellow);

        box-shadow:
            0 35px 90px rgba(0,0,0,.55),
            0 0 90px rgba(250,204,21,.14);
    }

    .avatar-shell img{
        width:100%;
        height:100%;

        object-fit:cover;
    }

    .scanner-line{
        position:absolute;

        left:0;
        top:0;

        width:100%;
        height:4px;

        background:
            linear-gradient(
                90deg,
                transparent,
                var(--green),
                transparent
            );

        box-shadow:
            0 0 25px rgba(0,255,127,.9);

        animation:scanLine 2s linear infinite;
    }

    .center-zone{
        height:100%;

        display:flex;
        flex-direction:column;
        justify-content:center;
    }

    .welcome-chip{
        width:fit-content;

        padding:
            12px
            18px;

        margin-bottom:24px;

        border-radius:999px;

        background:
            rgba(255,255,255,.05);

        border:
            1px solid rgba(255,255,255,.08);

        backdrop-filter:blur(14px);

        font-size:13px;
        font-weight:1000;

        letter-spacing:3px;

        text-transform:uppercase;

        color:#fde047;

        box-shadow:
            0 10px 30px rgba(0,0,0,.2);
    }

    .person-name{
        font-size:
            clamp(52px, 6vw, 110px);

        line-height:.9;

        font-weight:1000;

        letter-spacing:-.07em;

        text-transform:uppercase;

        color:#fff;

        margin-bottom:18px;

        text-shadow:
            0 20px 60px rgba(0,0,0,.55);

        word-break:break-word;
    }

    .person-role{
        max-width:760px;

        font-size:
            clamp(16px, 1.3vw, 26px);

        line-height:1.5;

        color:var(--text-soft);

        margin-bottom:40px;
    }

    .activity-strip{
        width:fit-content;

        display:flex;
        align-items:center;
        gap:12px;

        padding:
            15px
            20px;

        border-radius:18px;

        background:
            rgba(255,255,255,.05);

        border:
            1px solid rgba(255,255,255,.06);

        backdrop-filter:blur(14px);

        font-size:13px;
        font-weight:900;

        letter-spacing:2px;

        box-shadow:
            0 15px 40px rgba(0,0,0,.22);
    }

    .activity-pulse{
        width:12px;
        height:12px;

        border-radius:50%;

        background:var(--green);

        animation:pulse 1.2s infinite;
    }

    .right-zone{
        height:100%;

        display:flex;
        flex-direction:column;
        justify-content:center;

        gap:22px;
    }

    .metric-card{
        position:relative;

        overflow:hidden;

        min-height:220px;

        padding:28px;

        border-radius:30px;

        display:flex;
        flex-direction:column;
        justify-content:space-between;

        background:
            linear-gradient(
                180deg,
                rgba(255,255,255,.05),
                rgba(255,255,255,.025)
            );

        border:
            1px solid rgba(255,255,255,.06);

        backdrop-filter:blur(18px);

        box-shadow:
            0 20px 60px rgba(0,0,0,.3);
    }

    .metric-card::before{
        content:'';

        position:absolute;

        top:0;
        left:0;
        right:0;

        height:4px;

        background:
            linear-gradient(
                90deg,
                transparent,
                rgba(255,255,255,.3),
                transparent
            );
    }

    .metric-card.primary{
        box-shadow:
            inset 0 0 80px rgba(255,255,255,.015),
            0 20px 60px rgba(0,0,0,.3);
    }

    .metric-card.yellow{
        background:
            linear-gradient(
                135deg,
                #facc15,
                #fde047
            );

        box-shadow:
            0 20px 60px rgba(250,204,21,.18);
    }

    .metric-label{
        font-size:13px;
        font-weight:900;

        letter-spacing:2px;

        color:rgba(255,255,255,.68);
    }

    .metric-label.dark{
        color:#111827;
    }

    .metric-value{
        font-size:
            clamp(46px, 4vw, 82px);

        font-weight:1000;

        line-height:1;

        letter-spacing:-.06em;

        color:#fff;
    }

    .metric-value.dark{
        color:#111827;
    }

    .bottom-terminal{
        position:fixed;

        left:50%;
        bottom:24px;

        transform:translateX(-50%);

        width:
            min(1280px, calc(100% - 40px));

        min-height:74px;

        display:flex;
        align-items:center;
        justify-content:center;

        flex-wrap:wrap;

        gap:22px;

        padding:
            18px
            28px;

        border-radius:24px;

        background:
            rgba(255,255,255,.045);

        border:
            1px solid rgba(255,255,255,.06);

        backdrop-filter:blur(20px);

        box-shadow:
            0 20px 60px rgba(0,0,0,.25);

        z-index:10;
    }

    .terminal-item{
        display:flex;
        align-items:center;
        gap:12px;

        font-size:13px;
        font-weight:900;

        letter-spacing:1.5px;

        color:rgba(255,255,255,.82);

        white-space:nowrap;
    }

    .terminal-dot{
        width:10px;
        height:10px;

        border-radius:50%;

        background:var(--green);

        animation:pulse 1.2s infinite;
    }

    .terminal-divider{
        width:1px;
        height:20px;

        background:
            rgba(255,255,255,.08);
    }

    .success-flash{
        animation:successFlash .45s ease;
    }

    .error-flash{
        animation:errorFlash .45s ease;
    }

    @keyframes pulse{

        0%{
            opacity:1;
            transform:scale(1);
        }

        50%{
            opacity:.35;
            transform:scale(1.25);
        }

        100%{
            opacity:1;
            transform:scale(1);
        }

    }

    @keyframes rotate{

        from{
            transform:rotate(0deg);
        }

        to{
            transform:rotate(360deg);
        }

    }

    @keyframes rotateReverse{

        from{
            transform:rotate(360deg);
        }

        to{
            transform:rotate(0deg);
        }

    }

    @keyframes scanLine{

        0%{
            transform:translateY(0);
        }

        100%{
            transform:translateY(calc(100% - 6px));
        }

    }

    @keyframes successFlash{

        0%{
            background-color:rgba(34,197,94,0);
        }

        50%{
            background-color:rgba(34,197,94,.18);
        }

        100%{
            background-color:rgba(34,197,94,0);
        }

    }

    @keyframes errorFlash{

        0%{
            background-color:rgba(239,68,68,0);
        }

        50%{
            background-color:rgba(239,68,68,.22);
        }

        100%{
            background-color:rgba(239,68,68,0);
        }

    }

    @media(max-width:1200px){

        body{
            overflow:auto;
        }

        .scanner-core{
            height:auto;
            min-height:100vh;

            grid-template-columns:1fr;

            text-align:center;

            padding-bottom:180px;
        }

        .center-zone,
        .right-zone{
            align-items:center;
        }

        .right-zone{
            width:100%;

            flex-direction:row;
        }

        .metric-card{
            flex:1;
        }

        .activity-strip{
            margin-inline:auto;
        }

    }

    @media(max-width:768px){

        .scanner-core{
            gap:34px;

            padding:
                20px
                16px
                190px;
        }

        .person-name{
            font-size:
                clamp(42px, 13vw, 72px);
        }

        .person-role{
            font-size:15px;
        }

        .right-zone{
            width:100%;

            flex-direction:column;
        }

        .metric-card{
            width:100%;

            min-height:180px;
        }

        .bottom-terminal{
            width:calc(100% - 24px);

            border-radius:20px;

            padding:16px;

            gap:14px;
        }

        .terminal-divider{
            display:none;
        }

    }

</style>
