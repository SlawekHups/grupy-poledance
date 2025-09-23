<?php

namespace App\Providers;

use App\Events\UserInvited;
use App\Events\PasswordResetRequested;
use App\Events\PreRegistrationConverted;
use App\Listeners\LogOutgoingMail;
use App\Listeners\SendUserInvitation;
use App\Listeners\HandlePasswordResetRequest;
use App\Listeners\SendUserInvitationAfterConversion;
use App\Observers\NavigationBadgeObserver;
use App\Models\User;
use App\Models\Payment;
use App\Models\Lesson;
use App\Models\Attendance;
use App\Models\UserMailMessage;
use App\Models\PasswordResetLog;
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
        // Laravel automatycznie wykrywa listenery na podstawie typu parametru w metodzie handle()
        // Nie rejestrujemy listenerów jawnie, aby uniknąć duplikacji
        
        \Illuminate\Support\Facades\Log::info('AppServiceProvider boot method called - using auto-discovery for listeners');
        
        // Rejestracja observers dla cache navigation badges
        User::observe(NavigationBadgeObserver::class);
        Payment::observe(NavigationBadgeObserver::class);
        Lesson::observe(NavigationBadgeObserver::class);
        Attendance::observe(NavigationBadgeObserver::class);
        UserMailMessage::observe(NavigationBadgeObserver::class);
        PasswordResetLog::observe(NavigationBadgeObserver::class);
    }
}
