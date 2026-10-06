<?php

namespace App\Models\POS;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class POSSubscriptionInvoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pos_subscription_invoices';

    protected $fillable = [
        'invoice_no',
        'tenant_id',
        'subscription_id',
        'plan_name',
        'billing_cycle',
        'duration_days',
        'max_terminals',
        'max_products',
        'amount',
        'discount_amount',
        'net_amount',
        'currency',
        'payment_method',
        'payment_reference',
        'payment_proof',
        'payment_sender_name',
        'payment_sender_phone',
        'payment_status',
        'billing_date',
        'due_date',
        'paid_at',
        'period_start',
        'period_end',
        'notes',
        'rejection_reason',
        'verified_by',
        'created_by',
    ];

    protected $casts = [
        'amount'          => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'net_amount'      => 'decimal:2',
        'duration_days'   => 'integer',
        'max_terminals'   => 'integer',
        'max_products'    => 'integer',
        'billing_date'    => 'date',
        'due_date'        => 'date',
        'paid_at'         => 'datetime',
        'period_start'    => 'date',
        'period_end'      => 'date',
    ];

    /**
     * Auto-generate sequential invoice number if not present
     */
    protected static function booted()
    {
        static::creating(function ($invoice) {
            if (empty($invoice->invoice_no)) {
                $yearMonth = now()->format('Ym');
                $lastInvoice = static::where('invoice_no', 'like', "INV-{$yearMonth}-%")
                    ->orderBy('id', 'desc')
                    ->first();

                if ($lastInvoice && preg_match('/INV-\d{6}-(\d+)/', $lastInvoice->invoice_no, $matches)) {
                    $nextSeq = str_pad((int)$matches[1] + 1, 4, '0', STR_PAD_LEFT);
                } else {
                    $nextSeq = '0001';
                }
                $invoice->invoice_no = "INV-{$yearMonth}-{$nextSeq}";
            }

            if (empty($invoice->billing_date)) {
                $invoice->billing_date = now()->toDateString();
            }

            if (empty($invoice->due_date)) {
                $invoice->due_date = now()->addDays(3)->toDateString();
            }

            if (empty($invoice->net_amount)) {
                $invoice->net_amount = (float)$invoice->amount - (float)($invoice->discount_amount ?? 0);
            }
        });
    }

    /* ── Relationships ───────────────────────────────────── */

    public function tenant()
    {
        return $this->belongsTo(POSTenant::class, 'tenant_id');
    }

    public function subscription()
    {
        return $this->belongsTo(POSSubscription::class, 'subscription_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /* ── Helper Status Checks ────────────────────────────── */

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isPending(): bool
    {
        return $this->payment_status === 'pending';
    }

    public function isOverdue(): bool
    {
        return $this->payment_status === 'overdue' || ($this->isPending() && $this->due_date && $this->due_date->isPast());
    }

    public function isRejected(): bool
    {
        return $this->payment_status === 'rejected';
    }

    /* ── Badge Display Attributes ────────────────────────── */

    public function getStatusBadgeAttribute(): array
    {
        if ($this->isPaid()) {
            return [
                'class' => 'bg-success text-white',
                'label' => 'Paid & Active',
                'icon'  => 'bi-check-circle-fill'
            ];
        }

        if ($this->isRejected()) {
            return [
                'class' => 'bg-danger text-white',
                'label' => 'Rejected',
                'icon'  => 'bi-x-circle-fill'
            ];
        }

        if ($this->isOverdue()) {
            return [
                'class' => 'bg-danger-subtle text-danger border border-danger-subtle',
                'label' => 'Payment Overdue',
                'icon'  => 'bi-exclamation-octagon-fill'
            ];
        }

        // Pending
        return [
            'class' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
            'label' => 'Pending Verification',
            'icon'  => 'bi-hourglass-split'
        ];
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match (strtolower($this->payment_method ?? '')) {
            'qrph'          => 'QRPH (InstaPay / Maya)',
            'gcash'         => 'GCash Mobile',
            'maya'          => 'Maya Wallet',
            'bank_transfer' => 'Bank Transfer',
            'cash'          => 'Direct Cash',
            'manual_sa'     => 'Manual SA Ledger',
            default         => strtoupper($this->payment_method ?? 'Pending')
        };
    }
}
