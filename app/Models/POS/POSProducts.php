<?php

namespace App\Models\POS;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class POSProducts extends Model
{

    protected $table = 'pos_products';

    protected $fillable = [
        'stock_on_hand',
        'tenant_id',
        'category_id',
        'unit_id',
        'barcode',
        'sku',
        'name',
        'description',
        'cost_price',
        'selling_price',
        'wholesale_price',
        'reorder_level',
        'status',
        'archived',
        'created_by',
        'updated_by',
    ];

    public function category()
    {
        return $this->belongsTo(
            POSCategories::class,
            'category_id'
        );
    }

    public function unit()
    {
        return $this->belongsTo(
            POSUnits::class,
            'unit_id'
        );
    }

    public function createdBy()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function updatedBy()
    {
        return $this->belongsTo(
            User::class,
            'updated_by'
        );
    }

    public function stocks()
    {
        return $this->hasMany(
            Stocks::class,
            'product_id'
        );
    }
}
