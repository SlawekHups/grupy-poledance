# Funkcja Pre-rejestracji z SMS i Email

## 📋 Przegląd

System pre-rejestracji umożliwia tworzenie zaproszeń do rejestracji w systemie Grupy Poledance z możliwością wysyłania linków przez SMS i email. Użytkownicy mogą zostać zaproszeni do systemu bez konieczności posiadania konta, a następnie ukończyć rejestrację używając otrzymanego linku.

## 🎯 Główne funkcje

### 1. **Tworzenie pre-rejestracji**
- Ręczne tworzenie zaproszeń przez administratora
- Automatyczne generowanie unikalnych tokenów
- Opcjonalne pola: imię, email, telefon
- Walidacja danych wejściowych

### 2. **Wysyłanie SMS**
- Wysyłanie linków pre-rejestracji przez SMS
- Niestandardowe wiadomości SMS
- Masowe wysyłanie SMS
- Walidacja numerów telefonów
- Logowanie wszystkich wysłanych SMS

### 3. **Wysyłanie Email**
- Wysyłanie linków pre-rejestracji przez email
- Profesjonalne szablony HTML
- Niestandardowe wiadomości email
- Masowe wysyłanie emaili
- Walidacja adresów email

### 4. **Zarządzanie pre-rejestracjami**
- Lista wszystkich pre-rejestracji
- Filtrowanie i wyszukiwanie
- Edycja i usuwanie
- Konwersja na użytkowników
- Statusy i walidacja

## 🔧 Konfiguracja

### Wymagane zmienne środowiskowe

```env
# SMSAPI Configuration
SMSAPI_AUTH_TOKEN=your_smsapi_token
SMSAPI_FROM_NAME=Poledance
SMSAPI_TEST_MODE=true
SMSAPI_DEBUG=false

# Email Configuration
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME="Grupy Poledance"
```

### Baza danych

System używa tabeli `pre_registrations` z następującymi kolumnami:
- `id` - unikalny identyfikator
- `name` - imię i nazwisko (nullable)
- `email` - adres email (nullable)
- `phone` - numer telefonu (nullable)
- `token` - unikalny token pre-rejestracji
- `created_at` - data utworzenia
- `updated_at` - data aktualizacji

## 📱 SMS - Szczegóły implementacji

### SmsService
```php
// Wysyłanie SMS z linkiem pre-rejestracji
$smsService = new \App\Services\SmsService();
$url = route('pre-register', $record->token);
$result = $smsService->sendPreRegistrationLink($phone, $url);
```

### Funkcje SMS
- **`sendPreRegistrationLink()`** - wysyłanie linku pre-rejestracji
- **`sendCustomMessage()`** - wysyłanie niestandardowej wiadomości
- **`sendPasswordResetLink()`** - wysyłanie linku resetu hasła
- **`sendPaymentReminder()`** - wysyłanie przypomnienia o płatności

### Walidacja numerów telefonów
- Format: 9-15 cyfr
- Opcjonalny prefiks "+"
- Walidacja regex: `/^(\+?[0-9]{9,15})$/`

### Logowanie SMS
Wszystkie SMS są logowane w tabeli `sms_logs`:
- Numer telefonu
- Treść wiadomości
- Typ SMS
- Status wysłania
- Koszt
- Data i czas

## 📧 Email - Szczegóły implementacji

### EmailService
```php
// Wysyłanie email z linkiem pre-rejestracji
$emailService = new \App\Services\EmailService();
$url = route('pre-register', $record->token);
$result = $emailService->sendPreRegistrationLink($email, $url);
```

### Funkcje Email
- **`sendPreRegistrationLink()`** - wysyłanie linku pre-rejestracji
- **`sendPasswordResetLink()`** - wysyłanie linku resetu hasła
- **`sendCustomEmailWithLink()`** - wysyłanie niestandardowego email
- **`sendUserInvitation()`** - wysyłanie zaproszenia użytkownika

### Szablony Email
- **`pre-registration-link.blade.php`** - szablon pre-rejestracji
- **`custom-link.blade.php`** - szablon niestandardowych linków
- **Responsywny design** z gradientami
- **Profesjonalny wygląd** z logo i stopką

### Walidacja email
- Format: `user@example.com`
- Maksymalnie 255 znaków
- Walidacja Laravel: `email|max:255`

## 🎛️ Panel Administratora

### PreRegistrationResource

#### Akcje pojedyncze
- **"Wyślij SMS"** - wysyłanie SMS z linkiem
- **"Wyślij Email"** - wysyłanie email z linkiem
- **"Konwertuj na użytkownika"** - konwersja na pełne konto
- **"Eksportuj do CSV"** - eksport danych

#### Akcje masowe
- **"Wyślij SMS (masowo)"** - masowe wysyłanie SMS
- **"Wyślij Email (masowo)"** - masowe wysyłanie emaili
- **"Usuń zaznaczone"** - usuwanie pre-rejestracji

#### Formularz tworzenia/edycji
- **Imię i nazwisko** (opcjonalne)
- **Email** (opcjonalne)
- **Telefon** (opcjonalne)
- **Walidacja** wszystkich pól
- **Placeholdery** z instrukcjami

### UserResource

#### Akcje dla użytkowników bez hasła
- **"Wyślij SMS z linkiem"** - SMS z linkiem resetu hasła
- **"Wyślij Email z linkiem"** - email z linkiem resetu hasła
- **"Wyślij zaproszenie"** - standardowe zaproszenie

## 🔄 Proces pre-rejestracji

### 1. Tworzenie pre-rejestracji
```
Administrator → Panel Admin → Pre-rejestracje → Nowa pre-rejestracja
```

### 2. Wysyłanie zaproszenia
```
SMS: Administrator → "Wyślij SMS" → Wprowadź telefon → Wyślij
Email: Administrator → "Wyślij Email" → Wprowadź email → Wyślij
```

### 3. Rejestracja użytkownika
```
Użytkownik → Kliknie link → Formularz rejestracji → Ustawienie hasła → Konto aktywne
```

### 4. Konwersja na użytkownika
```
Administrator → "Konwertuj na użytkownika" → Automatyczne utworzenie konta
```

## 📊 Statusy i walidacja

### Statusy pre-rejestracji
- **Ważna** - token aktywny, można używać
- **Nieprawidłowa** - brak wymaganych danych
- **Wygasła** - token wygasł (domyślnie 24h)

### Walidacja
- **Telefon**: 9-15 cyfr, opcjonalny "+"
- **Email**: format email, max 255 znaków
- **Imię**: max 255 znaków
- **Token**: unikalny, 64 znaki

## 🚀 Komendy Artisan

### Testowanie SMS
```bash
php artisan sms:test 48123456789
php artisan sms:test-pre-registration 48123456789
```

### Testowanie Email
```bash
php artisan email:test test@example.com --type=pre-registration
php artisan email:test test@example.com --type=password-reset
php artisan email:test test@example.com --type=custom
```

### Sprawdzanie połączenia
```bash
php artisan sms:test-connection
```

## 📈 Monitoring i logi

### Logi SMS
- Wszystkie wysłane SMS w `sms_logs`
- Błędy w `storage/logs/laravel.log`
- Status wysłania i koszt

### Logi Email
- Błędy w `storage/logs/laravel.log`
- Status wysłania przez Laravel Mail
- Kolejka email w tabeli `jobs`

### Monitoring
- Liczba wysłanych SMS/email
- Błędy wysyłania
- Koszty SMS
- Wydajność systemu

## 🔒 Bezpieczeństwo

### Tokeny pre-rejestracji
- **Unikalne** - każdy token jest unikalny
- **Losowe** - 64 znaki losowe
- **Wygasające** - domyślnie 24h
- **Jednorazowe** - po użyciu są dezaktywowane

### Walidacja danych
- **Sanityzacja** wszystkich danych wejściowych
- **Walidacja** formatów telefonów i email
- **Escape** danych w szablonach
- **CSRF** protection w formularzach

### Dostęp
- **Tylko administratorzy** mogą tworzyć pre-rejestracje
- **Autoryzacja** wszystkich akcji
- **Logowanie** wszystkich operacji

## 🎨 Interfejs użytkownika

### Design
- **Responsywny** - działa na wszystkich urządzeniach
- **Intuicyjny** - łatwe w użyciu
- **Spójny** - zgodny z resztą systemu
- **Profesjonalny** - wysokiej jakości

### Kolory i ikony
- **SMS**: zielony kolor, ikona chat-bubble
- **Email**: niebieski kolor, ikona envelope
- **Sukces**: zielony, ikona check
- **Błąd**: czerwony, ikona x-mark

### Powiadomienia
- **Sukces** - zielone powiadomienia
- **Błąd** - czerwone powiadomienia
- **Informacja** - niebieskie powiadomienia
- **Ostrzeżenie** - żółte powiadomienia

## 📝 Przykłady użycia

### Wysyłanie SMS do pre-rejestracji
```php
// W panelu administratora
$record = PreRegistration::find(1);
$url = route('pre-register', $record->token);
$smsService = new SmsService();
$result = $smsService->sendPreRegistrationLink($record->phone, $url);
```

### Wysyłanie email do pre-rejestracji
```php
// W panelu administratora
$record = PreRegistration::find(1);
$url = route('pre-register', $record->token);
$emailService = new EmailService();
$result = $emailService->sendPreRegistrationLink($record->email, $url);
```

### Masowe wysyłanie SMS
```php
// W panelu administratora - akcja masowa
$records = PreRegistration::whereIn('id', $selectedIds)->get();
foreach ($records as $record) {
    if ($record->phone) {
        $url = route('pre-register', $record->token);
        $smsService->sendPreRegistrationLink($record->phone, $url);
    }
}
```

## 🐛 Rozwiązywanie problemów

### SMS nie są wysyłane
1. Sprawdź konfigurację SMSAPI w `.env`
2. Sprawdź logi w `storage/logs/laravel.log`
3. Sprawdź status konta SMSAPI
4. Uruchom `php artisan sms:test-connection`

### Email nie są wysyłane
1. Sprawdź konfigurację mail w `.env`
2. Sprawdź logi w `storage/logs/laravel.log`
3. Sprawdź kolejkę: `php artisan queue:work`
4. Uruchom `php artisan email:test`

### Błędy walidacji
1. Sprawdź format numeru telefonu
2. Sprawdź format adresu email
3. Sprawdź długość pól
4. Sprawdź logi walidacji

## 🔄 Aktualizacje i rozwój

### Planowane funkcje
- **Szablony SMS** - możliwość tworzenia szablonów
- **Harmonogram wysyłania** - zaplanowane wysyłanie
- **Statystyki** - szczegółowe raporty
- **API** - zewnętrzne API do integracji

### Wersjonowanie
- **v1.0** - podstawowa funkcjonalność SMS i email
- **v1.1** - masowe wysyłanie
- **v1.2** - niestandardowe wiadomości
- **v1.3** - walidacja i bezpieczeństwo

## 📞 Wsparcie

### Dokumentacja
- **README.md** - główna dokumentacja
- **docs/** - szczegółowa dokumentacja
- **Komendy Artisan** - testowanie i debugowanie

### Kontakt
- **Email**: support@example.com
- **Telefon**: +48 123 456 789
- **Strona**: https://example.com

---

**Ostatnia aktualizacja**: 19 września 2025  
**Wersja**: 1.3  
**Autor**: System Grupy Poledance
