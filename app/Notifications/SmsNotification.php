<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Smsapi\SmsapiChannel;
use NotificationChannels\Smsapi\SmsapiSmsMessage;

class SmsNotification extends Notification
{
    use Queueable;

    protected $message;
    protected $fromName;
    protected $testMode;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $message, string $fromName = 'Poledance', bool $testMode = true)
    {
        $this->message = $message;
        $this->fromName = $fromName;
        $this->testMode = $testMode;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [SmsapiChannel::class];
    }

    /**
     * Get the SMS representation of the notification.
     */
    public function toSmsapi(object $notifiable): SmsapiSmsMessage
    {
        return (new SmsapiSmsMessage())
            ->content($this->message)
            ->from($this->fromName)
            ->test($this->testMode);
    }
}
