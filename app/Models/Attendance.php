<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'group_id',
        'date',
        'present',
        'note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
    protected static function booted()
    {
        static::creating(function ($attendance) {
            if (empty($attendance->group_id) && !empty($attendance->user_id)) {
                $attendance->group_id = \App\Models\User::find($attendance->user_id)?->group_id;
            }
        });
    }
}
