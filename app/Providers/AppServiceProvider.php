<?php

namespace App\Providers;

use App\Events\UserInvited;
use App\Listeners\LogOutgoingMail;
use App\Listeners\SendUserInvitation;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(
            UserInvited::class,
            [SendUserInvitation::class, 'handle']
        );

        // Logowanie wysyłanych maili
        Event::listen(
            MessageSent::class,
            [LogOutgoingMail::class, 'handle']
        );
    }
}
