<?php

namespace App\Listeners;

use App\Events\PasswordResetRequested;
use App\Models\PasswordResetLog;
use App\Mail\PasswordResetInvitationMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class HandlePasswordResetRequest implements ShouldQueue
{
    use InteractsWithQueue;

    public $tries = 3;
    public $timeout = 60;

    public function handle(PasswordResetRequested $event): void
    {
        try {
            $user = $event->user;
            $admin = $event->admin;

            // Usuń stare tokeny dla tego użytkownika
            $this->deleteOldTokens($user);

            // Wygeneruj JEDEN surowy token dla obu maili
            $rawToken = Str::random(64);
            
            // Zapisz zahashowany token w tabeli password_reset_tokens
            DB::table('password_reset_tokens')->insert([
                'email' => $user->email,
                'token' => Hash::make($rawToken),
                'created_at' => now(),
            ]);
            
            // Użyj tego samego tokena w obu mailach
            $token = $rawToken;

            // Oblicz datę wygaśnięcia (72 godziny)
            $expiresAt = Carbon::now()->addHours(72);

            // Zapisz log operacji
            $resetLog = PasswordResetLog::create([
                'user_id' => $user->id,
                'user_email' => $user->email,
                'admin_id' => $admin->id,
                'admin_email' => $admin->email,
                'reason' => $event->reason,
                'reset_type' => $event->resetType,
                'token_expires_at' => $expiresAt,
                'status' => 'pending',
            ]);

            // Usuń hasło użytkownika
            $user->update(['password' => null]);

            // Wyślij JEDEN email z zaproszeniem (nie dwa!)
            Mail::to($user->email)->send(
                new PasswordResetInvitationMail($user, $token, $expiresAt, $admin->name)
            );
            
            // NIE wysyłaj UserInvitationMail - to powoduje duplikowanie!

            // Zaktualizuj log
            $resetLog->update(['status' => 'pending']);

            Log::info('Hasło zostało zresetowane i zaproszenie wysłane', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'admin_id' => $admin->id,
                'admin_email' => $admin->email,
                'reset_type' => $event->resetType,
                'token_expires_at' => $expiresAt,
            ]);

        } catch (\Exception $e) {
            Log::error('Błąd podczas resetowania hasła', [
                'user_id' => $event->user->id,
                'admin_id' => $event->admin->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    private function deleteOldTokens($user): void
    {
        // Usuń stare tokeny z tabeli password_reset_tokens
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->where('email', $user->email)
            ->delete();
    }

    public function failed(PasswordResetRequested $event, \Throwable $exception): void
    {
        Log::error('Listener HandlePasswordResetRequest nie powiódł się', [
            'user_id' => $event->user->id,
            'admin_id' => $event->admin->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
