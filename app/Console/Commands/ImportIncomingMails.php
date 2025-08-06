<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserMailMessage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ImportIncomingMails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mails:import-incoming {--days=7 : Liczba dni wstecz do sprawdzenia}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importuje maile przychodzące z IMAP dla zarejestrowanych użytkowników';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Rozpoczynam import maili przychodzących...');
        
        // Sprawdź konfigurację IMAP
        $host = config('mail.imap.host');
        $port = config('mail.imap.port');
        $username = config('mail.imap.username');
        $password = config('mail.imap.password');
        
        if (!$host || !$username || !$password) {
            $this->error('Brak konfiguracji IMAP w config/mail.php');
            return 1;
        }
        
        try {
            // Połącz z serwerem IMAP
            $mailbox = "{{$host}:{$port}/imap/ssl/novalidate-cert}INBOX";
            $inbox = imap_open($mailbox, $username, $password);
            
            if (!$inbox) {
                $this->error('Nie można połączyć się z serwerem IMAP: ' . imap_last_error());
                return 1;
            }
            
            $days = $this->option('days');
            $date = date('d-M-Y', strtotime("-{$days} days"));
            
            // Pobierz maile z ostatnich X dni
            $emails = imap_search($inbox, "SINCE {$date}");
            
            if (!$emails) {
                $this->info('Brak nowych maili do importu.');
                imap_close($inbox);
                return 0;
            }
            
            $imported = 0;
            $skipped = 0;
            
            foreach ($emails as $email_number) {
                $header = imap_headerinfo($inbox, $email_number);
                $from_email = $header->from[0]->mailbox . '@' . $header->from[0]->host;
                
                // Sprawdź czy nadawca jest zarejestrowanym użytkownikiem
                $user = User::where('email', $from_email)->first();
                
                if (!$user) {
                    $skipped++;
                    continue; // Pomiń maile od niezarejestrowanych użytkowników
                }
                
                // Sprawdź czy mail już został zaimportowany
                $messageId = $header->message_id ?? uniqid();
                // Wyczyść message_id z nieprawidłowych znaków
                if ($messageId && $messageId !== uniqid()) {
                    $messageId = trim($messageId, '<>');
                    $messageId = preg_replace('/[^a-zA-Z0-9@._-]/', '', $messageId);
                }
                $existing = UserMailMessage::where('message_id', $messageId)->first();
                
                if ($existing) {
                    $skipped++;
                    continue;
                }
                
                // Pobierz treść maila
                $body = imap_fetchbody($inbox, $email_number, 1);
                $subject = $header->subject ?? 'Brak tematu';
                
                // Zapisz mail do bazy
                UserMailMessage::create([
                    'user_id' => $user->id,
                    'direction' => 'in',
                    'email' => $from_email,
                    'subject' => $subject,
                    'content' => $this->cleanContent($body),
                    'sent_at' => date('Y-m-d H:i:s', strtotime($header->date)),
                    'headers' => $this->extractHeaders($header),
                    'message_id' => $messageId,
                ]);
                
                $imported++;
                $this->line("Zaimportowano mail od: {$from_email} - {$subject}");
            }
            
            imap_close($inbox);
            
            $this->info("Import zakończony. Zaimportowano: {$imported}, Pominięto: {$skipped}");
            
            Log::info('Import maili przychodzących zakończony', [
                'imported' => $imported,
                'skipped' => $skipped,
                'days' => $days
            ]);
            
        } catch (\Exception $e) {
            $this->error('Błąd podczas importu: ' . $e->getMessage());
            Log::error('Błąd importu maili', ['error' => $e->getMessage()]);
            return 1;
        }
        
        return 0;
    }
    
    /**
     * Wyczyść treść maila
     */
    private function cleanContent($content): string
    {
        // Usuń nagłówki MIME
        $content = preg_replace('/^.*?\r?\n\r?\n/s', '', $content);
        
        // Dekoduj quoted-printable
        $content = quoted_printable_decode($content);
        
        // Dekoduj base64
        if (preg_match('/Content-Transfer-Encoding: base64/i', $content)) {
            $content = base64_decode($content);
        }
        
        // Jeśli to HTML, spróbuj wyciągnąć treść z div.message-content
        if (strpos($content, '<div class="message-content">') !== false) {
            preg_match('/<div class="message-content">(.*?)<\/div>/s', $content, $matches);
            if (!empty($matches[1])) {
                $content = $matches[1];
            }
        }
        
        // Jeśli to HTML, spróbuj wyciągnąć treść z div.content
        if (strpos($content, '<div class="content">') !== false) {
            preg_match('/<div class="content">(.*?)<\/div>/s', $content, $matches);
            if (!empty($matches[1])) {
                $content = $matches[1];
            }
        }
        
        // Usuń wszystkie tagi HTML
        $content = strip_tags($content);
        
        // Dekoduj encje HTML
        $content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // Usuń znaki kontrolne i niebezpieczne
        $content = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $content);
        
        // Usuń nadmiarowe białe znaki i nowe linie
        $content = preg_replace('/\s+/', ' ', $content);
        $content = trim($content);
        
        // Usuń emoji i znaki specjalne
        $content = preg_replace('/[\x{1F600}-\x{1F64F}]|[\x{1F300}-\x{1F5FF}]|[\x{1F680}-\x{1F6FF}]|[\x{1F1E0}-\x{1F1FF}]|[\x{2600}-\x{26FF}]|[\x{2700}-\x{27BF}]/u', '', $content);
        
        // Ogranicz długość
        if (strlen($content) > 5000) {
            $content = substr($content, 0, 5000) . '... [treść została skrócona]';
        }
        
        return $content ?: 'Treść niedostępna';
    }
    
    /**
     * Wyciągnij nagłówki z maila
     */
    private function extractHeaders($header): array
    {
        // Wyczyść message_id z nieprawidłowych znaków
        $messageId = $header->message_id ?? null;
        if ($messageId) {
            // Usuń nieprawidłowe znaki z message_id
            $messageId = trim($messageId, '<>');
            $messageId = preg_replace('/[^a-zA-Z0-9@._-]/', '', $messageId);
        }
        
        return [
            'from' => $header->from[0]->mailbox . '@' . $header->from[0]->host,
            'to' => $header->to[0]->mailbox . '@' . $header->to[0]->host ?? null,
            'date' => $header->date ?? null,
            'message_id' => $messageId,
        ];
    }
}
