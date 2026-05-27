<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $token, public string $email) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Restablecer contraseña — La Despensa');
    }

    public function content(): Content
    {
        $deepLink = 'nutricasa://reset-password?token=' . urlencode($this->token) . '&email=' . urlencode($this->email);

        return new Content(markdown: 'emails.reset-password', with: [
            'token'    => $this->token,
            'email'    => $this->email,
            'deepLink' => $deepLink,
        ]);
    }

    public function attachments(): array
    {
        return [];
    }
}
