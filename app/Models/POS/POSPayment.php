<?php

namespace App\Models\POS;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class POSPayment extends Model
{
    protected $table = 'pos_payments';

    protected $fillable = [
        'sale_id',
        'tenant_id',
        'customer_id',
        'payment_method',
        'amount',
        'reference_number',
        'payment_date',
        'notes',
        'tendered_amount',
        'change_amount',
        'terminal_id',
        'drawer_id',
        'shift_id',
        'created_by',
        'updated_by',
        'status',
        'archived',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'amount' => 'decimal:2',
        'tendered_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
    ];

    public function sale()
    {
        return $this->belongsTo(POSSale::class, 'sale_id');
    }

    public function customer()
    {
        return $this->belongsTo(POSCustomers::class, 'customer_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function terminal()
    {
        return $this->belongsTo(POSTerminal::class, 'terminal_id');
    }

    public function shift()
    {
        return $this->belongsTo(POSCashShift::class, 'shift_id');
    }

    public function drawer()
    {
        return $this->belongsTo(POSCashDrawer::class, 'drawer_id');
    }
}
