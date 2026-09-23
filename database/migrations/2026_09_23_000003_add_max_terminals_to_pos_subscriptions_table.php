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
                if (!Schema::hasColumn('pos_subscriptions', 'max_terminals')) {
                    $table->unsignedInteger('max_terminals')->default(1)->after('max_cashier_accounts');
                }
            });

            // Update plan tiers for optimized monetization
            // 1. Free Trial Tier -> 1 Terminal, 1 Cashier, 300 Products
            DB::table('pos_subscriptions')->where('id', 4)->update([
                'name' => 'Free Trial Tier (14 Days)',
                'description' => '14-Day Full Access Free Trial for single counter retail stores (1 POS Terminal, 1 Cashier)',
                'price' => 0.00,
                'duration_days' => 14,
                'max_users' => 2,
                'max_admin_accounts' => 1,
                'max_cashier_accounts' => 1,
                'max_terminals' => 1,
                'max_products' => 300,
                'max_branches' => 1,
                'allow_multi_branch' => 0,
            ]);

            // 2. Tindahan Starter -> 1 Terminal, 2 Cashiers (morning/evening shift), 1,500 Products
            DB::table('pos_subscriptions')->where('id', 1)->update([
                'name' => 'Tindahan Starter',
                'description' => 'Perfect for single-register kiosks & botika (₱16/day • 1 POS Terminal, 2 Cashiers)',
                'price' => 499.00,
                'duration_days' => 30,
                'max_users' => 3,
                'max_admin_accounts' => 1,
                'max_cashier_accounts' => 2,
                'max_terminals' => 1,
                'max_products' => 1500,
                'max_branches' => 1,
                'allow_multi_branch' => 0,
            ]);

            // 3. Suki Growth (⭐ Most Popular Cash Cow) -> 3 Terminals (Counter 1, Counter 2, Mobile), 6 Cashiers, 10,000 Products
            DB::table('pos_subscriptions')->where('id', 2)->update([
                'name' => 'Suki Growth',
                'description' => 'Most popular for multi-counter minimarts (₱33/day • 3 POS Terminals, 6 Cashiers)',
                'price' => 999.00,
                'duration_days' => 30,
                'max_users' => 8,
                'max_admin_accounts' => 2,
                'max_cashier_accounts' => 6,
                'max_terminals' => 3,
                'max_products' => 10000,
                'max_branches' => 1,
                'allow_multi_branch' => 0,
            ]);

            // 4. Negosyo Pro (Enterprise Whale Plan) -> 10 Terminals, Up to 5 Branches, 50,000 Products, 20 Cashiers
            DB::table('pos_subscriptions')->where('id', 3)->update([
                'name' => 'Negosyo Pro',
                'description' => 'Enterprise plan for multi-branch chains (₱66/day • 10 POS Terminals, 5 Branches, 20 Cashiers)',
                'price' => 1999.00,
                'duration_days' => 30,
                'max_users' => 25,
                'max_admin_accounts' => 5,
                'max_cashier_accounts' => 20,
                'max_terminals' => 10,
                'max_products' => 50000,
                'max_branches' => 5,
                'allow_multi_branch' => 1,
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
                if (Schema::hasColumn('pos_subscriptions', 'max_terminals')) {
                    $table->dropColumn('max_terminals');
                }
            });
        }
    }
};
