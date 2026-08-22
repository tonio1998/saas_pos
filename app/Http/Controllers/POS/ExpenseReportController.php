<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\POS\POSCashTransaction;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ExpenseReportController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());

        $transactions = POSCashTransaction::where('tenant_id', $tenantId)
            ->whereIn('transaction_type', ['out', 'expense', 'payout', 'OUT'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->get();

        $totalExpenseAmount = (float)$transactions->sum('amount');
        $totalExpenseCount = $transactions->count();
        $avgExpensePerTxn = $totalExpenseCount > 0 ? ($totalExpenseAmount / $totalExpenseCount) : 0;
        $maxExpense = (float)($transactions->max('amount') ?? 0);

        if ($request->ajax()) {
            $query = POSCashTransaction::where('tenant_id', $tenantId)
                ->whereIn('transaction_type', ['out', 'expense', 'payout', 'OUT'])
                ->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->with(['cashier'])
                ->latest('created_at');

            return DataTables::of($query)
                ->with('stats', [
                    'totalExpenseAmount' => number_format($totalExpenseAmount, 2),
                    'totalExpenseCount' => number_format($totalExpenseCount),
                    'avgExpensePerTxn' => number_format($avgExpensePerTxn, 2),
                    'maxExpense' => number_format($maxExpense, 2),
                ])
                ->addColumn('reference_no', function($row) {
                    $ref = e($row->reference_no ?: ($row->transaction_code ?: ('EXP-' . $row->id)));
                    return '<span class="font-mono fw-black text-danger fs-6"><i class="bi bi-receipt-cutoff me-1"></i>' . $ref . '</span>';
                })
                ->addColumn('category_desc', function($row) {
                    return '<div>
                        <div class="fw-black text-dark fs-6">' . e($row->category ?: 'General Store Expense') . '</div>
                        <div class="text-muted extra-small font-mono fw-bold">' . e($row->remarks ?: 'Store payout') . '</div>
                    </div>';
                })
                ->addColumn('amount_formatted', function($row) {
                    $amt = (float)$row->amount;
                    $badgeClass = $amt >= 1000 ? 'bg-danger text-white' : ($amt >= 300 ? 'bg-warning text-dark' : 'bg-danger-subtle text-danger border-danger-subtle');
                    return '<span class="badge ' . $badgeClass . ' rounded-pill px-3 py-1 font-mono fw-black fs-6 shadow-xs">-₱' . number_format($amt, 2) . '</span>';
                })
                ->addColumn('logged_by', function($row) {
                    return '<span class="fw-bold text-dark"><i class="bi bi-person-fill me-1 text-danger"></i>' . e($row->cashier?->name ?? 'System Cashier') . '</span>';
                })
                ->addColumn('date_formatted', function($row) {
                    return '<span class="font-mono fw-bold extra-small text-dark">' . ($row->created_at ? \Carbon\Carbon::parse($row->created_at)->format('M d, Y h:i A') : '-') . '</span>';
                })
                ->rawColumns(['reference_no', 'category_desc', 'amount_formatted', 'logged_by', 'date_formatted'])
                ->make(true);
        }

        return view('pages.pos.reports.expenses', compact(
            'totalExpenseAmount',
            'totalExpenseCount',
            'avgExpensePerTxn',
            'maxExpense',
            'startDate',
            'endDate'
        ));
    }
}
