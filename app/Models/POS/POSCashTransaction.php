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
        'transaction_code',
        'shift_id',
        'drawer_id',
        'cashier_id',
        'transaction_type',
        'category',
        'amount',
        'reference_no',
        'approved_by',
        'remarks',
        'status',
        'created_by',
        'updated_by',
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
