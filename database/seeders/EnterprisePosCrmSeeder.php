<?php

namespace Database\Seeders;

use App\Models\POS\POSCashDrawer;
use App\Models\POS\POSCashShift;
use App\Models\POS\POSCashTransaction;
use App\Models\POS\POSCustomerLedger;
use App\Models\POS\POSCustomers;
use App\Models\POS\POSPayment;
use App\Models\POS\POSProducts;
use App\Models\POS\POSSale;
use App\Models\POS\POSSaleItem;
use App\Models\POS\POSTenant;
use App\Models\POS\POSTerminal;
use App\Models\POS\Stocks;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EnterprisePosCrmSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = POSTenant::all();
        if ($tenants->isEmpty()) {
            $this->command->warn('No tenants found. Skipping POS CRM seeding.');
            return;
        }

        foreach ($tenants as $tenant) {
            $tenantId = $tenant->id;
            $user = User::where('tenant_id', $tenantId)->first() ?? User::first();
            $userId = $user?->id ?? 1;

            $this->command->info("Seeding Enterprise CRM & Sales for Tenant [ID: {$tenantId}] {$tenant->name}...");

            // 1. Ensure Terminal & Cash Drawer exist
            $terminal = POSTerminal::firstOrCreate(
                ['tenant_id' => $tenantId],
                [
                    'terminal_name' => 'Main Checkout POS-01',
                    'terminal_code' => 'TERM-01',
                    'status' => 'active',
                    'created_by' => $userId,
                ]
            );

            $drawer = POSCashDrawer::firstOrCreate(
                ['tenant_id' => $tenantId],
                [
                    'drawer_name' => 'Main Cash Drawer 01',
                    'status' => 'active',
                    'created_by' => $userId,
                    'created_at' => Carbon::now()->subDays(30),
                ]
            );

            // 2. Seed Rich Customer CRM Profiles
            $customerData = [
                ['name' => 'Aling Nena Store', 'type' => 'business', 'phone' => '09171234567', 'address' => 'Brgy. San Antonio, Pasig City', 'limit' => 25000.00, 'balance' => 4500.00, 'points' => 350],
                ['name' => 'Mang Kanor Sari-Sari', 'type' => 'business', 'phone' => '09189876543', 'address' => 'Purok 4, Quezon City', 'limit' => 15000.00, 'balance' => 1850.00, 'points' => 210],
                ['name' => 'Maria Clara Santos (Senior)', 'type' => 'senior', 'phone' => '09223344556', 'address' => 'Unit 402 Green Residences, Manila', 'limit' => 0.00, 'balance' => 0.00, 'points' => 120, 'discount' => 20.00],
                ['name' => 'Juan Dela Cruz (PWD)', 'type' => 'pwd', 'phone' => '09335566778', 'address' => 'Blk 12 Lot 5, Caloocan City', 'limit' => 0.00, 'balance' => 0.00, 'points' => 85, 'discount' => 20.00],
                ['name' => 'Tita Baby Canteen', 'type' => 'regular', 'phone' => '09194455667', 'address' => 'Near Elementary School, Marikina', 'limit' => 10000.00, 'balance' => 3200.00, 'points' => 450],
                ['name' => 'Kapitan Berting', 'type' => 'credit', 'phone' => '09178899001', 'address' => 'Barangay Hall Complex', 'limit' => 30000.00, 'balance' => 0.00, 'points' => 890],
                ['name' => 'Teacher Grace Ramos', 'type' => 'regular', 'phone' => '09287766554', 'address' => 'Teachers Village, QC', 'limit' => 5000.00, 'balance' => 650.00, 'points' => 175],
                ['name' => 'Kuya Jun Karinderya', 'type' => 'business', 'phone' => '09156677889', 'address' => 'Market Place, Mandaluyong', 'limit' => 20000.00, 'balance' => 7200.00, 'points' => 520],
                ['name' => 'Ate Linda Bakery', 'type' => 'regular', 'phone' => '09201122334', 'address' => 'San Roque, Marikina', 'limit' => 8000.00, 'balance' => 1200.00, 'points' => 310],
                ['name' => 'Dr. Eduardo Gomez', 'type' => 'credit', 'phone' => '09173344556', 'address' => 'Medical Center Clinic', 'limit' => 50000.00, 'balance' => 0.00, 'points' => 640],
            ];

            $createdCustomers = [];
            foreach ($customerData as $idx => $c) {
                $code = 'CUST-' . strtoupper(Str::random(4)) . '-' . str_pad($idx + 1, 3, '0', STR_PAD_LEFT);
                $customer = POSCustomers::updateOrCreate(
                    [
                        'tenant_id' => $tenantId,
                        'CustomerName' => $c['name'],
                    ],
                    [
                        'customer_code' => $code,
                        'customer_type' => $c['type'],
                        'mobile_number' => $c['phone'],
                        'CustomerAddress' => $c['address'],
                        'credit_limit' => $c['limit'],
                        'TotalPoints' => $c['points'],
                        'discount_percent' => $c['discount'] ?? 0.00,
                        'status' => 'active',
                        'created_by' => $userId,
                        'created_at' => Carbon::now()->subDays(30),
                    ]
                );
                $createdCustomers[] = $customer;

                // Seed Customer Ledger (Utang & Repayments)
                if ($c['balance'] > 0) {
                    POSCustomerLedger::create([
                        'tenant_id' => $tenantId,
                        'customer_id' => $customer->id,
                        'reference_no' => 'INV-' . rand(10000, 99999),
                        'transaction_type' => 'SALE',
                        'debit' => $c['balance'] + 1500.00,
                        'credit' => 0.00,
                        'running_balance' => $c['balance'] + 1500.00,
                        'remarks' => 'Grocery and wholesale credit purchase',
                        'created_by' => $userId,
                        'created_at' => Carbon::now()->subDays(12),
                    ]);

                    POSCustomerLedger::create([
                        'tenant_id' => $tenantId,
                        'customer_id' => $customer->id,
                        'reference_no' => 'PAY-' . rand(10000, 99999),
                        'transaction_type' => 'PAYMENT',
                        'debit' => 0.00,
                        'credit' => 1500.00,
                        'running_balance' => $c['balance'],
                        'remarks' => 'Partial cash repayment via cashier counter',
                        'created_by' => $userId,
                        'created_at' => Carbon::now()->subDays(3),
                    ]);
                }
            }

            // 3. Get Products for realistic sales & stock logs
            $products = POSProducts::where('tenant_id', $tenantId)->get();
            if ($products->isEmpty()) {
                $products = POSProducts::all();
            }

            if ($products->isEmpty()) {
                continue;
            }

            // 4. Seed Cashier Shift
            $shift = POSCashShift::firstOrCreate(
                [
                    'tenant_id' => $tenantId,
                    'drawer_id' => $drawer->id,
                    'status' => 'open',
                ],
                [
                    'shift_code' => 'SHIFT-' . Carbon::today()->format('ymd') . '-01',
                    'cashier_id' => $userId,
                    'opening_cash' => 2000.00,
                    'opened_at' => Carbon::now()->startOfDay(),
                    'created_by' => $userId,
                    'created_at' => Carbon::now()->startOfDay(),
                ]
            );

            // Seed Shift Cash Float
            POSCashTransaction::firstOrCreate(
                [
                    'tenant_id' => $tenantId,
                    'shift_id' => $shift->id,
                    'transaction_type' => 'cash_in',
                    'category' => 'Opening Cash',
                ],
                [
                    'transaction_code' => 'FLOAT-' . rand(1000, 9999),
                    'drawer_id' => $drawer->id,
                    'cashier_id' => $userId,
                    'amount' => 2000.00,
                    'reference_no' => 'OPEN-' . Carbon::today()->format('Ymd'),
                    'remarks' => 'Shift starting cash float',
                    'status' => 'active',
                    'approved_by' => $userId,
                    'created_by' => $userId,
                    'created_at' => Carbon::now()->startOfDay(),
                ]
            );

            // 5. Generate 80+ Realistic Historical Sales over past 30 days
            $paymentMethods = ['cash', 'gcash', 'maya', 'credit'];
            $startDate = Carbon::now()->subDays(30);

            for ($i = 0; $i < 80; $i++) {
                $saleDate = (clone $startDate)->addMinutes(rand(1, 43200)); // Random time over 30 days
                $selectedCustomer = (rand(1, 100) <= 60 && !empty($createdCustomers))
                    ? $createdCustomers[array_rand($createdCustomers)]
                    : null;

                $isSeniorOrPwd = $selectedCustomer && in_array($selectedCustomer->customer_type, ['senior', 'pwd']);
                $discountType = $isSeniorOrPwd ? ($selectedCustomer->customer_type === 'senior' ? 'senior_citizen' : 'pwd') : null;
                $discountHolder = $isSeniorOrPwd ? $selectedCustomer->CustomerName : null;
                $discountIdNo = $isSeniorOrPwd ? 'OSCA-' . rand(100000, 999999) : null;

                // Pick 1 to 4 random products for this basket
                $basketProducts = $products->random(min(rand(1, 4), $products->count()));
                $subtotal = 0.00;
                $itemsPayload = [];

                foreach ($basketProducts as $prod) {
                    $qty = $prod->allow_decimal_qty ? round(rand(25, 250) / 100, 2) : rand(1, 4); // 0.25kg to 2.5kg or 1-4 pcs
                    $unitPrice = (float)($prod->selling_price ?: 50.00);
                    $lineTotal = round($qty * $unitPrice, 2);

                    $subtotal += $lineTotal;
                    $itemsPayload[] = [
                        'product_id' => $prod->id,
                        'barcode' => $prod->barcode,
                        'sku' => $prod->sku,
                        'product_name' => $prod->name,
                        'qty' => $qty,
                        'unit_price' => $unitPrice,
                        'discount_amount' => 0.00,
                        'tax_amount' => 0.00,
                        'line_total' => $lineTotal,
                    ];
                }

                $discountAmount = $isSeniorOrPwd ? round($subtotal * 0.20, 2) : 0.00;
                $totalAmount = max(0, $subtotal - $discountAmount);
                $taxAmount = $isSeniorOrPwd ? 0.00 : round($totalAmount - ($totalAmount / 1.12), 2);

                $payMethod = $selectedCustomer && in_array($selectedCustomer->customer_type, ['business', 'credit']) && rand(1, 10) <= 4
                    ? 'credit'
                    : $paymentMethods[array_rand($paymentMethods)];

                $tendered = $payMethod === 'cash' ? ceil($totalAmount / 50) * 50 : $totalAmount;
                if ($tendered < $totalAmount) $tendered = $totalAmount;
                $change = round($tendered - $totalAmount, 2);

                $saleCode = 'POS-' . $tenantId . '-' . $saleDate->format('ymd') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT) . '-' . rand(100, 999);
                $invoiceNo = 'INV-' . $tenantId . '-' . $saleDate->format('ym') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT) . '-' . rand(100, 999);

                // Create Sale (conforming to pos_sales table schema)
                $sale = POSSale::create([
                    'tenant_id' => $tenantId,
                    'sale_code' => $saleCode,
                    'customer_id' => $selectedCustomer?->id,
                    'cash_shift_id' => $shift->id,
                    'drawer_id' => $drawer->id,
                    'terminal_id' => $terminal->id,
                    'cashier_id' => $userId,
                    'invoice_no' => $invoiceNo,
                    'sale_date' => $saleDate,
                    'subtotal' => $subtotal,
                    'payment_method' => $payMethod,
                    'discount_amount' => $discountAmount,
                    'discount_type' => $discountType,
                    'discount_holder' => $discountHolder,
                    'discount_id_no' => $discountIdNo,
                    'tax_amount' => $taxAmount,
                    'total_amount' => $totalAmount,
                    'reference_number' => in_array($payMethod, ['gcash', 'maya', 'card']) ? 'REF-' . rand(10000000, 99999999) : null,
                    'notes' => 'POS terminal sale',
                    'sale_status' => 'completed',
                    'created_by' => $userId,
                    'created_at' => $saleDate,
                    'updated_at' => $saleDate,
                    'status' => 'active',
                ]);

                // Create Sale Items
                foreach ($itemsPayload as $item) {
                    $item['sale_id'] = $sale->id;
                    $item['created_by'] = $userId;
                    $item['created_at'] = $saleDate;
                    $item['updated_at'] = $saleDate;
                    $item['status'] = 'active';
                    POSSaleItem::create($item);

                    // Seed Stock Deduction Entry
                    Stocks::create([
                        'tenant_id' => $tenantId,
                        'product_id' => $item['product_id'],
                        'transaction_type' => Stocks::TYPE_OUT,
                        'quantity' => $item['qty'],
                        'stock_before' => 100.00,
                        'stock_after' => 100.00 - $item['qty'],
                        'unit_cost' => $item['unit_price'] * 0.8,
                        'reference_type' => 'sale',
                        'reference_id' => $sale->id,
                        'remarks' => "Sale #{$sale->sale_code}",
                        'created_by' => $userId,
                        'created_at' => $saleDate,
                        'status' => 'active',
                    ]);
                }

                // Create Payment Record
                POSPayment::create([
                    'tenant_id' => $tenantId,
                    'sale_id' => $sale->id,
                    'customer_id' => $selectedCustomer?->id ?? 0,
                    'terminal_id' => $terminal->id,
                    'drawer_id' => $drawer->id,
                    'shift_id' => $shift->id,
                    'payment_method' => $payMethod,
                    'amount' => $totalAmount,
                    'tendered_amount' => $tendered,
                    'change_amount' => $change,
                    'reference_number' => $sale->reference_number,
                    'payment_date' => $saleDate,
                    'created_by' => $userId,
                    'created_at' => $saleDate,
                    'updated_at' => $saleDate,
                    'status' => 'active',
                ]);
            }

            // 6. Seed Restock Activities in Stocks table
            foreach ($products->take(25) as $p) {
                Stocks::create([
                    'tenant_id' => $tenantId,
                    'product_id' => $p->id,
                    'transaction_type' => Stocks::TYPE_IN,
                    'quantity' => rand(50, 200),
                    'stock_before' => 10.00,
                    'stock_after' => 10.00 + rand(50, 200),
                    'unit_cost' => (float)$p->cost_price ?: 35.00,
                    'reference_type' => 'supplier_receive',
                    'reference_id' => rand(100, 999),
                    'remarks' => 'Direct supplier batch delivery',
                    'created_by' => $userId,
                    'created_at' => Carbon::now()->subDays(rand(1, 25)),
                    'status' => 'active',
                ]);
            }
        }

        $this->command->info('✅ Enterprise POS CRM Seeder successfully generated complete customer CRM, 30-day sales, ledgers, and stock analytics data!');
    }
}
