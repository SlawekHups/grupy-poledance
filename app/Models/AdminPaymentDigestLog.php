<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminPaymentDigestLog extends Model
{
    protected $fillable = [
        'date',
        'weekday',
        'sent_at',
        'groups_count',
        'users_count',
        'unpaid_count',
        'total_amount',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'sent_at' => 'datetime',
        'total_amount' => 'decimal:2',
    ];
}


