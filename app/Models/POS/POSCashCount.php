<?php

namespace App\Models\POS;

use Illuminate\Database\Eloquent\Model;

class POSCashCount extends Model
{
    protected $table = 'pos_cash_counts';

    protected $fillable = [
        'tenant_id',
        'shift_id',
        'denomination',
        'quantity',
        'amount',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'archived',
    ];

    public function shift()
    {
        return $this->belongsTo(
            POSCashShift::class,
            'shift_id'
        );
    }
}
