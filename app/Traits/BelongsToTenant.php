<?php

namespace App\Traits;

use App\Models\POS\POSTenant;
use App\Scopes\TenantScope;
use App\Services\Tenant\TenantContext;

trait BelongsToTenant
{
    /**
     * Boot the BelongsToTenant trait for a model.
     */
    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope());

        static::creating(function ($model) {
            if (empty($model->tenant_id) && TenantContext::hasTenant()) {
                $model->tenant_id = TenantContext::getTenantId();
            }
        });
    }

    /**
     * Relationship to the tenant that owns this record.
     */
    public function tenant()
    {
        return $this->belongsTo(POSTenant::class, 'tenant_id');
    }

    /**
     * Scope to bypass tenant global scope (for platform/super-admin operations).
     */
    public function scopeWithoutTenant($query)
    {
        return $query->withoutGlobalScope(TenantScope::class);
    }

    /**
     * Scope query to a specific tenant explicitly.
     */
    public function scopeForTenant($query, int $tenantId)
    {
        return $query->withoutGlobalScope(TenantScope::class)
            ->where($this->getTable() . '.tenant_id', $tenantId);
    }
}
