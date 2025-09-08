<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminDailyPaymentDigestMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $subjectLine;
    public array $digest;

    public function __construct(array $digest, string $subjectLine)
    {
        $this->digest = $digest;
        $this->subjectLine = $subjectLine;
        $this->subject($this->subjectLine);
    }

    public function build()
    {
        return $this->view('emails.admin-payment-digest')
            ->with([
                'subject' => $this->subjectLine,
                'digest' => $this->digest,
            ]);
    }
}


