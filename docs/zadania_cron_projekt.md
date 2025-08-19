# ⏰ Zadania Cron w Projekcie - Kompletna Dokumentacja

## 📋 Przegląd

Projekt zawiera **12 zaplanowanych zadań cron** zorganizowanych w **7 kategorii**. Wszystkie zadania są skonfigurowane w jednym pliku `bootstrap/schedule.php` i uruchamiane przez Laravel Scheduler.

## 🚀 Jak uruchomić wszystkie zadania

### **1. Konfiguracja cron na serwerze:**
```bash
# Dodaj do crontab:
* * * * * cd /ścieżka/do/projektu && php artisan schedule:run >> /dev/null 2>&1
```

### **2. Dla cPanel/WHM:**
1. **cPanel** → **Cron Jobs**
2. **Command:** `cd /home/username/domains/twoja-domena.pl/public_html && php artisan schedule:run`
3. **Common Settings:** `Every Minute`

### **3. Sprawdź czy cron działa:**
```bash
crontab -l
tail -f /var/log/cron
```

## 📊 Lista wszystkich zadań

### **🔹 FINANSE - PŁATNOŚCI I PRZYPOMNIENIA**

#### **1. Przypomnienia o płatnościach**
- **Komenda:** `payments:send-reminders`
- **Harmonogram:** Codziennie od poniedziałku do piątku o 9:00
- **Opis:** Wysyła przypomnienia o płatnościach do użytkowników z zaległościami
- **Funkcje:**
  - Automatyczne wykrywanie grup na dany dzień
  - Sprawdzanie zaległości w płatnościach
  - Generowanie i wysyłanie emaili
  - Logowanie wszystkich operacji
- **Opcje:** `--dry-run` (tryb testowy)

#### **2. Generowanie płatności miesięcznych**
- **Komenda:** `payments:generate`
- **Harmonogram:** Co miesiąc, 1. dnia o 6:00
- **Opis:** Tworzy płatności dla aktywnych użytkowników na nowy miesiąc
- **Funkcje:**
  - Sprawdzanie aktywnych użytkowników
  - Tworzenie nowych płatności
  - Unikanie duplikatów

---

### **🔹 KOMUNIKACJA - MAILE I IMPORT**

#### **3. Import maili przychodzących**
- **Komenda:** `mails:import-incoming --days=30`
- **Harmonogram:** Codziennie o 8:00
- **Opis:** Importuje maile z serwera IMAP
- **Funkcje:**
  - Połączenie z serwerem IMAP
  - Filtrowanie maili od zarejestrowanych użytkowników
  - Czyszczenie treści (HTML, CSS, emoji)
  - Zapisywanie do bazy danych
- **Parametry:** `--days=30` (import z ostatnich 30 dni)

---

### **🔹 SYSTEM - KOLEJKA I CACHE**

#### **4. Przetwarzanie kolejki**
- **Komenda:** `queue:work --stop-when-empty --max-time=300`
- **Harmonogram:** Co 5 minut
- **Opis:** Przetwarza zadania w kolejce
- **Funkcje:**
  - Przetwarzanie emaili
  - Przetwarzanie powiadomień
  - Automatyczne zatrzymanie po 5 minutach
  - Obsługa błędów

#### **5. Sprawdzanie nieudanych zadań**
- **Komenda:** `queue:failed --force`
- **Harmonogram:** Codziennie o 7:00
- **Opis:** Sprawdza nieudane zadania w kolejce
- **Funkcje:**
  - Lista nieudanych zadań
  - Możliwość ponownego uruchomienia
  - Czyszczenie starych błędów

---

### **🔹 MONITORING I LOGI**

#### **6. Czyszczenie logów**
- **Komenda:** `log:clear`
- **Harmonogram:** Co tydzień w niedzielę o 2:00
- **Opis:** Czyści stare pliki logów
- **Funkcje:**
  - Usuwanie starych plików logów
  - Oszczędność miejsca na dysku
  - Zachowanie najnowszych logów

#### **7. Health check aplikacji**
- **Komenda:** `app:health-check`
- **Harmonogram:** Co godzinę
- **Opis:** Sprawdza stan zdrowia aplikacji
- **Funkcje:**
  - Sprawdzanie połączenia z bazą danych
  - Sprawdzanie dysku
  - Sprawdzanie pamięci
  - Alerty w przypadku problemów

---

### **🔹 BACKUP I BEZPIECZEŃSTWO**

#### **8. Backup bazy danych**
- **Komenda:** `backup:run`
- **Harmonogram:** Codziennie o 3:00
- **Opis:** Tworzy backup bazy danych
- **Funkcje:**
  - Backup całej bazy danych
  - Kompresja plików
  - Upload do chmury (opcjonalnie)
  - Powiadomienia o statusie

#### **9. Czyszczenie starych backupów**
- **Komenda:** `backup:clean`
- **Harmonogram:** Co tydzień w sobotę o 4:00
- **Opis:** Usuwa stare pliki backupów
- **Funkcje:**
  - Usuwanie backupów starszych niż X dni
  - Oszczędność miejsca na dysku
  - Zachowanie najnowszych backupów

---

### **🔹 CACHE I OPTYMALIZACJA**

#### **10. Czyszczenie cache**
- **Komenda:** `cache:clear`
- **Harmonogram:** Codziennie o 5:00
- **Opis:** Czyści cache aplikacji
- **Funkcje:**
  - Czyszczenie cache aplikacji
  - Czyszczenie cache konfiguracji
  - Czyszczenie cache routingu
  - Czyszczenie cache widoków

#### **11. Optymalizacja autoloadera**
- **Komenda:** `optimize:clear`
- **Harmonogram:** Co tydzień w niedzielę o 1:00
- **Opis:** Optymalizuje autoloader i cache
- **Funkcje:**
  - Czyszczenie wszystkich cache
  - Regeneracja autoloadera
  - Optymalizacja wydajności
  - Reset konfiguracji

---

### **🔹 STATYSTYKI I RAPORTY**

#### **12. Raport dzienny**
- **Komenda:** `reports:generate-daily`
- **Harmonogram:** Codziennie o 23:00
- **Opis:** Generuje dzienny raport aktywności
- **Funkcje:**
  - Statystyki użytkowników
  - Statystyki płatności
  - Statystyki obecności
  - Eksport do PDF/Excel

---

### **🔹 INFORMACJE O ZADANIACH (tylko dev)**

#### **13. Lista zadań (development)**
- **Komenda:** `schedule:list`
- **Harmonogram:** Codziennie o 6:00 (tylko w development)
- **Opis:** Wyświetla listę wszystkich zaplanowanych zadań
- **Funkcje:**
  - Lista wszystkich zadań
  - Szczegóły harmonogramu
  - Status zadań

## 📅 Harmonogram dzienny

```
00:00 - (brak zadań)
01:00 - Optymalizacja autoloadera (niedziela)
02:00 - Czyszczenie logów (niedziela)
03:00 - Backup bazy danych
04:00 - Czyszczenie backupów (sobota)
05:00 - Czyszczenie cache
06:00 - Generowanie płatności miesięcznych + Lista zadań (dev)
07:00 - Sprawdzanie nieudanych zadań w kolejce
08:00 - Import maili przychodzących
09:00 - Przypomnienia o płatnościach (pon-pt)
10:00 - Health check aplikacji
11:00 - Health check aplikacji
12:00 - Health check aplikacji
13:00 - Health check aplikacji
14:00 - Health check aplikacji
15:00 - Health check aplikacji
16:00 - Health check aplikacji
17:00 - Health check aplikacji
18:00 - Health check aplikacji
19:00 - Health check aplikacji
20:00 - Health check aplikacji
21:00 - Health check aplikacji
22:00 - Health check aplikacji
23:00 - Raport dzienny
```

## 🔧 Konfiguracja i opcje

### **Opcje zadań:**
- **`withoutOverlapping()`** - zapobiega uruchomieniu wielu instancji
- **`runInBackground()`** - uruchamia w tle (nie blokuje)
- **`onOneServer()`** - uruchamia tylko na jednym serwerze (dla klastrów)
- **`description()`** - opis zadania dla logów
- **`onSuccess()`** - callback po sukcesie
- **`onFailure()`** - callback po błędzie

### **Strefy czasowe:**
- **Domyślna:** UTC
- **Można zmienić:** `->timezone('Europe/Warsaw')`

### **Logowanie:**
- **Wszystkie zadania** są logowane
- **Sukces:** `Log::info()`
- **Błędy:** `Log::error()`
- **Lokalizacja:** `storage/logs/laravel.log`

## 🧪 Testowanie zadań

### **1. Sprawdź listę wszystkich zadań:**
```bash
php artisan schedule:list-all
php artisan schedule:list-all --detailed
```

### **2. Uruchom zadanie ręcznie:**
```bash
# Przypomnienia o płatnościach
php artisan payments:send-reminders --dry-run

# Import maili
php artisan mails:import-incoming --days=30

# Generowanie płatności
php artisan payments:generate
```

### **3. Sprawdź logi:**
```bash
tail -f storage/logs/laravel.log
```

## 🚨 Rozwiązywanie problemów

### **Problem: Zadania nie są uruchamiane**
1. Sprawdź czy cron jest aktywny: `crontab -l`
2. Sprawdź logi cron: `tail -f /var/log/cron`
3. Sprawdź logi aplikacji: `tail -f storage/logs/laravel.log`
4. Uruchom ręcznie: `php artisan schedule:run`

### **Problem: Błędy w zadaniach**
1. Sprawdź logi aplikacji
2. Uruchom zadanie ręcznie
3. Sprawdź konfigurację
4. Sprawdź uprawnienia na serwerze

### **Problem: Zadania się nakładają**
1. Dodaj `withoutOverlapping()`
2. Sprawdź czas wykonania zadań
3. Zwiększ `max-time` dla długich zadań

## 📈 Monitoring i alerty

### **Co monitorować:**
- **Status zadań** - sukces/błąd
- **Czas wykonania** - czy nie trwa za długo
- **Użycie zasobów** - CPU, pamięć, dysk
- **Logi błędów** - automatyczne alerty

### **Narzędzia monitoringu:**
- **Laravel Log** - wbudowane logowanie
- **Cron Log** - logi systemu cron
- **System Monitor** - monitoring serwera
- **Email Alerts** - powiadomienia o błędach

## 🎯 Podsumowanie

Projekt ma **kompleksowy system zadań cron** z:
- ✅ **12 zaplanowanych zadań** w 7 kategoriach
- ✅ **Automatyczne zarządzanie** przez Laravel Scheduler
- ✅ **Logowanie i monitoring** wszystkich operacji
- ✅ **Obsługa błędów** i powiadomienia
- ✅ **Optymalizacja wydajności** i zasobów
- ✅ **Backup i bezpieczeństwo** danych
- ✅ **Raporty i statystyki** automatyczne

**Wszystkie zadania są w jednym pliku** `bootstrap/schedule.php` i uruchamiane przez **jedną linię cron**! 🚀
