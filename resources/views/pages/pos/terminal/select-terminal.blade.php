@extends('layouts.app')

@section('title', 'Select POS Device')
@section('shortText', 'Pumili ng POS device para makapagsimula sa cashiering terminal')

@section('content')

    <x-page-header />

    @php
        $tenantId = auth()->check() ? auth()->user()->tenant_id : null;
        $deviceCheck = (new \App\Services\Tenant\TenantSubscriptionService())->canCreateDevice($tenantId);
    @endphp

    <div class="row justify-content-center">
        <div class="col-xl-9">

            @if(session('error'))
                <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 p-3 d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-danger-subtle text-danger p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width:40px;height:40px;">
                        <i class="bi bi-exclamation-octagon-fill fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-danger">Aksyon Hindi Pinayagan</div>
                        <div class="small text-dark">{{ session('error') }}</div>
                    </div>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 p-3 d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-success-subtle text-success p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width:40px;height:40px;">
                        <i class="bi bi-check-circle-fill fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-success">Tagumpay</div>
                        <div class="small text-dark">{{ session('success') }}</div>
                    </div>
                </div>
            @endif

            @if(isset($userActiveShift) && $userActiveShift)
                <div class="alert alert-primary border-0 shadow-sm rounded-4 mb-4 p-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2.5">
                        <div class="rounded-circle bg-white text-primary p-2 d-flex align-items-center justify-content-center shadow-xs" style="width:38px;height:38px;">
                            <i class="bi bi-person-badge-fill fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-0">Aktibo ang Iyong Shift</h6>
                            <small class="text-muted">Kasalukuyan kang may bukas na kaha sa <strong>{{ $userActiveShift->drawer?->drawer_name ?? 'iyong counter' }}</strong> (Shift #{{ $userActiveShift->shift_code }}).</small>
                        </div>
                    </div>
                    <a href="{{ route('terminal.index') }}" class="btn btn-sm btn-primary rounded-pill px-3 py-1.5 fw-bold shadow-xs">
                        <i class="bi bi-play-circle-fill me-1"></i> Ipagpatuloy ang Aking POS
                    </a>
                </div>
            @endif

            <form action="{{ route('terminal.select') }}" method="POST">
                @csrf

                <x-card>

                    <div class="text-center mb-4">
                        <div class="terminal-icon mx-auto mb-3 shadow-sm">
                            <i class="bi bi-tablet-landscape"></i>
                        </div>

                        <h3 class="fw-extrabold text-dark mb-1">
                            Pumili ng POS Device
                        </h3>

                        <p class="text-muted small mb-3">
                            Pumili ng rehistradong POS counter device o tablet kung saan ka magta-transact ngayong shift.
                        </p>

                        <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">
                            <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-3 py-2 fw-bold">
                                <i class="bi bi-tablet-fill me-1"></i> {{ $terminals->count() }} Registered POS Device{{ $terminals->count() > 1 ? 's' : '' }}
                            </span>
                            <span class="badge rounded-pill bg-light text-dark border px-3 py-2 fw-semibold">
                                Plan: {{ $deviceCheck['plan_name'] }} (Max {{ $deviceCheck['limit'] }} Devices)
                            </span>
                        </div>
                    </div>

                    <div class="row g-3">
                        @forelse($terminals as $terminal)
                            @php
                                $activeShift = $terminal->drawer?->activeShift;
                                $isOwnedByMe = $activeShift && $activeShift->cashier_id === auth()->id();
                                $isOccupiedByOther = $activeShift && $activeShift->cashier_id !== auth()->id();
                                $isAvailable = $terminal->status === 'active' && !$activeShift;
                            @endphp
                            <div class="col-lg-6">
                                <label for="terminal{{ $terminal->id }}" 
                                       class="terminal-card shadow-sm {{ $isOccupiedByOther ? 'terminal-card-locked' : '' }} {{ $isOwnedByMe ? 'terminal-card-owned' : '' }}">
                                    
                                    <input type="radio" 
                                           id="terminal{{ $terminal->id }}" 
                                           name="terminal_id" 
                                           value="{{ encryptId($terminal->id) }}" 
                                           {{ $isOccupiedByOther ? 'disabled' : '' }}
                                           {{ $isOwnedByMe ? 'checked' : '' }}
                                           required>

                                    <div class="terminal-check">
                                        @if($isOccupiedByOther)
                                            <i class="bi bi-lock-fill text-muted fs-5"></i>
                                        @else
                                            <i class="bi bi-check-circle-fill text-success fs-4"></i>
                                        @endif
                                    </div>

                                    <div class="d-flex justify-content-between align-items-start mb-2 pe-4">
                                        <div>
                                            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-1.5">
                                                <i class="bi bi-tablet-landscape text-primary"></i>
                                                <span>{{ $terminal->terminal_name }}</span>
                                            </h5>
                                            <small class="text-muted font-mono extra-small">
                                                Device Code: {{ $terminal->terminal_code }}
                                            </small>
                                        </div>

                                        <div>
                                            @if($isOwnedByMe)
                                                <span class="badge bg-primary text-white rounded-pill px-2.5 py-1 fw-bold shadow-xs">
                                                    <i class="bi bi-person-check-fill me-1"></i> Your Shift
                                                </span>
                                            @elseif($isOccupiedByOther)
                                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1 fw-bold" title="In use by {{ $activeShift->cashier?->name }}">
                                                    <i class="bi bi-lock-fill me-1"></i> Busy ({{ $activeShift->cashier?->name ?? 'Cashier' }})
                                                </span>
                                            @elseif($terminal->status == 'active')
                                                <span class="badge bg-success text-white rounded-pill px-2.5 py-1 fw-bold">
                                                    <i class="bi bi-check2 me-1"></i> Available
                                                </span>
                                            @elseif($terminal->status == 'maintenance')
                                                <span class="badge bg-warning text-dark rounded-pill px-2.5 py-1">Maintenance</span>
                                            @else
                                                <span class="badge bg-danger text-white rounded-pill px-2.5 py-1">Inactive</span>
                                            @endif
                                        </div>
                                    </div>

                                    <hr class="my-2.5">

                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <div class="drawer-icon me-2.5">
                                                <i class="bi bi-safe2 text-success fs-5"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block extra-small">Konektadong Cash Drawer</small>
                                                <div class="fw-bold text-dark small">
                                                    {{ $terminal->drawer?->drawer_name ?? 'No Drawer Assigned' }}
                                                </div>
                                            </div>
                                        </div>

                                        @if($isOccupiedByOther)
                                            <small class="text-danger extra-small fw-bold font-mono">
                                                <i class="bi bi-shield-lock-fill me-1"></i> Locked
                                            </small>
                                        @elseif($isOwnedByMe)
                                            <small class="text-primary extra-small fw-bold font-mono">
                                                <i class="bi bi-broadcast me-1"></i> Active
                                            </small>
                                        @endif
                                    </div>
                                </label>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-warning text-center p-4 rounded-4 mb-0">
                                    <i class="bi bi-exclamation-triangle-fill fs-3 d-block text-warning mb-2"></i>
                                    <h6 class="fw-bold text-dark mb-1">Walang Rehistradong POS Device</h6>
                                    <p class="small text-muted mb-3">Kailangan mo munang magrehistro ng kahit 1 POS Device para makapagsimula.</p>
                                    <a href="{{ route('terminal.create') }}" class="btn btn-success fw-bold px-4 py-2 rounded-3" style="background-color:#059669; border:none;">
                                        + Rehistro ng Bagong POS Device
                                    </a>
                                </div>
                            </div>
                        @endforelse
                    </div>

                </x-card>

                @if($terminals->count())
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 mt-4">
                        <div id="selectedTerminal" class="text-muted small fw-semibold">
                            Pumili ng POS Device sa itaas...
                        </div>

                        <div class="d-flex gap-2 w-100 w-sm-auto">
                            <a href="{{ route('terminal.create') }}" class="btn btn-light border px-3 py-2 rounded-3 text-dark fw-bold w-50 w-sm-auto">
                                + Add Device
                            </a>
                            <button type="submit" class="btn btn-emerald px-4 py-2 rounded-3 fw-bold text-white shadow-sm w-50 w-sm-auto" id="btnContinue" disabled style="background-color:#059669; border:none;">
                                <i class="bi bi-play-circle-fill me-1"></i> Open POS Terminal
                            </button>
                        </div>
                    </div>
                @endif

            </form>

        </div>
    </div>

    <style>
        .terminal-icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: #ecfdf5;
            color: #059669;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
        }

        .terminal-card {
            position: relative;
            display: block;
            padding: 1.25rem;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            background: #ffffff;
            cursor: pointer;
            transition: all 0.2s ease;
            height: 100%;
        }

        .terminal-card:not(.terminal-card-locked):hover {
            transform: translateY(-2px);
            border-color: #059669;
            box-shadow: 0 8px 20px rgba(5, 150, 105, 0.08) !important;
        }

        .terminal-card input {
            display: none;
        }

        .terminal-check {
            position: absolute;
            top: 16px;
            right: 16px;
            font-size: 1.2rem;
            color: #cbd5e1;
            transition: all 0.2s ease;
        }

        .terminal-card:has(input:checked) {
            border: 2px solid #059669;
            background: #f0fdf4;
            box-shadow: 0 10px 24px rgba(5, 150, 105, 0.12) !important;
        }

        .terminal-card:has(input:checked) .terminal-check {
            color: #059669;
        }

        .terminal-card.terminal-card-owned {
            border: 2px solid #7c3aed;
            background: #fbf8ff;
        }

        .terminal-card.terminal-card-locked {
            opacity: 0.68;
            cursor: not-allowed !important;
            background: #f8fafc !important;
            border-color: #e2e8f0 !important;
        }

        .drawer-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: #ecfdf5;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #059669;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const radios = document.querySelectorAll('input[name="terminal_id"]:not(:disabled)');
            const button = document.getElementById('btnContinue');
            const selected = document.getElementById('selectedTerminal');

            function updateSelected(radio) {
                if (!radio) return;
                button.disabled = false;
                const card = radio.closest('.terminal-card');
                const title = card.querySelector('h5 span') ? card.querySelector('h5 span').innerText.trim() : card.querySelector('h5').innerText.trim();
                selected.innerHTML = '<span class="text-success"><i class="bi bi-check-circle-fill me-1"></i> <strong>Selected Device:</strong> ' + title + '</span>';
            }

            radios.forEach(radio => {
                radio.addEventListener('change', () => updateSelected(radio));
            });

            // If a radio is already checked (e.g. current cashier's active shift)
            const checkedRadio = document.querySelector('input[name="terminal_id"]:checked');
            if (checkedRadio) {
                updateSelected(checkedRadio);
            }
        });
    </script>
@endsection
