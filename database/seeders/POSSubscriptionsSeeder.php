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
                'name' => 'Free Trial Tier (5 Days)',
                'description' => '5-Day Full Access Free Trial for new retail stores & minimarts (1 Admin, 2 Cashiers)',
                'price' => 0.00,
                'billing_cycle' => 'monthly',
                'duration_days' => 5,
                'max_users' => 3,
                'max_admin_accounts' => 1,
                'max_cashier_accounts' => 2,
                'max_products' => 500,
                'max_branches' => 1,
                'allow_inventory' => 1,
                'allow_reports' => 1,
                'allow_multi_branch' => 0,
                'allow_api_access' => 1,
                'status' => 'active',
                'sort_order' => 0,
            ],
            [
                'id' => 1,
                'name' => 'Tindahan Starter',
                'description' => 'Affordable plan for single-register stores (₱10/day • 1 Admin, 1 Cashier account)',
                'price' => 299.00,
                'billing_cycle' => 'monthly',
                'duration_days' => 30,
                'max_users' => 2,
                'max_admin_accounts' => 1,
                'max_cashier_accounts' => 1,
                'max_products' => 1000,
                'max_branches' => 1,
                'allow_inventory' => 1,
                'allow_reports' => 1,
                'allow_multi_branch' => 0,
                'allow_api_access' => 1,
                'status' => 'active',
                'sort_order' => 1,
            ],
            [
                'id' => 2,
                'name' => 'Suki Growth',
                'description' => 'Most popular plan for multi-shift minimarts (₱20/day • 1 Admin, 3 Cashier accounts)',
                'price' => 599.00,
                'billing_cycle' => 'monthly',
                'duration_days' => 30,
                'max_users' => 4,
                'max_admin_accounts' => 1,
                'max_cashier_accounts' => 3,
                'max_products' => 5000,
                'max_branches' => 1,
                'allow_inventory' => 1,
                'allow_reports' => 1,
                'allow_multi_branch' => 0,
                'allow_api_access' => 1,
                'status' => 'active',
                'sort_order' => 2,
            ],
            [
                'id' => 3,
                'name' => 'Negosyo Pro',
                'description' => 'Enterprise plan for multi-branch minimart chains (₱43/day • 5 Admins, 20 Cashiers)',
                'price' => 1299.00,
                'billing_cycle' => 'monthly',
                'duration_days' => 30,
                'max_users' => 25,
                'max_admin_accounts' => 5,
                'max_cashier_accounts' => 20,
                'max_products' => 50000,
                'max_branches' => 5,
                'allow_inventory' => 1,
                'allow_reports' => 1,
                'allow_multi_branch' => 1,
                'allow_api_access' => 1,
                'status' => 'active',
                'sort_order' => 3,
            ],
        ];

        foreach ($plans as $planData) {
            POSSubscription::updateOrCreate(['id' => $planData['id']], $planData);
        }
    }
}
