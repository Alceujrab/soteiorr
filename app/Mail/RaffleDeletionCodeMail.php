<?php

namespace App\Mail;

use App\Models\Raffle;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RaffleDeletionCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Raffle $raffle,
        public string $code,
        public int $expiresInMinutes
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Código de exclusão da Ação Promocional #'.$this->raffle->id,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.raffle-deletion-code',
        );
    }
}
