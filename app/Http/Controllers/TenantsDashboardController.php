<?php

namespace App\Http\Controllers;

use App\Models\POS\POSCashDrawer;
use App\Models\POS\POSCashShift;
use App\Models\POS\POSCustomerLedger;
use App\Models\POS\POSCustomers;
use App\Models\POS\POSPayment;
use App\Models\POS\POSProducts;
use App\Models\POS\POSSale;
use App\Models\POS\POSSaleItem;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TenantsDashboardController extends Controller
{
    /**
     * Instant Load with Pre-Computed 7-Day Analytics for Zero-Wait Chart Rendering
     */
    public function index()
    {
        $tenantId = auth()->user()->tenant_id;
        $initialAnalytics = $this->buildAnalyticsPayload($tenantId, '7days');

        return view('pages.tenants.dashboard.index', compact('initialAnalytics'));
    }

    /**
     * 1. Successive Endpoint: Top 6 Real-Time KPIs & Active Shift
     */
    public function kpis(): JsonResponse
    {
        $tenantId = auth()->user()->tenant_id;
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $thisMonthStart = Carbon::now()->startOfMonth();
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        // 1. Sales KPI Metrics
        $todaySales = (float) POSSale::where('tenant_id', $tenantId)
            ->whereDate('sale_date', $today)
            ->whereIn('sale_status', ['completed', 'refund'])
            ->sum('total_amount');

        $yesterdaySales = (float) POSSale::where('tenant_id', $tenantId)
            ->whereDate('sale_date', $yesterday)
            ->whereIn('sale_status', ['completed', 'refund'])
            ->sum('total_amount');

        $salesGrowth = $yesterdaySales > 0 
            ? round((($todaySales - $yesterdaySales) / $yesterdaySales) * 100, 1) 
            : ($todaySales > 0 ? 100 : 0);

        $todayOrdersCount = POSSale::where('tenant_id', $tenantId)
            ->whereDate('sale_date', $today)
            ->where('sale_status', 'completed')  // orders = only real sales, not refunds
            ->count();

        $monthSales = (float) POSSale::where('tenant_id', $tenantId)
            ->where('sale_date', '>=', $thisMonthStart)
            ->whereIn('sale_status', ['completed', 'refund'])
            ->sum('total_amount');

        $lastMonthSales = (float) POSSale::where('tenant_id', $tenantId)
            ->whereBetween('sale_date', [$lastMonthStart, $lastMonthEnd])
            ->whereIn('sale_status', ['completed', 'refund'])
            ->sum('total_amount');

        $monthGrowth = $lastMonthSales > 0
            ? round((($monthSales - $lastMonthSales) / $lastMonthSales) * 100, 1)
            : ($monthSales > 0 ? 100 : 0);

        // 2. Gross Profit Calculation (Today)
        $todaySaleIds = POSSale::where('tenant_id', $tenantId)
            ->whereDate('sale_date', $today)
            ->whereIn('sale_status', ['completed', 'refund'])
            ->pluck('id');

        $todayCostOfGoods = (float) POSSaleItem::whereIn('sale_id', $todaySaleIds)
            ->join('pos_products', 'pos_sale_items.product_id', '=', 'pos_products.id')
            ->selectRaw('SUM(pos_sale_items.qty * COALESCE(pos_products.cost_price, 0)) as total_cost')
            ->value('total_cost') ?? 0;

        $todayGrossProfit = max(0, $todaySales - $todayCostOfGoods);
        $todayProfitMargin = $todaySales > 0 ? round(($todayGrossProfit / $todaySales) * 100, 1) : 0;

        // 3. Active Cash Shift & Drawer Balance
        $activeShift = POSCashShift::with(['cashier', 'drawer'])
            ->where('tenant_id', $tenantId)
            ->where('status', 'open')
            ->latest('opened_at')
            ->first();

        $drawerStartingCash = $activeShift ? (float)$activeShift->opening_cash : 2000.00;
        $shiftCashSales = $activeShift
            ? (float) POSPayment::where('shift_id', $activeShift->id)->where('payment_method', 'cash')->sum('amount')
            : (float) POSPayment::where('tenant_id', $tenantId)->whereDate('payment_date', $today)->where('payment_method', 'cash')->sum('amount');

        $currentDrawerBalance = $drawerStartingCash + $shiftCashSales;

        // 4. CRM & Suki Ledgers (Utang Monitoring)
        $totalDebits = (float) POSCustomerLedger::where('tenant_id', $tenantId)->sum('debit');
        $totalCredits = (float) POSCustomerLedger::where('tenant_id', $tenantId)->sum('credit');
        $totalUtangReceivables = max(0, $totalDebits - $totalCredits);
        
        $customersWithUtangCount = POSCustomerLedger::where('tenant_id', $tenantId)
            ->select('customer_id')
            ->groupBy('customer_id')
            ->havingRaw('(SUM(debit) - SUM(credit)) > 0')
            ->get()
            ->count();

        // 5. Inventory & Stock Health
        $totalProducts = POSProducts::where('tenant_id', $tenantId)->count();
        $totalInventoryCost = (float) POSProducts::where('tenant_id', $tenantId)
            ->selectRaw('SUM(COALESCE(stock_on_hand, 0) * COALESCE(cost_price, 0)) as total_val')
            ->value('total_val') ?? 0;

        $lowStockCount = POSProducts::where('tenant_id', $tenantId)
            ->where('stock_on_hand', '>', 0)
            ->whereColumn('stock_on_hand', '<=', 'reorder_level')
            ->count();

        $outOfStockCount = POSProducts::where('tenant_id', $tenantId)
            ->where('stock_on_hand', '<=', 0)
            ->count();

        return response()->json([
            'today_sales' => $todaySales,
            'today_sales_formatted' => '₱' . number_format($todaySales, 2),
            'yesterday_sales' => $yesterdaySales,
            'sales_growth' => $salesGrowth,
            'today_orders_count' => $todayOrdersCount,
            'month_sales' => $monthSales,
            'month_sales_formatted' => '₱' . number_format($monthSales, 2),
            'month_growth' => $monthGrowth,
            'today_gross_profit' => $todayGrossProfit,
            'today_gross_profit_formatted' => '₱' . number_format($todayGrossProfit, 2),
            'today_profit_margin' => $todayProfitMargin,
            'active_shift' => $activeShift ? [
                'id' => $activeShift->id,
                'shift_code' => $activeShift->shift_code ?: ('SHIFT-' . $activeShift->id),
                'cashier_name' => $activeShift->cashier?->name ?? 'Cashier',
            ] : null,
            'drawer_starting_cash' => $drawerStartingCash,
            'drawer_starting_cash_formatted' => '₱' . number_format($drawerStartingCash, 2),
            'current_drawer_balance' => $currentDrawerBalance,
            'current_drawer_balance_formatted' => '₱' . number_format($currentDrawerBalance, 2),
            'total_utang_receivables' => $totalUtangReceivables,
            'total_utang_receivables_formatted' => '₱' . number_format($totalUtangReceivables, 2),
            'customers_with_utang_count' => $customersWithUtangCount,
            'total_inventory_cost' => $totalInventoryCost,
            'total_inventory_cost_formatted' => '₱' . number_format($totalInventoryCost, 2),
            'total_products_count' => $totalProducts,
            'low_stock_total' => $lowStockCount + $outOfStockCount,
            'low_stock_count' => $lowStockCount,
            'out_of_stock_count' => $outOfStockCount,
        ]);
    }

    /**
     * 2. Successive Endpoint: Dynamic Sales Chart Trends & Payment Channels
     */
    public function analyticsData(Request $request): JsonResponse
    {
        $tenantId = auth()->user()->tenant_id;
        $period = $request->query('period', '7days');

        $data = $this->buildAnalyticsPayload($tenantId, $period);

        return response()->json($data);
    }

    /**
     * Helper to compute analytics arrays
     */
    protected function buildAnalyticsPayload($tenantId, string $period = '7days'): array
    {
        $days = match ($period) {
            'today' => 1,
            'weekly', '7days' => 7,
            '30days', 'monthly' => 30,
            default => 7,
        };

        $startDate = Carbon::now()->subDays($days - 1)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        $rawSales = POSSale::where('tenant_id', $tenantId)
            ->whereIn('sale_status', ['completed', 'refund'])
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->selectRaw('DATE(sale_date) as date, SUM(total_amount) as total_revenue, COUNT(CASE WHEN sale_status = \'completed\' THEN 1 END) as total_orders')
            ->groupBy(DB::raw('DATE(sale_date)'))
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $labels = [];
        $revenues = [];
        $orders = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $currDate = Carbon::now()->subDays($i)->format('Y-m-d');
            $displayLabel = $days <= 7 
                ? Carbon::parse($currDate)->format('D (M d)') 
                : Carbon::parse($currDate)->format('M d');

            $labels[] = $displayLabel;
            $revenues[] = (float)($rawSales[$currDate]->total_revenue ?? 0);
            $orders[] = (int)($rawSales[$currDate]->total_orders ?? 0);
        }

        // Payment Methods Breakdown (Last 30 Days)
        $paymentBreakdown = POSPayment::where('tenant_id', $tenantId)
            ->where('payment_date', '>=', Carbon::now()->subDays(30))
            ->select('payment_method', DB::raw('SUM(amount) as total_amount'), DB::raw('COUNT(*) as tx_count'))
            ->groupBy('payment_method')
            ->get()
            ->keyBy('payment_method');

        $paymentStats = [
            'cash' => (float)($paymentBreakdown['cash']->total_amount ?? 0),
            'gcash' => (float)($paymentBreakdown['gcash']->total_amount ?? 0),
            'maya' => (float)($paymentBreakdown['maya']->total_amount ?? 0),
            'credit' => (float)($paymentBreakdown['credit']->total_amount ?? 0),
            'card' => (float)($paymentBreakdown['card']->total_amount ?? 0),
        ];

        $totalPaymentSum = array_sum($paymentStats) ?: 1;
        $paymentPercentages = [
            'cash' => round(($paymentStats['cash'] / $totalPaymentSum) * 100, 1),
            'gcash' => round(($paymentStats['gcash'] / $totalPaymentSum) * 100, 1),
            'maya' => round(($paymentStats['maya'] / $totalPaymentSum) * 100, 1),
            'credit' => round(($paymentStats['credit'] / $totalPaymentSum) * 100, 1),
            'card' => round(($paymentStats['card'] / $totalPaymentSum) * 100, 1),
        ];

        return [
            'labels' => $labels,
            'revenues' => $revenues,
            'orders' => $orders,
            'period' => $period,
            'payment_stats' => $paymentStats,
            'payment_percentages' => $paymentPercentages,
        ];
    }

    /**
     * 3. Successive Endpoint: Recent Live Sales Feed
     */
    public function recentSales(): JsonResponse
    {
        $tenantId = auth()->user()->tenant_id;

        $recentSales = POSSale::with('customer')
            ->where('tenant_id', $tenantId)
            ->orderByDesc('sale_date')
            ->limit(6)
            ->get()
            ->map(function ($sale) {
                return [
                    'id' => $sale->id,
                    'invoice_no' => $sale->invoice_no ?: $sale->sale_code,
                    'customer_name' => $sale->customer?->CustomerName ?? 'Walk-In Customer',
                    'payment_method' => $sale->payment_method ?: 'cash',
                    'total_amount' => (float)$sale->total_amount,
                    'total_amount_formatted' => '₱' . number_format($sale->total_amount, 2),
                    'time_formatted' => $sale->sale_date ? $sale->sale_date->format('M d, h:i A') : '-',
                    'details_url' => route('sales.details', $sale->id),
                ];
            });

        return response()->json([
            'sales' => $recentSales,
        ]);
    }

    /**
     * 4. Successive Endpoint: Low Stock & Out-of-Stock Alerts
     */
    public function inventoryAlerts(): JsonResponse
    {
        $tenantId = auth()->user()->tenant_id;

        $lowStockProducts = POSProducts::with('unit')
            ->where('tenant_id', $tenantId)
            ->where(function ($q) {
                $q->whereColumn('stock_on_hand', '<=', 'reorder_level')
                  ->orWhere('stock_on_hand', '<=', 0);
            })
            ->orderBy('stock_on_hand')
            ->limit(6)
            ->get()
            ->map(function ($prod) {
                return [
                    'id' => $prod->id,
                    'encrypted_id' => encrypt($prod->id),
                    'name' => $prod->name ?: 'Unnamed Product',
                    'barcode' => $prod->barcode ?: $prod->sku ?: 'No Code',
                    'category' => $prod->category?->name ?? 'General',
                    'stock' => (float)$prod->stock_on_hand,
                    'unit' => $prod->unit?->name ?? 'pcs',
                    'is_out_of_stock' => $prod->stock_on_hand <= 0,
                    'restock_url' => route('products.stock.receive', encrypt($prod->id)),
                ];
            });

        return response()->json([
            'products' => $lowStockProducts,
        ]);
    }

    /**
     * 5. Successive Endpoint: Top Suki Customers CRM Leaderboard
     */
    public function topSuki(): JsonResponse
    {
        $tenantId = auth()->user()->tenant_id;

        $totalCustomers = POSCustomers::where('tenant_id', $tenantId)->count();
        $totalSukiPoints = (int) POSCustomers::where('tenant_id', $tenantId)->sum('TotalPoints');

        $topCustomers = POSCustomers::where('tenant_id', $tenantId)
            ->orderByDesc('TotalPoints')
            ->limit(5)
            ->get()
            ->map(function ($cust) use ($tenantId) {
                $bal = (float) POSCustomerLedger::where('tenant_id', $tenantId)
                    ->where('customer_id', $cust->id)
                    ->selectRaw('COALESCE(SUM(debit) - SUM(credit), 0) as bal')
                    ->value('bal');

                return [
                    'id' => $cust->id,
                    'name' => $cust->CustomerName ?: 'Customer',
                    'code' => $cust->customer_code ?: 'CUST',
                    'type' => $cust->customer_type ?: 'regular',
                    'phone' => $cust->mobile_number ?: 'No Contact',
                    'points' => (int)$cust->TotalPoints,
                    'points_formatted' => number_format($cust->TotalPoints) . ' pts',
                    'balance' => max(0, $bal),
                    'balance_formatted' => '₱' . number_format(max(0, $bal), 2),
                ];
            });

        return response()->json([
            'total_customers' => $totalCustomers,
            'total_suki_points' => number_format($totalSukiPoints) . ' pts',
            'customers' => $topCustomers,
        ]);
    }

    /**
     * 6. Successive Endpoint: Fast-Moving Products (Top Velocity Sales)
     */
    public function fastMoving(): JsonResponse
    {
        $tenantId = auth()->user()->tenant_id;

        $fastMovingItems = POSSaleItem::whereHas('sale', function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId)->where('sale_status', 'completed');
            })
            ->selectRaw('product_id, SUM(ABS(qty)) as total_sold_qty, SUM(ABS(line_total)) as total_revenue')
            ->groupBy('product_id')
            ->orderByDesc('total_sold_qty')
            ->limit(6)
            ->get()
            ->map(function ($item) {
                $product = POSProducts::with('category', 'unit')->find($item->product_id);
                return [
                    'id' => $item->product_id,
                    'name' => $product ? ($product->name ?: 'Unnamed Product') : 'Deleted SKU',
                    'barcode' => $product ? ($product->barcode ?: $product->sku ?: 'No Code') : 'N/A',
                    'category' => $product && $product->category ? $product->category->name : 'General',
                    'stock' => $product ? (float)$product->stock_on_hand : 0,
                    'unit' => $product && $product->unit ? $product->unit->name : 'pcs',
                    'total_sold_qty' => (float)$item->total_sold_qty,
                    'total_sold_formatted' => number_format($item->total_sold_qty) . ' ' . ($product && $product->unit ? $product->unit->name : 'pcs'),
                    'total_revenue' => (float)$item->total_revenue,
                    'total_revenue_formatted' => '₱' . number_format($item->total_revenue, 2),
                ];
            });

        return response()->json([
            'fast_moving' => $fastMovingItems,
        ]);
    }
}
