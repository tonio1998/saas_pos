@extends('layouts.app')

@section('title', 'Select POS Device')
@section('shortText', 'Pumili ng POS device para makapagsimula sa cashiering terminal')

@section('content')

    <x-page-header />

    @php
        $tenantId = auth()->check() ? auth()->user()->tenant_id : null;
        $deviceCheck = (new \App\Services\Tenant\TenantSubscriptionService())->canCreateDevice($tenantId);
    @endphp

    <form action="{{ route('terminal.select') }}" method="POST">
        @csrf

        <div class="row justify-content-center">
            <div class="col-xl-9">

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
                            <div class="col-lg-6">
                                <label for="terminal{{ $terminal->id }}" class="terminal-card shadow-sm">
                                    <input type="radio" id="terminal{{ $terminal->id }}" name="terminal_id" value="{{ encryptId($terminal->id) }}" required>

                                    <div class="terminal-check">
                                        <i class="bi bi-check-circle-fill text-success fs-4"></i>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-start mb-2 pe-4">
                                        <div>
                                            <h5 class="fw-bold text-dark mb-0">
                                                <i class="bi bi-tablet-landscape text-success me-2"></i>
                                                {{ $terminal->terminal_name }}
                                            </h5>
                                            <small class="text-muted">
                                                Device Code: {{ $terminal->terminal_code }}
                                            </small>
                                        </div>

                                        <div>
                                            @if($terminal->status == 'active')
                                                <span class="badge bg-success text-white rounded-pill px-2.5 py-1">Ready</span>
                                            @elseif($terminal->status == 'maintenance')
                                                <span class="badge bg-warning text-dark rounded-pill px-2.5 py-1">Maintenance</span>
                                            @else
                                                <span class="badge bg-danger text-white rounded-pill px-2.5 py-1">Inactive</span>
                                            @endif
                                        </div>
                                    </div>

                                    <hr class="my-2.5">

                                    <div class="d-flex align-items-center">
                                        <div class="drawer-icon me-3">
                                            <i class="bi bi-safe2 text-success fs-5"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block extra-small">Konektadong Cash Drawer</small>
                                            <div class="fw-bold text-dark small">
                                                {{ $terminal->drawer?->drawer_name ?? 'No Drawer Assigned' }}
                                            </div>
                                        </div>
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

            </div>
        </div>
    </form>

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

        .terminal-card:hover {
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
            const radios = document.querySelectorAll('input[name="terminal_id"]');
            const button = document.getElementById('btnContinue');
            const selected = document.getElementById('selectedTerminal');

            radios.forEach(radio => {
                radio.addEventListener('change', () => {
                    button.disabled = false;
                    const card = radio.closest('.terminal-card');
                    const title = card.querySelector('h5').innerText.trim();
                    selected.innerHTML = '<span class="text-success"><i class="bi bi-check-circle-fill me-1"></i> <strong>Selected Device:</strong> ' + title + '</span>';
                });
            });
        });
    </script>
@endsection
