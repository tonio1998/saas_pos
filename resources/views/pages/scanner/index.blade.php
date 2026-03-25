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

            <div class="scan-display">
                <img id="person-photo" src="{{ asset('images/avatar.png') }}" class="person-photo">

                <div class="person-info">
                    <div id="person-name" class="person-name">Waiting for Scan</div>
                    <div id="person-role" class="person-role">Student / Staff</div>
                    <div id="scan-status" class="scan-status idle">Idle</div>
                    <div class="scan-time" id="scan-time">--:--</div>
                </div>
            </div>

            <div class="scanner-side">

                <div class="scan-indicator">

                    <input
                        type="text"
                        id="scan-input"
                        class="scan-input"
                        placeholder="Scan QR / NFC or Enter Student ID"
                        autofocus>

                    <div class="scan-message">
                        Scan QR / NFC or Enter Student ID
                    </div>

                </div>

                <div class="scanner-logs">
                    <div class="logs-title">Recent Logs</div>
                    <div class="logs-container">
                        <table class="logs-table">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Time</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody id="scan-logs">
                            <tr>
                                <td colspan="3" class="logs-empty">No scans yet</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
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

        .header-text{
            line-height:1.2;
        }

        .agency{
            font-size:20px;
            font-weight:700;
            text-transform:uppercase;
        }

        .school{
            font-size:13px;
            opacity:.9;
        }

        .header-right{
            display:flex;
            align-items:center;
            gap:10px;
        }

        .status-indicator{
            background:#28a745;
            padding:6px 16px;
            border-radius:20px;
            font-size:13px;
            font-weight:600;
        }

        .exit-btn{
            background:#C8102E;
            color:white;
            padding:6px 16px;
            border-radius:8px;
            text-decoration:none;
            font-weight:600;
        }

        .scanner-main{
            flex:1;
            display:grid;
            grid-template-columns:2fr 1fr;
            gap:20px;
            padding:20px;
        }

        .scan-display{
            background:white;
            border-radius:14px;
            display:flex;
            align-items:center;
            justify-content:center;
            gap:40px;
            box-shadow:0 4px 20px rgba(0,0,0,.08);
        }

        .person-photo{
            width:220px;
            height:220px;
            border-radius:50%;
            object-fit:cover;
            border:6px solid #e9ecef;
        }

        .person-info{
            text-align:left;
        }

        .person-name{
            font-size:36px;
            font-weight:800;
        }

        .person-role{
            font-size:18px;
            color:#6c757d;
            margin-top:5px;
        }

        .scan-status{
            margin-top:12px;
            display:inline-block;
            padding:8px 24px;
            border-radius:25px;
            font-weight:700;
            color:white;
        }

        .scan-status.idle{background:#6c757d;}
        .scan-status.in{background:#28a745;}
        .scan-status.out{background:#007bff;}
        .scan-status.denied{background:#C8102E;}

        .scan-time{
            margin-top:10px;
            font-size:22px;
            font-weight:600;
            color:#6c757d;
        }

        .scanner-side{
            display:flex;
            flex-direction:column;
            gap:20px;
        }

        .scan-indicator{
            background:white;
            border-radius:14px;
            padding:30px;
            text-align:center;
            box-shadow:0 4px 20px rgba(0,0,0,.08);
        }

        .scan-input{
            width:100%;
            padding:14px 16px;
            font-size:18px;
            border-radius:10px;
            border:2px solid #e0e0e0;
            outline:none;
            text-align:center;
            transition:.2s;
        }

        .scan-input:focus{
            border-color:#003A8F;
            box-shadow:0 0 0 3px rgba(0,58,143,0.15);
        }

        .scan-message{
            margin-top:12px;
            font-size:18px;
            font-weight:600;
            color:#6c757d;
        }

        .scanner-logs{
            background:white;
            border-radius:14px;
            padding:15px;
            box-shadow:0 4px 20px rgba(0,0,0,.08);
            flex:1;
            display:flex;
            flex-direction:column;
        }

        .logs-title{
            font-weight:700;
            margin-bottom:10px;
        }

        .logs-container{
            overflow:auto;
            flex:1;
        }

        .logs-table{
            width:100%;
            border-collapse:collapse;
            font-size:14px;
        }

        .logs-table th{
            text-align:left;
            padding:8px;
            border-bottom:1px solid #eee;
        }

        .logs-table td{
            padding:8px;
            border-bottom:1px solid #f2f2f2;
        }

        .logs-empty{
            text-align:center;
            color:#999;
        }

    </style>

    <script>

        const input = document.getElementById('scan-input')

        document.addEventListener('click', () => input.focus())

        input.focus()
        renderLogs()

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
                timeout: 5000,

                success: function(data){

                    $('#person-name').text(data.name || 'Unknown')
                    $('#person-role').text(data.role || '')
                    $('#person-photo').attr('src', data.photo)

                    $('#scan-status')
                        .attr('class', 'scan-status ' + (data.status || 'denied'))

                    $('#scan-time').text(data.time || '--:--')

                    addLog(data)
                },

                error: function(xhr){

                    $('#person-name').text('System Error')
                    $('#person-role').text('')
                    $('#person-photo').attr('src', '/images/avatar.png')

                    $('#scan-status')
                        .attr('class', 'scan-status denied')

                    $('#scan-time').text('--:--')
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

            logs.unshift({
                name: data.name,
                time: data.time,
                status: data.status
            })

            if(logs.length > 50){
                logs.pop()
            }

            localStorage.setItem(key, JSON.stringify(logs))

            renderLogs()
        }

        function renderLogs(){

            const table = document.getElementById('scan-logs')
            const key = getTodayKey()
            const logs = JSON.parse(localStorage.getItem(key) || '[]')

            table.innerHTML = ''

            if(!logs.length){
                table.innerHTML = `
<tr>
    <td colspan="3" class="logs-empty">No scans yet</td>
</tr>`
                return
            }

            logs.forEach(log => {

                const row = document.createElement('tr')

                row.innerHTML = `
<td>${log.name}</td>
<td>${log.time}</td>
<td>${log.status.toUpperCase()}</td>
`

                table.appendChild(row)
            })
        }

    </script>

@endsection
