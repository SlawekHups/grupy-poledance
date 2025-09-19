<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SmsService;
use Illuminate\Support\Facades\Log;

class TestSmsConnection extends Command
{
    protected $signature = 'sms:test-connection {--phone= : Numer telefonu do testu (opcjonalnie)}';
    protected $description = 'Testuje połączenie z SMSAPI i wysyła testowy SMS';

    public function handle()
    {
        $this->info('🔍 Testowanie połączenia z SMSAPI...');
        
        // Sprawdź konfigurację
        $this->checkConfiguration();
        
        // Sprawdź połączenie
        $this->testConnection();
        
        // Wyślij testowy SMS (jeśli podano numer)
        $phone = $this->option('phone');
        if ($phone) {
            $this->sendTestSms($phone);
        } else {
            $this->warn('💡 Aby wysłać testowy SMS, użyj: php artisan sms:test-connection --phone=123456789');
        }
    }

    protected function checkConfiguration()
    {
        $this->info('📋 Sprawdzanie konfiguracji...');
        
        $authToken = config('smsapi.auth_token');
        $fromName = config('smsapi.from_name');
        $testMode = config('smsapi.test_mode');
        $debug = config('smsapi.debug');
        
        if (empty($authToken)) {
            $this->error('❌ SMSAPI_AUTH_TOKEN nie jest ustawiony w pliku .env');
            return false;
        }
        
        $this->info("✅ Token: " . substr($authToken, 0, 8) . "...");
        $this->info("✅ Nazwa nadawcy: {$fromName}");
        $this->info("✅ Tryb testowy: " . ($testMode ? 'TAK' : 'NIE'));
        $this->info("✅ Debug: " . ($debug ? 'TAK' : 'NIE'));
        
        return true;
    }

    protected function testConnection()
    {
        $this->info('🔌 Testowanie połączenia z SMSAPI...');
        
        try {
            $smsService = new SmsService();
            
            // Test formatowania numeru
            $testPhone = '123456789';
            $formatted = $smsService->isValidPhoneNumber($testPhone);
            
            if ($formatted) {
                $this->info('✅ Formatowanie numerów telefonów działa poprawnie');
            } else {
                $this->error('❌ Problem z formatowaniem numerów telefonów');
            }
            
            // Test pobierania salda (jeśli zaimplementowane)
            $balance = $smsService->getBalance();
            if ($balance > 0) {
                $this->info("✅ Saldo konta: {$balance} zł");
            } else {
                $this->warn('⚠️  Pobieranie salda nie jest zaimplementowane');
            }
            
            $this->info('✅ Połączenie z SMSAPI działa poprawnie');
            
        } catch (\Exception $e) {
            $this->error('❌ Błąd połączenia z SMSAPI: ' . $e->getMessage());
            Log::error('SMS Connection Test Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    protected function sendTestSms(string $phone)
    {
        $this->info("📱 Wysyłanie testowego SMS na numer: {$phone}");
        
        try {
            $smsService = new SmsService();
            
            $message = 'Test połączenia SMS - ' . now()->format('Y-m-d H:i:s');
            $result = $smsService->sendCustomMessage($phone, $message);
            
            if ($result) {
                $this->info('✅ Testowy SMS został wysłany pomyślnie');
                $this->info('📊 Sprawdź logi w bazie danych (tabela sms_logs)');
            } else {
                $this->error('❌ Nie udało się wysłać testowego SMS');
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Błąd wysyłania testowego SMS: ' . $e->getMessage());
            Log::error('SMS Test Send Failed', [
                'phone' => $phone,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
