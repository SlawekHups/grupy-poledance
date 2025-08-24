<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class PasswordResetInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $token,
        public Carbon $expiresAt,
        public string $adminName
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Zaproszenie do ustawienia nowego hasła - Grupy Poledance',
        );
    }

    public function content(): Content
    {
        $resetUrl = route('set-password', ['token' => $this->token, 'email' => $this->user->email]);
        
        return new Content(
            view: 'emails.password-reset-invitation',
            with: [
                'user' => $this->user,
                'resetUrl' => $resetUrl,
                'expiresAt' => $this->expiresAt,
                'adminName' => $this->adminName,
            ],
        );
    }
}
