<?php

namespace App\Models\POS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class POSStoreReview extends Model
{
    use HasFactory;

    protected $table = 'pos_store_reviews';

    protected $fillable = [
        'tenant_id',
        'reviewer_name',
        'store_name',
        'avatar_initials',
        'rating',
        'review_text',
        'is_approved',
        'is_featured',
    ];

    public function tenant()
    {
        return $this->belongsTo(POSTenant::class, 'tenant_id');
    }
}
