<?php

namespace App\Traits;

trait Tenantable
{
    protected static function bootTenantable(): void
    {
        static::creating(function ($model) {
            $tenantId = session('tenant_id') ?? \App\Services\Tenant\TenantContext::getTenantId() ?? auth()->user()?->tenant_id;
            if ($tenantId && empty($model->tenant_id)) {
                $model->tenant_id = $tenantId;
            }
        });

        static::addGlobalScope(
            new \App\Scopes\TenantScope
        );
    }
}
