<?php

namespace App\Models\POS;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Stocks extends Model
{
    use HasFactory;

    protected $table = 'pos_stock_transactions';

    protected $fillable = [
        'tenant_id',
        'product_id',
        'variant_id',
        'transaction_type',
        'quantity',
        'stock_before',
        'stock_after',
        'unit_cost',
        'reference_type',
        'reference_id',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'stock_before' => 'decimal:2',
        'stock_after' => 'decimal:2',
        'unit_cost' => 'decimal:2',
    ];

    public const TYPE_IN = 'IN';

    public const TYPE_OUT = 'OUT';

    public const TYPE_ADJUSTMENT = 'ADJUSTMENT';

    public const TYPE_RETURN_IN = 'RETURN_IN';

    public const TYPE_RETURN_OUT = 'RETURN_OUT';

    public function product()
    {
        return $this->belongsTo(
            POSProducts::class,
            'product_id'
        );
    }

    public function variant()
    {
        return $this->belongsTo(
            POSProductVariant::class,
            'variant_id'
        );
    }

    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function reference()
    {
        return $this->morphTo(
            __FUNCTION__,
            'reference_type',
            'reference_id'
        );
    }

    public function scopeStockIn($query)
    {
        return $query->where(
            'transaction_type',
            self::TYPE_IN
        );
    }

    public function scopeStockOut($query)
    {
        return $query->where(
            'transaction_type',
            self::TYPE_OUT
        );
    }

    public function scopeAdjustments($query)
    {
        return $query->where(
            'transaction_type',
            self::TYPE_ADJUSTMENT
        );
    }

    public function isStockIn(): bool
    {
        return $this->transaction_type === self::TYPE_IN;
    }

    public function isStockOut(): bool
    {
        return $this->transaction_type === self::TYPE_OUT;
    }
}
