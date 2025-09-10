<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PreRegistration;
use Carbon\Carbon;

class GeneratePreRegistrationTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pre-register:generate {count=10 : Liczba tokenów do wygenerowania} {--minutes=30 : Czas ważności w minutach}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generuje tokeny pre-rejestracji';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = (int) $this->argument('count');
        $minutes = (int) $this->option('minutes');
        
        $this->info("Generowanie {$count} tokenów pre-rejestracji (ważne przez {$minutes} minut)...");
        
        $tokens = [];
        $expiresAt = now()->addMinutes($minutes);
        
        for ($i = 0; $i < $count; $i++) {
            $token = PreRegistration::generateToken();
            
            $preReg = PreRegistration::create([
                'token' => $token,
                'name' => '',
                'email' => '',
                'phone' => '',
                'expires_at' => $expiresAt,
            ]);
            
            $url = route('pre-register', ['token' => $token]);
            $tokens[] = [
                'id' => $preReg->id,
                'token' => $token,
                'url' => $url,
                'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
            ];
        }
        
        $this->info("✅ Wygenerowano {$count} tokenów:");
        $this->newLine();
        
        // Wyświetl tabelę
        $headers = ['ID', 'Token', 'URL', 'Wygasa'];
        $rows = array_map(function($token) {
            return [
                $token['id'],
                substr($token['token'], 0, 8) . '...',
                $token['url'],
                $token['expires_at'],
            ];
        }, $tokens);
        
        $this->table($headers, $rows);
        
        $this->newLine();
        $this->info("💡 Wszystkie tokeny są ważne przez {$minutes} minut");
        $this->info("📧 Możesz wysłać te linki przez SMS, Messenger lub email");
        
        return 0;
    }
}
