<?php

namespace App\Models\POS;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryMovement extends Model
{
    use SoftDeletes;

    protected $table = 'pos_inventory_movements';

    protected $fillable = [
        'tenant_id',
        'product_id',
        'variant_id',
        'movement_type',
        'reference_type',
        'reference_id',
        'qty',
        'remarks',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(POSProducts::class, 'product_id');
    }

    public function variant()
    {
        return $this->belongsTo(POSProductVariant::class, 'variant_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
