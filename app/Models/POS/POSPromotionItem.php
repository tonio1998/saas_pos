<?php

namespace App\Models\POS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class POSPromotionItem extends Model
{
    use HasFactory;

    protected $table = 'pos_promotion_items';

    protected $fillable = [
        'tenant_id',
        'promotion_id',
        'item_type',
        'item_id',
        'custom_discount_value',
    ];

    protected $casts = [
        'custom_discount_value' => 'decimal:2',
    ];

    public function promotion()
    {
        return $this->belongsTo(POSPromotion::class, 'promotion_id');
    }

    public function product()
    {
        return $this->belongsTo(POSProducts::class, 'item_id');
    }

    public function category()
    {
        return $this->belongsTo(POSCategories::class, 'item_id');
    }

    public function variant()
    {
        return $this->belongsTo(POSProductVariant::class, 'item_id');
    }
}
