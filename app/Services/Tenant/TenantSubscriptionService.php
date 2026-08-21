<?php

namespace App\Services\Tenant;

use App\Models\POS\POSTenant;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class TenantSubscriptionService
{
    /**
     * Get cached subscription limits for a tenant.
     * High performance caching for enterprise scalability.
     */
    public function getTenantSubscription(int $tenantId)
    {
        return Cache::remember("tenant_{$tenantId}_sub_limits", 3600, function () use ($tenantId) {
            $tenant = POSTenant::with('subscription')->find($tenantId);
            return $tenant?->subscription;
        });
    }

    /**
     * Clear cached subscription limits when a tenant upgrades or modifies subscription.
     */
    public function clearTenantCache(int $tenantId): void
    {
        Cache::forget("tenant_{$tenantId}_sub_limits");
    }

    /**
     * Check if a tenant can create a new user for a specific role based on subscription limits.
     *
     * @param int $tenantId
     * @param string $role (e.g. 'admin', 'tenant', 'cashier', 'manager')
     * @return array ['allowed' => bool, 'message' => string, 'current' => int, 'limit' => int]
     */
    public function canCreateUser(int $tenantId, string $role): array
    {
        $subscription = $this->getTenantSubscription($tenantId);

        // Default fallback limits if no subscription record assigned yet
        $maxAdmins = $subscription?->max_admin_accounts ?? 1;
        $maxCashiers = $subscription?->max_cashier_accounts ?? 1;
        $maxTotalUsers = $subscription?->max_users ?? 2;

        $planName = $subscription?->name ?? 'Level I (Basic)';

        // Standardize role check
        $normalizedRole = strtolower(trim($role));
        $isAdminRole = in_array($normalizedRole, ['admin', 'tenant', 'owner', 'super_admin']);
        $isCashierRole = in_array($normalizedRole, ['cashier', 'cashier_user', 'staff']);

        if ($isAdminRole) {
            $currentCount = User::where('tenant_id', $tenantId)
                ->whereHas('roles', function ($q) {
                    $q->whereIn('name', ['admin', 'tenant', 'owner']);
                })
                ->count();

            if ($currentCount >= $maxAdmins) {
                return [
                    'allowed' => false,
                    'message' => "Your current subscription plan ({$planName}) allows up to {$maxAdmins} Admin account(s). You currently have {$currentCount}. Please upgrade your subscription to add more Admin accounts.",
                    'current' => $currentCount,
                    'limit' => $maxAdmins,
                ];
            }
        } elseif ($isCashierRole) {
            $currentCount = User::where('tenant_id', $tenantId)
                ->whereHas('roles', function ($q) {
                    $q->whereIn('name', ['cashier', 'cashier_user', 'staff']);
                })
                ->count();

            if ($currentCount >= $maxCashiers) {
                return [
                    'allowed' => false,
                    'message' => "Your current subscription plan ({$planName}) allows up to {$maxCashiers} Cashier account(s). You currently have {$currentCount}. Please upgrade your subscription to Level II or III to add more Cashier accounts.",
                    'current' => $currentCount,
                    'limit' => $maxCashiers,
                ];
            }
        }

        // Check total user limit fallback
        $totalUsers = User::where('tenant_id', $tenantId)->count();
        if ($totalUsers >= $maxTotalUsers) {
            return [
                'allowed' => false,
                'message' => "Your subscription plan ({$planName}) max user limit ({$maxTotalUsers}) has been reached.",
                'current' => $totalUsers,
                'limit' => $maxTotalUsers,
            ];
        }

        return [
            'allowed' => true,
            'message' => 'User creation permitted.',
            'current' => $isAdminRole ? ($currentCount ?? 0) : ($currentCount ?? 0),
            'limit' => $isAdminRole ? $maxAdmins : $maxCashiers,
        ];
    }

    /**
     * Get complete subscription usage summary for tenant dashboard.
     */
    public function getUsageSummary(int $tenantId): array
    {
        $subscription = $this->getTenantSubscription($tenantId);

        $adminCount = User::where('tenant_id', $tenantId)
            ->whereHas('roles', fn($q) => $q->whereIn('name', ['admin', 'tenant', 'owner']))
            ->count();

        $cashierCount = User::where('tenant_id', $tenantId)
            ->whereHas('roles', fn($q) => $q->whereIn('name', ['cashier', 'cashier_user', 'staff']))
            ->count();

        return [
            'plan_name' => $subscription?->name ?? 'Level I (Basic)',
            'admins' => [
                'current' => $adminCount,
                'limit' => $subscription?->max_admin_accounts ?? 1,
            ],
            'cashiers' => [
                'current' => $cashierCount,
                'limit' => $subscription?->max_cashier_accounts ?? 1,
            ],
            'total_users' => [
                'current' => User::where('tenant_id', $tenantId)->count(),
                'limit' => $subscription?->max_users ?? 2,
            ],
        ];
    }

    /**
     * Check if a tenant can register a new POS Device based on subscription limits.
     *
     * @param int $tenantId
     * @return array ['allowed' => bool, 'message' => string, 'current' => int, 'limit' => int]
     */
    public function canCreateDevice(?int $tenantId): array
    {
        if (!$tenantId) {
            return [
                'allowed' => true,
                'message' => 'Device registration permitted.',
                'current' => 0,
                'limit' => 1,
                'plan_name' => 'Tindahan Starter',
            ];
        }

        $subscription = $this->getTenantSubscription($tenantId);
        $planName = $subscription?->name ?? 'Tindahan Starter';
        $planTier = strtolower($planName);

        if (str_contains($planTier, 'starter') || str_contains($planTier, 'level i')) {
            $maxDevices = 1;
        } elseif (str_contains($planTier, 'growth') || str_contains($planTier, 'level ii')) {
            $maxDevices = 3;
        } else {
            $maxDevices = 20;
        }

        $currentDevices = \App\Models\POS\POSTerminal::where('tenant_id', $tenantId)
            ->where('archived', 0)
            ->count();

        if ($currentDevices >= $maxDevices) {
            return [
                'allowed' => false,
                'message' => "Nakarating na kayo sa maximum limit na {$maxDevices} POS Device(s) para sa inyong {$planName} Plan. Mag-upgrade sa mas mataas na plan para makapag-add ng karagdagang POS Device.",
                'current' => $currentDevices,
                'limit' => $maxDevices,
                'plan_name' => $planName,
            ];
        }

        return [
            'allowed' => true,
            'message' => 'Device registration permitted.',
            'current' => $currentDevices,
            'limit' => $maxDevices,
            'plan_name' => $planName,
        ];
    }
}
