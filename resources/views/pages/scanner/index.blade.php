@extends('layouts.scanner')
@section('title','DepEd Gate Scanner')
@section('content')

    <div class="scanner-wrapper">
        <div class="scanner-header">
            <div class="header-left">
                <img src="{{ asset('images/deped.png') }}" class="deped-logo">
                <img src="{{ asset('images/snsu.png') }}" class="deped-logo">
                <img src="{{ asset('images/logo.png') }}" class="deped-logo">
                <div class="header-text">
                    <div class="agency">{{ config('app.name') }}</div>
                    <div class="school">School Gate Monitoring System</div>
                </div>
            </div>

            <div class="header-right">
                <div class="status-indicator">READY</div>
                <a href="{{ route('dashboard') }}" class="exit-btn">Exit</a>
            </div>
        </div>

        <div class="scanner-main">

            <!-- CENTER DISPLAY -->
            <div class="scan-display-card">

                <div class="photo-section">
                    <img id="person-photo" src="{{ asset('images/avatar.png') }}" class="person-photo">
                    <div id="scan-status" class="status-badge idle">IDLE</div>
                </div>

                <div class="info-section">
                    <div id="person-name" class="person-name">Waiting for Scan</div>
                    <div id="person-role" class="person-role">Student / Staff</div>

                    <div class="meta-row">
                        <div class="meta-label">TIME</div>
                        <div class="meta-value" id="scan-time">--:--</div>
                    </div>
                </div>

            </div>

        </div>

        <!-- BOTTOM PANEL -->
        <div class="scanner-bottom">

            <input
                type="text"
                id="scan-input"
                class="scan-input"
                placeholder="Scan QR / NFC or Enter ID"
                autofocus>

            <div class="logs-container" id="scan-logs"></div>

        </div>

    </div>

    <style>
        html,body{
            height:100%;
            margin:0;
            overflow:hidden;
            background:#f4f6f9;
            font-family:system-ui;
        }

        .scanner-wrapper{
            height:100vh;
            display:flex;
            flex-direction:column;
        }

        .scanner-header{
            display:flex;
            justify-content:space-between;
            align-items:center;
            padding:12px 20px;
            background:#003A8F;
            color:white;
        }

        .header-left{
            display:flex;
            align-items:center;
            gap:10px;
        }

        .deped-logo{
            width:60px;
            height:60px;
            background:white;
            border-radius:50%;
            padding:3px;
        }

        .agency{
            font-size:20px;
            font-weight:700;
        }

        .school{
            font-size:13px;
            opacity:.9;
        }

        .header-right{
            display:flex;
            gap:10px;
        }

        .status-indicator{
            background:#28a745;
            padding:6px 16px;
            border-radius:20px;
        }

        .exit-btn{
            background:#C8102E;
            color:white;
            padding:6px 16px;
            border-radius:8px;
            text-decoration:none;
        }

        .scanner-main{
            flex:1;
            display:flex;
            align-items:center;
            justify-content:center;
        }

        .scan-display-card{
            width:100%;
            max-width:1000px;
            background:white;
            border-radius:20px;
            padding:40px;
            display:flex;
            gap:50px;
            box-shadow:0 20px 60px rgba(0,0,0,.15);
        }

        .photo-section{
            position:relative;
        }

        .person-photo{
            width:300px;
            height:300px;
            border-radius:20px;
            object-fit:cover;
        }

        .status-badge{
            position:absolute;
            bottom:-15px;
            left:50%;
            transform:translateX(-50%);
            padding:10px 28px;
            border-radius:25px;
            font-weight:700;
            font-size:18px;
            color:white;
        }

        .status-badge.idle{background:#6c757d;}
        .status-badge.in{background:#28a745;}
        .status-badge.out{background:#007bff;}
        .status-badge.denied{background:#C8102E;}

        .person-name{
            font-size:56px;
            font-weight:900;
        }

        .person-role{
            font-size:22px;
            margin-top:10px;
            color:#6c757d;
        }

        .meta-value{
            font-size:36px;
            font-weight:800;
        }

        .scanner-bottom{
            padding:15px;
            background:white;
            border-top:1px solid #eee;
        }

        .scan-input{
            width:100%;
            padding:14px;
            font-size:18px;
            border-radius:10px;
            border:2px solid #ddd;
            margin-bottom:10px;
            text-align:center;
        }

        .logs-container{
            display:flex;
            gap:10px;
            overflow-x:auto;
        }

        .log-item{
            min-width:180px;
            background:#f8f9fb;
            padding:10px;
            border-radius:10px;
        }

        .log-status{
            margin-top:5px;
            font-size:12px;
            padding:3px 8px;
            border-radius:20px;
            color:white;
        }

        .log-status.in{background:#28a745;}
        .log-status.out{background:#007bff;}
        .log-status.denied{background:#C8102E;}
    </style>

    <script>
        const input = document.getElementById('scan-input')

        input.focus()
        renderLogs()

        document.addEventListener('click', () => input.focus())

        input.addEventListener('keydown', function(e){
            if(e.key === 'Enter'){
                e.preventDefault()
                const code = this.value.trim()
                if(!code) return
                processScan(code)
                this.value = ''
            }
        })

        function processScan(code){
            $.ajax({
                url: '/scan',
                type: 'POST',
                data: JSON.stringify({ code }),
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data){

                    $('#person-name').text(data.name || 'Unknown')
                    $('#person-role').text(data.role || '')
                    $('#person-photo').attr('src', data.photo || '/images/avatar.png')

                    $('#scan-status')
                        .attr('class', 'status-badge ' + (data.status || 'denied'))
                        .text((data.status || 'denied').toUpperCase())

                    $('#scan-time').text(data.time || '--:--')

                    addLog(data)
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
            renderLogs()
        }

        function renderLogs(){
            const container = document.getElementById('scan-logs')
            const logs = JSON.parse(localStorage.getItem(getTodayKey()) || '[]')

            container.innerHTML = ''

            logs.forEach(log=>{
                const el = document.createElement('div')
                el.className='log-item'
                el.innerHTML = `
            <div>${log.name}</div>
            <div style="font-size:12px;color:#666">${log.time}</div>
            <div class="log-status ${log.status}">
                ${log.status.toUpperCase()}
            </div>
        `
                container.appendChild(el)
            })
        }
    </script>

@endsection
