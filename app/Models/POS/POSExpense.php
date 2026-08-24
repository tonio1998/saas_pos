<?php

namespace App\Models\POS;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class POSExpense extends Model
{
    use SoftDeletes;

    protected $table = 'pos_expenses';

    protected $fillable = [
        'tenant_id',
        'expense_code',
        'expense_date',
        'category',
        'title',
        'amount',
        'payment_method',
        'payee',
        'reference_no',
        'notes',
        'attachment',
        'drawer_id',
        'cash_shift_id',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount'       => 'decimal:2',
    ];

    public static function categories(): array
    {
        return [
            'utilities'           => 'Utilities (Kuryente, Tubig, Internet)',
            'rent'                => 'Rent (Upa sa Pwesto / Stall)',
            'salaries'            => 'Payroll & Wages (Sweldo ng Tauhan)',
            'supplies'            => 'Store Supplies (Plastics, Receipt Rolls)',
            'delivery_gas'        => 'Logistics, Delivery & Fuel',
            'repairs_maintenance'=> 'Maintenance & Repairs',
            'taxes_permits'       => 'Taxes, Permits & Licenses',
            'meals_allowance'     => 'Staff Meals & Allowances',
            'marketing'           => 'Marketing & Promotions',
            'other'               => 'Other Miscellaneous Expenses',
        ];
    }

    public static function paymentMethods(): array
    {
        return [
            'cash'          => 'Cash (From Register / Drawer)',
            'gcash'         => 'GCash',
            'maya'          => 'Maya',
            'bank_transfer' => 'Bank Transfer',
            'card'          => 'Credit / Debit Card',
            'other'         => 'Other Payment Method',
        ];
    }

    protected static function booted()
    {
        static::creating(function ($expense) {
            if (empty($expense->expense_code)) {
                $todayPrefix = 'EXP-' . date('ymd');
                $lastExpense = self::where('tenant_id', $expense->tenant_id ?? auth()->user()?->tenant_id)
                    ->where('expense_code', 'LIKE', $todayPrefix . '-%')
                    ->orderByDesc('id')
                    ->first();

                $nextSeq = 1;
                if ($lastExpense && preg_match('/-(\d+)$/', $lastExpense->expense_code, $matches)) {
                    $nextSeq = (int)$matches[1] + 1;
                }
                $expense->expense_code = $todayPrefix . '-' . str_pad($nextSeq, 3, '0', STR_PAD_LEFT);
            }

            if (empty($expense->tenant_id) && auth()->check()) {
                $expense->tenant_id = auth()->user()->tenant_id;
            }

            if (empty($expense->created_by) && auth()->check()) {
                $expense->created_by = auth()->id();
            }
        });
    }

    public function tenant()
    {
        return $this->belongsTo(POSTenant::class, 'tenant_id');
    }

    public function drawer()
    {
        return $this->belongsTo(POSCashDrawer::class, 'drawer_id');
    }

    public function cashShift()
    {
        return $this->belongsTo(POSCashShift::class, 'cash_shift_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
