<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Events\UserInvited;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CheckUserInvitations extends Command
{
    protected $signature = 'users:check-invitations {--dry-run : Tylko pokaż co zostanie wysłane, nie wysyłaj}';
    protected $description = 'Sprawdza użytkowników bez hasła i wygasłe linki zaproszeń (co 1 minutę)';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('🔍 TRYB TESTOWY - nic nie zostanie wysłane');
            $this->newLine();
        }

        $this->info('Sprawdzam użytkowników bez hasła i wygasłe linki...');

        // 1. Sprawdź wygasłe linki zaproszeń (72h)
        $this->checkExpiredInvitations();

        // 2. Znajdź użytkowników bez hasła, którzy są aktywni
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

        $expiredCount = 0;
        $activeCount = 0;

        foreach ($usersWithoutPassword as $user) {
            // Sprawdź czy użytkownik ma aktywny link zaproszenia (mniej niż 72h)
            $activeInvitation = $this->getActiveInvitation($user);
            
            if ($activeInvitation) {
                $expiresAt = Carbon::parse($activeInvitation->created_at)->addHours(72);
                $this->line("⏳ {$user->name} ({$user->email}) - aktywny link zaproszenia (wygaśnie: {$expiresAt->format('d.m.Y H:i:s')})");
                $activeCount++;
            } else {
                // Link wygasł - nie wysyłaj automatycznie, tylko oznacz
                $this->warn("❌ {$user->name} ({$user->email}) - link wygasł (72h), wymaga ręcznego zaproszenia przez admina");
                $expiredCount++;
                
                // Oznacz w logach że link wygasł
                \Illuminate\Support\Facades\Log::info("Link zaproszenia wygasł - wymaga ręcznego zaproszenia przez admina", [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'expired_at' => now(),
                    'action_required' => 'Admin must send manual invitation'
                ]);
            }
        }

        $this->newLine();
        $this->info("=== PODSUMOWANIE ===");
        $this->info("Aktywne linki: {$activeCount}");
        $this->info("Wygasłe linki: {$expiredCount}");
        $this->info("Łącznie przetworzono: " . ($activeCount + $expiredCount));

        if ($expiredCount > 0) {
            $this->warn("⚠️  {$expiredCount} użytkowników ma wygasłe linki - administrator musi wysłać ręczne zaproszenia!");
        }

        return 0;
    }

    /**
     * Sprawdza i oznacza wygasłe linki zaproszeń (72h)
     */
    private function checkExpiredInvitations(): void
    {
        $expiredTokens = DB::table('password_reset_tokens')
            ->where('created_at', '<', now()->subHours(72))
            ->get();

        if ($expiredTokens->isNotEmpty()) {
            $this->info("Znaleziono {$expiredTokens->count()} wygasłych linków zaproszeń (72h):");
            
            foreach ($expiredTokens as $token) {
                $this->line("   - {$token->email} - wygasł: " . Carbon::parse($token->created_at)->addHours(72)->format('d.m.Y H:i:s'));
                
                // Usuń wygasły token
                DB::table('password_reset_tokens')
                    ->where('email', $token->email)
                    ->delete();
            }
            
            $this->newLine();
        }
    }

    /**
     * Sprawdza czy użytkownik ma aktywny link zaproszenia (mniej niż 72h)
     */
    private function getActiveInvitation(User $user): ?object
    {
        return DB::table('password_reset_tokens')
            ->where('email', $user->email)
            ->where('created_at', '>=', now()->subHours(72))
            ->first();
    }
}
