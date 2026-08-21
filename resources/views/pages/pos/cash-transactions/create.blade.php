@extends('layouts.app')

@section('title', 'Add Cash Transaction')
@section('shortText', 'Record a new cash transaction')

@section('content')

    <x-page-header>
        <x-slot:action>
            <a href="{{ route('cashiering.cash-transactions.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                Back
            </a>
        </x-slot:action>
    </x-page-header>

    <div class="row justify-content-center">
        <div class="col-lg-8">

            <x-card title="New Cash Transaction">

                <form action="{{ route('cashiering.cash-transactions.store') }}" method="POST">
                    @csrf

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Cash Shift <span class="text-danger">*</span></label>
                            <select name="shift_id" class="form-select @error('shift_id') is-invalid @enderror" required>
                                <option value="">-- Select Shift --</option>
                                @foreach($shifts as $shift)
                                    <option
                                        value="{{ encryptId($shift->id) }}"
                                        {{ old('shift_id') == encryptId($shift->id) ? 'selected' : '' }}
                                    >
                                        {{ $shift->shift_code }} &mdash; {{ $shift->drawer?->drawer_name ?? '?' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('shift_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Cash Drawer <span class="text-danger">*</span></label>
                            <select name="drawer_id" class="form-select @error('drawer_id') is-invalid @enderror" required>
                                <option value="">-- Select Drawer --</option>
                                @foreach($drawers as $drawer)
                                    <option
                                        value="{{ $drawer->id }}"
                                        {{ old('drawer_id') == $drawer->id ? 'selected' : '' }}
                                    >
                                        {{ $drawer->drawer_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('drawer_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Transaction Type <span class="text-danger">*</span></label>
                            <select name="transaction_type" class="form-select @error('transaction_type') is-invalid @enderror" required>
                                <option value="">-- Select Type --</option>
                                <option value="CASH_IN" {{ old('transaction_type') === 'CASH_IN' ? 'selected' : '' }}>Cash In</option>
                                <option value="CASH_OUT" {{ old('transaction_type') === 'CASH_OUT' ? 'selected' : '' }}>Cash Out</option>
                                <option value="ADJUSTMENT" {{ old('transaction_type') === 'ADJUSTMENT' ? 'selected' : '' }}>Adjustment</option>
                            </select>
                            @error('transaction_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Amount <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">&#8369;</span>
                                <input
                                    type="number"
                                    name="amount"
                                    step="0.01"
                                    min="0.01"
                                    class="form-control @error('amount') is-invalid @enderror"
                                    value="{{ old('amount') }}"
                                    placeholder="0.00"
                                    required
                                >
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Reference Number</label>
                            <input
                                type="text"
                                name="reference_no"
                                class="form-control @error('reference_no') is-invalid @enderror"
                                value="{{ old('reference_no') }}"
                                placeholder="Optional reference"
                            >
                            @error('reference_no')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Remarks</label>
                            <textarea
                                name="remarks"
                                rows="3"
                                class="form-control @error('remarks') is-invalid @enderror"
                                placeholder="Optional remarks..."
                            >{{ old('remarks') }}</textarea>
                            @error('remarks')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 text-end">
                            <a href="{{ route('cashiering.cash-transactions.index') }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i>
                                Save Transaction
                            </button>
                        </div>

                    </div>

                </form>

            </x-card>

        </div>
    </div>

@endsection
