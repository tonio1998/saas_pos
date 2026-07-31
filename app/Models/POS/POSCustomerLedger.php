<?php

namespace App\Models\POS;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class POSCustomerLedger extends Model
{
    use SoftDeletes;

    protected $table = 'pos_customer_ledgers';

    protected $fillable = [
        'tenant_id',
        'customer_id',
        'sale_id',
        'payment_id',
        'reference_no',
        'transaction_type',
        'debit',
        'credit',
        'running_balance',
        'remarks',
        'created_by',
        'updated_by',
        'created_at',
        'status',
        'archived',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'archived' => 'integer',
    ];

    public function createdBy()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function updatedBy()
    {   return $this->belongsTo(
            User::class,
            'updated_by'
        );
    }

    public function customer()
    {
        return $this->belongsTo(
            POSCustomers::class,
            'customer_id'
        );
    }

    public function sale()
    {
        return $this->belongsTo(
            POSSale::class,
            'sale_id'
        );
    }
}
