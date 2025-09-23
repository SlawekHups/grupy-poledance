<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Filament\Models\Contracts\FilamentUser;

class User extends Authenticatable implements FilamentUser
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
            // 'password' => 'hashed', // Usunięte - powodowało problemy z linkami zaproszenia
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
    public function groups()
    {
        return $this->belongsToMany(Group::class)->withTimestamps();
    }

    protected static function booted()
    {
        static::created(function (self $user) {
            // Jeśli przy tworzeniu ustawiono legacy group_id – przypnij do pivotu
            if (!empty($user->group_id)) {
                $user->groups()->syncWithoutDetaching([$user->group_id]);
            }
        });

        static::updated(function (self $user) {
            // Jeśli zmieniono legacy group_id – przypnij do pivotu
            if ($user->wasChanged('group_id') && !empty($user->group_id)) {
                $user->groups()->syncWithoutDetaching([$user->group_id]);
            }
        });

        // Synchronizacja w drugą stronę - gdy grupa jest dodana przez pivot table
        static::saved(function (self $user) {
            // Sprawdź czy group_id pasuje do pierwszej grupy w pivot table
            if ($user->groups()->count() > 0) {
                $firstGroup = $user->groups()->first();
                if ($firstGroup && $user->group_id !== $firstGroup->id) {
                    $user->updateQuietly(['group_id' => $firstGroup->id]);
                    Log::info('Synchronizacja group_id z pivot table', [
                        'user_id' => $user->id,
                        'old_group_id' => $user->group_id,
                        'new_group_id' => $firstGroup->id,
                        'group_name' => $firstGroup->name
                    ]);
                }
            } elseif ($user->groups()->count() === 0 && !empty($user->group_id)) {
                // Jeśli użytkownik nie ma grup, usuń group_id
                $user->updateQuietly(['group_id' => null]);
                Log::info('Usunięto group_id - użytkownik bez grup', [
                    'user_id' => $user->id,
                    'old_group_id' => $user->group_id
                ]);
            }
        });


        static::updating(function (self $user) {
            if ($user->isDirty(['name','email','phone','amount','terms_accepted_at'])) {
                Log::info('Aktualizacja profilu użytkownika', [
                    'user_id' => $user->id,
                    'by' => Auth::id(),
                    'changed' => $user->getDirty(),
                ]);
            }
        });
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

    /**
     * Sprawdza czy użytkownik może uzyskać dostęp do panelu Filament
     */
    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        return match ($panel->getId()) {
            'admin' => $this->isAdmin(),
            'user' => $this->isUser(),
            default => false,
        };
    }


    /**
     * Mutator dla hasła - haszuje tylko jeśli nie jest null
     */
    public function setPasswordAttribute($value)
    {
        if ($value !== null) {
            $this->attributes['password'] = \Illuminate\Support\Facades\Hash::make($value);
        } else {
            $this->attributes['password'] = null;
        }
    }
}
