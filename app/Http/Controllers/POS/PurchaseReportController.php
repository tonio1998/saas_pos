<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\POS\InventoryMovement;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PurchaseReportController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());

        $movements = InventoryMovement::where('tenant_id', $tenantId)
            ->whereIn('movement_type', ['in', 'receive', 'purchase'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->with('product')
            ->get();

        $totalBatchesCount = $movements->count();
        $totalReceivedQty = $movements->sum('qty');
        $totalInvestmentValue = $movements->sum(function($m) {
            return (float)$m->qty * (float)($m->product?->cost_price ?? 0);
        });

        if ($request->ajax()) {
            $query = InventoryMovement::where('tenant_id', $tenantId)
                ->whereIn('movement_type', ['in', 'receive', 'purchase'])
                ->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->with(['product', 'creator'])
                ->latest();

            return DataTables::of($query)
                ->with('stats', [
                    'totalBatchesCount' => number_format($totalBatchesCount),
                    'totalReceivedQty' => number_format($totalReceivedQty),
                    'totalInvestmentValue' => number_format($totalInvestmentValue, 2),
                ])
                ->addColumn('product_name', function($row) {
                    return '<div class="fw-black text-dark fs-6">' . e($row->product?->name ?? 'N/A') . '</div>
                            <div class="text-muted extra-small font-mono fw-bold"><i class="bi bi-barcode me-1"></i>' . e($row->product?->barcode ?: ('SKU: ' . ($row->product?->sku ?: 'N/A'))) . '</div>';
                })
                ->addColumn('received_qty', function($row) {
                    return '<span class="badge bg-success text-white rounded-pill px-2.5 py-1 font-mono fw-black shadow-xs"><i class="bi bi-box-arrow-in-down me-1"></i>+' . number_format($row->qty, 2) . ' units</span>';
                })
                ->addColumn('total_cost', function($row) {
                    $val = (float)$row->qty * (float)($row->product?->cost_price ?? 0);
                    return '<span class="font-mono fw-black text-primary fs-6">₱' . number_format($val, 2) . '</span>';
                })
                ->addColumn('received_by', function($row) {
                    return '<span class="fw-bold text-dark"><i class="bi bi-person-circle me-1 text-primary"></i>' . e($row->creator?->name ?? 'System Admin') . '</span>';
                })
                ->addColumn('date_formatted', function($row) {
                    return '<span class="font-mono fw-bold extra-small text-dark">' . ($row->created_at ? $row->created_at->format('M d, Y h:i A') : '-') . '</span>';
                })
                ->rawColumns(['product_name', 'received_qty', 'total_cost', 'received_by', 'date_formatted'])
                ->make(true);
        }

        return view('pages.pos.reports.purchases', compact(
            'totalBatchesCount',
            'totalReceivedQty',
            'totalInvestmentValue',
            'startDate',
            'endDate'
        ));
    }
}
