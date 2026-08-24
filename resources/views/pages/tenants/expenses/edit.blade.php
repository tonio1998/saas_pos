@extends('layouts.app')

@section('title', 'Edit Store Expense')

@section('content')
<div class="container-fluid px-3 py-3">

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-light border rounded-3 p-2 text-dark shadow-xs hover-lift d-inline-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h4 class="fw-black text-dark font-mono mb-0">Edit Expense [{{ $expense->expense_code }}]</h4>
                <div class="text-muted extra-small">Update expense details or change category</div>
            </div>
        </div>
        <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-light border fw-bold rounded-pill px-3 py-1.5 shadow-xs">
            <i class="bi bi-list-ul me-1"></i> View All Expenses
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-xs rounded-4 bg-white p-4">
                <form method="POST" action="{{ route('expenses.update', $expense->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        {{-- Title / Description --}}
                        <div class="col-12 col-md-8">
                            <label class="form-label fw-bold small text-dark">Expense Description / Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control rounded-3" value="{{ old('title', $expense->title) }}" required>
                        </div>

                        {{-- Amount --}}
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-bold small text-dark">Amount (₱) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold font-mono">₱</span>
                                <input type="number" step="0.01" name="amount" class="form-control font-mono fw-black text-danger rounded-end-3" value="{{ old('amount', $expense->amount) }}" required>
                            </div>
                        </div>

                        {{-- Category --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold small text-dark">Expense Category <span class="text-danger">*</span></label>
                            <select name="category" class="form-select rounded-3" required>
                                @foreach(\App\Models\POS\POSExpense::categories() as $key => $catName)
                                    <option value="{{ $key }}" {{ old('category', $expense->category) === $key ? 'selected' : '' }}>{{ $catName }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Date --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold small text-dark">Date Incurred <span class="text-danger">*</span></label>
                            <input type="date" name="expense_date" class="form-control rounded-3" value="{{ old('expense_date', $expense->expense_date?->format('Y-m-d')) }}" required>
                        </div>

                        {{-- Payment Method --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold small text-dark">Payment Method <span class="text-danger">*</span></label>
                            <select name="payment_method" class="form-select rounded-3" required>
                                @foreach(\App\Models\POS\POSExpense::paymentMethods() as $key => $methodName)
                                    <option value="{{ $key }}" {{ old('payment_method', $expense->payment_method) === $key ? 'selected' : '' }}>{{ $methodName }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Payee / Paid To --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold small text-dark">Payee / Provider</label>
                            <input type="text" name="payee" class="form-control rounded-3" value="{{ old('payee', $expense->payee) }}">
                        </div>

                        {{-- Reference / OR Number --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold small text-dark">Receipt / Invoice / Reference #</label>
                            <input type="text" name="reference_no" class="form-control font-mono rounded-3" value="{{ old('reference_no', $expense->reference_no) }}">
                        </div>

                        {{-- Attachment / Receipt Photo --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold small text-dark">Replace Receipt / Photo</label>
                            <input type="file" name="attachment" class="form-control rounded-3" accept="image/*,application/pdf">
                            @if($expense->attachment)
                                <div class="mt-1">
                                    <a href="{{ asset('storage/' . $expense->attachment) }}" target="_blank" class="extra-small text-primary fw-bold text-decoration-none">
                                        <i class="bi bi-file-earmark-image me-1"></i>View Current Attachment
                                    </a>
                                </div>
                            @endif
                        </div>

                        {{-- Notes --}}
                        <div class="col-12">
                            <label class="form-label fw-bold small text-dark">Notes / Remarks</label>
                            <textarea name="notes" class="form-control rounded-3" rows="2">{{ old('notes', $expense->notes) }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-light border fw-bold rounded-pill px-3 py-1.5">Cancel</a>
                        <button type="submit" class="btn btn-sm btn-danger fw-bold rounded-pill px-4 py-1.5 shadow-xs">
                            <i class="bi bi-check-circle me-1"></i> Update Expense
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
