@extends('layouts.sa')

@section('title', 'Backup Management')

@section('content')

    <div class="backup-page">

        <div class="backup-card">

            <div class="backup-header">

                <div class="backup-title-wrap">
                    <h4>
                        Backup Management
                    </h4>

                    <p>
                        Generate and manage secure system backups
                    </p>
                </div>

                <button
                    type="button"
                    class="backup-generate-btn"
                    id="generateBackupBtn"
                >
                <span id="generateBackupText">
                    Generate Backup
                </span>

                    <span
                        class="spinner-border spinner-border-sm d-none"
                        id="generateBackupLoader"
                    ></span>
                </button>

            </div>

            <div
                class="backup-progress"
                id="backupProgress"
            ></div>

            <div class="backup-config">
                <label class="backup-option">
                    <input
                        type="radio"
                        name="backup_type"
                        value="full"
                        checked
                    >
                    <div class="backup-option-box">
                        <div class="backup-option-icon">
                            <i class="bi bi-server"></i>
                        </div>

                        <div class="backup-option-content">
                            <h6>
                                Full System
                            </h6>

                            <p>
                                Database, uploaded files, storage and application data
                            </p>
                        </div>

                    </div>

                </label>

                <label class="backup-option">

                    <input
                        type="radio"
                        name="backup_type"
                        value="db"
                    >

                    <div class="backup-option-box">

                        <div class="backup-option-icon">
                            <i class="bi bi-database"></i>
                        </div>

                        <div class="backup-option-content">
                            <h6>
                                Database Only
                            </h6>

                            <p>
                                Backup only the MySQL database structure and records
                            </p>
                        </div>

                    </div>

                </label>

                <label class="backup-option">

                    <input
                        type="radio"
                        name="backup_type"
                        value="files"
                    >

                    <div class="backup-option-box">

                        <div class="backup-option-icon">
                            <i class="bi bi-folder2-open"></i>
                        </div>

                        <div class="backup-option-content">
                            <h6>
                                Files Only
                            </h6>

                            <p>
                                Backup uploaded files, media and storage contents
                            </p>
                        </div>

                    </div>

                </label>

            </div>

            <div class="backup-meta">

                <div class="backup-meta-item">
                    <i class="bi bi-archive"></i>

                    <span>
                    {{ $summary['total_backups'] }} backups
                </span>
                </div>

                <div class="backup-meta-item">
                    <i class="bi bi-hdd-stack"></i>

                    <span>
                    {{ $summary['storage_used'] }}
                </span>
                </div>

                <div class="backup-meta-item">
                    <i class="bi bi-clock-history"></i>

                    <span>
                    {{ $summary['last_backup'] }}
                </span>
                </div>

            </div>

        </div>

        <div class="backup-card">

            <div class="backup-table-wrap">

                <table class="backup-table">

                    <thead>
                    <tr>
                        <th>
                            Backup File
                        </th>

                        <th>
                            Size
                        </th>

                        <th>
                            Date
                        </th>

                        <th>
                            Status
                        </th>

                        <th class="text-end">
                            Actions
                        </th>
                    </tr>
                    </thead>

                    <tbody id="backupTableBody">

                    @forelse($backups as $backup)

                        <tr>

                            <td>

                                <div class="backup-file">

                                    <div class="backup-file-icon">
                                        <i class="bi bi-file-earmark-zip"></i>
                                    </div>

                                    <div class="backup-file-name">
                                        {{ $backup['name'] }}
                                    </div>

                                </div>

                            </td>
            <td>
                {{ $backup['backup_type'] }}
            </td>
                            <td>
                                {{ $backup['size'] }}
                            </td>

                            <td>
                                {{ $backup['date'] }}
                            </td>

                            <td>

                                <span class="backup-status">
                                    <i class="bi bi-check-circle-fill"></i>
                                    Ready
                                </span>

                            </td>

                            <td>

                                <div class="backup-actions">

                                    <a
                                        href="{{ route('sa.backups.download', ($backup['id'])) }}"
                                        class="backup-action-btn"
                                    >
                                        <i class="bi bi-download"></i>
                                    </a>

                                    <button
                                        type="button"
                                        class="backup-action-btn delete delete-backup-btn"
                                        data-file="{{ encrypt($backup['id']) }}"
                                    >
                                        <i class="bi bi-trash3"></i>
                                    </button>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="5">

                                <div class="backup-empty">

                                    <i class="bi bi-inbox"></i>

                                    <div>
                                        No backup archives available
                                    </div>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    <script>
        const generateBackupBtn = document.getElementById('generateBackupBtn');
        const generateBackupLoader = document.getElementById('generateBackupLoader');
        const generateBackupText = document.getElementById('generateBackupText');
        const backupProgress = document.getElementById('backupProgress');

        generateBackupBtn.addEventListener('click', async () => {

            const selectedType = document.querySelector(
                'input[name="backup_type"]:checked'
            ).value;

            generateBackupBtn.disabled = true;

            generateBackupLoader.classList.remove('d-none');

            backupProgress.classList.add('active');

            generateBackupText.textContent = 'Generating...';

            try {

                const response = await fetch(
                    "{{ route('sa.backups.generate') }}",
                    {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            type: selectedType
                        })
                    }
                );

                const result = await response.json();

                if (!response.ok) {
                    throw new Error(result.message || 'Backup failed');
                }

                window.location.reload();

            } catch (error) {

                alert(error.message);

            } finally {

                generateBackupBtn.disabled = false;

                generateBackupLoader.classList.add('d-none');

                backupProgress.classList.remove('active');

                generateBackupText.textContent = 'Generate Backup';

            }

        });

        document.addEventListener('click', async (e) => {

            const btn = e.target.closest('.delete-backup-btn');

            if (!btn) {
                return;
            }

            if (!confirm('Delete this backup archive?')) {
                return;
            }

            try {

                btn.disabled = true;

                const response = await fetch(
                    `/sa/backups/${btn.dataset.file}`,
                    {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        }
                    }
                );

                const result = await response.json();

                if (!response.ok) {
                    throw new Error(result.message || 'Delete failed');
                }

                window.location.reload();

            } catch (error) {

                alert(error.message);

                btn.disabled = false;

            }

        });
    </script>

@endsection
