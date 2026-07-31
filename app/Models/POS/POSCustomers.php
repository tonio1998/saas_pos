<?php

namespace App\Models\POS;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class POSCustomers extends Model
{
    use SoftDeletes;

    protected $table = 'pos_customers';

    protected $fillable = [
        'tenant_id',
        'customer_code',
        'CustomerName',
        'TotalPoints',
        'CustomerAddress',
        'company_name',
        'email',
        'mobile_number',
        'address',
        'customer_type',
        'discount_percent',
        'credit_limit',
        'current_balance',
        'remarks',
        'created_by',
        'updated_by',
        'status',
        'archived'
    ];

    protected $casts = [
        'discount_percent' => 'decimal:2',
        'credit_limit' => 'decimal:2',
        'current_balance' => 'decimal:2'
    ];

    public function createdBy()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function getFullNameAttribute()
    {
        return trim(
            $this->first_name . ' ' .
            $this->middle_name . ' ' .
            $this->last_name
        );
    }

    public function credit()
    {
        return $this->hasOne(POSCustomerLedger::class, 'customer_id')
            ->latestOfMany();
    }

    public function ledger()
    {
        return $this->hasMany(POSCustomerLedger::class, 'customer_id');
    }
}
