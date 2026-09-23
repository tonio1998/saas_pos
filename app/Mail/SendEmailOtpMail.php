<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendEmailOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $otp;
    public string $name;

    /**
     * Create a new message instance.
     */
    public function __construct(string $otp, string $name = 'Store Owner')
    {
        $this->otp = $otp;
        $this->name = $name;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'LikhaPOS: Your 6-Digit Email Verification Code is ' . $this->otp,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.verify_otp',
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
