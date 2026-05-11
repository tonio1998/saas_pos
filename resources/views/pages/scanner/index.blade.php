@extends('layouts.scanner')

@section('title','Gate Scanner')

@section('content')

    <div class="container-fluid kiosk-wrap p-0">

        <div class="scanner-bg"></div>
        <div class="scanner-bg scanner-bg-2"></div>

        <div
            id="status-bar"
            class="status-bar idle"
        >
            <div class="status-indicator"></div>

            <div class="status-content">
                <div class="status-title">
                    READY TO SCAN
                </div>

                <div class="status-subtitle">
                    Waiting for NFC or QR Code
                </div>
            </div>
        </div>

        <div class="scanner-body">

            <div class="scanner-card">

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
                                    SCANS
                                </div>

                                <div
                                    id="scan-count"
                                    class="big-value"
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

        <input
            type="text"
            id="scan-input"
            autofocus
        >

    </div>

    <style>

        body{
            background:
                radial-gradient(circle at top left,#174c29 0%,#0b1f13 45%,#030805 100%);
            overflow:hidden;
            font-family:
                Inter,
                system-ui,
                sans-serif;
        }

        .kiosk-wrap{
            height:100vh;
            position:relative;
            overflow:hidden;
            padding:24px;
        }

        .scanner-bg{
            position:absolute;
            width:700px;
            height:700px;
            border-radius:50%;
            background:rgba(255,255,255,.05);
            top:-280px;
            right:-180px;
            animation:floatBg 10s linear infinite;
            filter:blur(10px);
        }

        .scanner-bg-2{
            width:500px;
            height:500px;
            top:auto;
            bottom:-180px;
            left:-140px;
            animation-duration:14s;
        }

        @keyframes floatBg{
            0%{
                transform:rotate(0deg) translateY(0px);
            }
            50%{
                transform:rotate(180deg) translateY(20px);
            }
            100%{
                transform:rotate(360deg) translateY(0px);
            }
        }

        .status-bar{
            position:relative;
            z-index:10;
            display:flex;
            align-items:center;
            gap:20px;
            padding:28px 36px;
            border-radius:32px;
            transition:.25s ease;
            box-shadow:
                0 20px 60px rgba(0,0,0,.35);
            border:3px solid rgba(255,255,255,.15);
            backdrop-filter:blur(18px);
        }

        .status-bar.idle{
            background:
                linear-gradient(
                    135deg,
                    #0B7A2A,
                    #00A63E
                );
        }

        .status-bar.success{
            background:
                linear-gradient(
                    135deg,
                    #00C853,
                    #00E676
                );
        }

        .status-bar.out{
            background:
                linear-gradient(
                    135deg,
                    #0077FF,
                    #00A2FF
                );
        }

        .status-bar.error{
            background:
                linear-gradient(
                    135deg,
                    #C62828,
                    #FF1744
                );
        }

        .status-indicator{
            width:24px;
            height:24px;
            border-radius:50%;
            background:#fff;
            position:relative;
            flex-shrink:0;
            box-shadow:
                0 0 25px rgba(255,255,255,.9);
        }

        .status-indicator::after{
            content:'';
            position:absolute;
            inset:-12px;
            border-radius:50%;
            border:3px solid rgba(255,255,255,.55);
            animation:pulseStatus 1.8s infinite;
        }

        @keyframes pulseStatus{
            0%{
                transform:scale(.7);
                opacity:1;
            }
            100%{
                transform:scale(1.8);
                opacity:0;
            }
        }

        .status-title{
            color:#fff;
            font-size:54px;
            font-weight:1000;
            letter-spacing:3px;
            line-height:1;
            text-shadow:
                0 4px 18px rgba(0,0,0,.35);
        }

        .status-subtitle{
            color:rgba(255,255,255,.95);
            margin-top:8px;
            font-size:18px;
            font-weight:600;
            letter-spacing:1px;
        }

        .scanner-body{
            height:calc(100vh - 145px);
            display:flex;
            align-items:center;
            justify-content:center;
            position:relative;
            z-index:2;
        }

        .scanner-card{
            width:100%;
            max-width:1500px;
            border-radius:42px;
            background:
                linear-gradient(
                    180deg,
                    rgba(255,255,255,.16),
                    rgba(255,255,255,.08)
                );
            border:3px solid rgba(255,255,255,.18);
            backdrop-filter:blur(24px);
            box-shadow:
                0 35px 90px rgba(0,0,0,.45);
            padding:54px;
        }

        .scanner-grid{
            display:grid;
            grid-template-columns:460px 1fr;
            align-items:center;
            gap:70px;
        }

        .scanner-left{
            display:flex;
            justify-content:center;
        }

        .scanner-ring{
            width:380px;
            height:380px;
            border-radius:50%;
            position:relative;
            display:flex;
            align-items:center;
            justify-content:center;
            background:
                radial-gradient(
                    circle,
                    rgba(255,255,255,.14),
                    transparent 70%
                );
        }

        .scanner-ring::before{
            content:'';
            position:absolute;
            inset:0;
            border-radius:50%;
            border:4px solid rgba(255,255,255,.35);
            animation:rotateRing 8s linear infinite;
        }

        .scanner-ring-2{
            position:absolute;
            inset:30px;
            border-radius:50%;
            border:3px dashed rgba(255,255,255,.25);
            animation:rotateRingReverse 12s linear infinite;
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

        .photo-wrap{
            width:290px;
            height:290px;
            border-radius:50%;
            overflow:hidden;
            border:8px solid #FFD600;
            box-shadow:
                0 25px 80px rgba(255,214,0,.45);
            background:#111;
        }

        .photo-wrap img{
            width:100%;
            height:100%;
            object-fit:cover;
        }

        .scanner-chip{
            display:inline-flex;
            align-items:center;
            gap:12px;
            padding:12px 22px;
            border-radius:999px;
            background:rgba(255,255,255,.14);
            color:#fff;
            font-size:15px;
            font-weight:800;
            letter-spacing:1px;
            margin-bottom:34px;
            border:2px solid rgba(255,255,255,.18);
        }

        .scanner-dot{
            width:12px;
            height:12px;
            border-radius:50%;
            background:#00FF7F;
            animation:pulseDot 1.4s infinite;
            box-shadow:
                0 0 18px #00FF7F;
        }

        @keyframes pulseDot{
            0%{
                opacity:1;
            }
            50%{
                opacity:.35;
            }
            100%{
                opacity:1;
            }
        }

        .name-text{
            font-size:60px;
            font-weight:1000;
            color:#fff;
            line-height:1.02;
            margin-bottom:14px;
            /*word-break:break-word;*/
            overflow: hidden;
            text-shadow:
                0 5px 20px rgba(0,0,0,.4);
        }

        .role-text{
            font-size:30px;
            color:rgba(255,255,255,.98);
            margin-bottom:46px;
            font-weight:700;
            letter-spacing:1px;
            text-shadow:
                0 3px 10px rgba(0,0,0,.3);
        }

        .info-grid{
            display:grid;
            grid-template-columns:repeat(2,minmax(220px,260px));
            gap:24px;
        }

        .info-box{
            background:
                linear-gradient(
                    180deg,
                    rgba(255,255,255,.18),
                    rgba(255,255,255,.1)
                );
            border:3px solid rgba(255,255,255,.14);
            border-radius:32px;
            padding:34px;
            backdrop-filter:blur(16px);
            box-shadow:
                0 15px 40px rgba(0,0,0,.18);
        }

        .info-box.yellow{
            background:
                linear-gradient(
                    135deg,
                    #FFD600,
                    #FFF176
                );
            color:#000;
        }

        .info-label{
            font-size:18px;
            font-weight:900;
            letter-spacing:2px;
            opacity:.92;
            margin-bottom:18px;
        }

        .big-value{
            font-size:52px;
            font-weight:1000;
            line-height:1;
            color: #fff2f2;
        }

        .scanner-footer{
            margin-top:42px;
            display:flex;
            align-items:center;
            gap:14px;
            color:#fff;
            font-size:18px;
            font-weight:700;
            letter-spacing:1px;
        }

        .pulse-dot{
            width:14px;
            height:14px;
            border-radius:50%;
            background:#00FF7F;
            box-shadow:
                0 0 22px #00FF7F;
            animation:pulseDot 1.2s infinite;
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
                gap:42px;
            }

            .scanner-left{
                order:1;
            }

            .scanner-right{
                order:2;
            }

            .scanner-card{
                padding:38px;
            }

            .status-title{
                font-size:38px;
            }

            .name-text{
                font-size:54px;
            }

            .role-text{
                font-size:24px;
            }

            .info-grid{
                justify-content:center;
                grid-template-columns:repeat(2,minmax(180px,220px));
            }

        }

    </style>

    <script>

        const input = document.getElementById('scan-input')
        input.focus()
        renderLogs()

        setInterval(()=>input.focus(),500)

        input.addEventListener('keydown', function(e){
            if(e.key === 'Enter'){
                e.preventDefault()
                const code = this.value.trim()
                if(!code) return
                processScan(code)
                this.value=''
            }
        })

        function processScan(code){
            $.ajax({
                url:'/scan',
                type:'POST',
                data:JSON.stringify({code}),
                contentType:'application/json',
                headers:{
                    'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
                },
                success:function(data){

                    $('#person-name').text(data.name || 'UNKNOWN')
                    $('#person-role').text(data.role || '')
                    $('#person-photo').attr('src', data.photo || '/images/avatar.png')
                    $('#scan-time').text(data.time || '--:--')

                    const bar = document.getElementById('status-bar')
                    bar.classList.remove('idle','success','error','out')

                    if(data.status === 'in'){
                        bar.classList.add('success')
                        bar.querySelector('.status-title').innerText = 'ACCESS GRANTED'
                        bar.querySelector('.status-subtitle').innerText = 'Entry successfully recorded'
                    }else if(data.status === 'out'){
                        bar.classList.add('out')
                        bar.querySelector('.status-title').innerText = 'EXIT RECORDED'
                        bar.querySelector('.status-subtitle').innerText = 'Exit successfully recorded'
                    }else{
                        bar.classList.add('error')
                        bar.querySelector('.status-title').innerText = 'ACCESS DENIED'
                        bar.querySelector('.status-subtitle').innerText = 'Unauthorized access detected'
                    }

                    addLog(data)

                    setTimeout(()=>{
                        bar.className = 'status-bar idle'
                        bar.querySelector('.status-title').innerText = 'READY TO SCAN'
                        bar.querySelector('.status-subtitle').innerText = 'Waiting for NFC or QR Code'
                    },2000)

                }, error: function(err) {

                    $('#person-name').text('USER NOT FOUND')
                    $('#person-role').text('INVALID QR / NFC')
                    $('#person-photo').attr('src', '/images/avatar.png')
                    $('#scan-time').text('--:--')

                    const bar = document.getElementById('status-bar')
                    bar.classList.remove('idle','success','error','out')

                    bar.classList.add('error')
                    bar.querySelector('.status-title').innerText = 'ACCESS DENIED'
                    bar.querySelector('.status-subtitle').innerText = 'Invalid scan detected'

                    addLog({
                        name:'UNKNOWN',
                        role:'INVALID',
                        status:'error',
                        time:new Date().toLocaleTimeString()
                    })

                    setTimeout(()=>{
                        bar.className = 'status-bar idle'
                        bar.querySelector('.status-title').innerText = 'READY TO SCAN'
                        bar.querySelector('.status-subtitle').innerText = 'Waiting for NFC or QR Code'
                    },2000)

                    console.error(err)
                }
            })
        }

        function getTodayKey(){
            const d = new Date()
            return 'scan_logs_' + d.toISOString().slice(0,10)
        }

        function addLog(data){
            const key = getTodayKey()
            const logs = JSON.parse(localStorage.getItem(key) || '[]')

            logs.unshift(data)
            if(logs.length > 20) logs.pop()

            localStorage.setItem(key, JSON.stringify(logs))
            document.getElementById('scan-count').innerText = logs.length
        }

        function renderLogs(){
            const logs = JSON.parse(localStorage.getItem(getTodayKey()) || '[]')
            document.getElementById('scan-count').innerText = logs.length
        }

    </script>

@endsection
