<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    protected static function booted()
    {
        static::created(function ($group) {
            // Create a default attendance for the new group
            Attendance::create([
                'group_id' => $group->id,
                'date' => now(),
                'status' => 'present',
            ]);
        });
    }
}
