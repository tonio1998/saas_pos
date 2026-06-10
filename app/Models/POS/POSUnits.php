<?php

namespace App\Models\POS;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class POSUnits extends Model
{
    protected $table = 'pos_units';

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
}
