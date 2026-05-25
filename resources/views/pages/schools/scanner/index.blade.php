@extends('layouts.scanner')

@section('title','Gate Scanner')

@section('content')

    <div class="container-fluid kiosk-wrap p-0" style="background: var(--theme-bg);">

        <div class="scanner-bg"></div>
        <div class="scanner-bg scanner-bg-2"></div>

        <div
            id="status-bar"
            class="status-bar idle"
        >

            <div class="status-indicator"></div>

            <div class="status-content">

                <div class="status-title">
                    TIME IN MODE
                </div>

                <div class="status-subtitle">
                    Waiting for entry scan
                </div>

            </div>

        </div>

        <div id="live-clock"></div>

        <div class="scanner-body bg-white">

            <div class="scanner-card">

                <div class="mode-switch">

                    <button
                        class="mode-btn active"
                        data-mode="TIME_IN"
                    >
                        <i class="bi bi-box-arrow-in-right"></i>
                        TIME IN
                    </button>

                    <button
                        class="mode-btn"
                        data-mode="TIME_OUT"
                    >
                        <i class="bi bi-box-arrow-left"></i>
                        TIME OUT
                    </button>

                </div>

                <div class="scanner-grid">

                    <div class="scanner-left">

                        <div class="scanner-ring">

                            <div class="scanner-ring-2"></div>

                            <div class="photo-wrap">

                                <img
                                    id="person-photo"
                                    src="{{ asset('images/avatar.png') }}"
                                >

                            </div>

                        </div>

                    </div>

                    <div class="scanner-right">

                        <div class="scanner-chip">

                            <div class="scanner-dot"></div>

                            LIVE GATE TERMINAL

                        </div>

                        <div
                            id="greeting-text"
                            class="greeting-text"
                        >
                            READY TO WELCOME
                        </div>

                        <div
                            id="person-name"
                            class="name-text"
                        >
                            WAITING...
                        </div>

                        <div
                            id="person-role"
                            class="role-text"
                        >
                            TAP CARD / SCAN QR
                        </div>

                        <div class="info-grid">

                            <div class="info-box">

                                <div class="info-label">
                                    TIME
                                </div>

                                <div
                                    id="scan-time"
                                    class="big-value"
                                >
                                    --:--
                                </div>

                            </div>

                            <div class="info-box yellow">

                                <div class="info-label">
                                    TOTAL SCANS
                                </div>

                                <div
                                    id="scan-count"
                                    class="big-value dark"
                                >
                                    0
                                </div>

                            </div>

                        </div>

                        <div class="scanner-footer">

                            <div class="pulse-dot"></div>

                            Scanner Active

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <audio
            id="success-sound"
            src="{{ asset('sounds/success.mp3') }}"
            preload="auto"
        ></audio>

        <audio
            id="error-sound"
            src="{{ asset('sounds/error.mp3') }}"
            preload="auto"
        ></audio>

        <input
            type="text"
            id="scan-input"
            autofocus
        >

    </div>

    <style>

        :root{

            --primary:#16a34a;
            --primary-dark:#14532d;
            --blue:#2563eb;
            --danger:#dc2626;
            --warning:#facc15;
            --glass:rgba(255,255,255,.08);
            --glass-border:rgba(255,255,255,.12);

        }

        body{
            margin:0;
            overflow:hidden;
            font-family:
                Inter,
                system-ui,
                sans-serif;

            background:
                radial-gradient(circle at top left,#14532d 0%,#08140d 55%,#020403 100%);
        }

        .kiosk-wrap{
            height:100vh;
            position:relative;
            overflow:hidden;
            padding:28px;
        }

        .scanner-bg{
            position:absolute;
            width:700px;
            height:700px;
            border-radius:50%;
            background:rgba(255,255,255,.04);
            filter:blur(20px);
            top:-250px;
            right:-200px;
            animation:floatBg 14s linear infinite;
        }

        .scanner-bg-2{
            width:500px;
            height:500px;
            top:auto;
            bottom:-180px;
            left:-150px;
            animation-duration:18s;
        }

        @keyframes floatBg{

            0%{
                transform:translateY(0px) rotate(0deg);
            }

            50%{
                transform:translateY(20px) rotate(180deg);
            }

            100%{
                transform:translateY(0px) rotate(360deg);
            }

        }

        #live-clock{
            position:absolute;
            top:24px;
            right:30px;
            z-index:20;

            padding:14px 22px;

            border-radius:18px;

            background:rgba(255,255,255,.08);

            border:1px solid rgba(255,255,255,.12);

            backdrop-filter:blur(18px);

            color:#fff;

            font-size:32px;
            font-weight:1000;
            letter-spacing:2px;

            box-shadow:
                0 10px 35px rgba(0,0,0,.25);
        }

        .status-bar{
            position:relative;
            z-index:10;

            display:flex;
            align-items:center;
            gap:20px;

            padding:24px 32px;

            backdrop-filter:blur(20px);

            border:1px solid var(--glass-border);

            box-shadow:
                0 20px 50px rgba(0,0,0,.35);

            transition:.25s ease;
        }

        .status-bar.idle{
            background:
                linear-gradient(
                    135deg,
                    #15803d,
                    #16a34a
                );
        }

        .status-bar.success{
            background:
                linear-gradient(
                    135deg,
                    #00c853,
                    #00e676
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

        .status-indicator{
            width:22px;
            height:22px;
            border-radius:50%;
            background:#fff;

            box-shadow:
                0 0 25px rgba(255,255,255,.9);

            flex-shrink:0;
        }

        .status-title{
            font-size:42px;
            font-weight:1000;
            line-height:1;
            color:#fff;
            letter-spacing:2px;
        }

        .status-subtitle{
            margin-top:6px;
            font-size:16px;
            font-weight:600;
            color:rgba(255,255,255,.92);
        }

        .mode-switch{
            margin-top:22px;

            display:flex;
            gap:14px;

            position:relative;
            z-index:10;
        }

        .mode-btn{
            border:none;

            padding:14px 26px;

            border-radius:18px;

            background:rgba(255,255,255,.08);

            color:#fff;

            font-size:15px;
            font-weight:900;
            letter-spacing:1px;

            border:1px solid rgba(255,255,255,.12);

            backdrop-filter:blur(12px);

            transition:.2s ease;
        }

        .mode-btn:hover{
            transform:translateY(-2px);
        }

        .mode-btn.active{
            background:#facc15;
            color:#111827;

            box-shadow:
                0 10px 30px rgba(250,204,21,.35);
        }

        .scanner-body{
            height:calc(100vh - 170px);

            display:flex;
            align-items:center;
            justify-content:center;

            position:relative;
            z-index:2;
        }

        .scanner-card{

            width:100%;
            max-width:1450px;

            border-radius:40px;

            padding:50px;

            background:
                linear-gradient(
                    180deg,
                    rgba(255,255,255,.10),
                    rgba(255,255,255,.05)
                );

            border:1px solid rgba(255,255,255,.10);

            backdrop-filter:blur(24px);

            box-shadow:
                0 30px 80px rgba(0,0,0,.45);
        }

        .scanner-grid{
            display:grid;
            grid-template-columns:420px 1fr;
            gap:70px;
            align-items:center;
        }

        .scanner-left{
            display:flex;
            justify-content:center;
        }

        .scanner-ring{
            width:340px;
            height:340px;

            position:relative;

            display:flex;
            align-items:center;
            justify-content:center;
        }

        .scanner-ring::before{
            content:'';

            position:absolute;
            inset:0;

            border-radius:50%;

            border:4px solid rgba(255,255,255,.20);

            animation:rotateRing 10s linear infinite;
        }

        .scanner-ring-2{
            position:absolute;
            inset:28px;

            border-radius:50%;

            border:3px dashed rgba(255,255,255,.18);

            animation:rotateRingReverse 14s linear infinite;
        }

        .photo-wrap{
            position:relative;

            width:260px;
            height:260px;

            border-radius:50%;

            overflow:hidden;

            border:7px solid #facc15;

            background:#111827;

            box-shadow:
                0 15px 40px rgba(0,0,0,.45);
        }

        .photo-wrap::after{

            content:'';

            position:absolute;

            left:0;
            top:0;

            width:100%;
            height:4px;

            background:
                linear-gradient(
                    90deg,
                    transparent,
                    #00ff7f,
                    transparent
                );

            box-shadow:
                0 0 20px rgba(0,255,127,.8);

            animation:scanLine 2s linear infinite;
        }

        @keyframes scanLine{

            0%{
                transform:translateY(0);
            }

            100%{
                transform:translateY(256px);
            }

        }

        .photo-wrap img{
            width:100%;
            height:100%;
            object-fit:cover;
        }

        @keyframes rotateRing{

            from{
                transform:rotate(0deg);
            }

            to{
                transform:rotate(360deg);
            }

        }

        @keyframes rotateRingReverse{

            from{
                transform:rotate(360deg);
            }

            to{
                transform:rotate(0deg);
            }

        }

        .scanner-chip{

            display:inline-flex;
            align-items:center;
            gap:10px;

            padding:10px 18px;

            border-radius:999px;

            background:rgba(255,255,255,.08);

            border:1px solid rgba(255,255,255,.12);

            color:#fff;

            font-size:13px;
            font-weight:900;
            letter-spacing:1px;

            margin-bottom:30px;
        }

        .scanner-dot{
            width:10px;
            height:10px;

            border-radius:50%;

            background:#00ff7f;

            animation:pulseDot 1.2s infinite;
        }

        @keyframes pulseDot{

            0%{
                opacity:1;
            }

            50%{
                opacity:.3;
            }

            100%{
                opacity:1;
            }

        }

        .greeting-text{
            font-size:22px;
            font-weight:1000;
            letter-spacing:4px;
            margin-bottom:12px;
            text-transform:uppercase;

            color:#facc15;

            text-shadow:
                0 0 20px rgba(250,204,21,.45);

            animation:greetPulse 1.5s infinite;
        }

        .name-text{
            font-size:64px;
            font-weight:1000;
            line-height:1;

            color:#fff;

            margin-bottom:12px;

            text-transform:uppercase;

            text-shadow:
                0 8px 30px rgba(0,0,0,.35);
        }

        .role-text{
            font-size:26px;
            font-weight:700;

            color:rgba(255,255,255,.9);

            margin-bottom:40px;
        }

        .info-grid{
            display:grid;
            grid-template-columns:repeat(2,minmax(300px,250px));
            gap:22px;
        }

        .info-box{

            padding:30px;

            border-radius:28px;

            background:
                linear-gradient(
                    180deg,
                    rgba(255,255,255,.10),
                    rgba(255,255,255,.06)
                );

            border:1px solid rgba(255,255,255,.10);

            backdrop-filter:blur(14px);
        }

        .info-box.yellow{

            background:
                linear-gradient(
                    135deg,
                    #facc15,
                    #fde047
                );

            color:#111827;
        }

        .info-label{
            font-size:15px;
            font-weight:900;
            letter-spacing:1px;

            margin-bottom:14px;
        }

        .big-value{
            font-size:46px;
            font-weight:1000;
            line-height:1;

            color:#fff;
        }

        .big-value.dark{
            color:#111827;
        }

        .scanner-footer{

            margin-top:36px;

            display:flex;
            align-items:center;
            gap:12px;

            color:#fff;

            font-size:16px;
            font-weight:700;
        }

        .pulse-dot{
            width:12px;
            height:12px;

            border-radius:50%;

            background:#00ff7f;

            animation:pulseDot 1.2s infinite;
        }

        .success-flash{
            animation:successFlash .45s ease;
        }

        .error-flash{
            animation:errorFlash .45s ease;
        }

        @keyframes successFlash{

            0%{
                background-color:rgba(34,197,94,0);
            }

            50%{
                background-color:rgba(34,197,94,.20);
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

        #scan-input{
            position:absolute;
            opacity:0;
            pointer-events:none;
        }

        @media(max-width:1200px){

            .scanner-grid{
                grid-template-columns:1fr;
                text-align:center;
            }

            .scanner-left{
                justify-content:center;
            }

            .info-grid{
                justify-content:center;
            }

            .scanner-chip{
                margin-inline:auto;
            }

            .scanner-footer{
                justify-content:center;
            }

        }

    </style>

    <script>

        document.addEventListener(
            'DOMContentLoaded',
            () => {

                const input =
                    document.getElementById(
                        'scan-input'
                    );

                const statusBar =
                    document.getElementById(
                        'status-bar'
                    );

                const personName =
                    document.getElementById(
                        'person-name'
                    );

                const personRole =
                    document.getElementById(
                        'person-role'
                    );

                const personPhoto =
                    document.getElementById(
                        'person-photo'
                    );

                const scanTime =
                    document.getElementById(
                        'scan-time'
                    );

                const scanCount =
                    document.getElementById(
                        'scan-count'
                    );

                const greetingText =
                    document.getElementById(
                        'greeting-text'
                    );

                const liveClock =
                    document.getElementById(
                        'live-clock'
                    );

                let processing = false;

                let scanMode = 'TIME_IN';

                input.focus();

                renderLogs();

                setMode(scanMode);

                startClock();

                document.addEventListener(
                    'click',
                    () => {
                        input.focus();
                    }
                );

                document.querySelectorAll(
                    '.mode-btn'
                ).forEach(btn => {

                    btn.addEventListener(
                        'click',
                        function(){

                            setMode(
                                this.dataset.mode
                            );

                        }
                    );

                });

                function startClock(){

                    function update(){

                        const now =
                            new Date();

                        liveClock.innerText =
                            now.toLocaleTimeString(
                                [],
                                {
                                    hour:'2-digit',
                                    minute:'2-digit',
                                    second:'2-digit'
                                }
                            );

                    }

                    update();

                    setInterval(
                        update,
                        1000
                    );

                }

                function setMode(mode){

                    scanMode = mode;

                    document
                        .querySelectorAll(
                            '.mode-btn'
                        )
                        .forEach(el => {

                            el.classList.remove(
                                'active'
                            );

                        });

                    document
                        .querySelector(
                            `.mode-btn[data-mode="${mode}"]`
                        )
                        ?.classList.add(
                        'active'
                    );

                    if(mode === 'TIME_IN'){

                        statusBar.className =
                            'status-bar idle';

                        statusBar.querySelector(
                            '.status-title'
                        ).innerText =
                            'TIME IN MODE';

                        statusBar.querySelector(
                            '.status-subtitle'
                        ).innerText =
                            'Waiting for entry scan';

                        greetingText.innerText =
                            'READY TO WELCOME';

                        greetingText.style.color =
                            '#facc15';

                    }else{

                        statusBar.className =
                            'status-bar out';

                        statusBar.querySelector(
                            '.status-title'
                        ).innerText =
                            'TIME OUT MODE';

                        statusBar.querySelector(
                            '.status-subtitle'
                        ).innerText =
                            'Waiting for exit scan';

                        greetingText.innerText =
                            'READY TO EXIT';

                        greetingText.style.color =
                            '#bfdbfe';

                    }

                    input.focus();

                }

                input.addEventListener(
                    'keydown',
                    function(e){

                        if(e.key !== 'Enter'){
                            return;
                        }

                        e.preventDefault();

                        if(processing){
                            return;
                        }

                        const code =
                            this.value.trim();

                        this.value = '';

                        if(!code){
                            return;
                        }

                        processing = true;

                        processScan(code);

                    }
                );

                function processScan(code){
                    fetch('/scan',{
                        method:'POST',
                        headers:{
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN':
                            document.querySelector(
                                'meta[name="csrf-token"]'
                            ).content
                        },
                        body:JSON.stringify({
                            code,
                            mode:scanMode
                        })
                    })
                        .then(async response => {
                            const data = await response.json();
                            if(!response.ok){
                                throw data;
                            }
                            updateScannerUI(data);
                            addLog(data);
                            processing = false;
                        })
                        .catch(error => {
                            showErrorState();

                            processing = false;

                            console.error(error);

                        });

                }

                function updateScannerUI(data){

                    const fullName =
                        data.name || 'UNKNOWN';

                    personName.innerText =
                        fullName;

                    personRole.innerText =
                        data.role || '';

                    personPhoto.src =
                        data.photo
                        || '/images/avatar.png';

                    scanTime.innerText =
                        data.time || '--:--';

                    statusBar.classList.remove(
                        'idle',
                        'success',
                        'error',
                        'out'
                    );

                    if(data.status === 'success'){
                        playSuccessSound();
                        flashSuccess();

                        if(data.mode === 'TIME_IN'){
                            greetingText.innerText =
                                '👋 WELCOME';

                            greetingText.style.color =
                                '#fde047';

                            statusBar.classList.add(
                                'success'
                            );

                            statusBar.querySelector(
                                '.status-title'
                            ).innerText =
                                'ENTRY RECORDED';

                            statusBar.querySelector(
                                '.status-subtitle'
                            ).innerText =
                                data.message
                                ||
                                'Have a great day!';

                            speakMessage(
                                `Welcome ${fullName}`
                            );

                        }else{
                            greetingText.innerText =
                                '🚪 GOODBYE';

                            greetingText.style.color =
                                '#bfdbfe';

                            statusBar.classList.add(
                                'out'
                            );

                            statusBar.querySelector(
                                '.status-title'
                            ).innerText =
                                'EXIT RECORDED';

                            statusBar.querySelector(
                                '.status-subtitle'
                            ).innerText =
                                data.message
                                ||
                                'See you again!';

                            speakMessage(
                                `Goodbye ${fullName}`
                            );

                        }

                    }else{

                        showErrorState();

                    }

                    setTimeout(() => {

                        resetScanner();

                    },1500);

                }

                function showErrorState(){

                    playErrorSound();

                    flashError();

                    speakMessage(
                        'Access denied'
                    );

                    greetingText.innerText =
                        '⚠ ACCESS';

                    greetingText.style.color =
                        '#fecaca';

                    personName.innerText =
                        'ACCESS DENIED';

                    personRole.innerText =
                        'INVALID QR OR RFID';

                    personPhoto.src =
                        '/images/avatar.png';

                    statusBar.classList.remove(
                        'idle',
                        'success',
                        'error',
                        'out'
                    );

                    statusBar.classList.add(
                        'error'
                    );

                    statusBar.querySelector(
                        '.status-title'
                    ).innerText =
                        'ACCESS DENIED';

                    statusBar.querySelector(
                        '.status-subtitle'
                    ).innerText =
                        'Invalid scan detected';

                    setTimeout(() => {

                        resetScanner();

                    },1500);

                }

                function resetScanner(){

                    setMode(scanMode);

                    personName.innerText =
                        'WAITING...';

                    personRole.innerText =
                        'TAP CARD / SCAN QR';

                    personPhoto.src =
                        '/images/avatar.png';

                    input.focus();

                }

                function speakMessage(message){

                    try{

                        if(!('speechSynthesis' in window)){
                            return;
                        }

                        window.speechSynthesis.cancel();

                        const speech =
                            new SpeechSynthesisUtterance();

                        speech.text = message;

                        const voices =
                            window.speechSynthesis.getVoices();

                        const preferredVoice =
                            voices.find(v =>
                                v.lang === 'fil-PH'
                            )
                            ||
                            voices.find(v =>
                                v.name.includes('Microsoft')
                            )
                            ||
                            voices.find(v =>
                                v.lang === 'en-US'
                            );

                        if(preferredVoice){
                            speech.voice = preferredVoice;
                        }

                        speech.lang = 'fil-PH';

                        speech.rate = 0.90;
                        speech.pitch = 0.95;
                        speech.volume = 1;

                        window.speechSynthesis.speak(
                            speech
                        );

                    }catch(error){

                        console.error(
                            'Speech error:',
                            error
                        );

                    }

                }

                function playSuccessSound(){

                    const audio =
                        document.getElementById(
                            'success-sound'
                        );

                    if(!audio){
                        return;
                    }

                    audio.currentTime = 0;

                    audio.play().catch(() => {});

                }

                function playErrorSound(){

                    const audio =
                        document.getElementById(
                            'error-sound'
                        );

                    if(!audio){
                        return;
                    }

                    audio.currentTime = 0;

                    audio.play().catch(() => {});

                }

                function flashSuccess(){

                    document.body.classList.add(
                        'success-flash'
                    );

                    setTimeout(() => {

                        document.body.classList.remove(
                            'success-flash'
                        );

                    },450);

                }

                function flashError(){

                    document.body.classList.add(
                        'error-flash'
                    );

                    setTimeout(() => {

                        document.body.classList.remove(
                            'error-flash'
                        );

                    },450);

                }

                function getTodayKey(){

                    const d =
                        new Date();

                    return (
                        'scan_logs_' +
                        d.toISOString().slice(0,10)
                    );

                }

                function addLog(data){

                    const key =
                        getTodayKey();

                    const logs =
                        JSON.parse(
                            localStorage.getItem(key)
                            || '[]'
                        );

                    logs.unshift(data);

                    if(logs.length > 20){
                        logs.pop();
                    }

                    localStorage.setItem(
                        key,
                        JSON.stringify(logs)
                    );

                    scanCount.innerText =
                        logs.length;

                }

                function renderLogs(){

                    const logs =
                        JSON.parse(
                            localStorage.getItem(
                                getTodayKey()
                            ) || '[]'
                        );

                    scanCount.innerText =
                        logs.length;

                }

            });

    </script>

@endsection
