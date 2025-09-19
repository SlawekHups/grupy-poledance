<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    protected $fillable = [
        'phone',
        'message',
        'type',
        'status',
        'message_id',
        'cost',
        'error_message',
        'sent_at'
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'cost' => 'decimal:2'
    ];

    /**
     * Scope dla SMS-ów wysłanych pomyślnie
     */
    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    /**
     * Scope dla SMS-ów z błędami
     */
    public function scopeErrors($query)
    {
        return $query->where('status', 'error');
    }

    /**
     * Scope dla konkretnego typu SMS
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope dla konkretnego numeru telefonu
     */
    public function scopeForPhone($query, string $phone)
    {
        return $query->where('phone', $phone);
    }
}
