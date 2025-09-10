<?php

namespace App\Console\Commands;

use App\Models\PreRegistration;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CleanupExpiredPreRegistrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pre-registrations:cleanup 
                            {--days=0 : Liczba dni po wygaśnięciu, po których usunąć pre-rejestracje (0 = natychmiast)}
                            {--used-only : Usuń tylko używane pre-rejestracje (po konwersji)}
                            {--dry-run : Pokaż co zostanie usunięte bez faktycznego usuwania}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Usuwa wygasłe pre-rejestracje (domyślnie natychmiast po wygaśnięciu)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $usedOnly = $this->option('used-only');
        $dryRun = $this->option('dry-run');
        
        if ($usedOnly) {
            // Usuwanie tylko używanych pre-rejestracji (po konwersji)
            $expiredPreRegistrations = PreRegistration::where('used', true)
                ->where('used_at', '<', now()->subDays($days))
                ->get();
        } elseif ($days === 0) {
            // Natychmiastowe usuwanie - usuń tylko wygasłe NIEUŻYTE
            $expiredPreRegistrations = PreRegistration::where('expires_at', '<', now())
                ->where('used', false)
                ->get();
        } else {
            // Usuwanie z opóźnieniem - usuń wygasłe NIEUŻYTE starsze niż X dni
            $cutoffDate = Carbon::now()->subDays($days);
            $expiredPreRegistrations = PreRegistration::where('expires_at', '<', $cutoffDate)
                ->where('used', false)
                ->get();
        }
            
        $count = $expiredPreRegistrations->count();
        
        if ($count === 0) {
            $this->info('Nie znaleziono pre-rejestracji do usunięcia.');
            return 0;
        }
        
        if ($dryRun) {
            if ($usedOnly) {
                $message = "DRY RUN: Znaleziono {$count} używanych pre-rejestracji starszych niż {$days} dni do usunięcia:";
            } elseif ($days === 0) {
                $message = "DRY RUN: Znaleziono {$count} wygasłych NIEUŻYTYCH pre-rejestracji do natychmiastowego usunięcia:";
            } else {
                $message = "DRY RUN: Znaleziono {$count} wygasłych NIEUŻYTYCH pre-rejestracji starszych niż {$days} dni do usunięcia:";
            }
            $this->info($message);
            $this->newLine();
            
            if ($usedOnly) {
                $headers = ['ID', 'Imię', 'Email', 'Token', 'Użyty', 'Utworzono'];
                $rows = $expiredPreRegistrations->map(function ($preReg) {
                    return [
                        $preReg->id,
                        $preReg->name ?: 'Brak',
                        $preReg->email ?: 'Brak',
                        substr($preReg->token, 0, 8) . '...',
                        $preReg->used_at->format('d.m.Y H:i'),
                        $preReg->created_at->format('d.m.Y H:i'),
                    ];
                })->toArray();
            } else {
                $headers = ['ID', 'Imię', 'Email', 'Token', 'Wygasł', 'Utworzono'];
                $rows = $expiredPreRegistrations->map(function ($preReg) {
                    return [
                        $preReg->id,
                        $preReg->name ?: 'Brak',
                        $preReg->email ?: 'Brak',
                        substr($preReg->token, 0, 8) . '...',
                        $preReg->expires_at->format('d.m.Y H:i'),
                        $preReg->created_at->format('d.m.Y H:i'),
                    ];
                })->toArray();
            }
            
            $this->table($headers, $rows);
            $this->newLine();
            $this->info("Aby faktycznie usunąć te pre-rejestracje, uruchom komendę bez --dry-run");
            return 0;
        }
        
        // Potwierdź usunięcie
        if (!$this->confirm("Czy na pewno chcesz usunąć {$count} wygasłych pre-rejestracji?")) {
            $this->info('Operacja anulowana.');
            return 0;
        }
        
        // Usuń pre-rejestracje
        $deletedCount = 0;
        foreach ($expiredPreRegistrations as $preReg) {
            try {
                $preReg->delete();
                $deletedCount++;
            } catch (\Exception $e) {
                $this->error("Błąd podczas usuwania pre-rejestracji ID {$preReg->id}: " . $e->getMessage());
            }
        }
        
        $this->info("✅ Pomyślnie usunięto {$deletedCount} wygasłych pre-rejestracji.");
        
        // Pokaż statystyki
        $remainingCount = PreRegistration::count();
        $this->info("📊 Pozostało {$remainingCount} pre-rejestracji w systemie.");
        
        return 0;
    }
}