<?php

namespace App\Models\POS;

use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    protected $table = 'pos_inventory_movements';

    protected $fillable = [
        'tenant_id',
        'product_id',
        'movement_type',
        'reference_type',
        'reference_id',
        'qty',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
    ];
}
