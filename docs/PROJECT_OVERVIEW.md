# Przegląd Projektu - System Zarządzania Grupami Pole Dance

## Cel i Nazwa Projektu
**System Zarządzania Grupami Pole Dance** - aplikacja webowa do kompleksowego zarządzania szkołą pole dance, obejmująca rejestrację użytkowników, zarządzanie grupami, obsługę płatności, kontrolę obecności i komunikację z uczestnikami.

## Stos Technologiczny
- **Backend**: Laravel 12, PHP 8.2+
- **Frontend**: Filament 3 (Admin Panel + User Panel)
- **Baza danych**: SQLite (dev) / MySQL (prod)
- **Cache**: Redis/Memcached
- **Kolejki**: Laravel Queue (Redis/Database)
- **Email**: Laravel Mail + IMAP import
- **Autentykacja**: Laravel Auth + Filament Auth

## Główne Modele i Relacje

| Model | Główne pola | Relacje | Opis |
|-------|-------------|---------|------|
| **User** | name, email, phone, amount, role, is_active, group_id | belongsTo(Group), belongsToMany(Groups), hasMany(Payment), hasMany(Attendance), hasMany(UserMailMessage), hasMany(Address) | Użytkownicy systemu (admin/user) |
| **Group** | name, description, status, max_size | hasMany(User), belongsToMany(User), hasMany(Attendance), hasMany(Lesson) | Grupy zajęć |
| **Payment** | user_id, month, amount, paid, payment_link | belongsTo(User) | Płatności miesięczne |
| **Attendance** | user_id, group_id, date, present | belongsTo(User), belongsTo(Group) | Kontrola obecności |
| **Lesson** | group_id, title, date, status, published_at | belongsTo(Group), belongsTo(User) | Zajęcia |
| **PreRegistration** | token, name, email, phone, expires_at, used | - | Pre-rejestracje przez tokeny |
| **File** | name, path, mime_type, size, is_public | belongsTo(User) | Pliki administratora |
| **UserMailMessage** | user_id, direction, email, subject, content | belongsTo(User) | Wiadomości email |
| **Address** | user_id, type, street, city, postal_code | belongsTo(User) | Adresy użytkowników |
| **Term** | name, content, active | - | Regulaminy |
| **LessonTemplate** | title, description, created_by | belongsTo(User) | Szablony zajęć |

## Panele Aplikacji

### Panel Administratora (`/admin`)
- **Użytkownicy i Grupy**: zarządzanie użytkownikami, grupami, adresami
- **Finanse**: płatności, przypomnienia, zestawienia
- **Zajęcia**: lekcje, szablony, kalendarz
- **Ustawienia**: pliki, wiadomości, konfiguracja

### Panel Użytkownika (`/panel`)
- **Dashboard**: statystyki obecności, płatności
- **Profil**: dane osobowe, adresy
- **Płatności**: historia, linki płatnicze
- **Obecność**: historia obecności
- **Zajęcia**: dostępne lekcje

## Najważniejsze Procesy Biznesowe

### System Płatności
- **Generowanie**: automatyczne tworzenie płatności miesięcznych
- **Przypomnienia**: wysyłanie przypomnień o zaległościach
- **Zestawienia**: dzienne raporty dla administracji
- **Linki płatnicze**: integracja z systemami płatności online

### System Obecności
- **Rejestracja**: zapisywanie obecności na zajęciach
- **Statystyki**: 
  - Frekwencja użytkowników (procent obecności)
  - Statystyki grup (średnia frekwencja)
  - Historia obecności z filtrowaniem
  - Widgety statystyczne w panelu użytkownika
- **Raportowanie**: zestawienia dla grup i administratorów
- **Kontrola**: automatyczne przypisywanie do grup na podstawie obecności

### System Komunikacji
- **Email**: wysyłanie przypomnień i powiadomień
- **IMAP**: import wiadomości przychodzących
- **Wiadomości**: archiwum korespondencji z użytkownikami

### System Pre-rejestracji
- **Tokeny**: generowanie unikalnych linków zaproszeń
- **Konwersja**: automatyczne tworzenie kont użytkowników
- **Wygasanie**: czyszczenie nieaktywnych tokenów

## Kroki Wdrożenia

### 1. Środowisko deweloperskie
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
npm install && npm run dev
php artisan serve
```

### 2. Konfiguracja
- Ustaw dane SMTP w `.env`
- Skonfiguruj IMAP dla importu maili
- Ustaw kolejki (Redis/Database)
- Skonfiguruj system płatności

### 3. Zadania cron
```bash
* * * * * cd /path/to/project && php artisan schedule:run
```

### 4. Produkcja
- Ustaw zmienne środowiskowe
- Uruchom migracje
- Skonfiguruj kolejki
- Ustaw cron jobs

## Do uzupełnienia
- Konfiguracja systemu płatności online
- Integracja z zewnętrznymi API
- Dokumentacja API endpoints
- Testy automatyczne
- Monitoring i logi aplikacji
