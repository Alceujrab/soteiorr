<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeRegisteredMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public ?string $generatedPassword = null
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Cadastro confirmado - Ação RR Veículos',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.welcome-registered',
        );
    }
}
