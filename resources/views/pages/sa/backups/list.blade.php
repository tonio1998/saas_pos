@extends('layouts.sa')

@section('content')

    <div class="backup-page">

        <div class="backup-header">
            <div>
                <h3>Backup Management</h3>
                <p>Manage and monitor SAFETRACK backup archives.</p>
            </div>

            <button
                class="btn-generate"
                id="generateBackupBtn"
            >
                <span class="btn-spinner d-none"></span>
                <span class="btn-label">
                Generate Backup
            </span>
            </button>
        </div>

        <div
            class="progress-wrapper d-none"
            id="progressWrapper"
        >
            <div class="progress-top">
                <div>
                    <h6 id="progressTitle">
                        Preparing Backup
                    </h6>

                    <small id="progressText">
                        Initializing backup process...
                    </small>
                </div>

                <span id="progressPercent">
                0%
            </span>
            </div>

            <div class="progress custom-progress">
                <div
                    class="progress-bar"
                    id="progressBar"
                ></div>
            </div>
        </div>

        <div
            class="summary-grid"
            id="summaryCards"
        >
            <div class="summary-card">
                <span>Total Backups</span>
                <h4>
                    {{ $summary['total_backups'] }}
                </h4>
            </div>

            <div class="summary-card">
                <span>Storage Used</span>
                <h4>
                    {{ $summary['storage_used'] }}
                </h4>
            </div>

            <div class="summary-card">
                <span>Last Backup</span>
                <h4>
                    {{ $summary['last_backup'] }}
                </h4>
            </div>
        </div>

        <div class="table-wrapper">

            <div class="table-header">
                <h5>Backup Archives</h5>
            </div>

            <div id="backupTableContainer">

                @if(count($backups))

                    <div class="table-responsive">

                        <table class="table backup-table align-middle">
                            <thead>
                            <tr>
                                <th>Archive</th>
                                <th>Size</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th width="140">Actions</th>
                            </tr>
                            </thead>

                            <tbody>

                            @foreach($backups as $backup)

                                <tr>

                                    <td>
                                        <div class="file-cell">
                                            <div class="zip-icon">
                                                <i class="bi bi-file-earmark-zip"></i>
                                            </div>

                                            <div>
                                                <div class="file-name">
                                                    {{ $backup['name'] }}
                                                </div>

                                                <small>
                                                    ZIP Archive
                                                </small>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        {{ $backup['size'] }}
                                    </td>

                                    <td>
                                        {{ $backup['date'] }}
                                    </td>

                                    <td>
                                        <span class="status-badge">
                                            {{ $backup['status'] }}
                                        </span>
                                    </td>

                                    <td>

                                        <div class="action-group">

                                            <a
                                                href="{{ route('sa.backups.download', $backup['name']) }}"
                                                class="btn-action"
                                            >
                                                <i class="bi bi-download"></i>
                                            </a>

                                            <button
                                                class="btn-action btn-delete"
                                                data-file="{{ $backup['name'] }}"
                                            >
                                                <i class="bi bi-trash"></i>
                                            </button>

                                        </div>

                                    </td>

                                </tr>

                            @endforeach

                            </tbody>
                        </table>

                    </div>

                @else

                    <div class="empty-state">

                        <div class="empty-icon">
                            <i class="bi bi-cloud-arrow-down"></i>
                        </div>

                        <h5>No Backup Archives</h5>

                        <p>
                            Generate your first backup archive to secure your SaaS data.
                        </p>

                    </div>

                @endif

            </div>

        </div>

    </div>

@endsection

@push('styles')

    <style>

        :root{
            --theme-bg:#ffffff;
            --theme-text:#101828;
            --theme-hover:#f9fafb;
            --theme-active:#eef2ff;
            --theme-subtext:#667085;
            --theme-border:#eaecf0;
        }

        .backup-page{
            display:flex;
            flex-direction:column;
            gap:18px;
        }

        .backup-header{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:16px;
            background:var(--theme-bg);
            border:1px solid var(--theme-border);
            border-radius:20px;
            padding:20px;
            box-shadow:0 4px 20px rgba(0,0,0,.03);
        }

        .backup-header h3{
            margin:0;
            font-weight:700;
            color:var(--theme-text);
        }

        .backup-header p{
            margin:4px 0 0;
            color:var(--theme-subtext);
            font-size:.92rem;
        }

        .btn-generate{
            border:none;
            background:var(--theme-text);
            color:#fff;
            border-radius:14px;
            padding:12px 18px;
            display:flex;
            align-items:center;
            gap:10px;
            font-weight:600;
            transition:.25s ease;
        }

        .btn-generate:hover{
            transform:translateY(-1px);
            opacity:.94;
        }

        .btn-generate:disabled{
            opacity:.7;
            cursor:not-allowed;
        }

        .btn-spinner{
            width:18px;
            height:18px;
            border-radius:50%;
            border:2px solid rgba(255,255,255,.25);
            border-top-color:#fff;
            animation:spin .7s linear infinite;
        }

        @keyframes spin{
            to{
                transform:rotate(360deg);
            }
        }

        .progress-wrapper{
            background:var(--theme-bg);
            border:1px solid var(--theme-border);
            border-radius:18px;
            padding:18px;
            box-shadow:0 4px 20px rgba(0,0,0,.03);
            transition:.3s ease;
        }

        .progress-top{
            display:flex;
            align-items:center;
            justify-content:space-between;
            margin-bottom:12px;
        }

        .progress-top h6{
            margin:0;
            font-weight:700;
        }

        .progress-top small{
            color:var(--theme-subtext);
        }

        .custom-progress{
            height:10px;
            border-radius:999px;
            overflow:hidden;
            background:var(--theme-hover);
        }

        .progress-bar{
            width:0%;
            transition:width .4s ease;
            background:linear-gradient(
                90deg,
                #111827,
                #374151
            );
        }

        .summary-grid{
            display:grid;
            grid-template-columns:repeat(3,1fr);
            gap:16px;
        }

        .summary-card{
            background:var(--theme-bg);
            border:1px solid var(--theme-border);
            border-radius:18px;
            padding:18px;
            box-shadow:0 4px 20px rgba(0,0,0,.03);
        }

        .summary-card span{
            font-size:.82rem;
            color:var(--theme-subtext);
        }

        .summary-card h4{
            margin:8px 0 0;
            font-size:1.5rem;
            font-weight:700;
            color:var(--theme-text);
        }

        .table-wrapper{
            background:var(--theme-bg);
            border:1px solid var(--theme-border);
            border-radius:20px;
            overflow:hidden;
            box-shadow:0 4px 20px rgba(0,0,0,.03);
        }

        .table-header{
            padding:18px 20px;
            border-bottom:1px solid var(--theme-border);
        }

        .table-header h5{
            margin:0;
            font-weight:700;
        }

        .backup-table{
            margin:0;
        }

        .backup-table thead th{
            background:#fcfcfd;
            color:var(--theme-subtext);
            font-size:.82rem;
            font-weight:600;
            border-bottom:1px solid var(--theme-border);
            padding:14px 18px;
        }

        .backup-table tbody td{
            padding:14px 18px;
            border-color:var(--theme-border);
        }

        .backup-table tbody tr{
            transition:.2s ease;
        }

        .backup-table tbody tr:hover{
            background:var(--theme-hover);
        }

        .file-cell{
            display:flex;
            align-items:center;
            gap:14px;
        }

        .zip-icon{
            width:42px;
            height:42px;
            border-radius:12px;
            display:flex;
            align-items:center;
            justify-content:center;
            background:var(--theme-active);
            font-size:1.1rem;
        }

        .file-name{
            font-weight:600;
            color:var(--theme-text);
        }

        .file-cell small{
            color:var(--theme-subtext);
        }

        .status-badge{
            background:#ecfdf3;
            color:#027a48;
            border-radius:999px;
            padding:6px 12px;
            font-size:.78rem;
            font-weight:600;
        }

        .action-group{
            display:flex;
            align-items:center;
            gap:8px;
        }

        .btn-action{
            width:38px;
            height:38px;
            border-radius:12px;
            border:1px solid var(--theme-border);
            background:#fff;
            display:flex;
            align-items:center;
            justify-content:center;
            color:var(--theme-text);
            text-decoration:none;
            transition:.2s ease;
        }

        .btn-action:hover{
            background:var(--theme-hover);
            transform:translateY(-1px);
        }

        .btn-delete:hover{
            background:#fef2f2;
            color:#dc2626;
        }

        .empty-state{
            padding:60px 20px;
            text-align:center;
        }

        .empty-icon{
            width:90px;
            height:90px;
            margin:auto auto 18px;
            border-radius:24px;
            background:var(--theme-active);
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:2rem;
        }

        .empty-state h5{
            margin-bottom:8px;
            font-weight:700;
        }

        .empty-state p{
            max-width:420px;
            margin:auto;
            color:var(--theme-subtext);
        }

        @media(max-width:992px){

            .summary-grid{
                grid-template-columns:1fr;
            }

        }

        @media(max-width:768px){

            .backup-header{
                flex-direction:column;
                align-items:flex-start;
            }

            .btn-generate{
                width:100%;
                justify-content:center;
            }

        }

    </style>

@endpush

@push('scripts')

    <script>

        const generateBtn = document.getElementById('generateBackupBtn')
        const progressWrapper = document.getElementById('progressWrapper')
        const progressBar = document.getElementById('progressBar')
        const progressTitle = document.getElementById('progressTitle')
        const progressText = document.getElementById('progressText')
        const progressPercent = document.getElementById('progressPercent')
        const summaryCards = document.getElementById('summaryCards')

        generateBtn.addEventListener('click', async () => {

            try {

                toggleGenerating(true)

                progressWrapper.classList.remove('d-none')

                await fakeProgress()

                const response = await fetch(
                    '{{ route('sa.backups.generate') }}',
                    {
                        method:'POST',
                        headers:{
                            'X-CSRF-TOKEN':'{{ csrf_token() }}',
                            'Accept':'application/json',
                        }
                    }
                )

                const data = await response.json()

                if(!response.ok){
                    throw new Error(data.message)
                }

                updateSummary(data.summary)

                await refreshTable()

                progressTitle.innerText = 'Backup Completed'
                progressText.innerText = data.message

                setProgress(100)

                setTimeout(() => {
                    progressWrapper.classList.add('d-none')
                }, 1800)

            } catch(error){

                progressTitle.innerText = 'Backup Failed'
                progressText.innerText = error.message

            } finally {

                toggleGenerating(false)

            }

        })

        async function fakeProgress(){

            const stages = [
                {
                    progress:15,
                    title:'Preparing Backup',
                    text:'Initializing backup resources...'
                },
                {
                    progress:40,
                    title:'Dumping Database',
                    text:'Creating database snapshot...'
                },
                {
                    progress:75,
                    title:'Compressing Files',
                    text:'Compressing backup archives...'
                },
                {
                    progress:92,
                    title:'Finalizing Archive',
                    text:'Completing backup process...'
                }
            ]

            for(const stage of stages){

                progressTitle.innerText = stage.title
                progressText.innerText = stage.text

                setProgress(stage.progress)

                await delay(1200)

            }

        }

        function setProgress(value){

            progressBar.style.width = value + '%'
            progressPercent.innerText = value + '%'

        }

        function toggleGenerating(state){

            generateBtn.disabled = state

            generateBtn.querySelector('.btn-spinner')
                .classList.toggle('d-none', !state)

            generateBtn.querySelector('.btn-label')
                .innerText = state
                ? 'Generating...'
                : 'Generate Backup'

        }

        async function refreshTable(){

            const response = await fetch(
                '{{ route('sa.backups.list') }}'
            )

            const data = await response.json()

            updateSummary(data.summary)

            location.reload()

        }

        function updateSummary(summary){

            summaryCards.innerHTML = `
        <div class="summary-card">
            <span>Total Backups</span>
            <h4>${summary.total_backups}</h4>
        </div>

        <div class="summary-card">
            <span>Storage Used</span>
            <h4>${summary.storage_used}</h4>
        </div>

        <div class="summary-card">
            <span>Last Backup</span>
            <h4>${summary.last_backup}</h4>
        </div>
    `

        }

        function delay(ms){
            return new Promise(resolve => setTimeout(resolve, ms))
        }

        document.addEventListener('click', async (e) => {

            const btn = e.target.closest('.btn-delete')

            if(!btn) return

            const file = btn.dataset.file

            if(!confirm('Delete this backup archive?')){
                return
            }

            try {

                btn.disabled = true

                const response = await fetch(
                    `/sa/backups/${file}`,
                    {
                        method:'DELETE',
                        headers:{
                            'X-CSRF-TOKEN':'{{ csrf_token() }}',
                            'Accept':'application/json',
                        }
                    }
                )

                const data = await response.json()

                if(!response.ok){
                    throw new Error(data.message)
                }

                location.reload()

            } catch(error){

                alert(error.message)

                btn.disabled = false

            }

        })

    </script>

@endpush
