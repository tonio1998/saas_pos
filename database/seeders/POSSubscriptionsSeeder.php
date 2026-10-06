<?php

namespace Database\Seeders;

use App\Models\POS\POSSubscription;
use Illuminate\Database\Seeder;

class POSSubscriptionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $posRoles = ['SA', 'tenant', 'admin', 'cashier', 'manager'];
        foreach ($posRoles as $roleName) {
            \Spatie\Permission\Models\Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }
        $plans = [
            [
                'id' => 4,
                'name' => 'Free Trial Tier (14 Days)',
                'description' => '14-Day Full Access Free Trial for single counter retail stores',
                'price' => 0.00,
                'billing_cycle' => 'monthly',
                'duration_days' => 14,
                'max_users' => 2,
                'max_admin_accounts' => 1,
                'max_cashier_accounts' => 1,
                'max_products' => 300,
                'max_customers' => 100,
                'max_branches' => 1,
                'allow_inventory' => 1,
                'allow_reports' => 1,
                'allow_multi_branch' => 0,
                'allow_api_access' => 1,
                'inclusions' => [
                    'Lightning Barcode Scanning',
                    'Real-Time Inventory Tracking',
                    'Suki Utang & Credit Ledger',
                    'Thermal Receipt Printing',
                    'Daily Sales Summary',
                ],
                'limitations' => [
                    'Hanggang 300 Products',
                    'Hanggang 100 Suki Profiles',
                    '14 Days Trial Period',
                ],
                'status' => 'active',
                'sort_order' => 0,
            ],
            [
                'id' => 1,
                'name' => 'Tindahan Starter',
                'description' => 'Abot-kaya para sa sari-sari store, bakery, at nagsisimulang negosyo (₱10/araw)',
                'price' => 300.00,
                'billing_cycle' => 'monthly',
                'duration_days' => 30,
                'max_users' => 2,
                'max_admin_accounts' => 1,
                'max_cashier_accounts' => 1,
                'max_products' => 1000,
                'max_customers' => 300,
                'max_branches' => 1,
                'allow_inventory' => 1,
                'allow_reports' => 1,
                'allow_multi_branch' => 0,
                'allow_api_access' => 1,
                'inclusions' => [
                    'Lightning Barcode Scanning',
                    'Real-Time Inventory & Auto-Deduct',
                    'Suki Utang & Repayment Ledger',
                    'Cash Drawer Auto-Pop Trigger',
                    'Daily Gross Sales & Shift Summary',
                    '58mm / 80mm Receipt Printing',
                    'Standard Technical Support',
                ],
                'limitations' => [
                    'Hanggang 1,000 Products / SKUs',
                    'Hanggang 300 Suki / Credit Profiles',
                    '1 Store Branch Lamang',
                ],
                'status' => 'active',
                'sort_order' => 1,
            ],
            [
                'id' => 2,
                'name' => 'Suki Growth',
                'description' => 'Para sa lumalaking minimart na may multiple shifting at maraming items (₱20/araw)',
                'price' => 600.00,
                'billing_cycle' => 'monthly',
                'duration_days' => 30,
                'max_users' => 8,
                'max_admin_accounts' => 2,
                'max_cashier_accounts' => 6,
                'max_products' => 5000,
                'max_customers' => 2000,
                'max_branches' => 1,
                'allow_inventory' => 1,
                'allow_reports' => 1,
                'allow_multi_branch' => 0,
                'allow_api_access' => 1,
                'inclusions' => [
                    'Lahat ng nasa Basic',
                    'Purchase Journal & Supplier Invoices',
                    'Cash Receipt & Cash Disbursement',
                    'Staff Anti-Kupit Void PIN Override',
                    'Cashier Shift Turnover & Drawer Audit',
                    'Net Profit & Margin Analytics',
                    'Top-Moving Product Insights',
                    'Priority Technical Support',
                ],
                'limitations' => [
                    'Hanggang 5,000 Products / SKUs',
                    'Hanggang 2,000 Suki / Credit Profiles',
                    '1 Main Store Branch',
                ],
                'status' => 'active',
                'sort_order' => 2,
            ],
            [
                'id' => 3,
                'name' => 'Negosyo Pro',
                'description' => 'Enterprise-grade cloud POS para sa supermarket chains at wholesalers (₱43/araw)',
                'price' => 1299.00,
                'billing_cycle' => 'monthly',
                'duration_days' => 30,
                'max_users' => 25,
                'max_admin_accounts' => 5,
                'max_cashier_accounts' => 20,
                'max_products' => 50000,
                'max_customers' => null,
                'max_branches' => 5,
                'allow_inventory' => 1,
                'allow_reports' => 1,
                'allow_multi_branch' => 1,
                'allow_api_access' => 1,
                'inclusions' => [
                    'Lahat ng nasa Pro',
                    'Unlimited Products & Barcodes (50k+ SKUs)',
                    'Unlimited Suki Customers & Suppliers',
                    'Multi-Branch Support (Up to 5 Branches Included)',
                    'Multi-Branch Central Consolidated Dashboard',
                    'Central Warehouse & Inter-Branch Stock Transfers',
                    'Unlimited Administrator & Cashier Accounts',
                    'BIR Compliance Support & Data Export',
                    'Dedicated Cloud Server & 24/7 VIP Support',
                ],
                'limitations' => [],
                'status' => 'active',
                'sort_order' => 3,
            ],
        ];

        foreach ($plans as $planData) {
            POSSubscription::updateOrCreate(['id' => $planData['id']], $planData);
        }
    }
}
