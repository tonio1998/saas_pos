<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\POS\POSSale;
use App\Models\POS\POSSaleItem;
use App\Models\POS\POSCashTransaction;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProfitReportController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());

        $sales = POSSale::where('tenant_id', $tenantId)
            ->where('sale_status', 'completed')
            ->whereDate('sale_date', '>=', $startDate)
            ->whereDate('sale_date', '<=', $endDate)
            ->with(['items.product'])
            ->get();

        $grossRevenue = (float)$sales->sum('total_amount');

        $cogs = 0;
        foreach ($sales as $sale) {
            foreach ($sale->items as $item) {
                $qty = (float)$item->qty;
                $cost = (float)($item->product?->cost_price ?? $item->cost_price ?? 0);
                $cogs += ($qty * $cost);
            }
        }

        $grossProfit = $grossRevenue - $cogs;

        $operatingExpenses = (float)POSCashTransaction::where('tenant_id', $tenantId)
            ->whereIn('transaction_type', ['out', 'expense', 'payout', 'OUT'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->sum('amount');

        $netProfit = $grossProfit - $operatingExpenses;
        $profitMargin = $grossRevenue > 0 ? (($netProfit / $grossRevenue) * 100) : 0;

        if ($request->ajax()) {
            return DataTables::of($sales)
                ->with('stats', [
                    'grossRevenue' => number_format($grossRevenue, 2),
                    'cogs' => number_format($cogs, 2),
                    'operatingExpenses' => number_format($operatingExpenses, 2),
                    'netProfit' => number_format($netProfit, 2),
                    'profitMargin' => number_format($profitMargin, 1),
                    'isPositive' => $netProfit >= 0
                ])
                ->addColumn('invoice_ref', function($row) {
                    return '<span class="font-mono fw-black text-primary fs-6"><i class="bi bi-receipt me-1"></i>' . e($row->invoice_no ?: $row->sale_code) . '</span>';
                })
                ->addColumn('revenue', function($row) {
                    return '<span class="font-mono fw-black text-dark fs-6">₱' . number_format($row->total_amount, 2) . '</span>';
                })
                ->addColumn('estimated_cogs', function($row) {
                    $itemCogs = 0;
                    foreach ($row->items as $i) {
                        $itemCogs += ((float)$i->qty * (float)($i->product?->cost_price ?? 0));
                    }
                    return '<span class="font-mono fw-bold text-muted">₱' . number_format($itemCogs, 2) . '</span>';
                })
                ->addColumn('estimated_profit', function($row) {
                    $itemCogs = 0;
                    foreach ($row->items as $i) {
                        $itemCogs += ((float)$i->qty * (float)($i->product?->cost_price ?? 0));
                    }
                    $profit = (float)$row->total_amount - $itemCogs;
                    $class = $profit >= 0 ? 'text-success fw-black' : 'text-danger fw-black';
                    return '<span class="font-mono ' . $class . ' fs-6">₱' . number_format($profit, 2) . '</span>';
                })
                ->addColumn('profit_margin', function($row) {
                    $itemCogs = 0;
                    foreach ($row->items as $i) {
                        $itemCogs += ((float)$i->qty * (float)($i->product?->cost_price ?? 0));
                    }
                    $rev = (float)$row->total_amount;
                    $margin = $rev > 0 ? ((($rev - $itemCogs) / $rev) * 100) : 0;

                    if ($margin >= 30) {
                        $badgeClass = 'bg-success text-white';
                    } elseif ($margin >= 15) {
                        $badgeClass = 'bg-primary text-white';
                    } elseif ($margin >= 0) {
                        $badgeClass = 'bg-warning text-dark';
                    } else {
                        $badgeClass = 'bg-danger text-white';
                    }

                    return '<span class="badge ' . $badgeClass . ' rounded-pill px-2.5 py-1 font-mono fw-black shadow-xs">' . number_format($margin, 1) . '%</span>';
                })
                ->rawColumns(['invoice_ref', 'revenue', 'estimated_cogs', 'estimated_profit', 'profit_margin'])
                ->make(true);
        }

        return view('pages.pos.reports.profit', compact(
            'grossRevenue',
            'cogs',
            'grossProfit',
            'operatingExpenses',
            'netProfit',
            'profitMargin',
            'startDate',
            'endDate'
        ));
    }
}
