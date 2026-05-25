<?php

namespace App\Traits;

trait Tenantable
{
    protected static function bootTenantable(): void
    {
        static::creating(function ($model) {

            if (
                session()->has('school_id') &&
                empty($model->school_id)
            ) {

                $model->school_id =
                    session('school_id');

            }

        });

        static::addGlobalScope(
            new \App\Scopes\TenantScope
        );
    }
}
