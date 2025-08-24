# 📅 Zadania Cron - Instrukcja

## 🎯 Przegląd Systemu

System wykorzystuje Laravel Scheduler do automatycznego wykonywania zadań w tle. Wszystkie zadania są skonfigurowane w pliku `bootstrap/schedule.php` i uruchamiane przez główny cron systemu.

## ⚙️ Konfiguracja Główna

### Cron System (Linux/macOS)
```bash
# Dodaj do crontab (crontab -e):
* * * * * cd /ścieżka/do/projektu && php artisan schedule:run
```

### Sprawdzenie Statusu
```bash
# Lista wszystkich zadań
php artisan schedule:list-all

# Lista zadań Laravel
php artisan schedule:list

# Test pojedynczego zadania
php artisan payments:generate-missing
php artisan mails:import-incoming
php artisan users:check-invitations
```

## 🚀 Zadania Co 1 Minutę

### 1. Sprawdzanie Zaproszeń i Wygasłych Linków
```bash
php artisan users:check-invitations
```
**Opis:** Sprawdza użytkowników bez hasła i wygasłe linki zaproszeń (72h)
**Funkcje:**
- Identyfikuje użytkowników bez hasła
- Sprawdza ważność linków zaproszeń
- Automatycznie usuwa wygasłe tokeny
- Loguje wymagające ręcznego zaproszenia przez admina

**Logi:** `storage/logs/laravel.log`
**Status:** ✅ Aktywne

### 2. Przetwarzanie Kolejki
```bash
php artisan queue:work
```
**Opis:** Przetwarza zadania w kolejce (maile, powiadomienia)
**Funkcje:**
- Wysyłanie maili w tle
- Przetwarzanie powiadomień
- Obsługa zadań asynchronicznych

**Status:** ✅ Aktywne

## ⚡ Zadania Co 2 Minuty

### 3. Sprawdzanie Brakujących Płatności
```bash
php artisan payments:generate-missing
```
**Opis:** Generuje brakujące płatności dla nowych użytkowników
**Funkcje:**
- Sprawdza nowych użytkowników
- Generuje płatności za bieżący miesiąc
- Aktualizuje status płatności

**Status:** ✅ Aktywne

### 4. Import Maili Przychodzących
```bash
php artisan mails:import-incoming
```
**Opis:** Importuje maile z serwera IMAP
**Funkcje:**
- Pobiera nowe maile
- Synchronizuje z bazą danych
- Aktualizuje status wiadomości

**Status:** ✅ Aktywne

## 📅 Zadania Dziennie

### 5. Backup Bazy Danych
```bash
php artisan backup:run
```
**Czas:** 03:00
**Opis:** Tworzy kopię zapasową bazy danych
**Status:** ✅ Aktywne

### 6. Czyszczenie Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```
**Czas:** 05:00
**Opis:** Czyści wszystkie cache systemu
**Status:** ✅ Aktywne

### 7. Sprawdzanie Nieudanych Zadań
```bash
php artisan queue:failed
```
**Czas:** 07:00
**Opis:** Sprawdza i raportuje nieudane zadania w kolejce
**Status:** ✅ Aktywne

### 8. Przypomnienia o Płatnościach
```bash
php artisan payments:send-reminders
```
**Czas:** 09:00 (poniedziałek-piątek)
**Opis:** Wysyła przypomnienia o zaległych płatnościach
**Funkcje:**
- Sprawdza zaległe płatności
- Wysyła maile przypominające
- Loguje wysłane przypomnienia

**Status:** ✅ Aktywne

## 📊 Zadania Tygodniowe

### 9. Czyszczenie Backupów
```bash
php artisan backup:clean
```
**Czas:** Sobota 04:00
**Opis:** Usuwa stare kopie zapasowe
**Status:** ✅ Aktywne

### 10. Optymalizacja Autoloadera
```bash
composer dump-autoload --optimize
```
**Czas:** Niedziela 01:00
**Opis:** Optymalizuje autoloader Composera
**Status:** ✅ Aktywne

### 11. Czyszczenie Logów
```bash
php artisan log:clear
```
**Czas:** Niedziela 02:00
**Opis:** Czyści stare pliki logów
**Status:** ✅ Aktywne

## 📈 Zadania Miesięczne

### 12. Generowanie Płatności
```bash
php artisan payments:generate
```
**Czas:** 1. dnia miesiąca 06:00
**Opis:** Generuje płatności dla wszystkich użytkowników
**Status:** ✅ Aktywne

## 🔧 Zarządzanie Zadaniami

### Sprawdzenie Statusu
```bash
# Lista wszystkich zadań z opisami
php artisan schedule:list-all

# Lista zadań Laravel
php artisan schedule:list

# Sprawdzenie logów
tail -f storage/logs/laravel.log
```

### Ręczne Uruchomienie
```bash
# Uruchomienie pojedynczego zadania
php artisan payments:generate-missing
php artisan mails:import-incoming
php artisan users:check-invitations

# Uruchomienie wszystkich zadań
php artisan schedule:run
```

### Debugowanie
```bash
# Sprawdzenie logów konkretnego zadania
grep "payments:generate-missing" storage/logs/laravel.log
grep "mails:import-incoming" storage/logs/laravel.log
grep "users:check-invitations" storage/logs/laravel.log
```

## 📋 Monitoring i Alerty

### Logi Systemowe
- **Lokalizacja:** `storage/logs/laravel.log`
- **Format:** JSON z timestamp
- **Poziom:** INFO, WARNING, ERROR

### Kluczowe Eventy
- ✅ Zadanie ukończone pomyślnie
- ❌ Błąd wykonania zadania
- ⚠️ Ostrzeżenia i problemy
- 📧 Wysłane maile i powiadomienia

### Sprawdzanie Statusu
```bash
# Sprawdź czy cron działa
crontab -l

# Sprawdź logi systemu
sudo tail -f /var/log/cron

# Test połączenia z bazą
php artisan tinker --execute="echo 'DB: ' . (DB::connection()->getPdo() ? 'OK' : 'Błąd');"
```

## 🚨 Rozwiązywanie Problemów

### Problem: Zadania nie uruchamiają się
```bash
# Sprawdź cron
crontab -l

# Sprawdź uprawnienia
ls -la /ścieżka/do/projektu

# Test ręczny
php artisan schedule:run
```

### Problem: Błędy w logach
```bash
# Sprawdź ostatnie błędy
tail -100 storage/logs/laravel.log | grep ERROR

# Sprawdź konkretne zadanie
grep "payments:generate-missing" storage/logs/laravel.log
```

### Problem: Wysokie zużycie zasobów
```bash
# Sprawdź procesy
ps aux | grep artisan

# Sprawdź pamięć
free -h

# Sprawdź CPU
top
```

## 📚 Przydatne Komendy

### System
```bash
# Status systemu
php artisan about

# Lista wszystkich komend
php artisan list

# Cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Queue
php artisan queue:work
php artisan queue:failed
php artisan queue:restart
```

### Monitoring
```bash
# Sprawdź status bazy
php artisan tinker --execute="echo 'DB: ' . DB::connection()->getPdo() ? 'OK' : 'Błąd';"

# Sprawdź użytkowników bez hasła
php artisan tinker --execute="echo 'Users bez hasła: ' . \App\Models\User::whereNull('password')->count();"

# Sprawdź zaległe płatności
php artisan tinker --execute="echo 'Zaległe płatności: ' . \App\Models\Payment::where('status', 'pending')->count();"
```

## 🔄 Aktualizacje i Zmiany

### Ostatnia Aktualizacja
- **Data:** {{ date('d.m.Y H:i:s') }}
- **Wersja:** 2.0
- **Zmiany:** Modernizacja częstotliwości zadań

### Historia Zmian
- **v2.0** - Sprawdzanie zaproszeń co 1 min, płatności i poczta co 2 min
- **v1.0** - Podstawowa konfiguracja zadań

---

**⚠️ Uwaga:** Przed zmianą konfiguracji cron zawsze zrób backup i przetestuj w środowisku deweloperskim!