<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentReminderLog extends Model
{
    protected $fillable = [
        'user_id',
        'group_name',
        'sent_date',
        'reminder_type',
        'unpaid_count',
        'total_amount',
    ];

    protected $casts = [
        'sent_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    /**
     * Relacja do użytkownika
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope dla logów z konkretnej daty
     */
    public function scopeForDate($query, $date)
    {
        return $query->where('sent_date', $date);
    }

    /**
     * Scope dla logów użytkownika
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope dla logów grupy
     */
    public function scopeForGroup($query, $groupName)
    {
        return $query->where('group_name', $groupName);
    }

    /**
     * Sprawdza czy użytkownik dostał przypomnienie danego dnia
     */
    public static function wasReminderSentToday(int $userId, string $date = null): bool
    {
        $date = $date ?: now()->toDateString();
        
        return static::where('user_id', $userId)
            ->where('sent_date', $date)
            ->exists();
    }

    /**
     * Sprawdza czy użytkownik dostał przypomnienie w tym tygodniu
     */
    public static function wasReminderSentThisWeek(int $userId): bool
    {
        $startOfWeek = now()->startOfWeek()->toDateString();
        $endOfWeek = now()->endOfWeek()->toDateString();
        
        return static::where('user_id', $userId)
            ->whereBetween('sent_date', [$startOfWeek, $endOfWeek])
            ->exists();
    }
}
