<?php

namespace App\Listeners;

use App\Events\PreRegistrationConverted;
use App\Events\UserInvited;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendUserInvitationAfterConversion implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PreRegistrationConverted $event): void
    {
        $user = $event->user;
        $preRegistration = $event->preRegistration;
        
        Log::info('SendUserInvitationAfterConversion listener called', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'pre_registration_id' => $preRegistration->id,
            'timestamp' => now()->toISOString(),
        ]);
        
        // Wyślij zaproszenie do użytkownika
        event(new UserInvited($user));
        
        Log::info('UserInvited event fired from SendUserInvitationAfterConversion', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'timestamp' => now()->toISOString(),
        ]);
    }
}
