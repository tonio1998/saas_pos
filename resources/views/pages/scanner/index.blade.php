@extends('layouts.scanner')
@section('title','DepEd Gate Scanner')
@section('content')
    <div class="scanner-wrapper">
        <div class="scanner-header">
            <div class="header-left">
                <img src="{{ asset('images/deped.png') }}" class="deped-logo">
                <img src="{{ asset('images/snsu.png') }}" class="deped-logo">
                <img src="{{ asset('images/tubajon.png') }}" class="deped-logo">
                <div class="header-text">
                    <div class="agency">{{ config('app.name') }}</div>
                    <div class="school">School Gate Monitoring System</div>
                </div>
            </div>

            <div class="header-right">
                <div class="status-indicator">
                    READY
                </div>
                <a href="{{ route('dashboard') }}" class="exit-btn">
                    Exit
                </a>
            </div>
        </div>

        <div class="scanner-main">
            <div class="scan-display">
                <img
                    id="person-photo"
                    src="{{ asset('images/avatar.png') }}"
                    class="person-photo">

                <div class="person-info">
                    <div id="person-name" class="person-name">
                        Waiting for Scan
                    </div>
                    <div id="person-role" class="person-role">
                        Student / Staff
                    </div>
                    <div id="scan-status" class="scan-status idle">
                        Idle
                    </div>
                    <div class="scan-time" id="scan-time">
                        --:--
                    </div>
                </div>
            </div>

            <div class="scanner-side">
                <div class="scan-indicator">
                    <input
                        type="text"
                        id="scan-input"
                        class="scan-input"
                        autofocus>

                    <div class="scan-message">
                        Tap NFC Card or Scan QR Code
                    </div>

                </div>

                <div class="scanner-logs">
                    <div class="logs-title">
                        Recent Logs
                    </div>
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
                                <td colspan="3" class="logs-empty">
                                    No scans yet
                                </td>
                            </tr>
                            </tbody>
                        </table>
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
                opacity:0;
                position:absolute;
            }

            .scan-message{
                font-size:22px;
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

            const input=document.getElementById('scan-input')

            document.addEventListener('click',()=>input.focus())

            input.focus()

            input.addEventListener('keydown',function(e){

                if(e.key==='Enter'){

                    e.preventDefault()

                    const code=this.value.trim()

                    if(!code) return

                    processScan(code)

                    this.value=''

                }

            })

            function processScan(code){

                fetch('/scan',{
                    method:'POST',
                    headers:{
                        'Content-Type':'application/json',
                        'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content
                    },
                    body:JSON.stringify({code})
                })
                    .then(res=>res.json())
                    .then(data=>{

                        document.getElementById('person-name').innerText=data.name
                        document.getElementById('person-role').innerText=data.role
                        document.getElementById('person-photo').src=data.photo

                        const status=document.getElementById('scan-status')

                        status.className='scan-status '+data.status

                        document.getElementById('scan-time').innerText=data.time

                        addLog(data)

                    })

            }

            function addLog(data){

                const table=document.getElementById('scan-logs')

                const row=document.createElement('tr')

                row.innerHTML=`
<td>${data.name}</td>
<td>${data.time}</td>
<td>${data.status.toUpperCase()}</td>
`

                table.prepend(row)

            }

        </script>

@endsection
