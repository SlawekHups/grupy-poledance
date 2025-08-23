<?php

namespace App\Providers;

use App\Events\UserInvited;
use App\Events\PasswordResetRequested;
use App\Listeners\LogOutgoingMail;
use App\Listeners\SendUserInvitation;
use App\Listeners\HandlePasswordResetRequest;
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
        // Laravel automatycznie rejestruje listenery przez autodiscovery
        // Usunięto ręczną rejestrację, aby uniknąć duplikowania
    }
}
