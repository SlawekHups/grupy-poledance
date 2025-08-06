# System Wiadomości Email - Grupy Poledance

## Cel
Panel mailowy pokazuje **WYŁĄCZNIE** komunikację z zarejestrowanymi użytkownikami systemu, bez spamu, newsletterów, reklam itp.

## Architektura Systemu

### 1. Model `UserMailMessage`
```php
// Pola w bazie danych:
- user_id (nullable) - ID użytkownika
- direction: 'in' (odebrana) lub 'out' (wysłana)
- email - adres email nadawcy/odbiorcy
- subject - temat wiadomości
- content - treść wiadomości (oczyszczona z HTML)
- sent_at - data wysłania/odebrania
- headers - nagłówki emaila (JSON)
- message_id - unikalny ID wiadomości (oczyszczony z nieprawidłowych znaków)
```

### 2. Automatyczne Logowanie Wysyłanych Maili
- **Listener:** `LogOutgoingMail`
- **Event:** `MessageSent` (Laravel)
- **Działanie:** Automatycznie zapisuje każdy wysłany email do zarejestrowanego użytkownika
- **Deduplikacja:** Sprawdza czy wiadomość już została zapisana w ciągu ostatnich 5 sekund

### 3. Import Maili Przychodzących
- **Command:** `php artisan mails:import-incoming --days=7`
- **Filtrowanie:** Tylko maile od zarejestrowanych użytkowników
- **Deduplikacja:** Sprawdza `message_id` przed importem
- **Czyszczenie treści:** Usuwa HTML, CSS, emoji, nieprawidłowe znaki
- **Czyszczenie message_id:** Usuwa `<>` i nieprawidłowe znaki

### 4. Wysyłanie Wiadomości Bezpośrednio z Zakładki Użytkowników
- **Akcja pojedyncza:** "Wyślij wiadomość" przy każdym użytkowniku
- **Akcja grupowa:** "Wyślij wiadomości" dla wielu użytkowników
- **Automatyczne wypełnianie:** Nazwa i email użytkownika
- **Rich Editor:** Formatowanie tekstu (pogrubienie, kursywa, linki, listy)

## Funkcjonalności

### Panel Administratora (`/admin/user-mail-messages`)
- ✅ **Przeglądanie wszystkich wiadomości** zarejestrowanych użytkowników
- ✅ **Filtrowanie** po kierunku, użytkowniku, dacie
- ✅ **Wyszukiwanie** po temacie, treści, emailu
- ✅ **Akcja "Odpowiedz"** dla maili przychodzących
- ✅ **Tworzenie ręczne** wiadomości
- ✅ **Edycja i usuwanie** wiadomości
- ✅ **Kolorowe badge'y** dla kierunku wiadomości (zielony=odebrana, pomarańczowy=wysłana)

### Panel Użytkownika (`/panel/user-mail-messages`)
- ✅ **Przeglądanie tylko swoich wiadomości**
- ✅ **Filtrowanie** po kierunku i dacie
- ✅ **Wyszukiwanie** po temacie i treści
- ✅ **Akcja "Odpowiedz"** dla maili przychodzących
- ✅ **Tylko podgląd** (brak edycji/tworzenia)

### Wysyłanie Wiadomości z Zakładki Użytkowników
- ✅ **Akcja "Wyślij wiadomość"** - pojedynczy użytkownik
- ✅ **Akcja "Wyślij wiadomości"** - grupa użytkowników
- ✅ **Modal z formularzem** - temat i treść
- ✅ **Rich Editor** - formatowanie tekstu
- ✅ **Automatyczne logowanie** - wiadomości zapisywane w bazie
- ✅ **Filtrowanie aktywnych** - tylko aktywni użytkownicy

## Konfiguracja

### 1. Konfiguracja IMAP (dla importu)
Dodaj do `config/mail.php`:
```php
'imap' => [
    'host' => env('MAIL_IMAP_HOST', 'mail.hupsnet.pl'),
    'port' => env('MAIL_IMAP_PORT', 993),
    'username' => env('MAIL_IMAP_USERNAME', 'info@hupsnet.pl'),
    'password' => env('MAIL_IMAP_PASSWORD'),
    'encryption' => env('MAIL_IMAP_ENCRYPTION', 'ssl'),
],
```

### 2. Zmienne środowiskowe
```env
MAIL_IMAP_HOST=mail.hupsnet.pl
MAIL_IMAP_PORT=993
MAIL_IMAP_USERNAME=info@hupsnet.pl
MAIL_IMAP_PASSWORD=twoje_haslo
MAIL_IMAP_ENCRYPTION=ssl
```

## Użycie

### Import Maili Przychodzących
```bash
# Import z ostatnich 7 dni (domyślnie)
php artisan mails:import-incoming

# Import z ostatnich 30 dni
php artisan mails:import-incoming --days=30

# Automatyzacja przez cron
# Dodaj do crona:
0 */6 * * * cd /ścieżka/do/projektu && php artisan mails:import-incoming
```

### Automatyczne Logowanie Wysyłanych Maili
System automatycznie loguje wszystkie wysyłane maile do zarejestrowanych użytkowników:
- Zaproszenia użytkowników
- Powiadomienia systemowe
- Wiadomości wysyłane z zakładki użytkowników
- Inne maile wysyłane przez aplikację

### Wysyłanie Wiadomości z Zakładki Użytkowników

#### Pojedynczy użytkownik:
1. Przejdź do listy użytkowników (`/admin/users`)
2. Znajdź użytkownika
3. Kliknij akcję "Wyślij wiadomość" (ikona 💬)
4. Wypełnij temat i treść w modalu
5. Kliknij "Wyślij wiadomość"

#### Grupa użytkowników:
1. Zaznacz wielu użytkowników na liście
2. Wybierz akcję masową "Wyślij wiadomości"
3. Wypełnij temat i treść w modalu
4. Kliknij "Wyślij wiadomości"

## Bezpieczeństwo

### Filtrowanie Dostępu
- **Admin:** Widzi wszystkie wiadomości zarejestrowanych użytkowników
- **Użytkownik:** Widzi tylko swoje wiadomości (`user_id = auth()->id()`)
- **Nieznajomi:** Brak dostępu do systemu

### Walidacja
- Tylko maile do/z zarejestrowanych użytkowników są logowane
- Sprawdzanie duplikatów przez `message_id`
- Walidacja formatu emaila
- Filtrowanie tylko aktywnych użytkowników

## Struktura Plików

```
app/
├── Models/
│   └── UserMailMessage.php
├── Listeners/
│   └── LogOutgoingMail.php
├── Console/Commands/
│   └── ImportIncomingMails.php
├── Mail/
│   └── UserMessageMail.php
└── Filament/
    ├── Admin/Resources/
    │   └── UserMailMessageResource.php
    └── UserPanel/Resources/
        └── UserMailMessageResource.php

resources/views/emails/
├── user-invitation.blade.php
└── user-message.blade.php
```

## Monitoring i Logi

### Logi Systemu
```bash
# Sprawdź logi importu
tail -f storage/logs/laravel.log | grep "Import maili"

# Sprawdź błędy
tail -f storage/logs/laravel.log | grep "ERROR"

# Sprawdź logi listenera
tail -f storage/logs/laravel.log | grep "LogOutgoingMail"
```

### Statystyki
- **Badge w nawigacji:** Pokazuje liczbę wiadomości
- **Filtry:** Po kierunku, użytkowniku, dacie
- **Wyszukiwanie:** Pełnotekstowe w temacie i treści

## Troubleshooting

### Problem: Maile nie są logowane
1. Sprawdź czy listener jest zarejestrowany w `AppServiceProvider`
2. Sprawdź czy email odbiorcy jest w bazie `users`
3. Sprawdź logi Laravel
4. Sprawdź czy nie ma duplikatów (listener sprawdza ostatnie 5 sekund)

### Problem: Import IMAP nie działa
1. Sprawdź konfigurację IMAP w `config/mail.php`
2. Sprawdź uprawnienia do serwera IMAP
3. Sprawdź czy PHP ma rozszerzenie `imap`
4. Sprawdź czy `message_id` jest poprawnie czyszczony

### Problem: Błąd 500 w panelu Filament
1. Sprawdź czy `message_id` nie zawiera nieprawidłowych znaków (`<>`)
2. Sprawdź czy treść wiadomości jest oczyszczona z HTML
3. Sprawdź czy relacja `user` nie powoduje problemów
4. Sprawdź logi Laravel pod kątem "Over 9 levels deep"

### Problem: Duplikaty wiadomości wychodzących
1. Sprawdź czy listener `LogOutgoingMail` ma sprawdzenie duplikatów
2. Sprawdź czy `message_id` jest unikalny
3. Sprawdź logi listenera

### Problem: Brak dostępu do wiadomości
1. Sprawdź czy użytkownik jest zalogowany
2. Sprawdź czy `user_id` jest poprawnie ustawiony
3. Sprawdź middleware autoryzacji

### Problem: Akcja "Wyślij wiadomość" nie działa
1. Sprawdź czy użytkownik jest aktywny (`is_active = true`)
2. Sprawdź czy email użytkownika jest poprawny
3. Sprawdź konfigurację SMTP

## Poprawki Wykonane

### 1. Czyszczenie message_id
- **Problem:** `message_id` zawierał nieprawidłowe znaki `<>`
- **Rozwiązanie:** Dodano czyszczenie w `ImportIncomingMails.php` i `LogOutgoingMail.php`
- **Kod:** `trim($messageId, '<>')` i `preg_replace('/[^a-zA-Z0-9@._-]/', '', $messageId)`

### 2. Czyszczenie treści wiadomości
- **Problem:** Treść zawierała pełny HTML z CSS
- **Rozwiązanie:** Dodano wyciąganie treści z `div.message-content` i usuwanie emoji
- **Kod:** `preg_match('/<div class="message-content">(.*?)<\/div>/s', $content, $matches)`

### 3. Deduplikacja wiadomości wychodzących
- **Problem:** Listener tworzył duplikaty wiadomości
- **Rozwiązanie:** Dodano sprawdzenie duplikatów w ciągu ostatnich 5 sekund
- **Kod:** Sprawdzenie `user_id`, `direction`, `email`, `subject` i `sent_at`

### 4. Uproszczenie Filament Resource
- **Problem:** Błąd "Over 9 levels deep" w panelu
- **Rozwiązanie:** Usunięto problematyczne relacje i uproszczono kolumny
- **Zmiany:** Usunięto relację `user` z filtrów i formularza

## Następne Kroki

### Opcjonalne Usprawnienia
- [ ] Automatyczne tagowanie wiadomości
- [ ] System powiadomień o nowych mailach
- [ ] Eksport wiadomości do PDF/CSV
- [ ] Integracja z systemem ticketing
- [ ] Automatyczne odpowiedzi
- [ ] Archiwizacja starych wiadomości
- [ ] Szablony wiadomości
- [ ] Planowanie wysyłki wiadomości

### Monitoring
- [ ] Dashboard z statystykami maili
- [ ] Alerty o problemach z importem
- [ ] Raporty aktywności użytkowników
- [ ] Analiza trendów komunikacji

---
**Status:** ✅ Gotowe do produkcji  
**Ostatnia aktualizacja:** 6 sierpnia 2025  
**Wersja:** 1.2 - Poprawki stabilności i deduplikacji 