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

- **System Zaproszeń Użytkowników** - automatyczne wysyłanie zaproszeń email z linkami do ustawienia hasła
- **Zarządzanie Wiadomościami Email** - kompleksowy system logowania i importu maili
- **Automatyczne Generowanie Płatności** - miesięczne płatności dla grup
- **System Obecności** - śledzenie frekwencji na zajęciach

## 🚀 Funkcjonalności

### Panel Administratora (`/admin`)
- **Zarządzanie Użytkownikami**
  - Tworzenie użytkowników bez hasła
  - Automatyczne wysyłanie zaproszeń email
  - Ponowne wysyłanie zaproszeń (pojedyncze i masowe)
  - Zarządzanie profilami i uprawnieniami
- **Zarządzanie Grupami** - tworzenie, edycja, przypisywanie użytkowników
- **Zarządzanie Płatnościami** - automatyczne generowanie, edycja kwot grup
- **Zarządzanie Obecnościami** - śledzenie frekwencji, statystyki
- **Zarządzanie Regulaminami** - akceptacja regulaminów przez użytkowników
- **System Wiadomości Email** - logowanie, import, zarządzanie

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

### Konfiguracja
1. **Baza danych** - ustawienie połączenia w `.env`
2. **Email** - konfiguracja SMTP i IMAP
3. **Uprawnienia** - ustawienie praw do katalogów `storage/` i `bootstrap/cache/`
4. **Cron jobs** - automatyczne zadania systemowe

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

### 🎯 Status Projektu
- [Status Projektu 2](docs/projekt_status_2.md) - wczesne etapy rozwoju
- [Status Projektu 3](docs/projekt_status_3.md) - implementacja podstawowych funkcji
- [Status Projektu 4](docs/projekt_status_4.md) - system płatności i grup
- [Status Projektu 5](docs/projekt_status_5.md) - system obecności
- [Status Projektu 6](docs/projekt_status_6.md) - system zaproszeń użytkowników
- [Status Projektu 7](docs/projekt_status_7.md) - system wiadomości email

### 🔧 Usprawnienia i Problemy
- [Planowane Usprawnienia Obecności](docs/planowane-usprawnienia-obecnosci.md)
- [Planowane Usprawnienia Płatności](docs/planowane-usprawnienia-platnosci.md)
- [Problemy i Błędy](docs/problemy-i-bledy.md)

### 📋 Zadania i Automatyzacja
- [Zadania Cron](docs/zadania-cron.md) - automatyczne zadania systemowe
- [Zadania System Zajęć](docs/zadania-system-zajec.md) - zarządzanie harmonogramem

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
```

### Testy Specyficzne
- **UserInvitationTest** - testy systemu zaproszeń użytkowników
- **UserMailMessageTest** - testy systemu wiadomości email
- **PaymentGenerationTest** - testy generowania płatności

## 📊 Struktura Projektu

```
grupy-poledance/
├── app/
│   ├── Console/Commands/          # Komendy Artisan
│   ├── Filament/                  # Panel Filament
│   │   ├── Admin/                 # Panel administratora
│   │   └── UserPanel/             # Panel użytkownika
│   ├── Http/Controllers/          # Kontrolery HTTP
│   ├── Http/Middleware/           # Middleware
│   ├── Livewire/                  # Komponenty Livewire
│   ├── Mail/                      # Klasy Mail
│   ├── Models/                    # Modele Eloquent
│   └── Providers/                 # Dostawcy usług
├── config/                        # Pliki konfiguracyjne
├── database/                      # Migracje, seedery, factory
├── docs/                          # Dokumentacja projektu
├── public/                        # Pliki publiczne
├── resources/                     # Widoki, CSS, JS
├── routes/                        # Definicje tras
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

## 🔒 Bezpieczeństwo

- **Autoryzacja oparta na rolach** - rozdzielenie paneli admin/user
- **Middleware bezpieczeństwa** - sprawdzanie uprawnień
- **Walidacja danych wejściowych** - ochrona przed nieprawidłowymi danymi
- **Szyfrowanie sesji** - bezpieczne przechowywanie sesji
- **Ochrona CSRF** - tokeny zabezpieczające
- **Bezpieczne linki zaproszeń** - podpisane cyfrowo, ważne 48h

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

# Sprawdzenie konfiguracji
php artisan config:show
```

### Automatyzacja
```bash
# Generowanie płatności miesięcznych
php artisan payments:generate

# Aktualizacja kwot grup
php artisan payments:update-group-amount

# Import wiadomości email
php artisan mails:import-incoming --days=30
```

## 📄 Licencja

Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

**Grupy Poledance** - System zarządzania szkołą tańca  
*Wersja:* 1.0.0 | *Ostatnia aktualizacja:* Sierpień 2025
