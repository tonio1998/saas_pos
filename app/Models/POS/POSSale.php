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
        'sale_code',
        'customer_id',
        'cashier_id',
        'terminal_id',
        'invoice_no',
        'cash_shift_id',
        'subtotal',
        'vatable_sales',
        'vat_exempt_sales',
        'zero_rated_sales',
        'discount_amount',
        'payment_method',
        'reference_number',
        'notes',
        'tax_amount',
        'total_amount',
        'tendered_amount',
        'change_amount',
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
        'vatable_sales' => 'decimal:2',
        'vat_exempt_sales' => 'decimal:2',
        'zero_rated_sales' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'tendered_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::creating(function ($sale) {
            $sale->sale_code = generateSalesCode($sale->tenant_id);
        });
    }

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

    public function tenant()
    {
        return $this->belongsTo(
            POSTenant::class,
            'tenant_id'
        );
    }

    public function scopeCompleted($query)
    {
        return $query->where('sale_status', 'completed');
    }

    public function scopeActive($query)
    {
        return $query->where('sale_status', 'active');
    }

    public function terminal()
    {
        return $this->belongsTo(
            POSTerminal::class,
            'terminal_id'
        );
    }

    public function cashShift()
    {
        return $this->belongsTo(
            POSCashShift::class,
            'cash_shift_id'
        );
    }

    public function cashMovements()
    {
        return $this->hasMany(
            POSCashMovement::class,
            'sale_id'
        );
    }


    public function drawer()
    {
        return $this->belongsTo(
            POSCashDrawer::class,
            'drawer_id'
        );
    }


}
