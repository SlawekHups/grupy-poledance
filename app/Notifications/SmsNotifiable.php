<?php

namespace App\Notifications;

use Illuminate\Notifications\Notifiable;

class SmsNotifiable
{
    use Notifiable;

    protected $phone;

    public function __construct(string $phone)
    {
        $this->phone = $phone;
    }

    public function routeNotificationForSmsapi()
    {
        return $this->phone;
    }
}