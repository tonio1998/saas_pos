<?php

namespace App\Models\POS;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class POSProductVariant extends Model
{
    use SoftDeletes;

    protected $table = 'pos_product_variants';

    protected $fillable = [
        'tenant_id',
        'product_id',
        'variant_name',
        'barcode',
        'sku',
        'qty_per_pack',
        'unit_id',
        'cost_price',
        'selling_price',
        'wholesale_price',
        'stock_on_hand',
        'reorder_level',
        'status',
        'created_by',
        'updated_by',
    ];

    public function product()
    {
        return $this->belongsTo(POSProducts::class, 'product_id');
    }

    public function unit()
    {
        return $this->belongsTo(POSUnits::class, 'unit_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}