<?php

namespace App\Models\POS;

use App\Models\User;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class POSCashShift extends Model
{
    use SoftDeletes, BelongsToTenant;

    protected $table = 'pos_cash_shifts';
    protected $fillable = [
        'shift_code',
        'tenant_id',
        'drawer_id',
        'cashier_id',
        'shift_date',
        'shift_type',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'archived',
    ];
    public $timestamps = false;

    protected $guarded = [];

    protected static function booted()
    {
        static::created(function ($drawer) {
            $drawer->updateQuietly([
                'shift_code' => generateCashShiftCode($drawer->id),
            ]);
        });
    }

    public function drawer()
    {
        return $this->belongsTo(POSCashDrawer::class);
    }

    public function cashier()
    {
        return $this->belongsTo(
            User::class,
            'cashier_id'
        );
    }

    public function transactions()
    {
        return $this->hasMany(
            POSCashTransaction::class,
            'shift_id'
        );
    }

    public function sales()
    {
        return $this->hasMany(POSSale::class, 'cash_shift_id')
            ->where('sale_status', 'completed');
    }

    public function getSalesTotalAttribute()
    {
        return $this->sales()->sum('total_amount');
    }

    public function getTransactionCountAttribute()
    {
        return $this->sales()->count();
    }

    public function salesTotal()
    {
        return POSSale::where('cash_shift_id', $this->id)
            ->where('sale_status', 'completed')
            ->sum('total_amount');
    }


}
