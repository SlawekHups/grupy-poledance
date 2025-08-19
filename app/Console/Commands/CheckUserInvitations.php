<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Events\UserInvited;
use Carbon\Carbon;

class CheckUserInvitations extends Command
{
    protected $signature = 'users:check-invitations {--dry-run : Tylko pokaż co zostanie wysłane, nie wysyłaj}';
    protected $description = 'Sprawdza użytkowników bez hasła i wysyła przypomnienia o zaproszeniach';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('🔍 TRYB TESTOWY - nic nie zostanie wysłane');
            $this->newLine();
        }

        $this->info('Sprawdzam użytkowników bez hasła...');

        // Znajdź użytkowników bez hasła, którzy są aktywni
        $usersWithoutPassword = User::where('is_active', true)
            ->whereNull('password')
            ->where('role', '!=', 'admin')
            ->get();

        if ($usersWithoutPassword->isEmpty()) {
            $this->info('✅ Wszyscy aktywni użytkownicy mają ustawione hasła');
            return 0;
        }

        $this->info("Znaleziono {$usersWithoutPassword->count()} użytkowników bez hasła:");
        $this->newLine();

        $sentCount = 0;
        $skippedCount = 0;

        foreach ($usersWithoutPassword as $user) {
            // Sprawdź czy użytkownik nie dostał zaproszenia w ciągu ostatnich 24h
            $lastInvitationSent = $this->getLastInvitationDate($user);
            $shouldSend = $this->shouldSendInvitation($user, $lastInvitationSent);

            if ($shouldSend) {
                if (!$dryRun) {
                    // Wyślij zaproszenie
                    UserInvited::dispatch($user);
                    
                    // Zaloguj wysłanie
                    \Illuminate\Support\Facades\Log::info("Automatyczne wysłanie zaproszenia", [
                        'user_id' => $user->id,
                        'user_email' => $user->email,
                        'last_invitation' => $lastInvitationSent,
                        'reason' => 'Automatyczne sprawdzenie cron'
                    ]);
                }

                $this->info("📧 {$user->name} ({$user->email}) - " . ($dryRun ? 'WYSŁANO BY' : 'WYSŁANO') . " zaproszenie");
                $sentCount++;
            } else {
                $this->line("⏳ {$user->name} ({$user->email}) - pominięto (ostatnie zaproszenie: {$lastInvitationSent})");
                $skippedCount++;
            }
        }

        $this->newLine();
        $this->info("=== PODSUMOWANIE ===");
        $this->info("Wysłano zaproszeń: {$sentCount}");
        $this->info("Pominięto: {$skippedCount}");
        $this->info("Łącznie przetworzono: " . ($sentCount + $skippedCount));

        if ($dryRun) {
            $this->warn("💡 Uruchom bez --dry-run aby rzeczywiście wysłać zaproszenia");
        }

        return 0;
    }

    /**
     * Sprawdza czy powinno się wysłać zaproszenie
     */
    private function shouldSendInvitation(User $user, ?string $lastInvitation): bool
    {
        // Jeśli nigdy nie wysłano - wyślij
        if (!$lastInvitation) {
            return true;
        }

        $lastInvitationDate = Carbon::parse($lastInvitation);
        $now = Carbon::now();

        // Wyślij jeśli minęło więcej niż 24h od ostatniego zaproszenia
        return $lastInvitationDate->diffInHours($now) >= 24;
    }

    /**
     * Pobiera datę ostatniego wysłania zaproszenia
     * (można rozszerzyć o logowanie w bazie)
     */
    private function getLastInvitationDate(User $user): ?string
    {
        // Na razie zwracamy null - można rozszerzyć o tabelę logów zaproszeń
        // lub sprawdzać w logach Laravel
        return null;
    }
}
