<?php

namespace App\Models\POS;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class POSCashMovement extends Model
{
    use SoftDeletes;

    protected $table = 'pos_cash_movements';

    protected $fillable = [
        'tenant_id',
        'movement_code',
        'shift_id',
        'drawer_id',
        'cashier_id',
        'type',
        'category',
        'amount',
        'reference_no',
        'remarks',
        'movement_date',
        'status',
        'created_by',
        'updated_by',
        'archived',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'movement_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function shift()
    {
        return $this->belongsTo(
            POSCashShift::class,
            'shift_id'
        );
    }

    public function drawer()
    {
        return $this->belongsTo(
            POSCashDrawer::class,
            'drawer_id'
        );
    }

    public function cashier()
    {
        return $this->belongsTo(
            User::class,
            'cashier_id'
        );
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

    public function scopeCashIn($query)
    {
        return $query->where('type', 'IN');
    }

    public function scopeCashOut($query)
    {
        return $query->where('type', 'OUT');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForShift($query, $shiftId)
    {
        return $query->where('cash_shift_id', $shiftId);
    }

    public function scopeForDrawer($query, $drawerId)
    {
        return $query->where('drawer_id', $drawerId);
    }

    public function scopeForCashier($query, $cashierId)
    {
        return $query->where('cashier_id', $cashierId);
    }
}
