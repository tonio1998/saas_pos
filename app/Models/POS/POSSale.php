<?php

namespace App\Models\POS;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class POSSale extends Model
{
    use SoftDeletes;

    protected $table = 'pos_sales';

    protected $fillable = [
        'tenant_id',
        'customer_id',
        'cashier_id',
        'invoice_no',
        'subtotal',
        'discount_amount',
        'payment_method',
        'reference_number',
        'notes',
        'tax_amount',
        'total_amount',
        'sale_status',
        'sale_date',
        'created_by',
        'updated_by',
        'discount_type',
        'discount_mode',
        'discount_value',
        'discount_holder',
        'discount_id_no',
    ];

    protected $casts = [
        'sale_date' => 'datetime',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function cashier()
    {
        return $this->belongsTo(
            User::class,
            'cashier_id'
        );
    }

    public function items()
    {
        return $this->hasMany(
            POSSaleItem::class,
            'sale_id'
        );
    }

    public function payments()
    {
        return $this->hasMany(
            POSPayment::class,
            'sale_id'
        );
    }
    public function customer()
    {
        return $this->belongsTo(
            POSCustomers::class,
            'customer_id'
        );
    }
}
