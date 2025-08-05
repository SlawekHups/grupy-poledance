<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development/)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# Grupy Poledance - System Zarządzania Szkołą Tańca

## 🎯 O Projekcie

**Grupy Poledance** to kompleksowy system zarządzania szkołą tańca, zbudowany w Laravel z wykorzystaniem Filament. System obsługuje zarządzanie użytkownikami, grupami, płatnościami, obecnościami i regulaminami, z rozdzielonymi panelami dla administratorów i użytkowników.

## 🚀 Nowy System Zaproszeń Użytkowników

### Jak to działa

1. **Administrator tworzy użytkownika** - podaje tylko podstawowe dane (imię, nazwisko, email)
2. **System automatycznie wysyła zaproszenie** - email z linkiem do ustawienia hasła (ważny 48h)
3. **Użytkownik ustawia hasło** - przez dedykowany formularz
4. **Automatyczne przekierowanie** - do panelu użytkownika z wymuszeniem uzupełnienia profilu
5. **Pełny dostęp** - po uzupełnieniu wszystkich wymaganych danych

### Funkcjonalności

- ✅ **Tworzenie użytkowników bez hasła** - administrator nie musi znać hasła użytkownika
- ✅ **Automatyczne wysyłanie zaproszeń** - email z linkiem do ustawienia hasła
- ✅ **Bezpieczne linki** - podpisane cyfrowo, ważne 48 godzin
- ✅ **Wymuszenie uzupełnienia profilu** - middleware sprawdza brakujące dane
- ✅ **Integracja z importem CSV** - automatyczne zaproszenia po imporcie
- ✅ **Asynchroniczne wysyłanie** - przez kolejki Laravel (jeśli włączone)
- ✅ **Ponowne wysyłanie zaproszeń** - akcje w panelu admin dla pojedynczych i masowych użytkowników

### Proces dla użytkownika

1. **Otrzymuje email** z linkiem "Ustaw hasło i rozpocznij"
2. **Klika link** - przechodzi do formularza ustawiania hasła
3. **Ustawia hasło** - zgodnie z wymaganiami bezpieczeństwa
4. **Zostaje zalogowany** - automatycznie przekierowany do panelu
5. **Uzupełnia profil** - telefon, grupa, akceptacja regulaminu
6. **Otrzymuje pełny dostęp** - do wszystkich funkcji systemu

### Wymagane pola profilu

- ✅ **Telefon** - numer kontaktowy
- ✅ **Przypisanie do grupy** - wybór grupy zajęć
- ✅ **Akceptacja regulaminu** - potwierdzenie regulaminu

## 🛠️ Technologie

- **Backend:** Laravel 12.14.1
- **Frontend:** Filament 3.3.14, Livewire 3.6.3
- **Baza danych:** MySQL 8.0
- **PHP:** 8.3
- **Serwer:** Herd (lokalny)

## 📋 Instalacja

### Wymagania
- PHP >= 8.1
- Composer 2.x
- Node.js 18.x i npm
- MySQL 8.0
- Minimum 2GB RAM

### Instalacja
```bash
composer install
npm install
php artisan migrate
php artisan storage:link
php artisan db:seed
```

### Konfiguracja
- Ustawienie .env
- Konfiguracja bazy danych
- Ustawienie uprawnień katalogów
- Konfiguracja cron jobs

## 🔧 Konfiguracja Email

Upewnij się, że w pliku `.env` masz poprawnie skonfigurowane ustawienia email:

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

## 🔄 Automatyzacja

### Cron Jobs
- ✅ Komenda `payments:generate` - generowanie płatności miesięcznych
- ✅ Komenda `payments:update-group-amount` - aktualizacja kwot grup

### Przykładowe zadania cron:
```bash
# Generowanie płatności co miesiąc
0 1 1 * * cd /ścieżka/do/projektu && php artisan payments:generate

# Codzienne generowanie (dla nowych użytkowników)
0 1 * * * cd /ścieżka/do/projektu && php artisan payments:generate
```

## 🧪 Testy

Uruchom testy dla nowego systemu zaproszeń:

```bash
php artisan test --filter=UserInvitationTest
```

## 📧 Zarządzanie Zaproszeniami

### Ponowne wysyłanie zaproszeń

**Akcja pojedyncza:**
1. Przejdź do listy użytkowników w panelu admin (`/admin/users`)
2. Znajdź użytkownika bez hasła (nie ma przycisku "Wyślij zaproszenie")
3. Kliknij przycisk "Wyślij zaproszenie" (ikona koperty)
4. Potwierdź w modalu
5. Użytkownik otrzyma nowy email z linkiem do ustawienia hasła

**Akcja masowa:**
1. Zaznacz wielu użytkowników na liście
2. Wybierz akcję masową "Wyślij zaproszenia"
3. Potwierdź w modalu
4. System automatycznie:
   - Wyśle zaproszenia tylko do użytkowników bez hasła
   - Pominie użytkowników z już ustawionym hasłem
   - Wyświetli podsumowanie operacji

### Bezpieczeństwo
- Linki są ważne 48 godzin
- Każde nowe zaproszenie unieważnia poprzednie
- Akcje wymagają potwierdzenia
- Logi wszystkich operacji w `storage/logs/laravel.log`

## 📊 Funkcjonalności

### Panel Administratora (`/admin`)
- Zarządzanie użytkownikami (z automatycznymi zaproszeniami)
  - **Ponowne wysyłanie zaproszeń** - przycisk "Wyślij zaproszenie" przy każdym użytkowniku bez hasła
  - **Masowe wysyłanie zaproszeń** - akcja masowa dla wielu zaznaczonych użytkowników
  - **Inteligentne filtrowanie** - akcje widoczne tylko dla użytkowników bez hasła
- Zarządzanie grupami
- Zarządzanie płatnościami
- Zarządzanie obecnościami
- Zarządzanie regulaminami

### Panel Użytkownika (`/panel`)
- Profil użytkownika
- Płatności
- Obecności
- Akceptacja regulaminu

## 🔒 Bezpieczeństwo

- Autoryzacja oparta na rolach
- Middleware bezpieczeństwa
- Walidacja danych wejściowych
- Szyfrowanie sesji
- Ochrona CSRF
- Bezpieczne linki zaproszeń

## 📈 Wydajność

- Cache dla widgetów i statystyk
- Zoptymalizowane zapytania SQL
- Lazy loading relacji
- Indeksy bazodanowe
- Asynchroniczne wysyłanie emaili

## 🐛 Znane Problemy

### Rozwiązane
- ✅ Problemy z sesją
- ✅ Błędy 500
- ✅ Problemy z cache
- ✅ Import CSV
- ✅ Automatyczne generowanie płatności

### W Trakcie
- [ ] Dalsza optymalizacja wydajności dashboardu
- [ ] Usprawnienie UX w panelu użytkownika
- [ ] Rozbudowa systemu raportowania

## 📞 Wsparcie

W przypadku problemów z systemem zaproszeń:
1. Sprawdź logi Laravel (`storage/logs/laravel.log`)
2. Zweryfikuj konfigurację email w `.env`
3. Sprawdź czy kolejki są uruchomione (jeśli używasz)
4. Uruchom testy: `php artisan test --filter=UserInvitationTest`

## 📄 Licencja

Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
