# 🎭 Grupy Poledance - System Zarządzania Szkołą Tańca

[![Laravel](https://img.shields.io/badge/Laravel-12.14.1-red.svg)](https://laravel.com)
[![Filament](https://img.shields.io/badge/Filament-3.3.14-blue.svg)](https://filamentphp.com)
[![PHP](https://img.shields.io/badge/PHP-8.3-purple.svg)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-orange.svg)](https://mysql.com)

## 📋 Spis Treści

- [🎯 O Projekcie](#-o-projekcie)
- [🚀 Funkcjonalności](#-funkcjonalności)
- [🛠️ Technologie](#️-technologie)
- [📦 Instalacja](#-instalacja)
- [🔧 Konfiguracja](#-konfiguracja)
- [📚 Dokumentacja](#-dokumentacja)
- [🧪 Testy](#-testy)
- [📊 Struktura Projektu](#-struktura-projektu)
- [🔒 Bezpieczeństwo](#-bezpieczeństwo)
- [📈 Wydajność](#-wydajność)
- [🐛 Znane Problemy](#-znane-problemy)
- [📞 Wsparcie](#-wsparcie)
- [📄 Licencja](#-licencja)

## 🎯 O Projekcie

**Grupy Poledance** to kompleksowy system zarządzania szkołą tańca, zbudowany w Laravel z wykorzystaniem Filament. System obsługuje zarządzanie użytkownikami, grupami, płatnościami, obecnościami i regulaminami, z rozdzielonymi panelami dla administratorów i użytkowników.

### 🆕 Najnowsze Funkcjonalności

- **System Pre-rejestracji z SMS i Email** - generowanie linków pre-rejestracji z możliwością wysyłania przez SMS i email
- **Wysyłanie SMS** - integracja z SMSAPI, wysyłanie linków pre-rejestracji i resetu hasła przez SMS
- **Wysyłanie Email** - profesjonalne szablony HTML, wysyłanie linków przez email
- **Masowe wysyłanie** - możliwość wysyłania SMS i email do wielu użytkowników jednocześnie
- **System Zaproszeń Użytkowników** - automatyczne wysyłanie zaproszeń email z linkami do ustawienia hasła
- **Zarządzanie Wiadomościami Email** - kompleksowy system logowania i importu maili
- **Automatyczne Generowanie Płatności** - miesięczne płatności dla grup
- **System Obecności** - śledzenie frekwencji na zajęciach
- **Ujednolicone akcje w tabelach** - `ActionGroup` z przyciskiem „Actions" i masowe akcje (płatności: oznacz opłacone/nieopłacone; obecności: oznacz obecny/nieobecny)
- **Logi resetów haseł** - zasób do podglądu i operacji: ponowne wysłanie zaproszenia, ponowny reset, oznaczanie statusów
- **Automatyczne czyszczenie pre-rejestracji** - usuwanie wygasłych i używanych linków pre-rejestracji

## 🚀 Funkcjonalności

### Panel Administratora (`/admin`)
- **System Pre-rejestracji z SMS i Email**
  - Generowanie pojedynczych i masowych linków pre-rejestracji (7-10 linków)
  - Linki ważne przez 30 minut z konkretną godziną wygaśnięcia
  - Kopiowanie linków do schowka (pojedynczo i wszystkie naraz)
  - **Wysyłanie SMS** - integracja z SMSAPI, wysyłanie linków pre-rejestracji przez SMS
  - **Wysyłanie Email** - profesjonalne szablony HTML, wysyłanie linków przez email
  - **Masowe wysyłanie** - możliwość wysyłania SMS i email do wielu użytkowników jednocześnie
  - **Niestandardowe wiadomości** - możliwość dodania własnego tekstu do SMS i email
  - **Walidacja danych** - sprawdzanie formatów telefonów i adresów email
  - Konwersja wypełnionych pre-rejestracji na pełnych użytkowników
  - Automatyczne czyszczenie wygasłych i używanych linków
- **Zarządzanie Użytkownikami**
  - Tworzenie użytkowników bez hasła
  - Automatyczne wysyłanie zaproszeń email
  - **Wysyłanie SMS z linkiem** - wysyłanie linków resetu hasła przez SMS
  - **Wysyłanie Email z linkiem** - wysyłanie linków resetu hasła przez email
  - Ponowne wysyłanie zaproszeń (pojedyncze i masowe)
  - Zarządzanie profilami i uprawnieniami
- **Zarządzanie Grupami** - tworzenie, edycja, przypisywanie użytkowników
- **Zarządzanie Płatnościami** - automatyczne generowanie, edycja kwot grup, masowe oznaczanie opłacone/nieopłacone
- **Zarządzanie Obecnościami** - śledzenie frekwencji, statystyki, masowe oznaczanie obecny/nieobecny
- **Zarządzanie Regulaminami** - akceptacja regulaminów przez użytkowników
- **System Wiadomości Email** - logowanie, import, zarządzanie
- **Logi resetów haseł** - podgląd i operacje administracyjne na wpisach (ponowne zaproszenie, ponowny reset, zmiana statusów)

### Panel Użytkownika (`/panel`)
- **Profil Użytkownika** - edycja danych, akceptacja regulaminów
- **Płatności** - przeglądanie historii płatności
- **Obecności** - sprawdzanie frekwencji na zajęciach
- **Wiadomości Email** - przeglądanie komunikacji

## 🛠️ Technologie

- **Backend:** Laravel 12.14.1
- **Frontend:** Filament 3.3.14, Livewire 3.6.3
- **Baza danych:** MySQL 8.0
- **PHP:** 8.3
- **Serwer:** Herd (lokalny)
- **Kolejki:** Laravel Queue z Redis/MySQL
- **Email:** Laravel Mail z SMTP/IMAP

## 📦 Instalacja

### Wymagania
- PHP >= 8.1
- Composer 2.x
- Node.js 18.x i npm
- MySQL 8.0
- Minimum 2GB RAM

### Instalacja
```bash
# Klonowanie repozytorium
git clone <repository-url>
cd grupy-poledance

# Instalacja zależności PHP
composer install

# Instalacja zależności Node.js
npm install

# Konfiguracja środowiska
cp .env.example .env
php artisan key:generate

# Migracje i seedery
php artisan migrate
php artisan storage:link
php artisan db:seed

# Kompilacja assetów
npm run build
```

### Konta startowe (po seederach)
- Panel admina: `admin@hups.pl` / `12hups34`
- Konto testowe: `test@hups.pl` / `test123`

Jeśli logowanie admina nie powiedzie się (np. po przywróceniu bazy), możesz jednorazowo ustawić hasło:

```bash
php artisan tinker --execute "App\\Models\\User::where('email','admin@hups.pl')->update(['password' => '12hups34'])"
```

### Konfiguracja
1. **Baza danych** - ustawienie połączenia w `.env`
2. **Email** - konfiguracja SMTP i IMAP
3. **Uprawnienia** - ustawienie praw do katalogów `storage/` i `bootstrap/cache/`
4. **Cron jobs** - automatyczne zadania systemowe
   - Czyszczenie wygasłych pre-rejestracji (co 5 minut)
   - Czyszczenie używanych pre-rejestracji (codziennie o 7:00)
   - Generowanie płatności miesięcznych
   - Import wiadomości email

## 🔧 Konfiguracja

### Email (SMTP)
```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME="Grupy Poledance"
```

### Email (IMAP - import wiadomości)
```env
MAIL_IMAP_HOST=mail.example.com
MAIL_IMAP_PORT=993
MAIL_IMAP_USERNAME=your-email
MAIL_IMAP_PASSWORD=your-password
MAIL_IMAP_ENCRYPTION=ssl
```

### SMS (SMSAPI)
```env
SMSAPI_AUTH_TOKEN=your_smsapi_token
SMSAPI_FROM_NAME=Poledance
SMSAPI_TEST_MODE=true
SMSAPI_DEBUG=false
```

### Kolejki
```env
QUEUE_CONNECTION=redis  # lub database
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

## 📚 Dokumentacja

### 📖 Instrukcje Uruchomienia
- [Instrukcja Uruchomienia](docs/instrukcja-uruchomienia.md) - podstawowa konfiguracja
- [Konfiguracja Import Maili](docs/konfiguracja-import-maili.md) - system wiadomości email
- [Konfiguracja Kolejki Produkcja](docs/konfiguracja-kolejki-produkcja.md) - zarządzanie kolejkami
- [Konfiguracja SMSAPI](docs/konfiguracja-smsapi.md) - system wysyłania SMS

### 🎯 Status Projektu
- [Aktualny Status Projektu](docs/projekt_status.md)

### 🔧 Usprawnienia i Problemy
- [Planowane Usprawnienia Obecności](docs/planowane-usprawnienia-obecnosci.md)
- [Planowane Usprawnienia Płatności](docs/planowane-usprawnienia-platnosci.md)
- [Problemy i Błędy](docs/problemy-i-bledy.md)

### 📋 Zadania i Automatyzacja
- [Zadania Cron](docs/zadania-cron.md) - automatyczne zadania systemowe
- [Zadania System Zajęć](docs/zadania-system-zajec.md) - zarządzanie harmonogramem
- [Instrukcja Pre-rejestracji](docs/instrukcja-pre-rejestracja.md) - szczegółowy przewodnik systemu pre-rejestracji
- [Funkcja Pre-rejestracji z SMS i Email](docs/funkcja-pre-rejestracja.md) - kompletna dokumentacja funkcji pre-rejestracji

### 📧 System Wiadomości Email
- [System Wiadomości Email](docs/system-wiadomosci-email.md) - kompleksowa dokumentacja

### 📝 Notatki Plików
- [Attendance Resource](docs/file-notes/attendance-resource.md)
- [Attendance Stats Blade](docs/file-notes/attendance-stats.blade.md)
- [List Attendances](docs/file-notes/list-attendances.md)
- [Profile Card](docs/file-notes/profile-card.md)
- [Terms Acceptance Integration](docs/file-notes/terms-acceptance-integration.md)

## 🧪 Testy

### Uruchomienie Testów
```bash
# Wszystkie testy
php artisan test

# Testy systemu zaproszeń
php artisan test --filter=UserInvitationTest

# Testy systemu wiadomości email
php artisan test --filter=UserMailMessageTest

# Testy systemu pre-rejestracji
php artisan test --filter=PreRegistrationTest
```

### Testy Specyficzne
- **UserInvitationTest** - testy systemu zaproszeń użytkowników
- **UserMailMessageTest** - testy systemu wiadomości email
- **PaymentGenerationTest** - testy generowania płatności
- **PreRegistrationTest** - testy systemu pre-rejestracji (nowe)
- **PreRegistrationCleanupTest** - testy czyszczenia pre-rejestracji (nowe)
- **SmsServiceTest** - testy systemu wysyłania SMS (nowe)

## 📊 Struktura Projektu

```
grupy-poledance/
├── app/
│   ├── Console/Commands/          # Komendy Artisan
│   │   ├── CleanupExpiredPreRegistrations.php  # Czyszczenie pre-rejestracji
│   │   ├── GeneratePreRegistrationTokens.php   # Generowanie linków
│   │   └── ...                    # Inne komendy
│   ├── Filament/                  # Panel Filament
│   │   ├── Admin/                 # Panel administratora
│   │   │   ├── Resources/         # Zasoby (Users, Groups, Payments, etc.)
│   │   │   │   └── PreRegistrationResource/  # Zasób pre-rejestracji
│   │   │   └── ...                # Inne pliki admina
│   │   └── UserPanel/             # Panel użytkownika
│   ├── Http/Controllers/          # Kontrolery HTTP
│   │   └── PreRegistrationController.php  # Kontroler pre-rejestracji
│   ├── Http/Middleware/           # Middleware
│   ├── Livewire/                  # Komponenty Livewire
│   ├── Mail/                      # Klasy Mail
│   ├── Models/                    # Modele Eloquent
│   │   ├── PreRegistration.php    # Model pre-rejestracji
│   │   ├── PasswordResetLog.php   # Model logów resetów
│   │   ├── SmsLog.php            # Model logów SMS (nowe)
│   │   └── ...                    # Inne modele
│   ├── Services/                  # Serwisy aplikacji
│   │   ├── SmsService.php         # Serwis wysyłania SMS (nowe)
│   │   └── EmailService.php       # Serwis wysyłania email (nowe)
│   └── Providers/                 # Dostawcy usług
├── config/                        # Pliki konfiguracyjne
├── database/                      # Migracje, seedery, factory
├── docs/                          # Dokumentacja projektu
├── public/                        # Pliki publiczne
├── resources/                     # Widoki, CSS, JS
│   ├── views/
│   │   ├── pre-registration/      # Widoki pre-rejestracji
│   │   │   ├── form.blade.php     # Formularz pre-rejestracji
│   │   │   ├── success.blade.php  # Strona sukcesu
│   │   │   └── expired.blade.php  # Strona wygaśnięcia
│   │   └── filament/admin/resources/pre-registration-resource/modals/
│   │       ├── copy-link-simple.blade.php    # Modal kopiowania pojedynczego linku
│   │       └── copy-all-links.blade.php      # Modal kopiowania wszystkich linków
│   └── ...                        # Inne widoki
├── routes/                        # Definicje tras
│   └── web.php                    # Trasy web (w tym pre-rejestracja)
├── storage/                       # Pliki tymczasowe
└── tests/                         # Testy aplikacji
```

### Kluczowe Modele
- **User** - użytkownicy systemu
- **Group** - grupy zajęć
- **Payment** - płatności
- **Attendance** - obecności
- **Lesson** - lekcje
- **Term** - regulaminy
- **UserMailMessage** - wiadomości email
- **PreRegistration** - pre-rejestracje (nowe)
- **PasswordResetLog** - logi resetów haseł (nowe)
- **SmsLog** - logi SMS (nowe)

## 🔒 Bezpieczeństwo

- **Autoryzacja oparta na rolach** - rozdzielenie paneli admin/user
- **Middleware bezpieczeństwa** - sprawdzanie uprawnień
- **Walidacja danych wejściowych** - ochrona przed nieprawidłowymi danymi
- **Szyfrowanie sesji** - bezpieczne przechowywanie sesji
- **Ochrona CSRF** - tokeny zabezpieczające
- **Bezpieczne linki zaproszeń** - podpisane cyfrowo, ważne 72h

## 📈 Wydajność

- **Cache dla widgetów** - statystyki i wykresy
- **Zoptymalizowane zapytania SQL** - indeksy bazodanowe
- **Lazy loading relacji** - ładowanie danych na żądanie
- **Asynchroniczne wysyłanie emaili** - kolejki Laravel
- **Optymalizacja obrazów** - automatyczne resize i kompresja

## 🐛 Znane Problemy

### ✅ Rozwiązane
- Problemy z sesją użytkowników
- Błędy 500 w panelu Filament
- Problemy z cache i optymalizacją
- Import CSV użytkowników
- Automatyczne generowanie płatności
- System zaproszeń użytkowników
- System wiadomości email
- Podwójne wywołanie `RolesAndUsersSeeder` w `DatabaseSeeder`
- Potencjalne podwójne haszowanie hasła admina w seederze (ujednolicone – hasło ustawiane plain, haszowane przez mutator)
- **System pre-rejestracji** - kompletna implementacja z kopiowaniem linków i walidacją wygaśnięcia
- **Automatyczne czyszczenie pre-rejestracji** - usuwanie wygasłych i używanych linków
- **Kopiowanie linków do schowka** - funkcjonalność JavaScript z wizualnym feedbackiem
- **Walidacja wygaśnięcia linków** - zarówno po stronie frontend jak i backend
- **System SMS i Email** - kompletna integracja z SMSAPI i Laravel Mail
- **Masowe wysyłanie** - możliwość wysyłania SMS i email do wielu użytkowników
- **Niestandardowe wiadomości** - możliwość dodania własnego tekstu
- **Walidacja danych** - sprawdzanie formatów telefonów i adresów email

### 🔄 W Trakcie
- Dalsza optymalizacja wydajności dashboardu
- Usprawnienie UX w panelu użytkownika
- Rozbudowa systemu raportowania
- Integracja z systemami płatności online

## 📞 Wsparcie

### Diagnostyka Problemów
1. **Sprawdź logi Laravel** - `storage/logs/laravel.log`
2. **Zweryfikuj konfigurację** - plik `.env`
3. **Sprawdź kolejki** - `php artisan queue:work`
4. **Uruchom testy** - `php artisan test`

### Komendy Diagnostyczne
```bash
# Sprawdzenie statusu kolejek
php artisan queue:failed

# Czyszczenie cache
php artisan optimize:clear

# Sprawdzenie tras
php artisan route:list

# Sprawdzenie tras pre-rejestracji
php artisan route:list --name=pre-register

# Sprawdzenie konfiguracji
php artisan config:show

# Testowanie SMS
php artisan sms:test-connection
php artisan sms:test 48123456789

# Testowanie Email
php artisan email:test test@example.com --type=pre-registration

# Reset hasła admina (jednorazowo)
php artisan tinker --execute "App\\Models\\User::where('email','admin@hups.pl')->update(['password' => '12hups34'])"
```

### Automatyzacja
```bash
# Generowanie płatności miesięcznych
php artisan payments:generate

# Aktualizacja kwot grup
php artisan payments:update-group-amount

# Import wiadomości email
php artisan mails:import-incoming --days=30

# System pre-rejestracji
php artisan pre-register:generate --count=10  # Generowanie 10 linków pre-rejestracji

# Czyszczenie pre-rejestracji
php artisan pre-registrations:cleanup --days=0  # Natychmiastowe usuwanie wygasłych
php artisan pre-registrations:cleanup --used-only --days=7  # Usuwanie używanych po 7 dniach
php artisan pre-registrations:cleanup --dry-run  # Podgląd bez usuwania
```

## 📄 Licencja

Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

**Grupy Poledance** - System zarządzania szkołą tańca  
*Wersja:* 1.2.0 | *Ostatnia aktualizacja:* Wrzesień 2025
