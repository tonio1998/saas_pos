@extends('layouts.app')

@section('title', 'Record Store Expense')

@section('content')
<div class="container-fluid px-3 py-3">

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-light border rounded-3 p-2 text-dark shadow-xs hover-lift d-inline-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h4 class="fw-black text-dark font-mono mb-0">Record Store Expense</h4>
                <div class="text-muted extra-small">Log business expenditures, utilities, rent, or maintenance</div>
            </div>
        </div>
        <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-light border fw-bold rounded-pill px-3 py-1.5 shadow-xs">
            <i class="bi bi-list-ul me-1"></i> View All Expenses
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-xs rounded-4 bg-white p-4">
                <form method="POST" action="{{ route('expenses.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        {{-- Title / Description --}}
                        <div class="col-12 col-md-8">
                            <label class="form-label fw-bold small text-dark">Expense Description / Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control rounded-3" placeholder="e.g. Meralco Electric Bill - August" value="{{ old('title') }}" required>
                        </div>

                        {{-- Amount --}}
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-bold small text-dark">Amount (₱) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold font-mono">₱</span>
                                <input type="number" step="0.01" name="amount" class="form-control font-mono fw-black text-danger rounded-end-3" placeholder="0.00" value="{{ old('amount') }}" required>
                            </div>
                        </div>

                        {{-- Category --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold small text-dark">Expense Category <span class="text-danger">*</span></label>
                            <select name="category" class="form-select rounded-3" required>
                                <option value="" disabled selected>Select category...</option>
                                @foreach(\App\Models\POS\POSExpense::categories() as $key => $catName)
                                    <option value="{{ $key }}" {{ old('category') === $key ? 'selected' : '' }}>{{ $catName }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Date --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold small text-dark">Date Incurred <span class="text-danger">*</span></label>
                            <input type="date" name="expense_date" class="form-control rounded-3" value="{{ old('expense_date', date('Y-m-d')) }}" required>
                        </div>

                        {{-- Payment Method --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold small text-dark">Payment Method <span class="text-danger">*</span></label>
                            <select name="payment_method" class="form-select rounded-3" required>
                                @foreach(\App\Models\POS\POSExpense::paymentMethods() as $key => $methodName)
                                    <option value="{{ $key }}" {{ old('payment_method', 'cash') === $key ? 'selected' : '' }}>{{ $methodName }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Payee / Paid To --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold small text-dark">Payee / Provider</label>
                            <input type="text" name="payee" class="form-control rounded-3" placeholder="e.g. Meralco, Landlord, Supplier" value="{{ old('payee') }}">
                        </div>

                        {{-- Reference / OR Number --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold small text-dark">Receipt / Invoice / Reference #</label>
                            <input type="text" name="reference_no" class="form-control font-mono rounded-3" placeholder="e.g. OR-998242" value="{{ old('reference_no') }}">
                        </div>

                        {{-- Attachment / Receipt Photo --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold small text-dark">Attach Receipt / Photo</label>
                            <input type="file" name="attachment" class="form-control rounded-3" accept="image/*,application/pdf">
                        </div>

                        {{-- Notes --}}
                        <div class="col-12">
                            <label class="form-label fw-bold small text-dark">Notes / Remarks</label>
                            <textarea name="notes" class="form-control rounded-3" rows="2" placeholder="Optional details or remarks...">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-light border fw-bold rounded-pill px-3 py-1.5">Cancel</a>
                        <button type="submit" class="btn btn-sm btn-danger fw-bold rounded-pill px-4 py-1.5 shadow-xs">
                            <i class="bi bi-check-circle me-1"></i> Save Expense
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
