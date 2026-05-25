<?php

namespace App\Traits;

trait UserTenantable
{
    protected static function bootUserTenantable(): void
    {
        static::creating(function ($model) {

            if (
                auth()->check() &&
                empty($model->school_id)
            ) {

                $model->school_id = auth()->user()->school_id;
            }
        });
    }
}
