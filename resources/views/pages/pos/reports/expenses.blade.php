@extends('layouts.app')

@section('title', 'Operating Expenses & Petty Cash Audit Report | LikhaPOS')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Executive Header --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
        <div class="d-flex align-items-center gap-2.5">
            <div class="kpi-icon-box rose" style="width:40px;height:40px;font-size:1.15rem;">
                <i class="bi bi-receipt"></i>
            </div>
            <div>
                <h4 class="fw-black text-dark mb-0 font-mono" style="letter-spacing:-0.4px;">Expense & Petty Cash Report</h4>
                <p class="text-muted extra-small mb-0">Monitor store payouts, operating overheads, and cashier drawer cash outs</p>
            </div>
        </div>

        {{-- Date Filter Form (AJAX) --}}
        <form id="reportFilterForm" class="d-flex align-items-center gap-2 flex-wrap bg-white p-2 rounded-3 border shadow-xs">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-calendar-range text-danger extra-small fw-bold"></i>
                <input type="date" id="startDate" name="start_date" class="form-control form-control-sm font-mono fw-bold py-1 text-dark" value="{{ $startDate }}" style="font-size:0.82rem;">
                <span class="text-muted extra-small fw-bold">to</span>
                <input type="date" id="endDate" name="end_date" class="form-control form-control-sm font-mono fw-bold py-1 text-dark" value="{{ $endDate }}" style="font-size:0.82rem;">
            </div>
            <button type="submit" class="btn btn-sm btn-success font-mono fw-bold px-3 py-1.5 shadow-xs" style="background:linear-gradient(135deg, #059669 0%, #047857 100%);border:none;">
                <i class="bi bi-filter me-1"></i> Filter
            </button>
            <button type="button" onclick="window.print()" class="btn btn-sm btn-light border fw-bold px-3 py-1.5 text-dark">
                <i class="bi bi-printer me-1"></i> Print / PDF
            </button>
        </form>
    </div>

    {{-- 4 Dashboard-Style KPI Cards --}}
    <div class="row g-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="likha-kpi-card rose h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Total Expense Amount</span>
                    <div class="kpi-icon-box rose"><i class="bi bi-cash-stack"></i></div>
                </div>
                <div class="kpi-value font-mono text-danger" id="statExpenseTotal">₱{{ number_format($totalExpenseAmount, 2) }}</div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1">
                    <span class="text-danger extra-small fw-bold"><i class="bi bi-arrow-down-right me-1"></i>Total store payouts</span>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="likha-kpi-card amber h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Expense Vouchers</span>
                    <div class="kpi-icon-box amber"><i class="bi bi-file-earmark-text"></i></div>
                </div>
                <div class="kpi-value font-mono" id="statExpenseCount">{{ number_format($totalExpenseCount) }}</div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1">
                    <span class="text-warning extra-small fw-bold"><i class="bi bi-journal-text me-1"></i>Logged vouchers</span>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="likha-kpi-card blue h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Average Payout</span>
                    <div class="kpi-icon-box blue"><i class="bi bi-calculator"></i></div>
                </div>
                <div class="kpi-value font-mono" id="statExpenseAvg">₱{{ number_format($avgExpensePerTxn, 2) }}</div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1">
                    <span class="text-primary extra-small fw-bold"><i class="bi bi-bar-chart me-1"></i>Mean payout</span>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="likha-kpi-card purple h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Highest Single Payout</span>
                    <div class="kpi-icon-box purple"><i class="bi bi-trophy"></i></div>
                </div>
                <div class="kpi-value font-mono" id="statExpenseMax">₱{{ number_format($maxExpense, 2) }}</div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1">
                    <span class="text-secondary extra-small fw-bold"><i class="bi bi-award me-1"></i>Max single payout</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="card border-0 rounded-3 shadow-xs bg-white overflow-hidden">
        <div class="card-header bg-white border-bottom py-2.5 px-3 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-table text-danger fw-bold"></i>
                <h6 class="fw-black text-dark mb-0 font-mono extra-small text-uppercase tracking-wider">Operating Expenses & Payout Audit Ledger</h6>
            </div>
            <span class="badge bg-light text-dark font-mono fw-bold extra-small border">PAYOUT LOG</span>
        </div>
        <div class="card-body p-3">
            <div class="table-responsive">
                <table id="expensesReportTable" class="table table-hover align-middle w-100 font-sans mb-0">
                    <thead class="bg-light text-dark extra-small text-uppercase font-mono fw-black">
                        <tr>
                            <th class="fw-black">Ref #</th>
                            <th class="fw-black">Category & Description</th>
                            <th class="fw-black">Amount</th>
                            <th class="fw-black">Logged By</th>
                            <th class="fw-black">Date & Time</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
(function () {
    function initExpensesReport($) {
        var table = $('#expensesReportTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('reports.expenses') }}",
                data: function (d) {
                    d.start_date = $('#startDate').val();
                    d.end_date = $('#endDate').val();
                }
            },
            columns: [
                { data: 'reference_no', name: 'reference_no' },
                { data: 'category_desc', name: 'remarks' },
                { data: 'amount_formatted', name: 'amount' },
                { data: 'logged_by', name: 'cashier.name' },
                { data: 'date_formatted', name: 'created_at' }
            ],
            order: [[4, 'desc']],
            pageLength: 25,
            responsive: true
        });

        table.on('xhr', function (e, settings, json) {
            if (json && json.stats) {
                $('#statExpenseTotal').text('₱' + json.stats.totalExpenseAmount);
                $('#statExpenseCount').text(json.stats.totalExpenseCount);
                $('#statExpenseAvg').text('₱' + json.stats.avgExpensePerTxn);
                $('#statExpenseMax').text('₱' + json.stats.maxExpense);
            }
        });

        $('#reportFilterForm').on('submit', function (e) {
            e.preventDefault();
            table.ajax.reload();
        });
    }

    function checkJQuery() {
        if (window.$ && window.$.fn && window.$.fn.DataTable) {
            initExpensesReport(window.$);
        } else {
            setTimeout(checkJQuery, 50);
        }
    }
    checkJQuery();
})();
</script>
@endpush
