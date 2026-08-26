<?php

namespace App\Mail;

use App\Models\Payment;
use App\Support\PurchaseReceiptDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PurchaseReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Payment $payment) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Comprovante de compra #'.$this->payment->id.' - Ação RR Veículos',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.purchase-receipt',
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $pdf = app(PurchaseReceiptDocument::class)->toPdf($this->payment);

        return [
            Attachment::fromData(fn () => $pdf, 'comprovante-compra-'.$this->payment->id.'.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
