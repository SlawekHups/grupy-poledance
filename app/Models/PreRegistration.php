<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PreRegistration extends Model
{
    protected $fillable = [
        'token',
        'name',
        'email',
        'phone',
        'expires_at',
        'used',
        'used_at',
        'notes',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
        'used' => 'boolean',
    ];

    /**
     * Generuje nowy token pre-rejestracji
     */
    public static function generateToken(): string
    {
        do {
            $token = Str::random(32);
        } while (self::where('token', $token)->exists());
        
        return $token;
    }

    /**
     * Sprawdza czy token jest ważny
     */
    public function isValid(): bool
    {
        return !$this->used && $this->expires_at->isFuture();
    }

    /**
     * Oznacza jako użyty
     */
    public function markAsUsed(): void
    {
        $this->update([
            'used' => true,
            'used_at' => now(),
        ]);
    }

    /**
     * Scope dla ważnych tokenów
     */
    public function scopeValid($query)
    {
        return $query->where('used', false)
                    ->where('expires_at', '>', now());
    }

    /**
     * Scope dla wygasłych tokenów
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    /**
     * Konwertuje pre-rejestrację na pełnego użytkownika
     */
    public function convertToUser($groupId = null)
    {
        // Sprawdź czy pre-rejestracja była użyta
        if (!$this->used) {
            throw new \Exception('Pre-rejestracja nie została jeszcze wypełniona');
        }

        // Sprawdź czy użytkownik już istnieje
        $existingUser = \App\Models\User::where('email', $this->email)->first();
        if ($existingUser) {
            throw new \Exception('Użytkownik z tym emailem już istnieje');
        }

        // Utwórz nowego użytkownika
        $user = \App\Models\User::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'amount' => 200, // Domyślna kwota
            'is_active' => true,
            'group_id' => $groupId,
        ]);

        // Jeśli podano grupę, przypisz do niej
        if ($groupId) {
            $group = \App\Models\Group::find($groupId);
            if ($group && !$user->groups()->where('group_id', $groupId)->exists()) {
                $user->groups()->attach($groupId);
            }
        }

        // Oznacz pre-rejestrację jako skonwertowaną
        $this->update([
            'notes' => $this->notes . "\n\nSkonwertowano na użytkownika ID: {$user->id}",
        ]);

        \Illuminate\Support\Facades\Log::info('Pre-registration converted to user', [
            'pre_registration_id' => $this->id,
            'user_id' => $user->id,
            'email' => $this->email,
            'group_id' => $groupId,
        ]);

        // Wyślij event o konwersji (tylko raz!)
        event(new \App\Events\PreRegistrationConverted($user, $this));

        return $user;
    }

    /**
     * Sprawdza czy można skonwertować na użytkownika
     */
    public function canConvertToUser(): bool
    {
        return $this->used && 
               !\App\Models\User::where('email', $this->email)->exists();
    }
}
