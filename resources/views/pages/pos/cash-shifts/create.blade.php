@extends('layouts.app')

@section('title', 'Open Cash Shift')
@section('shortText', 'Start a new cashiering shift and set the starting cash float')

@section('content')

    <x-page-header />

    <div class="row justify-content-center py-2">
        <div class="col-xl-8 col-lg-9 col-md-11">

            <!-- Top Gradient Hero Card -->
            <div class="rounded-4 p-4 p-md-4 mb-4 shadow position-relative text-white" 
                 style="background: linear-gradient(135deg, #059669 0%, #047857 50%, #0f172a 100%) !important; border: 1px solid rgba(255,255,255,0.15) !important;">
                <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-4 d-flex align-items-center justify-content-center text-white shadow-sm" 
                             style="width: 56px; height: 56px; background: rgba(255,255,255,0.2) !important; backdrop-filter: blur(10px); font-size: 1.6rem;">
                            <i class="bi bi-cash-coin"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge bg-white text-dark fw-bold rounded-pill px-2.5 py-0.5 text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.5px;">Shift Opening</span>
                                <span style="color: rgba(255,255,255,0.85); font-size: 0.82rem;"><i class="bi bi-clock-fill me-1"></i> {{ now()->format('h:i A') }}</span>
                            </div>
                            <h4 class="fw-extrabold text-white mb-1" style="color: #ffffff !important;">
                                Open Cash Shift
                            </h4>
                            <div style="color: rgba(255,255,255,0.9); font-size: 0.88rem;">
                                Verify drawer assignment and specify the starting cash float.
                            </div>
                        </div>
                    </div>

                    <div>
                        <span class="badge rounded-pill bg-warning text-dark px-3.5 py-2 fw-extrabold fs-6 shadow-sm">
                            <i class="bi bi-unlock-fill me-1"></i> New Shift
                        </span>
                    </div>
                </div>
            </div>

            <!-- Main Form Card with Generous Padding & Structured Spacing -->
            <div class="card border shadow-sm rounded-4 mb-4" style="background: #ffffff !important; border-color: #e2e8f0 !important;">
                <form action="{{ route('cashiering.cash-shifts.store') }}" method="POST">
                    @csrf

                    <input type="hidden" name="drawer_id" value="{{ $drawer->id }}">
                    @if(isset($terminal) && $terminal)
                        <input type="hidden" name="terminal_id" value="{{ encryptId($terminal->id) }}">
                    @elseif(request('terminal'))
                        <input type="hidden" name="terminal_id" value="{{ request('terminal') }}">
                    @endif

                    <div class="card-body p-4 p-md-5">

                        @if ($errors->any())
                            <div class="alert alert-danger rounded-3 p-3 mb-4">
                                <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Please review the following errors:</div>
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Shift Details Context Box -->
                        <div class="p-4 mb-5 rounded-4" style="background: linear-gradient(135deg, #f8fafc, #ffffff); border: 1px solid #e2e8f0;">
                            <div class="row g-4">
                                <div class="col-sm-6">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-3 d-flex align-items-center justify-content-center shadow-xs" style="width: 40px; height: 40px; background: #ecfdf5; color: #059669; font-size: 1.2rem;">
                                            <i class="bi bi-safe2-fill"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Cash Drawer</small>
                                            <span class="fw-bold text-dark fs-6">{{ $drawer->drawer_name }}</span>
                                            <span class="badge bg-light text-muted border ms-1 font-mono small">{{ $drawer->drawer_code }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-3 d-flex align-items-center justify-content-center shadow-xs" style="width: 40px; height: 40px; background: #eff6ff; color: #3b82f6; font-size: 1.2rem;">
                                            <i class="bi bi-person-badge-fill"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Assigned Cashier</small>
                                            <span class="fw-bold text-dark fs-6">{{ optional(auth()->user())->name ?? 'Active Cashier' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-3 d-flex align-items-center justify-content-center shadow-xs" style="width: 40px; height: 40px; background: #fef3c7; color: #d97706; font-size: 1.2rem;">
                                            <i class="bi bi-tablet-landscape-fill"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">POS Device</small>
                                            <span class="fw-bold text-dark fs-6">{{ $terminal->terminal_name ?? 'Active Counter Device' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-3 d-flex align-items-center justify-content-center shadow-xs" style="width: 40px; height: 40px; background: #f1f5f9; color: #64748b; font-size: 1.2rem;">
                                            <i class="bi bi-calendar-event-fill"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Date & Time</small>
                                            <span class="fw-bold text-dark fs-6">{{ now()->format('M d, Y h:i A') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Opening Float Input Section -->
                        <div class="text-center my-4 py-2">
                            <label for="opening_cash" class="form-label fw-extrabold text-dark fs-5 mb-1">
                                Opening Cash Float <span class="text-danger">*</span>
                            </label>
                            <p class="text-muted small mb-4">
                                Enter the starting cash amount currently inside the drawer.
                            </p>

                            <!-- High-Contrast Large Currency Input Field -->
                            <div class="input-group input-group-lg mx-auto shadow-sm rounded-4 overflow-hidden mb-3" style="max-width: 380px; border: 2px solid #059669;">
                                <span class="input-group-text bg-white border-0 fw-extrabold text-success fs-3 px-3.5">₱</span>
                                <input type="number" 
                                       id="opening_cash" 
                                       name="opening_cash" 
                                       class="form-control border-0 text-center fw-extrabold fs-2 text-dark py-3" 
                                       step="0.01" 
                                       min="0" 
                                       placeholder="0.00" 
                                       value="{{ old('opening_cash', '0.00') }}" 
                                       required
                                       autofocus>
                            </div>

                            <!-- Quick Amount Float Presets -->
                            <div class="d-flex justify-content-center gap-2 flex-wrap mt-3">
                                <button type="button" class="btn btn-light border fw-bold rounded-pill px-3 py-1.5 small quick-float" data-val="0.00">₱0 (No Float)</button>
                                <button type="button" class="btn btn-light border fw-bold rounded-pill px-3 py-1.5 small quick-float" data-val="500.00">+₱500</button>
                                <button type="button" class="btn btn-light border fw-bold rounded-pill px-3 py-1.5 small quick-float" data-val="1000.00">+₱1,000</button>
                                <button type="button" class="btn btn-light border fw-bold rounded-pill px-3 py-1.5 small quick-float" data-val="2000.00">+₱2,000</button>
                                <button type="button" class="btn btn-light border fw-bold rounded-pill px-3 py-1.5 small quick-float" data-val="5000.00">+₱5,000</button>
                            </div>
                        </div>

                        <!-- Remarks -->
                        <div class="mt-5 pt-2">
                            <label for="remarks" class="form-label fw-bold text-dark small mb-1">
                                Optional Shift Remarks / Notes
                            </label>
                            <textarea id="remarks" 
                                      name="remarks" 
                                      class="form-control rounded-3 p-3 border" 
                                      rows="3" 
                                      placeholder="e.g. Morning opening shift verified with store manager..." 
                                      style="border-color: #cbd5e1 !important;">{{ old('remarks') }}</textarea>
                        </div>

                    </div>

                    <!-- Footer Action Bar -->
                    <div class="card-footer bg-light px-4 p-md-4 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 border-top" style="border-color: #e2e8f0 !important;">
                        <a href="{{ route('terminal.index') }}" class="btn btn-light border px-4 py-2.5 rounded-3 fw-bold text-dark">
                            <i class="bi bi-arrow-left me-1"></i> Back to Devices
                        </a>

                        <button type="submit" class="btn btn-success px-5 py-2.5 rounded-3 fw-extrabold text-white shadow-sm d-flex align-items-center gap-2" style="background-color: #059669 !important; border: none !important;">
                            <i class="bi bi-unlock-fill fs-5"></i>
                            <span>Open Shift & Proceed to POS</span>
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const input = document.getElementById('opening_cash');
            const quickButtons = document.querySelectorAll('.quick-float');

            quickButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    input.value = parseFloat(btn.dataset.val).toFixed(2);
                    input.focus();
                });
            });
        });
    </script>

@endsection
