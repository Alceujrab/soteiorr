<?php

namespace App\Mail;

use App\Models\Payment;
use App\Support\ContactInfo;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PendingPaymentMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Payment $payment) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Finalize seu PIX — cotas reservadas por 30 minutos',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.pending-payment',
            with: [
                'secondsRemaining' => $this->payment->reservationSecondsRemaining(),
                'whatsappUrl' => ContactInfo::whatsappUrl(
                    'Olá! Reservei cotas na Ação RR e preciso de ajuda com o pagamento #'.$this->payment->id
                ),
            ],
        );
    }
}
