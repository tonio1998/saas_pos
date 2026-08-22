<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\POS\POSProducts;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class InventoryReportController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $products = POSProducts::where('tenant_id', $tenantId)
            ->where(function($q) {
                $q->where('archived', 0)->orWhereNull('archived');
            })->get();

        $totalProducts = $products->count();
        $totalStockOnHand = $products->sum('stock_on_hand');
        $totalCostValue = $products->sum(function($p) {
            return (float)($p->stock_on_hand ?? 0) * (float)($p->cost_price ?? 0);
        });
        $totalRetailValue = $products->sum(function($p) {
            return (float)($p->stock_on_hand ?? 0) * (float)($p->selling_price ?? 0);
        });

        if ($request->ajax()) {
            $query = POSProducts::where('tenant_id', $tenantId)
                ->where(function($q) {
                    $q->where('archived', 0)->orWhereNull('archived');
                });

            return DataTables::of($query)
                ->addColumn('product_name', function($row) {
                    return '<div class="fw-black text-dark fs-6">' . e($row->name) . '</div>
                            <div class="text-muted extra-small font-mono fw-bold"><i class="bi bi-barcode me-1"></i>' . e($row->barcode ?: ('SKU: ' . ($row->sku ?: 'N/A'))) . '</div>';
                })
                ->addColumn('stock_badge', function($row) {
                    $stock = (float)$row->stock_on_hand;
                    $reorder = (float)($row->reorder_level ?? 10);
                    if ($stock <= 0) {
                        return '<span class="badge bg-danger text-white rounded-pill px-2.5 py-1 font-mono fw-black shadow-xs"><i class="bi bi-exclamation-triangle-fill me-1"></i>Out of Stock (0)</span>';
                    } elseif ($stock <= $reorder) {
                        return '<span class="badge bg-warning text-dark rounded-pill px-2.5 py-1 font-mono fw-black shadow-xs"><i class="bi bi-exclamation-circle-fill me-1"></i>Low Stock (' . number_format($stock) . ')</span>';
                    }
                    return '<span class="badge bg-success text-white rounded-pill px-2.5 py-1 font-mono fw-black shadow-xs"><i class="bi bi-check-circle-fill me-1"></i>' . number_format($stock) . ' units</span>';
                })
                ->addColumn('cost_val', function($row) {
                    $val = (float)($row->stock_on_hand ?? 0) * (float)($row->cost_price ?? 0);
                    return '<span class="font-mono fw-black text-dark fs-6">₱' . number_format($val, 2) . '</span>';
                })
                ->addColumn('retail_val', function($row) {
                    $val = (float)($row->stock_on_hand ?? 0) * (float)($row->selling_price ?? 0);
                    return '<span class="font-mono fw-black text-success fs-6">₱' . number_format($val, 2) . '</span>';
                })
                ->rawColumns(['product_name', 'stock_badge', 'cost_val', 'retail_val'])
                ->make(true);
        }

        return view('pages.pos.reports.inventory', compact(
            'totalProducts',
            'totalStockOnHand',
            'totalCostValue',
            'totalRetailValue'
        ));
    }
}
