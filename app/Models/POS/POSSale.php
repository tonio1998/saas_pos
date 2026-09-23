<?php

namespace App\Models\POS;

use App\Models\User;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class POSSale extends Model
{
    use SoftDeletes, BelongsToTenant;

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
            if (empty($sale->sale_status)) {
                $sale->sale_status = 'pending';
            }
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

    public function getRefundSales()
    {
        $ref1 = $this->invoice_no;
        $ref2 = $this->sale_code;

        if (!$ref1 && !$ref2) {
            return collect();
        }

        return static::where('tenant_id', $this->tenant_id)
            ->where('sale_status', 'refund')
            ->where(function ($q) use ($ref1, $ref2) {
                if ($ref1) {
                    $q->where('reference_number', $ref1)
                      ->orWhere('notes', 'LIKE', '%[Ref: ' . $ref1 . ']%');
                }
                if ($ref2) {
                    $q->orWhere('reference_number', $ref2)
                      ->orWhere('notes', 'LIKE', '%[Ref: ' . $ref2 . ']%');
                }
            })
            ->with(['items'])
            ->get();
    }

    public function getRefundSummary(): array
    {
        $refundSales = $this->getRefundSales();
        $refundedAmount = abs((float) $refundSales->sum('total_amount'));
        $refundedQty = 0;
        $returnedMap = [];

        foreach ($refundSales as $rs) {
            foreach ($rs->items as $item) {
                $qty = abs((float) $item->qty);
                $refundedQty += $qty;
                $key = ($item->product_id ?? 0) . '_' . ($item->variant_id ?? 0);
                $returnedMap[$key] = ($returnedMap[$key] ?? 0) + $qty;
            }
        }

        return [
            'refund_sales'    => $refundSales,
            'refunded_amount' => $refundedAmount,
            'refunded_qty'    => $refundedQty,
            'returned_map'    => $returnedMap,
        ];
    }
}
