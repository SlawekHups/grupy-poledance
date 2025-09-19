# 📱 Konfiguracja SMSAPI - System SMS

## 🔧 Konfiguracja w pliku .env

### **Dodaj do pliku `.env`:**
```bash
# Konfiguracja SMSAPI
SMSAPI_AUTH_TOKEN=your_smsapi_token_here
SMSAPI_FROM_NAME=Poledance
SMSAPI_TEST_MODE=true
SMSAPI_DEBUG=false
```

### **Przykład konfiguracji:**
```bash
SMSAPI_AUTH_TOKEN=abc123def456ghi789
SMSAPI_FROM_NAME=Grupy Poledance
SMSAPI_TEST_MODE=true
SMSAPI_DEBUG=true
```

## 🚀 Jak uzyskać token SMSAPI

1. **Zarejestruj się** na [SMSAPI.pl](https://smsapi.pl)
2. **Zaloguj się** do panelu administracyjnego
3. **Przejdź** do sekcji "API" → "Tokeny"
4. **Utwórz nowy token** z uprawnieniami do wysyłania SMS
5. **Skopiuj token** i wklej do pliku `.env`

## 🧪 Testowanie połączenia

### **1. Sprawdź konfigurację:**
```bash
php artisan sms:test-connection
```

### **2. Wyślij testowy SMS:**
```bash
php artisan sms:test-connection --phone=123456789
```

### **3. Sprawdź logi:**
```bash
tail -f storage/logs/laravel.log
```

## 📊 Sprawdzanie logów SMS

### **W bazie danych:**
```sql
-- Wszystkie SMS-y
SELECT * FROM sms_logs ORDER BY created_at DESC;

-- SMS-y wysłane pomyślnie
SELECT * FROM sms_logs WHERE status = 'sent';

-- SMS-y z błędami
SELECT * FROM sms_logs WHERE status = 'error';
```

### **W logach Laravel:**
```bash
# Wszystkie SMS-y
grep "SMS" storage/logs/laravel.log

# Błędy SMS
grep "Błąd wysyłania SMS" storage/logs/laravel.log
```

## 🔄 Po zmianie konfiguracji

### **1. Wyczyść cache:**
```bash
php artisan config:clear
php artisan cache:clear
```

### **2. Przetestuj:**
```bash
php artisan sms:test-connection
```

## 📋 Dostępne funkcje SMS

### **1. Wysyłanie niestandardowej wiadomości:**
```php
$smsService = new \App\Services\SmsService();
$smsService->sendCustomMessage('123456789', 'Witaj!');
```

### **2. Wysyłanie linku pre-rejestracji:**
```php
$smsService->sendPreRegistrationLink('123456789', 'https://example.com/register');
```

### **3. Wysyłanie przypomnienia o płatności:**
```php
$smsService->sendPaymentReminder('123456789', 200.00, '2024-02-01', 'https://example.com/pay');
```

### **4. Wysyłanie linku resetu hasła:**
```php
$smsService->sendPasswordResetLink('123456789', 'https://example.com/reset');
```

## ⚙️ Ustawienia zaawansowane

### **Tryb testowy:**
- `SMSAPI_TEST_MODE=true` - SMS-y nie są wysyłane, tylko logowane
- `SMSAPI_TEST_MODE=false` - SMS-y są wysyłane rzeczywiście

### **Debug:**
- `SMSAPI_DEBUG=true` - szczegółowe logi
- `SMSAPI_DEBUG=false` - podstawowe logi

### **Nazwa nadawcy:**
- `SMSAPI_FROM_NAME` - nazwa wyświetlana jako nadawca SMS

## 🚨 Rozwiązywanie problemów

### **Problem: "SMSAPI_AUTH_TOKEN nie jest ustawiony"**
1. Sprawdź czy token jest w pliku `.env`
2. Uruchom `php artisan config:clear`
3. Sprawdź czy token jest poprawny

### **Problem: "Błąd połączenia z SMSAPI"**
1. Sprawdź czy token jest aktywny w panelu SMSAPI
2. Sprawdź czy masz saldo na koncie
3. Sprawdź logi Laravel

### **Problem: "SMS nie został wysłany"**
1. Sprawdź czy numer telefonu jest poprawny
2. Sprawdź czy tryb testowy jest wyłączony
3. Sprawdź logi w tabeli `sms_logs`

## 📈 Monitoring i statystyki

### **Statystyki SMS:**
```sql
-- Liczba SMS-ów wg typu
SELECT type, COUNT(*) as count FROM sms_logs GROUP BY type;

-- Liczba SMS-ów wg statusu
SELECT status, COUNT(*) as count FROM sms_logs GROUP BY status;

-- Koszty SMS-ów
SELECT SUM(cost) as total_cost FROM sms_logs WHERE cost IS NOT NULL;
```

### **Ostatnie SMS-y:**
```sql
SELECT phone, message, type, status, created_at 
FROM sms_logs 
ORDER BY created_at DESC 
LIMIT 10;
```
