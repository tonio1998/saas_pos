@extends('layouts.app')

@section('title', 'Store Operating Expenses')

@section('content')
<div class="container-fluid px-3 py-3">

    {{-- Header with Action --}}
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-3">
        <div>
            <h4 class="fw-black text-dark font-mono mb-0">
                <i class="bi bi-wallet2 text-danger me-2"></i>Store Operating Expenses
            </h4>
            <div class="text-muted small">
                Track daily operating costs, utilities, rent, supplies, and payroll to compute true net profit.
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('reports.expenses') }}" class="btn btn-sm btn-light border fw-bold text-secondary rounded-pill px-3 py-1.5 shadow-xs">
                <i class="bi bi-bar-chart-line me-1"></i> Expense Reports
            </a>
            <button type="button" class="btn btn-sm btn-danger fw-bold rounded-pill px-3.5 py-1.5 shadow-xs hover-lift" data-bs-toggle="modal" data-bs-target="#modalAddExpense">
                <i class="bi bi-plus-lg me-1"></i> Record New Expense
            </button>
        </div>
    </div>

    {{-- Executive KPI Metrics Cards --}}
    <div class="row g-3 mb-3">
        {{-- Today's Expenses --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-xs rounded-4 p-3 bg-white h-100 position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted extra-small fw-bold text-uppercase font-mono">Today's Expenses</span>
                    <div class="badge bg-danger-subtle text-danger rounded-circle p-2 d-flex align-items-center justify-content-center" style="width:34px;height:34px;">
                        <i class="bi bi-lightning-charge-fill"></i>
                    </div>
                </div>
                <div class="h3 fw-black text-danger font-mono mb-1" id="kpiTodayExpenses">{{ $todayExpensesFormatted }}</div>
                <div class="text-muted extra-small">Recorded for today</div>
            </div>
        </div>

        {{-- This Month's Expenses --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-xs rounded-4 p-3 bg-white h-100 position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted extra-small fw-bold text-uppercase font-mono">This Month</span>
                    <div class="badge bg-rose-subtle text-danger rounded-circle p-2 d-flex align-items-center justify-content-center" style="width:34px;height:34px;background:#ffe4e6;color:#e11d48;">
                        <i class="bi bi-calendar3"></i>
                    </div>
                </div>
                <div class="h3 fw-black text-dark font-mono mb-1" id="kpiMonthExpenses">{{ $monthExpensesFormatted }}</div>
                <div class="text-muted extra-small">Current billing cycle</div>
            </div>
        </div>

        {{-- Top Expense Category --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-xs rounded-4 p-3 bg-white h-100 position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted extra-small fw-bold text-uppercase font-mono">Top Category</span>
                    <div class="badge bg-amber-subtle text-warning rounded-circle p-2 d-flex align-items-center justify-content-center" style="width:34px;height:34px;background:#fef3c7;color:#d97706;">
                        <i class="bi bi-tag-fill"></i>
                    </div>
                </div>
                <div class="h5 fw-bold text-dark mb-1 text-truncate" id="kpiTopCategoryName" title="{{ $topCategoryName }}">{{ $topCategoryName }}</div>
                <div class="text-muted extra-small font-mono" id="kpiTopCategoryAmount">{{ $topCategoryAmountFormatted }} total</div>
            </div>
        </div>

        {{-- Cash Drawer Payouts --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-xs rounded-4 p-3 bg-white h-100 position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted extra-small fw-bold text-uppercase font-mono">Paid via Cash Drawer</span>
                    <div class="badge bg-success-subtle text-success rounded-circle p-2 d-flex align-items-center justify-content-center" style="width:34px;height:34px;">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                </div>
                <div class="h3 fw-black text-success font-mono mb-1">{{ $cashExpensesMonthFormatted }}</div>
                <div class="text-muted extra-small">Deducted from register</div>
            </div>
        </div>
    </div>

    {{-- Filter Toolbar --}}
    <div class="card border-0 shadow-xs rounded-4 bg-white p-3 mb-3">
        <div class="row g-2 align-items-center">
            <div class="col-12 col-md-3">
                <label class="form-label extra-small text-uppercase fw-bold text-muted mb-1">Category</label>
                <select id="filterCategory" class="form-select select2 form-select-sm" data-placeholder="All Categories" data-allow-clear="true">
                    <option></option>
                    @foreach($categories as $key => $catName)
                        <option value="{{ $key }}">{{ $catName }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label extra-small text-uppercase fw-bold text-muted mb-1">Payment Method</label>
                <select id="filterPaymentMethod" class="form-select select2 form-select-sm" data-placeholder="All Methods" data-allow-clear="true">
                    <option></option>
                    @foreach($paymentMethods as $key => $methodName)
                        <option value="{{ $key }}">{{ $methodName }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label extra-small text-uppercase fw-bold text-muted mb-1">Date From</label>
                <input type="date" id="filterDateFrom" class="form-control form-select-sm rounded-3">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label extra-small text-uppercase fw-bold text-muted mb-1">Date To</label>
                <input type="date" id="filterDateTo" class="form-control form-select-sm rounded-3">
            </div>
            <div class="col-12 col-md-2 d-flex align-items-end gap-1 mt-auto">
                <button type="button" id="btnApplyFilter" class="btn btn-sm btn-dark fw-bold rounded-3 w-100 py-1.5">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                <button type="button" id="btnResetFilter" class="btn btn-sm btn-light border rounded-3 py-1.5" title="Reset Filters">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Expenses DataTable Grid --}}
    <div class="card border-0 shadow-xs rounded-4 bg-white p-3">
        <div class="table-responsive">
            <table id="expensesTable" class="table table-hover align-middle mb-0" style="width:100%;">
                <thead class="bg-light text-muted extra-small text-uppercase font-mono">
                    <tr>
                        <th style="width:50px;">Actions</th>
                        <th>Code</th>
                        <th>Date</th>
                        <th>Expense Details</th>
                        <th>Category</th>
                        <th class="text-end">Amount</th>
                        <th>Payment</th>
                        <th>Reference #</th>
                        <th>Recorded By</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

</div>

{{-- Record New Expense Modal --}}
<div class="modal fade" id="modalAddExpense" tabindex="-1" aria-labelledby="modalAddExpenseLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form id="formAddExpense" action="javascript:void(0);" onsubmit="return false;" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-light border-0 px-4 py-3">
                    <div>
                        <h5 class="modal-title fw-black text-dark font-mono mb-0" id="modalAddExpenseLabel">
                            <i class="bi bi-wallet2 text-danger me-2"></i>Record Store Operating Expense
                        </h5>
                        <div class="text-muted extra-small">Log business expenditures, utilities, rent, or maintenance</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="row g-3">
                        {{-- Title / Description --}}
                        <div class="col-12 col-md-8">
                            <label class="form-label fw-bold small text-dark">Expense Description / Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="inpExpenseTitle" class="form-control rounded-3" placeholder="e.g. Meralco Electric Bill - August" required>
                        </div>

                        {{-- Amount --}}
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-bold small text-dark">Amount (₱) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold font-mono">₱</span>
                                <input type="number" step="0.01" name="amount" id="inpExpenseAmount" class="form-control font-mono fw-black text-danger rounded-end-3" placeholder="0.00" required>
                            </div>
                        </div>

                        {{-- Category --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold small text-dark">Expense Category <span class="text-danger">*</span></label>
                            <select name="category" id="modalCategorySelect" class="form-select select2 rounded-3" data-placeholder="Select expense category..." data-dropdown-parent="#modalAddExpense" required>
                                <option></option>
                                @foreach($categories as $key => $catName)
                                    <option value="{{ $key }}">{{ $catName }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Date --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold small text-dark">Date Incurred <span class="text-danger">*</span></label>
                            <input type="date" name="expense_date" id="inpExpenseDate" class="form-control rounded-3" value="{{ date('Y-m-d') }}" required>
                        </div>

                        {{-- Payment Method --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold small text-dark">Payment Method <span class="text-danger">*</span></label>
                            <select name="payment_method" id="selectPaymentMethod" class="form-select select2 rounded-3" data-placeholder="Select payment method..." data-dropdown-parent="#modalAddExpense" required>
                                <option></option>
                                @foreach($paymentMethods as $key => $methodName)
                                    <option value="{{ $key }}" {{ $key === 'cash' ? 'selected' : '' }}>{{ $methodName }}</option>
                                @endforeach
                            </select>
                        </div>


                        {{-- Payee / Paid To --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold small text-dark">Payee / Provider</label>
                            <input type="text" name="payee" id="inpExpensePayee" class="form-control rounded-3" placeholder="e.g. Meralco, Landlord, Supplier">
                        </div>

                        {{-- Reference / OR Number --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold small text-dark">Receipt / Invoice / Reference #</label>
                            <input type="text" name="reference_no" id="inpExpenseRefNo" class="form-control font-mono rounded-3" placeholder="e.g. OR-998242">
                        </div>

                        {{-- Attachment / Receipt Photo --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold small text-dark">Attach Receipt / Photo</label>
                            <input type="file" name="attachment" id="inpExpenseAttachment" class="form-control rounded-3" accept="image/*,application/pdf">
                        </div>

                        {{-- Paid from Cash Register Option --}}
                        <div class="col-12" id="drawerPayoutBox">
                            <div class="form-check form-switch p-2.5 rounded-3 border bg-light d-flex align-items-center justify-content-between">
                                <div class="ms-1">
                                    <label class="form-check-label fw-bold text-dark small" for="chkPaidFromDrawer">
                                        <i class="bi bi-cash-coin text-success me-1"></i> Deduct from Cash Register (Cash Drawer Payout)
                                    </label>
                                    <div class="text-muted extra-small">Auto-records a cash payout so your shift drawer balance stays balanced</div>
                                </div>
                                <input class="form-check-input ms-0 me-2" type="checkbox" role="switch" id="chkPaidFromDrawer" name="paid_from_drawer" value="1" {{ $activeShift ? 'checked' : '' }}>
                            </div>
                        </div>

                        {{-- Notes --}}
                        <div class="col-12">
                            <label class="form-label fw-bold small text-dark">Notes / Purpose</label>
                            <textarea name="notes" id="inpExpenseNotes" class="form-control rounded-3" rows="2" placeholder="Optional details or remarks..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-0 px-4 py-3">
                    <button type="button" class="btn btn-sm btn-light border fw-bold rounded-pill px-3 py-1.5" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="btnSaveExpense" class="btn btn-sm btn-danger fw-bold rounded-pill px-4 py-1.5 shadow-xs d-flex align-items-center gap-1.5">
                        <i class="bi bi-check-circle"></i>
                        <span>Save Expense</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    function initExpenses($) {
        // 1. Initialize DataTable
        const table = $('#expensesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('expenses.data') }}",
                data: function (d) {
                    d.category = $('#filterCategory').val();
                    d.payment_method = $('#filterPaymentMethod').val();
                    d.date_from = $('#filterDateFrom').val();
                    d.date_to = $('#filterDateTo').val();
                }
            },
            columns: [
                { data: 'actions', name: 'actions', orderable: false, searchable: false },
                { data: 'expense_code', name: 'expense_code' },
                { data: 'expense_date', name: 'expense_date' },
                { data: 'title', name: 'title' },
                { data: 'category_badge', name: 'category' },
                { data: 'amount', name: 'amount', className: 'text-end' },
                { data: 'payment_method_badge', name: 'payment_method' },
                { data: 'reference_no', name: 'reference_no' },
                { data: 'recorded_by', name: 'created_by' }
            ],
            order: [[2, 'desc']],
            pageLength: 25,
            language: {
                emptyTable: "No expenses recorded yet. Click 'Record New Expense' to log store expenses."
            }
        });

        $('#btnApplyFilter').on('click', function () {
            table.ajax.reload();
        });

        $('#btnResetFilter').on('click', function () {
            $('#filterCategory').val('').trigger('change');
            $('#filterPaymentMethod').val('').trigger('change');
            $('#filterDateFrom').val('');
            $('#filterDateTo').val('');
            table.ajax.reload();
        });

        // Toggle drawer payout checkbox based on payment method
        $('#selectPaymentMethod').on('change', function () {
            if ($(this).val() === 'cash') {
                $('#drawerPayoutBox').slideDown();
            } else {
                $('#drawerPayoutBox').slideUp();
                $('#chkPaidFromDrawer').prop('checked', false);
            }
        });

        // 2. AJAX-based Form Submission with Dynamic Button Loader
        $('#formAddExpense').on('submit', function (e) {
            e.preventDefault();
            e.stopPropagation();

            const btn = $('#btnSaveExpense');
            const origBtnContent = btn.html();

            // Put active loading spinner on button
            btn.prop('disabled', true).html(`
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                <span>Recording Expense...</span>
            `);

            const formData = new FormData(this);

            $.ajax({
                url: "{{ route('expenses.store') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                success: function (res) {
                    if (res && res.success) {
                        // Close modal
                        const modalEl = document.getElementById('modalAddExpense');
                        if (modalEl) {
                            const modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                            modalInstance.hide();
                        }

                        // Reset form & select2
                        $('#formAddExpense')[0].reset();
                        $('#modalCategorySelect').val('').trigger('change');
                        $('#selectPaymentMethod').val('cash').trigger('change');

                        // Reload Datatable & KPIs
                        table.ajax.reload(null, false);
                        refreshKpis();

                        // SweetAlert Feedback
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Expense Recorded!',
                                text: res.message,
                                timer: 2200,
                                showConfirmButton: false
                            });
                        }
                    } else {
                        alert(res.message || 'Error occurred while saving expense.');
                    }
                },
                error: function (xhr) {
                    console.error('Save expense error:', xhr);
                    let msg = 'Failed to record expense. Please check input fields.';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            text: msg
                        });
                    } else {
                        alert(msg);
                    }
                },
                complete: function () {
                    // Restore button
                    btn.prop('disabled', false).html(origBtnContent);
                }
            });

            return false;
        });

        // 3. AJAX Delete Expense
        $(document).on('click', '.btn-delete-expense', function () {
            const id = $(this).data('id');
            const title = $(this).data('title');

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Delete Expense?',
                    text: `Are you sure you want to remove "${title}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Yes, Delete'
                }).then((result) => {
                    if (result.isConfirmed) {
                        performDelete(id);
                    }
                });
            } else {
                if (confirm(`Are you sure you want to delete expense "${title}"?`)) {
                    performDelete(id);
                }
            }
        });

        function performDelete(id) {
            $.ajax({
                url: `/expenses/delete/${id}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                success: function (res) {
                    if (res && res.success) {
                        table.ajax.reload(null, false);
                        refreshKpis();
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: res.message,
                                timer: 1800,
                                showConfirmButton: false
                            });
                        }
                    }
                },
                error: function (xhr) {
                    console.error('Delete error:', xhr);
                }
            });
        }

        // 4. Real-time KPI update
        function refreshKpis() {
            $.ajax({
                url: "{{ route('expenses.kpis') }}",
                type: 'GET',
                success: function (d) {
                    if (d) {
                        $('#kpiTodayExpenses').text(d.today_expenses_formatted);
                        $('#kpiMonthExpenses').text(d.month_expenses_formatted);
                        $('#kpiTopCategoryName').text(d.top_category_name);
                        $('#kpiTopCategoryAmount').text(d.top_category_amount_formatted + ' total');
                    }
                }
            });
        }
    }

    function checkJQuery() {
        if (window.$ && window.$.fn && window.$.fn.DataTable) {
            initExpenses(window.$);
        } else {
            setTimeout(checkJQuery, 30);
        }
    }

    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        checkJQuery();
    } else {
        document.addEventListener('DOMContentLoaded', checkJQuery);
    }
})();
</script>
@endpush

@endsection
