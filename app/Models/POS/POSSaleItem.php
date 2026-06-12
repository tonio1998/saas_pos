<?php

namespace App\Models\POS;

use Illuminate\Database\Eloquent\Model;

class POSSaleItem extends Model
{
    protected $table = 'pos_sale_items';

    protected $fillable = [
        'sale_id',
        'product_id',
        'barcode',
        'sku',
        'product_name',
        'qty',
        'unit_price',
        'discount_amount',
        'tax_amount',
        'line_total',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    public function sale()
    {
        return $this->belongsTo(
            POSSale::class,
            'sale_id'
        );
    }

    public function product()
    {
        return $this->belongsTo(
            POSProducts::class,
            'product_id'
        );
    }
}
