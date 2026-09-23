<?php

namespace App\Services\Tenant;

use App\Models\POS\POSTenant;

class TenantContext
{
    /**
     * Explicit tenant ID override (useful for artisan commands, queues, or switching tenants)
     */
    protected static ?int $overrideTenantId = null;

    /**
     * Get the currently active tenant ID
     */
    public static function getTenantId(): ?int
    {
        if (static::$overrideTenantId !== null) {
            return static::$overrideTenantId;
        }

        if (auth()->check() && auth()->user()->tenant_id) {
            return (int) auth()->user()->tenant_id;
        }

        if (function_exists('session') && session()->has('tenant_id')) {
            $sessId = session('tenant_id');
            if ($sessId) {
                return (int) $sessId;
            }
        }

        return null;
    }

    /**
     * Check if there is an active tenant context
     */
    public static function hasTenant(): bool
    {
        return static::getTenantId() !== null;
    }

    /**
     * Get the active POSTenant model instance
     */
    public static function getTenant(): ?POSTenant
    {
        $id = static::getTenantId();
        if (!$id) {
            return null;
        }

        return POSTenant::find($id);
    }

    /**
     * Set a temporary explicit tenant ID
     */
    public static function setTenantId(?int $tenantId): void
    {
        static::$overrideTenantId = $tenantId;
    }

    /**
     * Clear explicit tenant ID override
     */
    public static function clear(): void
    {
        static::$overrideTenantId = null;
    }

    /**
     * Execute a callback in the context of a specific tenant
     */
    public static function runAs(int $tenantId, callable $callback)
    {
        $previous = static::$overrideTenantId;
        static::$overrideTenantId = $tenantId;

        try {
            return $callback();
        } finally {
            static::$overrideTenantId = $previous;
        }
    }
}
