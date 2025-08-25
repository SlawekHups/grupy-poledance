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
        // Preferuj HTML body – chcemy zachować formatowanie w podglądzie
        if ($message->getHtmlBody()) {
            $html = $message->getHtmlBody();

            // Zbuduj DOM i usuń <style>/<script>
            $dom = new \DOMDocument();
            libxml_use_internal_errors(true);
            $loaded = $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            libxml_clear_errors();

            if ($loaded) {
                $xpath = new \DOMXPath($dom);

                // Usuń wszystkie <style> i <script>
                foreach ($xpath->query('//style|//script') as $node) {
                    if ($node && $node->parentNode) {
                        $node->parentNode->removeChild($node);
                    }
                }

                // Spróbuj znaleźć główny kontener treści
                $container = $xpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " content ") or contains(concat(" ", normalize-space(@class), " "), " message-content ")]')->item(0);

                // Fallback: body
                if (!$container) {
                    $container = $xpath->query('//body')->item(0);
                }

                // Jeśli mamy kontener, zwróć jego innerHTML
                if ($container) {
                    return trim($this->getInnerHtml($container));
                }

                // Ostatecznie – zwróć całe HTML bez stylów/skryptów
                return trim($dom->saveHTML());
            }

            // Jeśli DOM nie załadował się – zwróć surowy HTML
            return trim($html);
        }

        // Tekstowe body – zwróć jako tekst (podgląd przerobi na HTML Markdownem)
        if ($message->getTextBody()) {
            return $message->getTextBody();
        }

        // Dla zwykłego body
        $body = $message->getBody();
        if (is_string($body)) {
            return $body;
        }

        return 'Treść niedostępna';
    }

    /**
     * Zwraca innerHTML danego węzła DOM.
     */
    private function getInnerHtml(\DOMNode $node): string
    {
        $innerHTML = '';
        foreach ($node->childNodes as $child) {
            $innerHTML .= $node->ownerDocument->saveHTML($child);
        }
        return $innerHTML;
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
