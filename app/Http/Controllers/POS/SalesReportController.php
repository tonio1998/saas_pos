<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\POS\POSSale;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SalesReportController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());

        $salesQuery = POSSale::where('tenant_id', $tenantId)
            ->whereIn('sale_status', ['completed', 'refund'])
            ->whereDate('sale_date', '>=', $startDate)
            ->whereDate('sale_date', '<=', $endDate);

        $totalRevenue    = (float)$salesQuery->sum('total_amount');  // refunds auto-deduct (negative)
        $totalSalesCount = POSSale::where('tenant_id', $tenantId)
            ->where('sale_status', 'completed')  // count real transactions only
            ->whereDate('sale_date', '>=', $startDate)
            ->whereDate('sale_date', '<=', $endDate)->count();
        $avgOrderValue   = $totalSalesCount > 0 ? ($totalRevenue / $totalSalesCount) : 0;
        $totalDiscounts  = (float)$salesQuery->where('sale_status', 'completed')->sum('discount_amount');

        if ($request->ajax()) {
            $sales = POSSale::where('tenant_id', $tenantId)
                ->whereIn('sale_status', ['completed', 'refund'])
                ->whereDate('sale_date', '>=', $startDate)
                ->whereDate('sale_date', '<=', $endDate)
                ->with(['customer', 'cashier'])
                ->latest('sale_date');

            return DataTables::of($sales)
                ->with('stats', [
                    'totalRevenue' => number_format($totalRevenue, 2),
                    'totalSalesCount' => number_format($totalSalesCount),
                    'avgOrderValue' => number_format($avgOrderValue, 2),
                    'totalDiscounts' => number_format($totalDiscounts, 2),
                ])
                ->addColumn('invoice_ref', function($row) {
                    $code = e($row->invoice_no ?: $row->sale_code);
                    return '<span class="font-mono fw-black text-primary fs-6"><i class="bi bi-receipt me-1"></i>' . $code . '</span>';
                })
                ->addColumn('customer_name', function($row) {
                    $name = e($row->customer?->name ?? 'Walk-in Customer');
                    $icon = $row->customer ? 'bi-person-check-fill text-success' : 'bi-person text-muted';
                    return '<div class="d-flex align-items-center gap-1.5"><i class="bi ' . $icon . '"></i><span class="fw-bold text-dark">' . $name . '</span></div>';
                })
                ->addColumn('payment_badge', function($row) {
                    $pm = strtoupper($row->payment_method ?: 'CASH');
                    if ($pm === 'CASH') {
                        $badgeClass = 'bg-success text-white';
                        $icon = 'bi-cash-stack';
                    } elseif (in_array($pm, ['GCASH', 'MAYA', 'E-WALLET'])) {
                        $badgeClass = 'bg-primary text-white';
                        $icon = 'bi-phone';
                    } else {
                        $badgeClass = 'bg-info text-white';
                        $icon = 'bi-credit-card-2-front';
                    }
                    return '<span class="badge ' . $badgeClass . ' rounded-pill px-2.5 py-1 font-mono fw-bold text-uppercase shadow-xs"><i class="bi ' . $icon . ' me-1"></i>' . $pm . '</span>';
                })
                ->addColumn('net_amount', function($row) {
                    $val = (float)$row->total_amount;
                    $color = $val >= 1000 ? 'text-success' : ($val >= 500 ? 'text-primary' : 'text-dark');
                    return '<span class="font-mono fw-black ' . $color . ' fs-6">₱' . number_format($val, 2) . '</span>';
                })
                ->addColumn('date_formatted', function($row) {
                    return '<span class="font-mono fw-bold extra-small text-dark">' . ($row->sale_date ? $row->sale_date->format('M d, Y h:i A') : $row->created_at->format('M d, Y h:i A')) . '</span>';
                })
                ->rawColumns(['invoice_ref', 'customer_name', 'payment_badge', 'net_amount', 'date_formatted'])
                ->make(true);
        }

        return view('pages.pos.reports.sales', compact(
            'totalRevenue',
            'totalSalesCount',
            'avgOrderValue',
            'totalDiscounts',
            'startDate',
            'endDate'
        ));
    }
}
