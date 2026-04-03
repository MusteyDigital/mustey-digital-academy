<?php

namespace App\Mail;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public Payment $payment;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment->load(['user', 'course']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment Confirmation - ' . ($this->payment->course->title ?? 'Course Enrollment')
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-receipt'
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
