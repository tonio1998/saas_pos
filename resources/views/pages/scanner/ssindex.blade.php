@extends('layouts.scanner')
@section('title','Gate Scanner')
@section('content')
    <div class="container-fluid kiosk-wrap d-flex flex-column p-0">
        <!-- STATUS BAR (VERY IMPORTANT) -->
        <div id="status-bar" class="status-bar idle">
            READY TO SCAN
        </div>

        <!-- MAIN -->
        <div class="flex-grow-1 d-flex align-items-center justify-content-center">

            <div class="text-center w-100 px-3">

                <!-- PHOTO -->
                <div class="photo-wrap mx-auto mb-3">
                    <img id="person-photo" src="{{ asset('images/avatar.png') }}">
                </div>

                <!-- NAME -->
                <div id="person-name" class="name-text">
                    WAITING...
                </div>

                <!-- ROLE -->
                <div id="person-role" class="role-text mb-4">
                    TAP CARD / SCAN QR
                </div>

                <!-- INFO -->
                <div class="row g-3 justify-content-center">

                    <div class="col-5 col-md-3">
                        <div class="info-box">
                            <div>TIME</div>
                            <div id="scan-time" class="big-value">--:--</div>
                        </div>
                    </div>

                    <div class="col-5 col-md-3">
                        <div class="info-box yellow">
                            <div>SCANS</div>
                            <div id="scan-count" class="big-value">0</div>
                        </div>
                    </div>

                </div>

            </div>

        </div>

        <!-- INPUT (HIDDEN STYLE BUT ACTIVE) -->
        <input type="text" id="scan-input" autofocus>
    </div>

    <style>

        /* BASE */
        body{
            background:#FFF;
            color:#fff;
            overflow:hidden;
        }

        /* FULLSCREEN */
        .kiosk-wrap{
            height:100vh;
        }

        /* STATUS BAR */
        .status-bar{
            font-size:48px;
            font-weight:900;
            text-align:center;
            padding:20px;
            letter-spacing:2px;
            transition:.2s;
        }

        .status-bar.idle{
            background:#004D1A;
        }

        .status-bar.success{
            background:#00c853;
        }

        .status-bar.out{
            background:#0091ea;
        }

        .status-bar.error{
            background:#d50000;
        }

        /* PHOTO */
        .photo-wrap{
            width:180px;
            height:180px;
            border-radius:50%;
            overflow:hidden;
            border:6px solid #FFD04C;
        }

        .photo-wrap img{
            width:100%;
            height:100%;
            object-fit:cover;
        }

        /* TEXT */
        .name-text{
            font-size:48px;
            font-weight:800;
        }

        .role-text{
            font-size:22px;
            color:#bbb;
        }

        /* INFO */
        .info-box{
            background:#111;
            padding:20px;
            border-radius:16px;
            text-align:center;
        }

        .info-box.yellow{
            background:#FFD04C;
            color:#000;
        }

        .big-value{
            font-size:32px;
            font-weight:900;
        }

        /* INPUT hidden but usable */
        #scan-input{
            position:absolute;
            opacity:0;
            pointer-events:none;
        }

    </style>

    <script>

        const input = document.getElementById('scan-input')
        input.focus()
        renderLogs()

        setInterval(()=>input.focus(),500)

        /* ENTER SCAN */
        input.addEventListener('keydown', function(e){
            if(e.key === 'Enter'){
                e.preventDefault()
                const code = this.value.trim()
                if(!code) return
                processScan(code)
                this.value=''
            }
        })

        /* PROCESS */
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
                        bar.innerText = 'ACCESS GRANTED'
                    }else if(data.status === 'out'){
                        bar.classList.add('out')
                        bar.innerText = 'EXIT RECORDED'
                    }else{
                        bar.classList.add('error')
                        bar.innerText = 'ACCESS DENIED'
                    }

                    addLog(data)

                    setTimeout(()=>{
                        bar.className = 'status-bar idle'
                        bar.innerText = 'READY TO SCAN'
                    },2000)
                }
            })
        }

        /* LOGS */
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
