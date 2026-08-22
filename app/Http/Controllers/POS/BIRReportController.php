<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\POS\POSSale;
use App\Models\POS\POSTenant;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class BIRReportController extends Controller
{
    public function xReading(Request $request)
    {
        $tenantId = session('tenant_id') ?? auth()->user()->tenant_id;
        $tenant = POSTenant::find($tenantId);

        $date = $request->input('date') ? Carbon::parse($request->input('date')) : Carbon::today();

        $sales = POSSale::where('tenant_id', $tenantId)
            ->whereDate('created_at', $date)
            ->where('sale_status', 'completed')
            ->with(['items', 'cashier'])
            ->get();

        $summary = $this->calculateSummary($sales);
        $type = 'X-READING (Mid-Day Audit)';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'date' => $date->format('Y-m-d'),
                'summary' => $summary,
                'tenant' => $tenant,
            ]);
        }

        return view('pages.pos.reports.bir_reading', compact('tenant', 'date', 'summary', 'type'));
    }

    public function zReading(Request $request)
    {
        $tenantId = session('tenant_id') ?? auth()->user()->tenant_id;
        $tenant = POSTenant::find($tenantId);

        $date = $request->input('date') ? Carbon::parse($request->input('date')) : Carbon::today();

        $sales = POSSale::where('tenant_id', $tenantId)
            ->whereDate('created_at', $date)
            ->where('sale_status', 'completed')
            ->with(['items', 'cashier'])
            ->get();

        $allTimeGrandTotal = POSSale::where('tenant_id', $tenantId)
            ->where('sale_status', 'completed')
            ->whereDate('created_at', '<=', $date)
            ->sum('total_amount');

        $summary = $this->calculateSummary($sales);
        $summary['grand_total'] = $allTimeGrandTotal;
        $type = 'Z-READING (End-of-Day Official Report)';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'date' => $date->format('Y-m-d'),
                'summary' => $summary,
                'tenant' => $tenant,
            ]);
        }

        return view('pages.pos.reports.bir_reading', compact('tenant', 'date', 'summary', 'type'));
    }

    private function calculateSummary($sales)
    {
        $grossSales = $sales->sum('subtotal');
        $totalDiscounts = $sales->sum('discount_amount');
        $netSales = $sales->sum('total_amount');
        $vatableSales = $sales->sum('vatable_sales');
        $vatAmount = $sales->sum('tax_amount');
        $vatExemptSales = $sales->sum('vat_exempt_sales');
        $zeroRatedSales = $sales->sum('zero_rated_sales');
        $totalTransactions = $sales->count();

        $scPwdDiscounts = $sales->whereNotNull('discount_id_no')->sum('discount_amount');
        $regularDiscounts = $totalDiscounts - $scPwdDiscounts;

        $firstInvoice = $sales->min('invoice_no') ?? 'N/A';
        $lastInvoice = $sales->max('invoice_no') ?? 'N/A';

        return [
            'gross_sales' => $grossSales,
            'total_discounts' => $totalDiscounts,
            'regular_discounts' => $regularDiscounts,
            'sc_pwd_discounts' => $scPwdDiscounts,
            'net_sales' => $netSales,
            'vatable_sales' => $vatableSales,
            'vat_amount' => $vatAmount,
            'vat_exempt_sales' => $vatExemptSales,
            'zero_rated_sales' => $zeroRatedSales,
            'total_transactions' => $totalTransactions,
            'first_invoice' => $firstInvoice,
            'last_invoice' => $lastInvoice,
        ];
    }
}
