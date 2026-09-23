<?php

namespace App\Models\POS;

use App\Models\User;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class POSPromotion extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $table = 'pos_promotions';

    protected $fillable = [
        'tenant_id',
        'title',
        'promo_code',
        'promo_type',
        'discount_value',
        'min_spend',
        'min_quantity',
        'get_quantity',
        'applies_to',
        'target_id',
        'target_ids',
        'start_date',
        'end_date',
        'is_active',
        'usage_count',
        'usage_limit',
        'description',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'discount_value' => 'decimal:2',
        'min_spend' => 'decimal:2',
        'min_quantity' => 'integer',
        'get_quantity' => 'integer',
        'target_ids' => 'array',
        'usage_count' => 'integer',
        'usage_limit' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public static function promoTypes(): array
    {
        return [
            'percentage'   => 'Percentage Discount (%)',
            'fixed_amount' => 'Fixed Amount Discount (₱)',
            'bulk_tier'    => 'Bulk / Wholesale Special Price (₱)',
        ];
    }

    public function scopeActive($query)
    {
        $today = now()->toDateString();
        return $query->where('is_active', true)
            ->where(function ($q) use ($today) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', $today);
            })
            ->where(function ($q) use ($today) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $today);
            })
            ->where(function ($q) {
                $q->whereNull('usage_limit')->orWhereRaw('usage_count < usage_limit');
            });
    }

    public function items()
    {
        return $this->hasMany(POSPromotionItem::class, 'promotion_id');
    }

    public function product()
    {
        return $this->belongsTo(POSProducts::class, 'target_id');
    }

    public function category()
    {
        return $this->belongsTo(POSCategories::class, 'target_id');
    }

    public function variant()
    {
        return $this->belongsTo(POSProductVariant::class, 'target_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
