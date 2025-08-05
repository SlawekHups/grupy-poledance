<?php

namespace App\Listeners;

use App\Events\UserInvited;
use App\Mail\UserInvitationMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class SendUserInvitation implements ShouldQueue
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
    public function handle(UserInvited $event): void
    {
        $user = $event->user;
        
        // Sprawdź czy użytkownik jest aktywny
        if (!$user->is_active) {
            \Illuminate\Support\Facades\Log::info('Skipping invitation for inactive user', [
                'user_id' => $user->id,
                'email' => $user->email,
                'is_active' => $user->is_active
            ]);
            return;
        }
        
        // Generuj token resetu hasła
        $token = Password::createToken($user);
        
        // Wyślij email z zaproszeniem
        Mail::to($user->email)->queue(new UserInvitationMail($user, $token));
        
        \Illuminate\Support\Facades\Log::info('Invitation sent to active user', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);
    }
}
