<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class UserInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $token;
    public string $resetUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $token)
    {
        $this->user = $user;
        $this->token = $token;
        $this->resetUrl = route('set-password', ['token' => $this->token, 'email' => $this->user->email]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Zaproszenie do systemu Grupy Poledance - Ustaw hasło',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.user-invitation',
            with: [
                'user' => $this->user,
                'resetUrl' => $this->resetUrl,
                'expiresAt' => now()->addHours(48)->format('d.m.Y H:i'),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
