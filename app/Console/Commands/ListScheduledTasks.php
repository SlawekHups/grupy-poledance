<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ListScheduledTasks extends Command
{
    protected $signature = 'schedule:list-all {--detailed : Pokaż szczegółowe informacje}';
    protected $description = 'Lista wszystkich zaplanowanych zadań cron w projekcie';

    public function handle()
    {
        $this->info('📅 LISTA WSZYSTKICH ZADAŃ CRON W PROJEKCIE');
        $this->line('=' . str_repeat('=', 60));
        
        // Pobierz konfigurację zadań cron
        $cronConfig = config('cron');
        
        if (!$cronConfig) {
            $this->error('Brak konfiguracji cron w config/cron.php');
            return 1;
        }
        
        $this->info("Znaleziono konfigurację zadań cron:");
        $this->newLine();
        
        // Wyświetl kategorie i zadania
        $this->displayCategories($cronConfig);
        
        // Podsumowanie
        $this->displaySummary($cronConfig);
        
        // Instrukcje cron
        $this->displayCronInstructions();
        
        return 0;
    }
    
    /**
     * Wyświetla kategorie i zadania
     */
    private function displayCategories(array $cronConfig): void
    {
        if (!isset($cronConfig['categories'])) {
            $this->warn('Brak kategorii w konfiguracji');
            return;
        }
        
        foreach ($cronConfig['categories'] as $categoryKey => $category) {
            $this->info("🔹 {$category['icon']} {$category['name']}");
            $this->line("   {$category['description']}");
            $this->line(str_repeat('-', 40));
            
            // Wyświetl zadania w tej kategorii
            if (isset($cronConfig['jobs'][$categoryKey])) {
                foreach ($cronConfig['jobs'][$categoryKey] as $jobKey => $jobConfig) {
                    $this->displayJob($jobConfig, $jobKey);
                }
            } else {
                $this->line("   Brak zadań w tej kategorii");
            }
            
            $this->newLine();
        }
    }
    
    /**
     * Wyświetla pojedyncze zadanie
     */
    private function displayJob(array $jobConfig, string $jobKey): void
    {
        $status = $jobConfig['enabled'] ? '✅' : '❌';
        $command = $jobConfig['command'];
        $description = $jobConfig['description'] ?? 'Brak opisu';
        $schedule = $jobConfig['schedule'] ?? 'N/A';
        $time = $jobConfig['time'] ?? '';
        
        $this->line("   {$status} <info>{$command}</info>");
        $this->line("      📝 {$description}");
        $this->line("      ⏰ {$schedule}" . ($time ? " o {$time}" : ""));
        
        if ($this->option('detailed')) {
            if (isset($jobConfig['options'])) {
                $this->line("      ⚙️ Opcje:");
                foreach ($jobConfig['options'] as $option => $value) {
                    $this->line("         • {$option}: " . ($value ? 'Tak' : 'Nie'));
                }
            }
            
            if (isset($jobConfig['arguments'])) {
                $this->line("      📋 Argumenty:");
                foreach ($jobConfig['arguments'] as $argument) {
                    $this->line("         • {$argument}");
                }
            }
        }
        
        $this->newLine();
    }
    
    /**
     * Wyświetla podsumowanie
     */
    private function displaySummary(array $cronConfig): void
    {
        $this->info('📊 PODSUMOWANIE:');
        $this->line('=' . str_repeat('=', 40));
        
        $totalJobs = 0;
        $enabledJobs = 0;
        $categories = count($cronConfig['categories'] ?? []);
        
        if (isset($cronConfig['jobs'])) {
            foreach ($cronConfig['jobs'] as $categoryJobs) {
                foreach ($categoryJobs as $jobConfig) {
                    $totalJobs++;
                    if ($jobConfig['enabled']) {
                        $enabledJobs++;
                    }
                }
            }
        }
        
        $this->line("• Kategorie: <info>{$categories}</info>");
        $this->line("• Łączna liczba zadań: <info>{$totalJobs}</info>");
        $this->line("• Włączone zadania: <info>{$enabledJobs}</info>");
        $this->line("• Wyłączone zadania: <info>" . ($totalJobs - $enabledJobs) . "</info>");
        
        $this->newLine();
    }
    
    /**
     * Wyświetla instrukcje cron
     */
    private function displayCronInstructions(): void
    {
        $this->info('⚙️ KONFIGURACJA CRON:');
        $this->line('=' . str_repeat('=', 40));
        
        $this->line('Dodaj do crontab:');
        $this->line('<comment>* * * * * cd ' . base_path() . ' && php artisan schedule:run</comment>');
        
        $this->newLine();
        
        $this->line('Dla cPanel/WHM:');
        $this->line('1. cPanel → Cron Jobs');
        $this->line('2. Command: cd ' . base_path() . ' && php artisan schedule:run');
        $this->line('3. Common Settings: Every Minute');
        
        $this->newLine();
        
        $this->line('🔍 Sprawdź czy cron działa:');
        $this->line('<comment>crontab -l</comment>');
        $this->line('<comment>tail -f /var/log/cron</comment>');
        
        $this->newLine();
        
        $this->line('📁 Pliki konfiguracyjne:');
        $this->line('• <info>config/cron.php</info> - konfiguracja zadań');
        $this->line('• <info>bootstrap/schedule.php</info> - harmonogram');
        $this->line('• <info>app/Console/Commands/</info> - komendy (wszystkie w głównym katalogu)');
    }
}
