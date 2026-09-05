<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\POS\POSPayment;
use App\Models\POS\POSTerminal;
use App\Models\User;
use App\Traits\TCommonFunctions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PaymentsController extends Controller
{
    use TCommonFunctions;

    public function index(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        $today = Carbon::today();

        // ── Summary KPI Calculations ─────────────────────────────────
        $paymentsQuery = POSPayment::where('tenant_id', $tenantId);

        $totalPaymentsCount  = (clone $paymentsQuery)->count();
        $totalAmountCollected = (float) (clone $paymentsQuery)->sum('amount');
        $totalCashCollected  = (float) (clone $paymentsQuery)->where('payment_method', 'cash')->sum('amount');
        $totalDigitalCollected = (float) (clone $paymentsQuery)->where('payment_method', '!=', 'cash')->sum('amount');
        $todayCollected      = (float) (clone $paymentsQuery)->whereDate('payment_date', $today)->sum('amount');

        // Payment Method breakdown for quick badges
        $methodStats = (clone $paymentsQuery)
            ->selectRaw('payment_method, COUNT(*) as count, SUM(amount) as total_amount')
            ->groupBy('payment_method')
            ->get();

        // Filter dropdown options
        $terminals = POSTerminal::where('tenant_id', $tenantId)->get();
        $cashiers = User::where('tenant_id', $tenantId)->get(['id', 'name', 'email']);
        $paymentMethods = (clone $paymentsQuery)
            ->distinct()
            ->pluck('payment_method')
            ->filter()
            ->values();

        return view('pages.pos.payments.index', compact(
            'totalPaymentsCount',
            'totalAmountCollected',
            'totalCashCollected',
            'totalDigitalCollected',
            'todayCollected',
            'methodStats',
            'terminals',
            'cashiers',
            'paymentMethods'
        ));
    }

    public function ajaxData(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $payments = POSPayment::query()
            ->with(['sale.customer', 'sale.cashier', 'terminal', 'shift', 'creator', 'customer'])
            ->where('tenant_id', $tenantId);

        // ── Filters ───────────────────────────────────────────────────
        if ($request->filled('payment_method')) {
            $payments->where('payment_method', $request->payment_method);
        }

        if ($request->filled('terminal_id')) {
            $payments->where('terminal_id', $request->terminal_id);
        }

        if ($request->filled('cashier_id')) {
            $cashierId = $request->cashier_id;
            $payments->where(function ($q) use ($cashierId) {
                $q->where('created_by', $cashierId)
                  ->orWhereHas('sale', function ($sq) use ($cashierId) {
                      $sq->where('cashier_id', $cashierId);
                  });
            });
        }

        if ($request->filled('date_from')) {
            $payments->whereDate('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $payments->whereDate('payment_date', '<=', $request->date_to);
        }

        return DataTables::of($payments)
            ->addColumn('reference_no', function ($row) {
                $ref = $row->reference_number ?: 'PAY-' . str_pad($row->id, 6, '0', STR_PAD_LEFT);
                return '<div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 border bg-light d-flex align-items-center justify-content-center flex-shrink-0" style="width:34px;height:34px;">
                        <i class="bi bi-cash-stack text-success"></i>
                    </div>
                    <div>
                        <span class="font-mono fw-bold text-dark">' . e($ref) . '</span>
                        <div class="text-muted extra-small font-mono">ID: #' . $row->id . '</div>
                    </div>
                </div>';
            })
            ->addColumn('sale_invoice', function ($row) {
                if ($row->sale) {
                    $invoice = $row->sale->invoice_no ?: $row->sale->sale_code ?: ('#' . $row->sale->id);
                    $birRoute = route('sales.bir-receipt', $row->sale->id);
                    return '<div class="d-flex flex-column">
                        <a href="' . $birRoute . '" target="_blank" class="fw-bold text-primary font-mono text-decoration-none d-inline-flex align-items-center gap-1">
                            <i class="bi bi-receipt"></i> ' . e($invoice) . '
                        </a>
                        <small class="text-muted extra-small font-mono">Sale #' . $row->sale->id . '</small>
                    </div>';
                }
                return '<span class="badge bg-secondary-subtle text-secondary border font-mono">Direct Collection</span>';
            })
            ->addColumn('customer', function ($row) {
                $customerName = $row->customer?->name 
                    ?? $row->sale?->customer?->name 
                    ?? null;

                if ($customerName) {
                    return '<div class="d-flex align-items-center gap-1.5">
                        <i class="bi bi-person-circle text-muted"></i>
                        <span class="fw-semibold text-dark">' . e($customerName) . '</span>
                    </div>';
                }
                return '<span class="badge bg-light text-muted border border-secondary-subtle rounded-pill px-2 py-0.5 extra-small">Walk-in</span>';
            })
            ->editColumn('payment_method', function ($row) {
                $method = strtolower($row->payment_method ?? 'cash');
                
                $badges = [
                    'cash' => '<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 font-mono fw-bold"><i class="bi bi-cash me-1"></i>Cash</span>',
                    'gcash' => '<span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 font-mono fw-bold"><i class="bi bi-phone me-1"></i>GCash</span>',
                    'maya' => '<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 font-mono fw-bold"><i class="bi bi-wallet2 me-1"></i>Maya</span>',
                    'card' => '<span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill px-2.5 py-1 font-mono fw-bold"><i class="bi bi-credit-card me-1"></i>Card</span>',
                    'credit_card' => '<span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill px-2.5 py-1 font-mono fw-bold"><i class="bi bi-credit-card me-1"></i>Credit Card</span>',
                    'debit_card' => '<span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill px-2.5 py-1 font-mono fw-bold"><i class="bi bi-credit-card-2-front me-1"></i>Debit Card</span>',
                    'bank_transfer' => '<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1 font-mono fw-bold"><i class="bi bi-bank me-1"></i>Bank Transfer</span>',
                    'utang' => '<span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1 font-mono fw-bold"><i class="bi bi-journal-text me-1"></i>Credit / AR</span>',
                    'charge' => '<span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1 font-mono fw-bold"><i class="bi bi-journal-text me-1"></i>Credit / AR</span>',
                ];

                return $badges[$method] ?? '<span class="badge bg-secondary-subtle text-secondary border rounded-pill px-2.5 py-1 font-mono fw-bold">' . e(ucfirst($method)) . '</span>';
            })
            ->editColumn('amount', function ($row) {
                $amt = (float) $row->amount;
                return '<div class="text-end">
                    <span class="font-mono fw-black text-dark fs-6">₱' . number_format($amt, 2) . '</span>
                </div>';
            })
            ->addColumn('tendered_change', function ($row) {
                if ($row->tendered_amount > 0) {
                    return '<div class="extra-small font-mono text-muted text-end">
                        <div>Tendered: ₱' . number_format($row->tendered_amount, 2) . '</div>
                        <div>Change: ₱' . number_format($row->change_amount, 2) . '</div>
                    </div>';
                }
                return '<div class="extra-small text-muted text-end font-mono">Exact</div>';
            })
            ->addColumn('terminal_shift', function ($row) {
                $term = $row->terminal?->name ?? 'T' . ($row->terminal_id ?: '1');
                $shift = $row->shift_id ? 'Shift #' . $row->shift_id : '-';
                return '<div class="font-mono extra-small">
                    <span class="badge bg-light text-dark border px-2 py-0.5">' . e($term) . '</span>
                    <span class="text-muted ms-1">' . e($shift) . '</span>
                </div>';
            })
            ->editColumn('payment_date', function ($row) {
                $dt = $row->payment_date ?: $row->created_at;
                return '<div class="font-mono extra-small text-dark">' . ($dt ? $dt->format('M d, Y h:i A') : '-') . '</div>';
            })
            ->addColumn('cashier', function ($row) {
                $cashierName = $row->creator?->name 
                    ?? $row->sale?->cashier?->name 
                    ?? 'System';
                return '<span class="fw-semibold text-dark extra-small">' . e($cashierName) . '</span>';
            })
            ->addColumn('actions', function ($row) {
                $viewBtn = '';
                if ($row->sale_id) {
                    $birRoute = route('sales.bir-receipt', $row->sale_id);
                    $viewBtn = '<a href="' . $birRoute . '" target="_blank" class="btn btn-sm btn-white border rounded-2 px-2 py-1 extra-small shadow-xs text-dark" title="View Receipt">
                        <i class="bi bi-printer text-success"></i>
                    </a>';
                }
                return '<div class="d-flex align-items-center justify-content-end gap-1">' . $viewBtn . '</div>';
            })
            ->rawColumns(['reference_no', 'sale_invoice', 'customer', 'payment_method', 'amount', 'tendered_change', 'terminal_shift', 'payment_date', 'cashier', 'actions'])
            ->orderColumn('payment_date', 'created_at $1')
            ->make(true);
    }

    public function show($id)
    {
        $payment = POSPayment::with(['sale.items.product', 'sale.customer', 'sale.cashier', 'terminal', 'shift', 'creator', 'customer'])
            ->where('tenant_id', auth()->user()->tenant_id)
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'payment' => $payment
        ]);
    }
}
