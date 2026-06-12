<?php

namespace App\Models\POS;

use Illuminate\Database\Eloquent\Model;

class POSPayment extends Model
{
    protected $table = 'pos_payments';

    protected $fillable = [
        'sale_id',
        'payment_method',
        'amount',
        'reference_number',
        'payment_date',
        'notes',
        'tendered_amount',
        'change_amount',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function sale()
    {
        return $this->belongsTo(
            POSSale::class,
            'sale_id'
        );
    }
}
