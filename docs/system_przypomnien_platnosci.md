# System Automatycznych Przypomnień o Płatnościach

## Opis systemu

System automatycznie wysyła przypomnienia o płatnościach do użytkowników z zaległościami, bazując na harmonogramie zajęć ich grup.

## Jak to działa

### 1. **Inteligentne wykrywanie grup**
- System automatycznie parsuje nazwy grup (np. "Poniedziałek 18:00", "Wtorek 19:00")
- Wykrywa, które grupy mają zajęcia w danym dniu tygodnia
- Wysyła przypomnienia tylko do użytkowników z grup, które mają zajęcia

### 2. **Sprawdzanie zaległości**
- Sprawdza wszystkie nieopłacone płatności użytkownika
- Rozróżnia płatności **bieżące** (za bieżący miesiąc) od **zaległych**
- Generuje różne treści wiadomości w zależności od typu zaległości

### 3. Automatyczne wysyłanie
- **Codziennie od poniedziałku do piątku o 9:00** - system sprawdza grupy na dany dzień
- **Poniedziałek 9:00** - sprawdza grupy poniedziałkowe
- **Wtorek 9:00** - sprawdza grupy wtorkowe
- **Środa 9:00** - sprawdza grupy środowe
- **Czwartek 9:00** - sprawdza grupy czwartkowe
- **Piątek 9:00** - sprawdza grupy piątkowe
- **Sobota/Niedziela** - brak akcji (nie ma grup weekendowych)

## Komenda artisan

```bash
# Wysłanie przypomnień (produkcja)
php artisan payments:send-reminders

# Tryb testowy - pokazuje co zostanie wysłane, ale nie wysyła
php artisan payments:send-reminders --dry-run
```

## Konfiguracja cron na serwerze

### **Krok 1: Dodaj do crontab**
```bash
crontab -e
```

### **Krok 2: Dodaj linię**
```bash
# Uruchamia Laravel scheduler co minutę
* * * * * cd /ścieżka/do/twojego/projektu && php artisan schedule:run >> /dev/null 2>&1
```

### **Przykład dla cPanel/WHM:**
```bash
# W cPanel -> Cron Jobs
# Command: cd /home/username/domains/twoja-domena.pl/public_html && php artisan schedule:run
# Common Settings: Every Minute
```

## Harmonogram zadań

| Zadanie | Częstotliwość | Czas | Opis |
|---------|---------------|------|------|
| `payments:send-reminders` | Codziennie (pon-pt) | 9:00 | Wysyłanie przypomnień o płatnościach |
| `payments:generate` | Co miesiąc | 1. dnia 6:00 | Generowanie nowych płatności |
| `mails:import-incoming` | Codziennie | 8:00 | Import maili z serwera IMAP |
| `queue:work` | Co 5 minut | - | Przetwarzanie zadań w kolejce |

## Struktura wiadomości email

### **Przypomnienie bieżące:**
- Temat: "Przypomnienie o płatności za [Miesiąc] - Grupa [Nazwa]"
- Treść: Informacja o płatności za bieżący miesiąc

### **Przypomnienie o zaległościach:**
- Temat: "PILNE: Zaległości w płatnościach - Grupa [Nazwa]"
- Treść: Lista wszystkich zaległych miesięcy + kwoty

### **Zawartość emaila:**
- Podsumowanie zaległości (liczba miesięcy, łączna kwota)
- Szczegółowa tabela z miesiącami i kwotami
- Instrukcje co dalej
- Dane kontaktowe
- Ostrzeżenie o konsekwencjach długotrwałych zaległości

## Logi i monitoring

### **Lokalizacja logów:**
```bash
tail -f storage/logs/laravel.log
```

### **Szukanie w logach:**
```bash
# Wszystkie wysłane przypomnienia
grep "Wysłano przypomnienie o płatności" storage/logs/laravel.log

# Błędy wysyłania
grep "Błąd wysyłania przypomnienia" storage/logs/laravel.log
```

### **Przykładowe logi:**
```
[2025-01-20 09:00:01] local.INFO: Wysłano przypomnienie o płatności {"user_id":5,"user_email":"user@example.com","group":"Poniedziałek 18:00","unpaid_count":2,"total_amount":400}
```

## Testowanie systemu

### **1. Sprawdź czy komenda działa:**
```bash
php artisan payments:send-reminders --dry-run
```

### **2. Sprawdź logi:**
```bash
tail -f storage/logs/laravel.log
```

### **3. Sprawdź czy cron działa:**
```bash
# Sprawdź czy cron jest aktywny
crontab -l

# Sprawdź logi cron
tail -f /var/log/cron
```

## Rozwiązywanie problemów

### **Problem: Przypomnienia nie są wysyłane**
1. Sprawdź czy cron jest aktywny: `crontab -l`
2. Sprawdź logi: `tail -f storage/logs/laravel.log`
3. Uruchom ręcznie: `php artisan payments:send-reminders --dry-run`

### **Problem: Błędy wysyłania emaili**
1. Sprawdź konfigurację mail w `.env`
2. Sprawdź logi błędów
3. Uruchom test: `php artisan tinker` -> `Mail::raw('test', function($msg) { $msg->to('test@example.com')->subject('test'); });`

### **Problem: Grupy nie są wykrywane**
1. Sprawdź nazwy grup w bazie danych
2. Upewnij się, że nazwy zawierają dni tygodnia (Poniedziałek, Wtorek, itd.)
3. Sprawdź czy grupy mają przypisanych użytkowników

## Dostosowywanie

### **Zmiana częstotliwości:**
Edytuj `bootstrap/schedule.php`:
```php
// Codziennie o 9:00 zamiast co poniedziałek
Schedule::command('payments:send-reminders')
    ->daily()
    ->at('09:00');
```

### **Zmiana treści emaili:**
Edytuj `app/Console/Commands/SendPaymentReminders.php` w metodzie `generateReminderContent()`

### **Dodanie nowych typów grup:**
Dodaj nowe dni w tablicy `$dayNames` w komendzie

## Bezpieczeństwo

- System wysyła przypomnienia tylko do aktywnych użytkowników
- Nie wysyła do administratorów
- Loguje wszystkie wysłane wiadomości
- Ma tryb testowy (`--dry-run`) do bezpiecznego testowania

## Wymagania systemowe

- Laravel 11+
- PHP 8.2+
- Dostęp do cron na serwerze
- Skonfigurowany system mail
- Baza danych z tabelami: `users`, `payments`, `groups`

## Wsparcie

W przypadku problemów:
1. Sprawdź logi aplikacji
2. Sprawdź logi cron
3. Uruchom komendę w trybie testowym
4. Sprawdź konfigurację mail
5. Sprawdź uprawnienia na serwerze
