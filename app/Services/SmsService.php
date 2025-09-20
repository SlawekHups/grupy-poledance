<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Models\SmsLog;
use App\Notifications\SmsNotification;
use App\Notifications\SmsNotifiable;

class SmsService
{
    protected $fromName;
    protected $testMode;
    protected $debug;

    public function __construct()
    {
        $this->fromName = config('smsapi.from_name');
        $this->testMode = config('smsapi.test_mode');
        $this->debug = config('smsapi.debug');
    }

    /**
     * Wysyła SMS
     */
    public function sendSms(string $phone, string $message, string $type = 'general'): bool
    {
        try {
            $phone = $this->formatPhoneNumber($phone);
            
            if (!$phone) {
                $this->logSms($phone, $message, $type, 'error', null, null, 'Nieprawidłowy numer telefonu');
                return false;
            }

            // Utwórz notyfikację SMS
            $notification = new SmsNotification($message, $this->fromName, $this->testMode);

            // Utwórz notyfikowalny obiekt
            $notifiable = new SmsNotifiable($phone);

            // Wyślij notyfikację
            Notification::send($notifiable, $notification);
            
            $this->logSms($phone, $message, $type, 'sent');

            Log::info('SMS wysłany pomyślnie', [
                'phone' => $phone,
                'type' => $type,
                'message' => $message
            ]);

            return true;

        } catch (\Exception $e) {
            $this->logSms($phone, $message, $type, 'error', null, null, $e->getMessage());
            
            Log::error('Błąd wysyłania SMS', [
                'phone' => $phone,
                'type' => $type,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Formatuje numer telefonu do formatu międzynarodowego
     */
    protected function formatPhoneNumber(string $phone): ?string
    {
        // Usuń wszystkie znaki niebędące cyframi
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Jeśli zaczyna się od 48, usuń
        if (str_starts_with($phone, '48')) {
            $phone = substr($phone, 2);
        }
        
        // Jeśli zaczyna się od 0, usuń
        if (str_starts_with($phone, '0')) {
            $phone = substr($phone, 1);
        }
        
        // Sprawdź czy ma 9 cyfr
        if (strlen($phone) === 9) {
            return '48' . $phone;
        }
        
        return null;
    }

    /**
     * Loguje SMS do bazy danych
     */
    protected function logSms(string $phone, string $message, string $type, string $status, ?string $messageId = null, ?float $cost = null, ?string $errorMessage = null): void
    {
        try {
            // Jeśli koszt nie został podany, oblicz go automatycznie
            if ($cost === null && $status === 'sent') {
                $cost = config('smsapi.pricing.cost_per_sms', 0.17);
            }
            
            SmsLog::create([
                'phone' => $phone,
                'message' => $message,
                'type' => $type,
                'status' => $status,
                'message_id' => $messageId,
                'cost' => $cost,
                'error_message' => $errorMessage,
                'sent_at' => $status === 'sent' ? now() : null,
            ]);
        } catch (\Exception $e) {
            Log::error('Błąd logowania SMS', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Wysyła SMS z linkiem do pre-rejestracji
     */
    public function sendPreRegistrationLink(string $phone, string $link): bool
    {
        $template = config('smsapi.templates.pre_registration');
        $message = str_replace('{link}', $link, $template);
        return $this->sendSms($phone, $message, 'pre_registration');
    }

    /**
     * Wysyła SMS z linkiem do poprawy danych
     */
    public function sendDataCorrectionLink(string $phone, string $link): bool
    {
        $template = config('smsapi.templates.data_correction');
        $message = str_replace('{link}', $link, $template);
        return $this->sendSms($phone, $message, 'data_correction');
    }

    /**
     * Wysyła SMS z linkiem do resetu hasła
     */
    public function sendPasswordResetLink(string $phone, string $link): bool
    {
        $template = config('smsapi.templates.password_reset');
        $message = str_replace('{link}', $link, $template);
        return $this->sendSms($phone, $message, 'password_reset');
    }

    /**
     * Wysyła przypomnienie o płatności
     */
    public function sendPaymentReminder(string $phone, float $amount, string $dueDate, string $paymentLink = ''): bool
    {
        $template = config('smsapi.templates.payment_reminder');
        $message = str_replace(['{amount}', '{due_date}', '{link}'], [$amount, $dueDate, $paymentLink], $template);
        return $this->sendSms($phone, $message, 'payment_reminder');
    }

    /**
     * Wysyła niestandardową wiadomość
     */
    public function sendCustomMessage(string $phone, string $message): bool
    {
        return $this->sendSms($phone, $message, 'custom');
    }

    /**
     * Pobiera saldo konta SMSAPI
     */
    public function getBalance(): ?float
    {
        try {
            $token = config('smsapi.auth_token');
            
            if (!$token) {
                Log::error('Brak tokenu SMS API');
                return null;
            }
            
            $url = 'https://api.smsapi.pl/profile';
            $ch = curl_init();
            
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer $token",
                "Content-Type: application/json"
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            
            curl_close($ch);
            
            if ($error) {
                Log::error('Błąd cURL przy pobieraniu salda SMS API', ['error' => $error]);
                return null;
            }
            
            if ($httpCode !== 200) {
                Log::error('Błąd HTTP przy pobieraniu salda SMS API', ['http_code' => $httpCode, 'response' => $response]);
                return null;
            }
            
            $data = json_decode($response, true);
            
            if (!$data || !isset($data['points'])) {
                Log::error('Nieprawidłowa odpowiedź z SMS API', ['response' => $response]);
                return null;
            }
            
            $balance = (float) $data['points'];
            
            Log::info('Pobrano saldo SMS API', [
                'balance' => $balance,
                'username' => $data['username'] ?? 'unknown'
            ]);
            
            return $balance;
            
        } catch (\Exception $e) {
            Log::error('Błąd podczas pobierania salda SMS API', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Sprawdza czy numer telefonu jest prawidłowy
     */
    public function isValidPhoneNumber(string $phone): bool
    {
        $formatted = $this->formatPhoneNumber($phone);
        return $formatted !== null;
    }
}
