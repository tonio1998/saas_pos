<?php

namespace App\Models\POS;

use App\Models\User;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class POSCashDrawer extends Model
{
    use SoftDeletes, BelongsToTenant;

    protected $table = 'pos_cash_drawers';
    protected $fillable = [
        'tenant_id',
        'drawer_name',
        'drawer_code',
        'status',
        'remarks',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'archived',
        'drawer_code'
    ];

    public $timestamps = false;

    protected static function booted()
    {
        static::created(function ($drawer) {
            $drawer->updateQuietly([
                'drawer_code' => getCashDrawerCode($drawer->id),
            ]);
        });
    }

    public function shifts()
    {
        return $this->hasMany(POSCashShift::class,'drawer_id');
    }

    public function activeShift()
    {
        return $this->hasOne(POSCashShift::class,'drawer_id')
            ->where('status','open');
    }

    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function updater()
    {
        return $this->belongsTo(
            User::class,
            'updated_by'
        );
    }
}
