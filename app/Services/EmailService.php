<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserInvitationMail;
use App\Mail\PasswordResetInvitationMail;
use App\Mail\PreRegistrationLinkMail;
use App\Mail\CustomLinkMail;
use App\Models\User;
use App\Models\PreRegistration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EmailService
{
    protected $fromName;
    protected $fromEmail;

    public function __construct()
    {
        $this->fromName = config('mail.from.name');
        $this->fromEmail = config('mail.from.address');
    }

    /**
     * Wysyła email z linkiem pre-rejestracji
     */
    public function sendPreRegistrationLink(string $email, string $link, ?string $customMessage = null): bool
    {
        try {
            // Użyj prostego podejścia z istniejącym szablonem
            $message = $customMessage ?: 'Witaj! Oto link do rejestracji w systemie Grupy Poledance.';
            
            Mail::send('emails.pre-registration-link', [
                'messageText' => $message,
                'link' => $link,
                'expiresAt' => now()->addHours(24)->format('d.m.Y H:i'),
            ], function ($mailMessage) use ($email) {
                $mailMessage->to($email)
                        ->subject('Zaproszenie do rejestracji - Grupy Poledance');
            });

            Log::info('Email pre-rejestracji wysłany pomyślnie', [
                'email' => $email,
                'link' => $link
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Błąd wysyłania email pre-rejestracji', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Wysyła email z linkiem resetu hasła
     */
    public function sendPasswordResetLink(string $email, string $link, ?string $customMessage = null): bool
    {
        try {
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                Log::error('Użytkownik nie znaleziony dla email resetu hasła', ['email' => $email]);
                return false;
            }

            // Generuj token resetowania hasła
            $rawToken = Str::random(64);
            DB::table('password_reset_tokens')->insert([
                'email' => $email,
                'token' => Hash::make($rawToken),
                'raw_token' => $rawToken,
                'created_at' => now(),
            ]);

            $expiresAt = Carbon::now()->addHours(72);
            $adminName = Auth::check() ? Auth::user()->name : 'Administrator';

            // Wyślij email z resetem hasła
            Mail::to($email)->send(new PasswordResetInvitationMail(
                $user,
                $rawToken,
                $expiresAt,
                $adminName
            ));

            Log::info('Email resetu hasła wysłany pomyślnie', [
                'email' => $email,
                'user_id' => $user->id
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Błąd wysyłania email resetu hasła', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Wysyła niestandardowy email z linkiem
     */
    public function sendCustomEmailWithLink(string $email, string $subject, string $message, string $link): bool
    {
        try {
            // Wyślij email używając Mailable
            Mail::to($email)->send(new CustomLinkMail($subject, $message, $link));

            Log::info('Niestandardowy email z linkiem wysłany pomyślnie', [
                'email' => $email,
                'subject' => $subject
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Błąd wysyłania niestandardowego email', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Wysyła email z linkiem pre-rejestracji do użytkownika
     */
    public function sendUserInvitation(string $email, ?string $customMessage = null): bool
    {
        try {
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                Log::error('Użytkownik nie znaleziony dla zaproszenia', ['email' => $email]);
                return false;
            }

            // Sprawdź czy użytkownik ma już token
            $tokenRecord = DB::table('password_reset_tokens')
                ->where('email', $email)
                ->where('created_at', '>=', now()->subHours(72))
                ->first();
            
            if ($tokenRecord) {
                $token = $tokenRecord->raw_token ?? $tokenRecord->token;
            } else {
                $rawToken = Str::random(64);
                DB::table('password_reset_tokens')->insert([
                    'email' => $email,
                    'token' => Hash::make($rawToken),
                    'raw_token' => $rawToken,
                    'created_at' => now(),
                ]);
                $token = $rawToken;
            }

            // Wyślij email zaproszenia
            Mail::to($email)->send(new UserInvitationMail($user, $token));

            Log::info('Email zaproszenia wysłany pomyślnie', [
                'email' => $email,
                'user_id' => $user->id
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Błąd wysyłania email zaproszenia', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Sprawdza czy adres email jest prawidłowy
     */
    public function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}
