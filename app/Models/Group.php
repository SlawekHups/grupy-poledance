<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Group extends Model
{
    protected $fillable = [
        'name',
        'description',
        'status',
        'max_size',
    ];

    protected $casts = [
        'max_size' => 'integer',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
    public function members()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }

    public function payments(): HasManyThrough
    {
        return $this->hasManyThrough(
            Payment::class,
            User::class,
            'group_id', // Klucz obcy w tabeli users
            'user_id',  // Klucz obcy w tabeli payments
            'id',       // Lokalny klucz w tabeli groups
            'id'        // Lokalny klucz w tabeli users
        );
    }

    public function updateStatusBasedOnCapacity(): void
    {
        $currentCount = $this->members()->count();
        
        if ($currentCount >= $this->max_size && $this->status === 'active') {
            $this->update(['status' => 'full']);
        } elseif ($currentCount < $this->max_size && $this->status === 'full') {
            $this->update(['status' => 'active']);
        }
    }

    public function hasSpace(): bool
    {
        return $this->members()->where('users.is_active', true)->count() < $this->max_size;
    }

    protected static function booted()
    {
        static::updated(function ($group) {
            if ($group->wasChanged('max_size')) {
                $group->updateStatusBasedOnCapacity();
            }
        });
    }
}
