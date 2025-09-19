<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PreRegistrationLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $messageText;
    public string $link;
    public string $expiresAt;

    /**
     * Create a new message instance.
     */
    public function __construct(string $message, string $link, string $expiresAt)
    {
        $this->messageText = $message;
        $this->link = $link;
        $this->expiresAt = $expiresAt;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Zaproszenie do rejestracji - Grupy Poledance',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.pre-registration-link',
            with: [
                'message' => $this->messageText,
                'link' => $this->link,
                'expiresAt' => $this->expiresAt,
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
