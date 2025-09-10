<?php

namespace App\Events;

use App\Models\User;
use App\Models\PreRegistration;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PreRegistrationConverted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public User $user,
        public PreRegistration $preRegistration
    ) {
        //
    }
}
