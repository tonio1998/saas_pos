<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\POS\POSCustomers;
use App\Models\POS\POSProducts;
use App\Models\POS\POSSale;
use App\Models\POS\POSSaleItem;
use App\Models\POS\POSPayment;
use App\Models\POS\POSTenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class POSApiController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        $login = trim($request->login);
        $user = User::where('email', $login)->orWhere('username', $login)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.',
            ], 401);
        }

        $tenant = POSTenant::find($user->tenant_id);

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'tenant_id' => $user->tenant_id,
            ],
            'tenant' => $tenant ? [
                'id' => $tenant->id,
                'business_name' => $tenant->business_name,
                'tin' => $tenant->tin,
                'branch_code' => $tenant->branch_code,
                'payment_status' => $tenant->payment_status,
                'is_active' => $tenant->isActive(),
            ] : null,
            'api_token' => base64_encode($user->id . '|' . Str::random(32)),
        ]);
    }

    public function products(Request $request)
    {
        $tenantId = $request->user()?->tenant_id ?? $request->input('tenant_id');

        $query = POSProducts::query();
        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'count' => $products->count(),
            'data' => $products,
        ]);
    }

    public function customers(Request $request)
    {
        $tenantId = $request->user()?->tenant_id ?? $request->input('tenant_id');
        $query = POSCustomers::query();
        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
        }

        return response()->json([
            'success' => true,
            'data' => $query->get(),
        ]);
    }

    public function storeCustomer(Request $request)
    {
        $validated = $request->validate([
            'tenant_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'tin' => 'nullable|string|max:50',
        ]);

        $customer = POSCustomers::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Customer created successfully.',
            'data' => $customer,
        ]);
    }

    public function createSale(Request $request)
    {
        $validated = $request->validate([
            'tenant_id' => 'required|integer',
            'cashier_id' => 'required|integer',
            'customer_id' => 'nullable|integer',
            'subtotal' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'discount_type' => 'nullable|string',
            'discount_holder' => 'nullable|string',
            'discount_id_no' => 'nullable|string',
            'payments' => 'required|array|min:1',
            'payments.*.method' => 'required|string',
            'payments.*.amount' => 'required|numeric|min:0.01',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        $totalPaid = collect($validated['payments'])->sum('amount');
        if ($totalPaid < $validated['total']) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient payment.',
            ], 422);
        }

        $change = max(0, $totalPaid - $validated['total']);
        $sale = null;

        DB::transaction(function () use ($validated, $totalPaid, $change, &$sale) {
            $discountType = $validated['discount_type'] ?? null;
            $isScPwd = in_array($discountType, ['sc_pwd', 'senior', 'pwd']) || !empty($validated['discount_id_no']);

            $subtotal = $validated['subtotal'];
            $total = $validated['total'];

            if ($isScPwd) {
                $vatExemptSales = round($subtotal / 1.12, 2);
                $vatableSales = 0;
                $taxAmount = 0;
                $zeroRatedSales = 0;
            } elseif ($discountType === 'zero_rated') {
                $vatExemptSales = 0;
                $vatableSales = 0;
                $taxAmount = 0;
                $zeroRatedSales = $total;
            } else {
                $vatableSales = round($total / 1.12, 2);
                $taxAmount = round($total - $vatableSales, 2);
                $vatExemptSales = 0;
                $zeroRatedSales = 0;
            }

            $sale = new POSSale();
            $sale->tenant_id = $validated['tenant_id'];
            $sale->invoice_no = generateSaleInvoiceNo();
            $sale->cashier_id = $validated['cashier_id'];
            $sale->customer_id = $validated['customer_id'] ?? null;
            $sale->payment_method = count($validated['payments']) === 1 ? $validated['payments'][0]['method'] : 'split';
            $sale->subtotal = $subtotal;
            $sale->discount_amount = $validated['discount'];
            $sale->discount_type = $discountType;
            $sale->discount_holder = $validated['discount_holder'] ?? null;
            $sale->discount_id_no = $validated['discount_id_no'] ?? null;
            $sale->vatable_sales = $vatableSales;
            $sale->vat_exempt_sales = $vatExemptSales;
            $sale->zero_rated_sales = $zeroRatedSales;
            $sale->tax_amount = $taxAmount;
            $sale->total_amount = $total;
            $sale->tendered_amount = $totalPaid;
            $sale->change_amount = $change;
            $sale->sale_status = 'completed';
            $sale->sale_date = now();
            $sale->save();

            foreach ($validated['items'] as $item) {
                $product = POSProducts::findOrFail($item['product_id']);
                $newSaleItem = new POSSaleItem();
                $newSaleItem->sale_id = $sale->id;
                $newSaleItem->product_id = $product->id;
                $newSaleItem->barcode = $product->barcode;
                $newSaleItem->sku = $product->sku;
                $newSaleItem->product_name = $product->name;
                $newSaleItem->qty = $item['qty'];
                $newSaleItem->unit_price = $item['price'];
                $newSaleItem->line_total = $item['qty'] * $item['price'];
                $newSaleItem->save();

                $product->decrement('stock_on_hand', $item['qty']);
            }

            foreach ($validated['payments'] as $p) {
                POSPayment::create([
                    'sale_id' => $sale->id,
                    'tenant_id' => $validated['tenant_id'],
                    'payment_method' => $p['method'],
                    'amount' => $p['amount'],
                    'tendered_amount' => $p['amount'],
                    'change_amount' => $change,
                    'payment_date' => now(),
                ]);
            }
        });

        $sale->load(['items', 'customer']);

        return response()->json([
            'success' => true,
            'message' => 'Sale completed successfully.',
            'sale' => $sale,
        ]);
    }

    public function saleDetails($id)
    {
        $sale = POSSale::with(['items.product', 'customer', 'cashier', 'payments'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $sale,
        ]);
    }
}
