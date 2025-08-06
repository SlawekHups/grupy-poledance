<?php

namespace App\Listeners;

use App\Models\User;
use App\Models\UserMailMessage;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Queue\InteractsWithQueue;

class LogOutgoingMail
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(MessageSent $event): void
    {
        $message = $event->message;
        $to = $message->getTo();
        
        if (!$to) {
            return;
        }

        // Pobierz pierwszy adres email z listy odbiorców
        $email = null;
        
        if (is_array($to)) {
            // Sprawdź czy to tablica obiektów Address
            if (!empty($to) && is_object($to[0]) && method_exists($to[0], 'getAddress')) {
                $email = $to[0]->getAddress();
            } else {
                // Standardowa tablica asocjacyjna
                $email = array_keys($to)[0] ?? null;
            }
        } elseif (is_string($to)) {
            $email = $to;
        } elseif (is_object($to) && method_exists($to, 'getAddress')) {
            $email = $to->getAddress();
        }
        
        if (!$email) {
            \Illuminate\Support\Facades\Log::warning('LogOutgoingMail: Nie można wyciągnąć adresu email', ['to' => $to]);
            return;
        }
        
        // Debug - sprawdź co otrzymujemy
        \Illuminate\Support\Facades\Log::info('LogOutgoingMail debug', [
            'to' => $to,
            'email' => $email,
            'to_type' => gettype($to),
            'to_keys' => is_array($to) ? array_keys($to) : 'not_array'
        ]);
        
        // Znajdź użytkownika po emailu
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            return; // Nie loguj maili do niezarejestrowanych użytkowników
        }

        // Sprawdź czy wiadomość już została zapisana (w ciągu ostatnich 5 sekund)
        $existingMessage = UserMailMessage::where('user_id', $user->id)
            ->where('direction', 'out')
            ->where('email', $email)
            ->where('subject', $message->getSubject() ?? 'Brak tematu')
            ->where('sent_at', '>=', now()->subSeconds(5))
            ->first();
            
        if ($existingMessage) {
            \Illuminate\Support\Facades\Log::info('LogOutgoingMail: Wiadomość już została zapisana', [
                'user_id' => $user->id,
                'email' => $email,
                'subject' => $message->getSubject()
            ]);
            return;
        }
        
        // Zapisz wiadomość do bazy
        UserMailMessage::create([
            'user_id' => $user->id,
            'direction' => 'out',
            'email' => $email,
            'subject' => $message->getSubject() ?? 'Brak tematu',
            'content' => $this->extractContent($message),
            'sent_at' => now(),
            'headers' => $this->extractHeaders($message, $email),
            'message_id' => uniqid(),
        ]);
    }

    /**
     * Wyciągnij treść z wiadomości
     */
    private function extractContent($message): string
    {
        // Dla wiadomości tekstowych
        if ($message->getTextBody()) {
            return $message->getTextBody();
        }
        
        // Dla wiadomości HTML - wyciągnij tylko treść z message-content
        if ($message->getHtmlBody()) {
            $html = $message->getHtmlBody();
            
            // Spróbuj wyciągnąć treść z div.message-content
            if (preg_match('/<div[^>]*class="[^"]*message-content[^"]*"[^>]*>(.*?)<\/div>/s', $html, $matches)) {
                $content = strip_tags($matches[1]);
                // Usuń nadmiarowe białe znaki
                $content = preg_replace('/\s+/', ' ', $content);
                return trim($content);
            }
            
            // Jeśli nie ma message-content, usuń wszystkie tagi HTML
            return strip_tags($html);
        }
        
        // Dla zwykłego body
        $body = $message->getBody();
        if (is_string($body)) {
            return strip_tags($body);
        }
        
        return 'Treść niedostępna';
    }

    /**
     * Wyciągnij nagłówki z wiadomości
     */
    private function extractHeaders($message, $email): array
    {
        try {
            return [
                'from' => config('mail.from.address'),
                'to' => $email,
                'subject' => $message->getSubject(),
                'date' => now()->format('Y-m-d H:i:s'),
            ];
        } catch (\Exception $e) {
            return [
                'error' => 'Nie można odczytać nagłówków: ' . $e->getMessage(),
            ];
        }
    }
    
    /**
     * Wyciągnij adres email z wiadomości
     */
    private function extractEmailFromMessage($message): ?string
    {
        $to = $message->getTo();
        
        if (is_array($to)) {
            return array_keys($to)[0] ?? null;
        } elseif (is_string($to)) {
            return $to;
        } elseif (is_object($to) && method_exists($to, 'getAddress')) {
            return $to->getAddress();
        }
        
        return null;
    }
}
