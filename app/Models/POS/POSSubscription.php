<?php

namespace App\Models\POS;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class POSSubscription extends Model
{
    protected $table = 'pos_subscriptions';

    protected $fillable = [
        'name',
        'description',
        'price',
        'is_promo',
        'promo_price',
        'promo_code',
        'badge_text',
        'promo_expires_at',
        'featured',
        'billing_cycle',
        'duration_days',
        'max_users',
        'max_admin_accounts',
        'max_cashier_accounts',
        'max_terminals',
        'max_products',
        'max_customers',
        'max_branches',
        'max_storage_mb',
        'inclusions',
        'limitations',
        'allow_inventory',
        'allow_reports',
        'allow_multi_branch',
        'allow_api_access',
        'trial_days',
        'sort_order',
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

    protected $casts = [
        'inclusions'       => 'array',
        'limitations'      => 'array',
        'price'            => 'float',
        'promo_price'      => 'float',
        'is_promo'         => 'boolean',
        'featured'         => 'boolean',
        'max_terminals'    => 'integer',
        'max_products'     => 'integer',
        'max_customers'    => 'integer',
        'max_users'        => 'integer',
        'max_cashier_accounts' => 'integer',
        'max_admin_accounts'   => 'integer',
        'max_branches'     => 'integer',
        'duration_days'    => 'integer',
        'promo_expires_at' => 'datetime',
    ];

    public function effectivePrice(): float
    {
        if ($this->is_promo && $this->promo_price !== null && $this->promo_price > 0) {
            return (float) $this->promo_price;
        }
        return (float) $this->price;
    }

    public function isExpiredPromo(): bool
    {
        return $this->is_promo && $this->promo_expires_at && now()->greaterThan($this->promo_expires_at);
    }

    public function tenants()
    {
        return $this->hasMany(POSTenant::class, 'subscription_id');
    }

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
