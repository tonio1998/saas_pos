<?php

namespace App\Models\POS;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class POSCashTransaction extends Model
{
    use SoftDeletes;

    protected $table = 'pos_cash_transactions';
    protected $fillable = [
        'tenant_id',
        'shift_id',
        'drawer_id',
        'cashier_id',
        'transaction_type',
        'transaction_date',
        'amount',
        'reference_number',
        'notes',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'archived',
    ];
    public $timestamps = false;

    protected $guarded = [];

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
}
