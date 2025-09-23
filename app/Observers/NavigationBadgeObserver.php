<?php

namespace App\Observers;

use App\Services\NavigationBadgeCacheService;
use App\Models\User;
use App\Models\Payment;
use App\Models\Lesson;
use App\Models\Attendance;
use App\Models\UserMailMessage;
use App\Models\PasswordResetLog;

class NavigationBadgeObserver
{
    /**
     * Handle the User "created" event.
     */
    public function userCreated(User $user): void
    {
        NavigationBadgeCacheService::clearRelevantBadges('User');
    }

    /**
     * Handle the User "updated" event.
     */
    public function userUpdated(User $user): void
    {
        NavigationBadgeCacheService::clearRelevantBadges('User');
        
        // Wyczyść cache dla konkretnego użytkownika
        NavigationBadgeCacheService::clearUserBadges($user->id);
    }

    /**
     * Handle the User "deleted" event.
     */
    public function userDeleted(User $user): void
    {
        NavigationBadgeCacheService::clearRelevantBadges('User');
        NavigationBadgeCacheService::clearUserBadges($user->id);
    }

    /**
     * Handle the Payment "created" event.
     */
    public function paymentCreated(Payment $payment): void
    {
        NavigationBadgeCacheService::clearRelevantBadges('Payment');
    }

    /**
     * Handle the Payment "updated" event.
     */
    public function paymentUpdated(Payment $payment): void
    {
        NavigationBadgeCacheService::clearRelevantBadges('Payment');
    }

    /**
     * Handle the Payment "deleted" event.
     */
    public function paymentDeleted(Payment $payment): void
    {
        NavigationBadgeCacheService::clearRelevantBadges('Payment');
    }

    /**
     * Handle the Lesson "created" event.
     */
    public function lessonCreated(Lesson $lesson): void
    {
        NavigationBadgeCacheService::clearRelevantBadges('Lesson');
    }

    /**
     * Handle the Lesson "updated" event.
     */
    public function lessonUpdated(Lesson $lesson): void
    {
        NavigationBadgeCacheService::clearRelevantBadges('Lesson');
    }

    /**
     * Handle the Lesson "deleted" event.
     */
    public function lessonDeleted(Lesson $lesson): void
    {
        NavigationBadgeCacheService::clearRelevantBadges('Lesson');
    }

    /**
     * Handle the Attendance "created" event.
     */
    public function attendanceCreated(Attendance $attendance): void
    {
        NavigationBadgeCacheService::clearRelevantBadges('Attendance');
    }

    /**
     * Handle the Attendance "updated" event.
     */
    public function attendanceUpdated(Attendance $attendance): void
    {
        NavigationBadgeCacheService::clearRelevantBadges('Attendance');
    }

    /**
     * Handle the Attendance "deleted" event.
     */
    public function attendanceDeleted(Attendance $attendance): void
    {
        NavigationBadgeCacheService::clearRelevantBadges('Attendance');
    }

    /**
     * Handle the UserMailMessage "created" event.
     */
    public function userMailMessageCreated(UserMailMessage $userMailMessage): void
    {
        NavigationBadgeCacheService::clearRelevantBadges('UserMailMessage');
    }

    /**
     * Handle the UserMailMessage "updated" event.
     */
    public function userMailMessageUpdated(UserMailMessage $userMailMessage): void
    {
        NavigationBadgeCacheService::clearRelevantBadges('UserMailMessage');
    }

    /**
     * Handle the UserMailMessage "deleted" event.
     */
    public function userMailMessageDeleted(UserMailMessage $userMailMessage): void
    {
        NavigationBadgeCacheService::clearRelevantBadges('UserMailMessage');
    }

    /**
     * Handle the PasswordResetLog "created" event.
     */
    public function passwordResetLogCreated(PasswordResetLog $passwordResetLog): void
    {
        NavigationBadgeCacheService::clearRelevantBadges('PasswordResetLog');
    }

    /**
     * Handle the PasswordResetLog "updated" event.
     */
    public function passwordResetLogUpdated(PasswordResetLog $passwordResetLog): void
    {
        NavigationBadgeCacheService::clearRelevantBadges('PasswordResetLog');
    }

    /**
     * Handle the PasswordResetLog "deleted" event.
     */
    public function passwordResetLogDeleted(PasswordResetLog $passwordResetLog): void
    {
        NavigationBadgeCacheService::clearRelevantBadges('PasswordResetLog');
    }
}
