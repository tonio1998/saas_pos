<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('pos_subscriptions')) {
            Schema::table('pos_subscriptions', function (Blueprint $table) {
                if (!Schema::hasColumn('pos_subscriptions', 'max_customers')) {
                    $table->unsignedInteger('max_customers')->nullable()->after('max_products');
                }
                if (!Schema::hasColumn('pos_subscriptions', 'inclusions')) {
                    $table->json('inclusions')->nullable()->after('max_storage_mb');
                }
                if (!Schema::hasColumn('pos_subscriptions', 'limitations')) {
                    $table->json('limitations')->nullable()->after('inclusions');
                }
            });

            // 1. Tindahan Starter (Basic: ₱300/mo - Masa-Friendly)
            DB::table('pos_subscriptions')->where('id', 1)->update([
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
                'inclusions' => json_encode([
                    'Lightning Barcode Scanning',
                    'Real-Time Inventory & Auto-Deduct',
                    'Suki Utang & Repayment Ledger',
                    'Cash Drawer Auto-Pop Trigger',
                    'Daily Gross Sales & Shift Summary',
                    '58mm / 80mm Receipt Printing',
                    'Standard Technical Support',
                ]),
                'limitations' => json_encode([
                    'Hanggang 1,000 Products / SKUs',
                    'Hanggang 300 Suki / Credit Profiles',
                    '1 Store Branch Lamang',
                ]),
            ]);

            // 2. Suki Growth (Pro: ₱600/mo - Multi-shift Minimarts)
            DB::table('pos_subscriptions')->where('id', 2)->update([
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
                'inclusions' => json_encode([
                    'Lahat ng nasa Basic',
                    'Purchase Journal & Supplier Invoices',
                    'Cash Receipt & Cash Disbursement',
                    'Staff Anti-Kupit Void PIN Override',
                    'Cashier Shift Turnover & Drawer Audit',
                    'Net Profit & Margin Analytics',
                    'Top-Moving Product Insights',
                    'Priority Technical Support',
                ]),
                'limitations' => json_encode([
                    'Hanggang 5,000 Products / SKUs',
                    'Hanggang 2,000 Suki / Credit Profiles',
                    '1 Main Store Branch',
                ]),
            ]);

            // 3. Negosyo Pro (Enterprise Cloud)
            DB::table('pos_subscriptions')->where('id', 3)->update([
                'name' => 'Negosyo Pro',
                'description' => 'Enterprise-grade cloud POS para sa supermarket chains at wholesalers',
                'price' => 1299.00,
                'billing_cycle' => 'monthly',
                'duration_days' => 30,
                'max_users' => 25,
                'max_admin_accounts' => 5,
                'max_cashier_accounts' => 20,
                'max_products' => 50000,
                'max_customers' => null,
                'max_branches' => 5,
                'allow_multi_branch' => 1,
                'inclusions' => json_encode([
                    'Lahat ng nasa Pro',
                    'Unlimited Products & Barcodes (50k+ SKUs)',
                    'Unlimited Suki Customers & Suppliers',
                    'Multi-Branch Support (Up to 5 Branches Included)',
                    'Multi-Branch Central Consolidated Dashboard',
                    'Central Warehouse & Inter-Branch Stock Transfers',
                    'Unlimited Administrator & Cashier Accounts',
                    'BIR Compliance Support & Data Export',
                    'Dedicated Cloud Server & 24/7 VIP Support',
                ]),
                'limitations' => json_encode([]),
            ]);

            // 4. Free Trial Tier
            DB::table('pos_subscriptions')->where('id', 4)->update([
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
                'inclusions' => json_encode([
                    'Lightning Barcode Scanning',
                    'Real-Time Inventory Tracking',
                    'Suki Utang & Credit Ledger',
                    'Thermal Receipt Printing',
                    'Daily Sales Summary',
                ]),
                'limitations' => json_encode([
                    'Hanggang 300 Products',
                    'Hanggang 100 Suki Profiles',
                    '14 Days Trial Period',
                ]),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('pos_subscriptions')) {
            Schema::table('pos_subscriptions', function (Blueprint $table) {
                if (Schema::hasColumn('pos_subscriptions', 'limitations')) {
                    $table->dropColumn('limitations');
                }
                if (Schema::hasColumn('pos_subscriptions', 'inclusions')) {
                    $table->dropColumn('inclusions');
                }
                if (Schema::hasColumn('pos_subscriptions', 'max_customers')) {
                    $table->dropColumn('max_customers');
                }
            });
        }
    }
};
