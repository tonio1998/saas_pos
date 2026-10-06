<?php

namespace App\Mail;

use App\Models\POS\POSTenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionRenewalReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public POSTenant $tenant;
    public ?string $customNote;
    public int|string|null $daysRemaining;
    public string $dueDateFormatted;
    public string $planName;
    public float $amountDue;

    /**
     * Create a new message instance.
     */
    public function __construct(POSTenant $tenant, ?string $customNote = null)
    {
        $this->tenant = $tenant;
        $this->customNote = $customNote;
        $this->daysRemaining = $tenant->days_until_due ?? 0;
        $this->dueDateFormatted = $tenant->subscription_end
            ? $tenant->subscription_end->format('F d, Y')
            : 'Immediate';
        $this->planName = $tenant->subscription?->name ?? 'Suki Growth Standard Plan';
        $this->amountDue = (float) ($tenant->subscription?->price ?? 300.00);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $urgencyPrefix = '';
        if ($this->daysRemaining !== null) {
            if ($this->daysRemaining < 0) {
                $urgencyPrefix = '[OVERDUE] ';
            } elseif ($this->daysRemaining === 0) {
                $urgencyPrefix = '[EXPIRING TODAY] ';
            } elseif ($this->daysRemaining <= 3) {
                $urgencyPrefix = "[URGENT - {$this->daysRemaining} DAYS LEFT] ";
            } else {
                $urgencyPrefix = '[RENEWAL NOTICE] ';
            }
        }

        return new Envelope(
            subject: "{$urgencyPrefix}LikhaPOS Subscription Renewal Reminder — {$this->tenant->business_name}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.subscriptions.renewal-reminder',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
