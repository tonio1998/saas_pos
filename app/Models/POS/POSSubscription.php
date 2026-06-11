<?php

namespace App\Models\POS;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class POSSubscription extends Model
{
    protected $table = 'pos_subscriptions';

    protected $fillable = [
        'tenant_id',
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

    public function tenant()
    {
        return $this->belongsTo(
            POSTenant::class,
            'tenant_id'
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
}
