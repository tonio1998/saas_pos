<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\POS\POSCashDrawer;
use App\Models\POS\POSCashShift;
use App\Models\POS\POSCashTransaction;
use App\Models\POS\POSExpense;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class ExpenseController extends Controller
{
    public function index()
    {
        $tenantId = auth()->user()->tenant_id;
        $today = Carbon::today();
        $thisMonthStart = Carbon::now()->startOfMonth();

        // 1. KPI Metrics
        $todayExpenses = (float) POSExpense::where('tenant_id', $tenantId)
            ->whereDate('expense_date', $today)
            ->where('status', 'approved')
            ->sum('amount');

        $monthExpenses = (float) POSExpense::where('tenant_id', $tenantId)
            ->where('expense_date', '>=', $thisMonthStart)
            ->where('status', 'approved')
            ->sum('amount');

        $topCategoryRow = POSExpense::where('tenant_id', $tenantId)
            ->where('expense_date', '>=', $thisMonthStart)
            ->where('status', 'approved')
            ->select('category', DB::raw('SUM(amount) as total_amount'))
            ->groupBy('category')
            ->orderByDesc('total_amount')
            ->first();

        $categories = POSExpense::categories();
        $topCategoryName = $topCategoryRow ? ($categories[$topCategoryRow->category] ?? ucfirst($topCategoryRow->category)) : 'None Yet';
        $topCategoryAmount = $topCategoryRow ? (float)$topCategoryRow->total_amount : 0;

        $cashExpensesMonth = (float) POSExpense::where('tenant_id', $tenantId)
            ->where('expense_date', '>=', $thisMonthStart)
            ->where('payment_method', 'cash')
            ->where('status', 'approved')
            ->sum('amount');

        $drawers = POSCashDrawer::where('tenant_id', $tenantId)->get();

        $activeShift = POSCashShift::where('tenant_id', $tenantId)
            ->where('status', 'open')
            ->latest('opened_at')
            ->first();

        return view('pages.tenants.expenses.index', [
            'todayExpenses' => $todayExpenses,
            'todayExpensesFormatted' => '₱' . number_format($todayExpenses, 2),
            'monthExpenses' => $monthExpenses,
            'monthExpensesFormatted' => '₱' . number_format($monthExpenses, 2),
            'topCategoryName' => $topCategoryName,
            'topCategoryAmount' => $topCategoryAmount,
            'topCategoryAmountFormatted' => '₱' . number_format($topCategoryAmount, 2),
            'cashExpensesMonth' => $cashExpensesMonth,
            'cashExpensesMonthFormatted' => '₱' . number_format($cashExpensesMonth, 2),
            'categories' => $categories,
            'paymentMethods' => POSExpense::paymentMethods(),
            'drawers' => $drawers,
            'activeShift' => $activeShift,
        ]);
    }

    public function ajaxData(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        $query = POSExpense::with(['creator', 'drawer'])
            ->where('tenant_id', $tenantId);

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('expense_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('expense_date', '<=', $request->date_to);
        }

        $categories = POSExpense::categories();

        return DataTables::of($query)
            ->addColumn('actions', function ($expense) {
                $id = $expense->id;
                $viewBtn = $expense->attachment ? '
                    <a href="' . asset('storage/' . $expense->attachment) . '" target="_blank" class="btn btn-xs btn-outline-info rounded-2 px-2 py-1" title="View Receipt Attachment">
                        <i class="bi bi-file-earmark-image"></i>
                    </a>
                ' : '';

                $deleteBtn = '
                    <button type="button" class="btn btn-xs btn-outline-danger rounded-2 px-2 py-1 btn-delete-expense" data-id="' . $id . '" data-title="' . htmlspecialchars($expense->title) . '" title="Delete Expense">
                        <i class="bi bi-trash"></i>
                    </button>
                ';

                return '<div class="d-flex align-items-center gap-1">' . $viewBtn . $deleteBtn . '</div>';
            })
            ->editColumn('expense_code', function ($expense) {
                return '<span class="badge bg-light text-dark border font-mono fw-bold" style="font-size:0.75rem;">' . ($expense->expense_code ?: ('EXP-' . $expense->id)) . '</span>';
            })
            ->editColumn('expense_date', function ($expense) {
                return $expense->expense_date ? $expense->expense_date->format('M d, Y') : '-';
            })
            ->editColumn('title', function ($expense) {
                $payeeHtml = $expense->payee ? '<div class="text-muted extra-small"><i class="bi bi-person me-1"></i>Payee: <span class="fw-semibold">' . htmlspecialchars($expense->payee) . '</span></div>' : '';
                return '<div><div class="fw-bold text-dark" style="font-size:0.88rem;">' . htmlspecialchars($expense->title) . '</div>' . $payeeHtml . '</div>';
            })
            ->addColumn('category_badge', function ($expense) use ($categories) {
                $catName = $categories[$expense->category] ?? ucfirst($expense->category);
                $badgeStyle = match ($expense->category) {
                    'utilities'            => 'background:#e0f2fe;color:#0369a1;border:1px solid #bae6fd;',
                    'rent'                 => 'background:#ede9fe;color:#5b21b6;border:1px solid #c4b5fd;',
                    'salaries'             => 'background:#dcfce7;color:#15803d;border:1px solid #86efac;',
                    'supplies'             => 'background:#fef3c7;color:#92400e;border:1px solid #fde68a;',
                    'delivery_gas'         => 'background:#e0e7ff;color:#3730a3;border:1px solid #c7d2fe;',
                    'repairs_maintenance' => 'background:#ffedd5;color:#9a3412;border:1px solid #fed7aa;',
                    'taxes_permits'        => 'background:#f1f5f9;color:#334155;border:1px solid #cbd5e1;',
                    default                => 'background:#f8fafc;color:#475569;border:1px solid #e2e8f0;',
                };
                return '<span class="badge fw-bold" style="' . $badgeStyle . 'font-size:0.75rem;padding:4px 8px;">' . $catName . '</span>';
            })
            ->editColumn('amount', function ($expense) {
                return '<span class="font-mono fw-black text-danger" style="font-size:0.92rem;">₱' . number_format($expense->amount, 2) . '</span>';
            })
            ->addColumn('payment_method_badge', function ($expense) {
                return match ($expense->payment_method) {
                    'cash'          => '<span class="badge bg-success-subtle text-success border border-success-subtle fw-bold"><i class="bi bi-cash me-1"></i>Cash</span>',
                    'gcash'         => '<span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-bold"><i class="bi bi-phone me-1"></i>GCash</span>',
                    'maya'          => '<span class="badge bg-info-subtle text-info border border-info-subtle fw-bold"><i class="bi bi-wallet2 me-1"></i>Maya</span>',
                    'bank_transfer' => '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle fw-bold"><i class="bi bi-bank me-1"></i>Bank</span>',
                    default         => '<span class="badge bg-light text-dark border fw-bold">' . ucfirst($expense->payment_method) . '</span>',
                };
            })
            ->editColumn('reference_no', function ($expense) {
                return $expense->reference_no ? '<span class="font-mono extra-small text-muted">' . htmlspecialchars($expense->reference_no) . '</span>' : '<span class="text-muted extra-small">-</span>';
            })
            ->addColumn('recorded_by', function ($expense) {
                return $expense->creator?->name ?? 'System';
            })
            ->rawColumns(['actions', 'expense_code', 'title', 'category_badge', 'amount', 'payment_method_badge', 'reference_no'])
            ->make(true);
    }

    public function store(Request $request): JsonResponse
    {
        $tenantId = auth()->user()->tenant_id;

        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'category'         => 'required|string|max:50',
            'amount'           => 'required|numeric|min:0.01',
            'expense_date'     => 'required|date',
            'payment_method'   => 'required|string|max:50',
            'payee'            => 'nullable|string|max:190',
            'reference_no'     => 'nullable|string|max:100',
            'notes'            => 'nullable|string',
            'drawer_id'        => 'nullable|integer',
            'attachment'       => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'paid_from_drawer' => 'nullable|boolean',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('expenses', 'public');
        }

        $activeShift = POSCashShift::where('tenant_id', $tenantId)
            ->where('status', 'open')
            ->latest('opened_at')
            ->first();

        $drawerId = $validated['drawer_id'] ?? ($activeShift?->drawer_id ?? null);
        $cashShiftId = $activeShift?->id;

        $expense = POSExpense::create([
            'tenant_id'      => $tenantId,
            'title'          => $validated['title'],
            'category'       => $validated['category'],
            'amount'         => $validated['amount'],
            'expense_date'   => $validated['expense_date'],
            'payment_method' => $validated['payment_method'],
            'payee'          => $validated['payee'] ?? null,
            'reference_no'   => $validated['reference_no'] ?? null,
            'notes'          => $validated['notes'] ?? null,
            'attachment'     => $attachmentPath,
            'drawer_id'      => $drawerId,
            'cash_shift_id'  => $cashShiftId,
            'status'         => 'approved',
            'created_by'     => auth()->id(),
        ]);

        // If paid in cash directly from Cash Drawer, record a Cash Movement/Transaction so the register balance stays reconciled!
        if ($validated['payment_method'] === 'cash' && !empty($request->paid_from_drawer) && $drawerId) {
            try {
                if (class_exists(POSCashTransaction::class)) {
                    POSCashTransaction::create([
                        'tenant_id'        => $tenantId,
                        'cash_shift_id'    => $cashShiftId,
                        'drawer_id'        => $drawerId,
                        'transaction_type' => 'payout',
                        'amount'           => $validated['amount'],
                        'reason'           => 'Operating Expense: ' . $validated['title'] . ' (' . $expense->expense_code . ')',
                        'created_by'       => auth()->id(),
                    ]);
                }
            } catch (\Exception $e) {
                // Ignore cash transaction failure if schema differs
                \Log::warning('Cash payout record skipped for expense: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Expense [{$expense->expense_code}] recorded successfully!",
            'expense' => $expense,
        ]);
    }

    public function create()
    {
        return view('pages.tenants.expenses.create');
    }

    public function edit($id)
    {
        $tenantId = auth()->user()->tenant_id;
        $expense = POSExpense::where('tenant_id', $tenantId)->findOrFail($id);
        return view('pages.tenants.expenses.edit', compact('expense'));
    }

    public function update(Request $request, $id)
    {
        $tenantId = auth()->user()->tenant_id;
        $expense = POSExpense::where('tenant_id', $tenantId)->findOrFail($id);

        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'category'       => 'required|string|max:50',
            'amount'         => 'required|numeric|min:0.01',
            'expense_date'   => 'required|date',
            'payment_method' => 'required|string|max:50',
            'payee'          => 'nullable|string|max:190',
            'reference_no'   => 'nullable|string|max:100',
            'notes'          => 'nullable|string',
            'attachment'     => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request->file('attachment')->store('expenses', 'public');
        }

        $validated['updated_by'] = auth()->id();
        $expense->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Expense [{$expense->expense_code}] updated successfully!",
                'expense' => $expense,
            ]);
        }

        return redirect()->route('expenses.index')->with('success', "Expense [{$expense->expense_code}] updated successfully!");
    }

    public function destroy($id): JsonResponse
    {
        $tenantId = auth()->user()->tenant_id;
        $expense = POSExpense::where('tenant_id', $tenantId)->findOrFail($id);
        $expense->delete();

        return response()->json([
            'success' => true,
            'message' => 'Expense entry removed successfully.',
        ]);
    }

    public function kpis(): JsonResponse
    {
        $tenantId = auth()->user()->tenant_id;
        $today = Carbon::today();
        $thisMonthStart = Carbon::now()->startOfMonth();

        $todayExpenses = (float) POSExpense::where('tenant_id', $tenantId)
            ->whereDate('expense_date', $today)
            ->where('status', 'approved')
            ->sum('amount');

        $monthExpenses = (float) POSExpense::where('tenant_id', $tenantId)
            ->where('expense_date', '>=', $thisMonthStart)
            ->where('status', 'approved')
            ->sum('amount');

        $topCategoryRow = POSExpense::where('tenant_id', $tenantId)
            ->where('expense_date', '>=', $thisMonthStart)
            ->where('status', 'approved')
            ->select('category', DB::raw('SUM(amount) as total_amount'))
            ->groupBy('category')
            ->orderByDesc('total_amount')
            ->first();

        $categories = POSExpense::categories();
        $topCategoryName = $topCategoryRow ? ($categories[$topCategoryRow->category] ?? ucfirst($topCategoryRow->category)) : 'None Yet';
        $topCategoryAmount = $topCategoryRow ? (float)$topCategoryRow->total_amount : 0;

        return response()->json([
            'today_expenses_formatted' => '₱' . number_format($todayExpenses, 2),
            'month_expenses_formatted' => '₱' . number_format($monthExpenses, 2),
            'top_category_name' => $topCategoryName,
            'top_category_amount_formatted' => '₱' . number_format($topCategoryAmount, 2),
        ]);
    }
}
