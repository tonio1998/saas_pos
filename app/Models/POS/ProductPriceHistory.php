<?php

namespace App\Models\POS;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductPriceHistory extends Model
{
    use SoftDeletes;

    protected $table = 'pos_product_price_histories';

    protected $fillable = [
        'tenant_id',
        'product_id',
        'variant_id',

        'cost_price',
        'new_cost_price',

        'selling_price',
        'new_selling_price',

        'wholesale_price',
        'new_wholesale_price',

        'reason',
        'remarks',
        'effective_date',

        'status',
        'archived',

        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'new_cost_price' => 'decimal:2',

        'selling_price' => 'decimal:2',
        'new_selling_price' => 'decimal:2',

        'wholesale_price' => 'decimal:2',
        'new_wholesale_price' => 'decimal:2',

        'effective_date' => 'datetime',

        'archived' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(POSProducts::class, 'product_id');
    }

    public function variant()
    {
        return $this->belongsTo(POSProductVariant::class, 'variant_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }
}
