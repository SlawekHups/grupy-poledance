<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\Payment;
use App\Models\Lesson;
use App\Models\Attendance;
use App\Models\UserMailMessage;
use App\Models\PasswordResetLog;
use Illuminate\Support\Facades\Auth;

class NavigationBadgeCacheService
{
    /**
     * Cache TTL w minutach
     */
    private const CACHE_TTL = 5;

    /**
     * Pobierz badge dla UserResource
     */
    public static function getUserBadge(): string
    {
        return Cache::remember('navigation_badge_users', self::CACHE_TTL, function () {
            return User::where('is_active', true)
                ->whereNot('role', 'admin')
                ->count();
        });
    }

    /**
     * Pobierz badge dla PaymentResource
     */
    public static function getPaymentBadge(): string
    {
        return Cache::remember('navigation_badge_payments', self::CACHE_TTL, function () {
            return Payment::where('paid', false)->count();
        });
    }

    /**
     * Pobierz badge dla LessonResource
     */
    public static function getLessonBadge(): string
    {
        return Cache::remember('navigation_badge_lessons', self::CACHE_TTL, function () {
            return Lesson::whereDate('date', today())
                ->where('status', 'published')
                ->count();
        });
    }

    /**
     * Pobierz badge dla AttendanceResource
     */
    public static function getAttendanceBadge(): string
    {
        return Cache::remember('navigation_badge_attendances', self::CACHE_TTL, function () {
            return Attendance::whereDate('created_at', today())
                ->where('present', true)
                ->count();
        });
    }

    /**
     * Pobierz badge dla UserMailMessageResource (admin)
     */
    public static function getAdminUserMailBadge(): string
    {
        return Cache::remember('navigation_badge_admin_user_mail', self::CACHE_TTL, function () {
            return UserMailMessage::count();
        });
    }

    /**
     * Pobierz badge dla UserMailMessageResource (user)
     */
    public static function getUserMailBadge(): string
    {
        $userId = Auth::id();
        return Cache::remember("navigation_badge_user_mail_{$userId}", self::CACHE_TTL, function () use ($userId) {
            return UserMailMessage::where('user_id', $userId)->count();
        });
    }

    /**
     * Pobierz badge dla PasswordResetLogResource
     */
    public static function getPasswordResetBadge(): string
    {
        return Cache::remember('navigation_badge_password_reset', self::CACHE_TTL, function () {
            return PasswordResetLog::where('status', 'pending')->count();
        });
    }

    /**
     * Wyczyść cache dla wszystkich badges
     */
    public static function clearAllBadges(): void
    {
        Cache::forget('navigation_badge_users');
        Cache::forget('navigation_badge_payments');
        Cache::forget('navigation_badge_lessons');
        Cache::forget('navigation_badge_attendances');
        Cache::forget('navigation_badge_admin_user_mail');
        Cache::forget('navigation_badge_password_reset');
        
        // Wyczyść cache dla wszystkich użytkowników (user mail)
        $userIds = User::pluck('id');
        foreach ($userIds as $userId) {
            Cache::forget("navigation_badge_user_mail_{$userId}");
        }
    }

    /**
     * Wyczyść cache dla konkretnego użytkownika
     */
    public static function clearUserBadges(int $userId): void
    {
        Cache::forget("navigation_badge_user_mail_{$userId}");
    }

    /**
     * Wyczyść cache po zmianie danych
     */
    public static function clearRelevantBadges(string $model): void
    {
        switch ($model) {
            case 'User':
                Cache::forget('navigation_badge_users');
                break;
            case 'Payment':
                Cache::forget('navigation_badge_payments');
                break;
            case 'Lesson':
                Cache::forget('navigation_badge_lessons');
                break;
            case 'Attendance':
                Cache::forget('navigation_badge_attendances');
                break;
            case 'UserMailMessage':
                Cache::forget('navigation_badge_admin_user_mail');
                // Wyczyść cache dla wszystkich użytkowników
                $userIds = User::pluck('id');
                foreach ($userIds as $userId) {
                    Cache::forget("navigation_badge_user_mail_{$userId}");
                }
                break;
            case 'PasswordResetLog':
                Cache::forget('navigation_badge_password_reset');
                break;
        }
    }
}
