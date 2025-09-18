<?php

namespace App\Console\Commands;

use App\Models\DataCorrectionLink;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CleanupDataCorrectionLinks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data-correction-links:cleanup 
                            {--used : Usuń tylko użyte linki}
                            {--expired : Usuń tylko przeterminowane linki}
                            {--all : Usuń wszystkie niepotrzebne linki (użyte i przeterminowane)}
                            {--days=30 : Usuń linki starsze niż X dni (domyślnie 30)}
                            {--dry-run : Pokaż co zostanie usunięte bez usuwania}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Czyści niepotrzebne linki do poprawy danych (użyte i przeterminowane)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧹 Rozpoczynam czyszczenie linków do poprawy danych...');
        
        $used = $this->option('used');
        $expired = $this->option('expired');
        $all = $this->option('all');
        $days = (int) $this->option('days');
        $dryRun = $this->option('dry-run');
        
        // Jeśli nie podano żadnej opcji, usuń wszystkie niepotrzebne
        if (!$used && !$expired && !$all) {
            $all = true;
        }
        
        $query = DataCorrectionLink::query();
        $conditions = [];
        
        if ($used || $all) {
            $query->where('used', true);
            $conditions[] = 'użyte';
        }
        
        if ($expired || $all) {
            $query->where('expires_at', '<', now());
            $conditions[] = 'przeterminowane';
        }
        
        // Dodatkowy filtr dla starych linków
        if ($days > 0) {
            $cutoffDate = now()->subDays($days);
            $query->where('created_at', '<', $cutoffDate);
            $conditions[] = "starsze niż {$days} dni";
        }
        
        $linksToDelete = $query->get();
        $count = $linksToDelete->count();
        
        if ($count === 0) {
            $this->info('✅ Nie znaleziono linków do usunięcia.');
            return 0;
        }
        
        $this->warn("📊 Znaleziono {$count} linków do usunięcia:");
        $this->line("   • Warunki: " . implode(', ', $conditions));
        
        // Pokaż statystyki
        $usedCount = $linksToDelete->where('used', true)->count();
        $expiredCount = $linksToDelete->where('expires_at', '<', now())->count();
        $oldCount = $linksToDelete->where('created_at', '<', now()->subDays($days))->count();
        
        $this->table(
            ['Typ', 'Liczba'],
            [
                ['Użyte', $usedCount],
                ['Przeterminowane', $expiredCount],
                ["Starsze niż {$days} dni", $oldCount],
                ['RAZEM', $count]
            ]
        );
        
        if ($dryRun) {
            $this->info('🔍 Tryb podglądu - nic nie zostało usunięte.');
            return 0;
        }
        
        // Potwierdź usunięcie
        if (!$this->confirm("Czy na pewno chcesz usunąć {$count} linków?")) {
            $this->info('❌ Anulowano usuwanie.');
            return 0;
        }
        
        // Usuń linki
        $deleted = $query->delete();
        
        if ($deleted > 0) {
            $this->info("✅ Pomyślnie usunięto {$deleted} linków do poprawy danych.");
            
            // Pokaż oszczędności
            $this->line("💾 Oszczędności:");
            $this->line("   • Zwolnione miejsce w bazie danych");
            $this->line("   • Szybsze wyszukiwanie w tabeli");
            $this->line("   • Czystszy interfejs administratora");
        } else {
            $this->error('❌ Nie udało się usunąć linków.');
            return 1;
        }
        
        return 0;
    }
}