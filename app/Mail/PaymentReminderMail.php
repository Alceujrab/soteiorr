<?php

namespace App\Mail;

use App\Models\Payment;
use App\Support\ContactInfo;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Payment $payment) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Lembrete: seu PIX ainda está pendente',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.payment-reminder',
            with: [
                'secondsRemaining' => $this->payment->reservationSecondsRemaining(),
                'whatsappUrl' => ContactInfo::whatsappUrl(
                    'Olá! Recebi o lembrete do PIX pendente #'.$this->payment->id.' e preciso de ajuda.'
                ),
            ],
        );
    }
}
