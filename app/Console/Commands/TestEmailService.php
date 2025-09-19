<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EmailService;

class TestEmailService extends Command
{
    protected $signature = 'email:test {email} {--type=pre-registration : Typ emaila (pre-registration, password-reset, custom)}';
    protected $description = 'Testuje EmailService - wysyła testowy email';

    public function handle()
    {
        $email = $this->argument('email');
        $type = $this->option('type');
        
        $this->info("Testowanie EmailService...");
        $this->info("Email: {$email}");
        $this->info("Typ: {$type}");
        
        $emailService = new EmailService();
        
        try {
            switch ($type) {
                case 'pre-registration':
                    $this->info("Wysyłanie email pre-rejestracji...");
                    $link = route('pre-register', 'test-token-123');
                    $result = $emailService->sendPreRegistrationLink($email, $link);
                    break;
                    
                case 'password-reset':
                    $this->info("Wysyłanie email resetu hasła...");
                    $link = route('set-password', ['token' => 'test-token-123', 'email' => $email]);
                    $result = $emailService->sendPasswordResetLink($email, $link);
                    break;
                    
                case 'custom':
                    $this->info("Wysyłanie niestandardowego email...");
                    $link = 'https://example.com/test-link';
                    $result = $emailService->sendCustomEmailWithLink(
                        $email,
                        'Test Email - Grupy Poledance',
                        'To jest testowy email z linkiem.',
                        $link
                    );
                    break;
                    
                default:
                    $this->error("Nieznany typ: {$type}");
                    return 1;
            }
            
            if ($result) {
                $this->info("✅ Email wysłany pomyślnie!");
            } else {
                $this->error("❌ Błąd wysyłania email");
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Błąd: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
