<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DataCorrectionLink extends Model
{
    protected $fillable = [
        'token',
        'user_id',
        'expires_at',
        'used',
        'used_at',
        'notes',
        'allowed_fields', // JSON array of fields user can edit
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
        'used' => 'boolean',
        'allowed_fields' => 'array',
    ];

    /**
     * Generuje nowy token do poprawy danych
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
     * Relacja do użytkownika
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Tworzy nowy link do poprawy danych
     */
    public static function createForUser(User $user, array $allowedFields = ['name', 'email', 'phone'], int $hoursValid = 24): self
    {
        // Usuń stare linki dla tego użytkownika
        self::where('user_id', $user->id)->delete();

        // Zawsze dodaj pole 'name' do dozwolonych pól
        if (!in_array('name', $allowedFields)) {
            $allowedFields[] = 'name';
        }

        return self::create([
            'token' => self::generateToken(),
            'user_id' => $user->id,
            'expires_at' => now()->addHours($hoursValid),
            'allowed_fields' => $allowedFields,
            'notes' => "Link do poprawy danych dla użytkownika {$user->name}",
        ]);
    }

    /**
     * Sprawdza czy pole może być edytowane
     */
    public function canEditField(string $field): bool
    {
        return in_array($field, $this->allowed_fields ?? []);
    }

    /**
     * Pobiera URL do formularza poprawy danych
     */
    public function getCorrectionUrl(): string
    {
        return route('data-correction', $this->token);
    }
}
