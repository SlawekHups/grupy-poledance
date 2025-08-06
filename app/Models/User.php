<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'is_active',
        'joined_at',
        'accepted_terms_at',
        'user_id',
        'type',
        'street',
        'city',
        'postal_code',
        'amount',
        'role',
        'rodo_accepted_at',
        'terms_accepted_at',
        'group_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'joined_at' => 'date',
            'accepted_terms_at' => 'datetime',
            'rodo_accepted_at' => 'datetime',
            'terms_accepted_at' => 'datetime',
            'amount' => 'decimal:2',
        ];
    }
    public function group()
    {
        return $this->belongsTo(Group::class);
    }
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
    public function payments()
    {
        return $this->hasMany(\App\Models\Payment::class, 'user_id', 'id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function mailMessages()
    {
        return $this->hasMany(UserMailMessage::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
    public function isUser(): bool
    {
        return $this->role === 'user';
    }
}
