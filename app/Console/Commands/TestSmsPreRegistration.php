<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PreRegistration;
use App\Services\SmsService;
use Illuminate\Support\Facades\Log;

class TestSmsPreRegistration extends Command
{
    protected $signature = 'sms:test-pre-registration {--phone= : Numer telefonu do testu} {--create : Utwórz nową pre-rejestrację do testu}';
    protected $description = 'Testuje wysyłanie SMS z linkiem pre-rejestracji';

    public function handle()
    {
        $this->info('🧪 Testowanie SMS z pre-rejestracją...');
        
        $phone = $this->option('phone');
        $create = $this->option('create');
        
        if ($create) {
            $this->createTestPreRegistration($phone);
        } else {
            $this->testExistingPreRegistration($phone);
        }
    }

    protected function createTestPreRegistration(?string $phone)
    {
        $this->info('📝 Tworzenie testowej pre-rejestracji...');
        
        if (!$phone) {
            $phone = $this->ask('Podaj numer telefonu do testu (format: 123456789)');
        }
        
        // Utwórz testową pre-rejestrację
        $preReg = PreRegistration::create([
            'token' => PreRegistration::generateToken(),
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => $phone,
            'expires_at' => now()->addHours(24),
        ]);
        
        $this->info("✅ Utworzono pre-rejestrację ID: {$preReg->id}");
        $this->info("📱 Numer telefonu: {$phone}");
        $this->info("🔗 Token: {$preReg->token}");
        
        // Wyślij SMS
        $this->sendTestSms($preReg);
    }

    protected function testExistingPreRegistration(?string $phone)
    {
        $this->info('🔍 Wyszukiwanie istniejących pre-rejestracji...');
        
        $query = PreRegistration::query();
        
        if ($phone) {
            $query->where('phone', 'like', "%{$phone}%");
        }
        
        $preRegs = $query->where('expires_at', '>', now())
                        ->where('used', false)
                        ->limit(5)
                        ->get();
        
        if ($preRegs->isEmpty()) {
            $this->warn('❌ Nie znaleziono ważnych pre-rejestracji');
            $this->info('💡 Użyj --create aby utworzyć nową pre-rejestrację do testu');
            return;
        }
        
        $this->info("📋 Znaleziono {$preRegs->count()} pre-rejestracji:");
        
        foreach ($preRegs as $index => $preReg) {
            $this->info("  " . ($index + 1) . ". {$preReg->name} - {$preReg->phone} (Token: {$preReg->token})");
        }
        
        if ($preRegs->count() === 1) {
            $selectedPreReg = $preRegs->first();
        } else {
            $choice = $this->ask('Wybierz numer pre-rejestracji (1-' . $preRegs->count() . ')');
            $selectedPreReg = $preRegs[$choice - 1] ?? null;
        }
        
        if (!$selectedPreReg) {
            $this->error('❌ Nieprawidłowy wybór');
            return;
        }
        
        $this->sendTestSms($selectedPreReg);
    }

    protected function sendTestSms(PreRegistration $preReg)
    {
        $this->info("📱 Wysyłanie SMS na numer: {$preReg->phone}");
        
        try {
            $smsService = new SmsService();
            $url = route('pre-register', $preReg->token);
            
            $this->info("🔗 Link pre-rejestracji: {$url}");
            
            $result = $smsService->sendPreRegistrationLink($preReg->phone, $url);
            
            if ($result) {
                $this->info('✅ SMS wysłany pomyślnie!');
                $this->info('📊 Sprawdź logi w bazie danych (tabela sms_logs)');
                $this->info('📋 Sprawdź logi Laravel: tail -f storage/logs/laravel.log');
            } else {
                $this->error('❌ Nie udało się wysłać SMS');
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Błąd wysyłania SMS: ' . $e->getMessage());
            Log::error('SMS Pre-registration Test Failed', [
                'pre_registration_id' => $preReg->id,
                'phone' => $preReg->phone,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
