<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\POS\POSProducts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockLowStockController extends Controller
{
    public function index()
    {
        $tenantId = auth()->user()->tenant_id;

        $lowStockProducts = POSProducts::where('tenant_id', $tenantId)
            ->where(function($q) {
                $q->where('archived', 0)->orWhereNull('archived');
            })
            ->with(['category', 'unit', 'variants'])
            ->get()
            ->filter(function($p) {
                if ($p->variants && $p->variants->count() > 0) {
                    $variantStockSum = (float) $p->variants->sum('stock_on_hand');
                    $effectiveStock = ($variantStockSum > 0 || (float)$p->stock_on_hand <= 0) ? $variantStockSum : (float)$p->stock_on_hand;
                } else {
                    $effectiveStock = (float) $p->stock_on_hand;
                }
                $reorderLevel = (float) ($p->reorder_level ?? 10);
                return $effectiveStock <= $reorderLevel;
            })
            ->sortBy(function($p) {
                if ($p->variants && $p->variants->count() > 0) {
                    return (float) $p->variants->sum('stock_on_hand');
                }
                return (float) $p->stock_on_hand;
            });

        return view('pages.tenants.inventory.low_stocks.index', compact('lowStockProducts'));
    }
}
