<?php

namespace App\Models\POS;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class POSTenant extends Model
{
    use SoftDeletes;

    protected $table = 'pos_tenants';

    protected $fillable = [
        'subscription_id',
        'business_name',
        'business_code',
        'owner_name',
        'email',
        'phone',
        'address',
        'logo',
        'subscription_start',
        'subscription_end',
        'trial_ends_at',
        'status',
        'archived',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'subscription_start' => 'date',
        'subscription_end' => 'date',
        'trial_ends_at' => 'datetime',
        'archived' => 'boolean',
    ];

    public function subscription()
    {
        return $this->belongsTo(
            PosSubscription::class,
            'subscription_id'
        );
    }

    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function updater()
    {
        return $this->belongsTo(
            User::class,
            'updated_by'
        );
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isLocked(): bool
    {
        return $this->status === 'locked';
    }

    public function isExpired(): bool
    {
        return $this->subscription_end &&
            now()->greaterThan(
                $this->subscription_end
            );
    }

    public function isTrialExpired(): bool
    {
        return $this->trial_ends_at &&
            now()->greaterThan(
                $this->trial_ends_at
            );
    }

    public function products()
    {
        return $this->hasMany(
            POSProducts::class,
            'tenant_id'
        );
    }

    public function users()
    {
        return $this->hasMany(
            User::class,
            'tenant_id'
        );
    }
}
