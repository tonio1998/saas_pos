<?php

namespace App\Models\POS;

use App\Models\User;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class POSCategories extends Model
{
    use BelongsToTenant;

    protected $table = 'pos_categories';

    protected $fillable = [
        'tenant_id',
        'name',
        'description',
        'status',
        'archived',
        'created_by',
        'updated_by',
    ];

    public function products()
    {
        return $this->hasMany(
            POSProducts::class,
            'category_id'
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
}
