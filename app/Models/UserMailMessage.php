<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserMailMessage extends Model
{
    protected $fillable = [
        'user_id',
        'direction',
        'email',
        'subject',
        'content',
        'sent_at',
        'headers',
        'message_id',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'headers' => 'array',
    ];

    /**
     * Relacja do użytkownika
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope dla wiadomości przychodzących
     */
    public function scopeIncoming($query)
    {
        return $query->where('direction', 'in');
    }

    /**
     * Scope dla wiadomości wychodzących
     */
    public function scopeOutgoing($query)
    {
        return $query->where('direction', 'out');
    }

    /**
     * Scope dla wiadomości użytkownika
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope dla wiadomości zarejestrowanych użytkowników
     */
    public function scopeForRegisteredUsers($query)
    {
        return $query->whereNotNull('user_id');
    }
}
