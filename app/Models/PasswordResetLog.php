<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PasswordResetLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_email',
        'admin_id',
        'admin_email',
        'reason',
        'reset_type', // 'single', 'bulk'
        'token_expires_at',
        'status', // 'pending', 'completed', 'expired'
    ];

    protected $casts = [
        'token_expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function isExpired(): bool
    {
        return $this->token_expires_at && $this->token_expires_at->isPast();
    }

    public function isPending(): bool
    {
        return $this->status === 'pending' && !$this->isExpired();
    }

    public function markAsCompleted(): void
    {
        $this->update(['status' => 'completed']);
    }

    public function markAsExpired(): void
    {
        $this->update(['status' => 'expired']);
    }
}
