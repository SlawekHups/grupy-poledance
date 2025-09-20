<?php

namespace App\Console\Commands;

use App\Services\SmsService;
use Illuminate\Console\Command;

class CheckSmsBalanceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:balance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sprawdza aktualne saldo konta SMS API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Sprawdzanie salda SMS API...');
        
        $smsService = new SmsService();
        $balance = $smsService->getBalance();
        
        if ($balance !== null) {
            $this->info("✅ Saldo SMS API: " . number_format($balance, 2) . " PLN (" . number_format($balance, 0) . " punktów)");
            
            // Sprawdź czy saldo jest niskie
            if ($balance < 1) {
                $this->warn("⚠️  UWAGA: Saldo jest bardzo niskie! Zasil konto, aby móc wysyłać SMS-y.");
            } elseif ($balance < 10) {
                $this->warn("⚠️  UWAGA: Saldo jest niskie. Rozważ zasilenie konta.");
            } else {
                $this->info("✅ Saldo jest wystarczające do wysyłania SMS-ów.");
            }
            
            // Oblicz ile SMS-ów można wysłać
            $costPerSms = config('smsapi.pricing.cost_per_sms', 0.17);
            $availableSms = floor($balance / $costPerSms);
            $this->info("📱 Można wysłać jeszcze: " . $availableSms . " SMS-ów");
            
        } else {
            $this->error("❌ Nie udało się pobrać salda SMS API. Sprawdź konfigurację i logi.");
            return 1;
        }
        
        return 0;
    }
}
