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
        'pending_plan_id',
        'business_name',
        'business_code',
        'owner_name',
        'email',
        'phone',
        'address',
        'tin',
        'branch_code',
        'bir_acc_no',
        'bir_acc_date',
        'bir_min',
        'bir_sn',
        'header_text',
        'footer_text',
        'currency_symbol',
        'theme_settings',
        'crm_settings',
        'logo',
        'subscription_start',
        'subscription_end',
        'trial_ends_at',
        'status',
        'payment_status',
        'payment_method',
        'payment_reference',
        'payment_proof',
        'payment_sender_name',
        'payment_sender_phone',
        'payment_amount',
        'paid_at',
        'payment_submitted_at',
        'payment_notes',
        'archived',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'theme_settings' => 'array',
        'crm_settings' => 'array',
        'subscription_start' => 'date',
        'subscription_end' => 'date',
        'trial_ends_at' => 'datetime',
        'paid_at' => 'datetime',
        'payment_submitted_at' => 'datetime',
        'payment_amount' => 'decimal:2',
        'bir_acc_date' => 'date',
        'archived' => 'boolean',
    ];

    public function branches()
    {
        return $this->hasMany(POSBranch::class, 'tenant_id');
    }

    public function mainBranch()
    {
        return $this->hasOne(POSBranch::class, 'tenant_id')->where('is_main_branch', true);
    }

    public function subscription()
    {
        return $this->belongsTo(
            POSSubscription::class,
            'subscription_id'
        );
    }

    public function pendingPlan()
    {
        return $this->belongsTo(
            POSSubscription::class,
            'pending_plan_id'
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

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid' || $this->status === 'active';
    }

    public function isPendingPayment(): bool
    {
        return ($this->payment_status === 'pending' || $this->payment_status === 'pending_verification') && $this->status !== 'active';
    }

    public function isPendingVerification(): bool
    {
        return $this->payment_status === 'pending_verification';
    }

    public function isPaymentRejected(): bool
    {
        return $this->payment_status === 'rejected';
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
