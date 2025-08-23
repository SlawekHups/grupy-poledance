<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PasswordResetLog;
use Carbon\Carbon;

class CheckExpiredPasswordTokens extends Command
{
    protected $signature = 'passwords:check-expired {--dry-run : Tylko pokaż co zostanie oznaczone jako wygasłe}';
    protected $description = 'Sprawdza i oznacza wygasłe tokeny resetowania haseł';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $now = Carbon::now();
        
        $this->info('🔍 Sprawdzam wygasłe tokeny resetowania haseł...');
        
        // Znajdź wygasłe tokeny
        $expiredTokens = PasswordResetLog::where('status', 'pending')
            ->where('token_expires_at', '<', $now)
            ->get();
        
        if ($expiredTokens->isEmpty()) {
            $this->info('✅ Brak wygasłych tokenów.');
            return 0;
        }
        
        $this->warn("⚠️  Znaleziono {$expiredTokens->count()} wygasłych tokenów:");
        
        foreach ($expiredTokens as $token) {
            $this->line("  • {$token->user_email} - wygasł: {$token->token_expires_at->format('d.m.Y H:i')}");
        }
        
        if ($dryRun) {
            $this->info('🔍 Tryb testowy - tokeny nie zostały oznaczone jako wygasłe.');
            return 0;
        }
        
        // Oznacz jako wygasłe
        $updatedCount = PasswordResetLog::where('status', 'pending')
            ->where('token_expires_at', '<', $now)
            ->update(['status' => 'expired']);
        
        $this->info("✅ Oznaczono {$updatedCount} tokenów jako wygasłe.");
        
        // Pokaż statystyki
        $this->newLine();
        $this->info('📊 Statystyki tokenów:');
        $this->line("  • Oczekujące: " . PasswordResetLog::where('status', 'pending')->count());
        $this->line("  • Zakończone: " . PasswordResetLog::where('status', 'completed')->count());
        $this->line("  • Wygasłe: " . PasswordResetLog::where('status', 'expired')->count());
        
        return 0;
    }
}
