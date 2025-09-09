# 📌 Status projektu – Grupy Poledance (aktualny)

Data: 2025-09-09

## 🎯 Cel i zakres
System do zarządzania szkołą tańca: użytkownicy, grupy, płatności, obecności, regulaminy, wiadomości email. Dwa panele (Admin i Użytkownik), automatyzacje przez cron i kolejki.

## 🏗️ Architektura (skrót)
- Backend: Laravel 12.14.1 (PHP 8.3)
- Frontend: Filament 3.3.14, Livewire 3.6.3
- Baza danych: MySQL 8.0
- Kolejki: Redis/Database
- E-mail: SMTP (wysyłka), IMAP (import)

Struktura (kluczowe):
```
app/
  Console/Commands/
  Filament/{Admin,UserPanel}/
  Http/{Controllers,Middleware}/
  Mail/
  Models/
config/
database/{migrations,seeders,factories}
docs/
resources/views/
routes/
```

## 🚀 Funkcjonalności

### Panel Administratora (`/admin`)
- Użytkownicy: tworzenie bez hasła, zaproszenia, reset haseł (pojedynczo/masowo), profile, role, import CSV
- Grupy: przypisywanie użytkowników, zmiana kwoty dla grupy (poziomy zakresu), bulk actions
- Płatności: lista, filtry, oznaczanie opłacone/nieopłacone, statystyki, automatyczne generowanie miesięczne (cron/komenda)
- Obecności: lista, filtry, statystyki, wykresy, top użytkownicy
- Regulaminy: zarządzanie treścią i aktywnością, podgląd akceptacji
- Wiadomości email: logi/operacje na wiadomościach (import IMAP)
- Logi resetów haseł: ponowne zaproszenia, ponowne resety, zmiana statusów

### Panel Użytkownika (`/panel`)
- Profil i dane, adresy
- Płatności: przegląd i statusy
- Obecności: historia i kalendarz
- Akceptacja regulaminów (wymuszana przy pierwszym logowaniu)

## 🔧 Automatyzacja i komendy
- `payments:generate` – generowanie płatności miesięcznych
- `payments:update-group-amount` – hurtowa zmiana kwot dla grup
- `mails:import-incoming --days=30` – import przychodzących e-maili (IMAP)

Przykładowe crony:
```
0 1 1 * * php artisan payments:generate
0 1 * * * php artisan payments:generate
* * * * * php artisan queue:work --timeout=60 --tries=3 --stop-when-empty
```

## 🛠️ Instalacja i wdrożenie (skrót)
Wymagania: PHP ≥ 8.1, Composer 2.x, Node 18.x, MySQL 8.0, 2GB RAM.

Instalacja:
```
composer install
npm install
cp .env.example .env && php artisan key:generate
php artisan migrate
php artisan storage:link
php artisan db:seed
npm run build
```

Konta startowe po seedach:
- Admin: admin@hups.pl / 12hups34
- Test: test@hups.pl / test123

## ⚙️ Konfiguracja (env)
E-mail (SMTP):
```
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME="Grupy Poledance"
```

IMAP (import):
```
MAIL_IMAP_HOST=mail.example.com
MAIL_IMAP_PORT=993
MAIL_IMAP_USERNAME=your-email
MAIL_IMAP_PASSWORD=your-password
MAIL_IMAP_ENCRYPTION=ssl
```

Kolejki (Redis lub database):
```
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

## 🔒 Bezpieczeństwo
- Rozdzielone panele i role, middleware `EnsureIsAdmin`, `EnsureIsUser`, `EnsureUserAcceptedTerms`
- CSRF, szyfrowanie sesji, walidacja danych
- Bezpieczne linki zaproszeń (podpis, ważność 72h)

## 📈 Wydajność
- Cache dla widgetów/statystyk (10 min)
- Zoptymalizowane zapytania, lazy loading, indeksy
- Asynchroniczne e-maile (kolejki)

## 🗄️ Dane i migracje
- Modele: User, Group, Payment, Attendance, Lesson, Term, Address, UserMailMessage
- Migracje: 20+ wykonanych; indeksy i relacje zdefiniowane

## 🧪 Testy
Uruchomienie:
```
php artisan test
php artisan test --filter=UserInvitationTest
php artisan test --filter=UserMailMessageTest
```

## 🐛 Problemy
### Rozwiązane
- Sesje i błędy 500 (uzupełnione middleware)
- Cache i optymalizacja widgetów
- Import CSV (limity, konwersje)
- Automatyczne generowanie płatności
- Podwójne wywołania seederów/haszowania – ujednolicone

### W toku
- Optymalizacja dashboardu
- UX panelu użytkownika
- Rozszerzenie raportowania
- Integracja płatności online

## 🆕 Ostatnie zmiany (2025-08-29)
- Ujednolicone `ActionGroup` w tabelach i akcje masowe (płatności i obecności)
- Zmiana kwoty w `Admin/GroupResource` (pojedynczo i masowo)
- Logi resetów haseł: ponowne zaproszenie/reset, zmiana statusów
- Poprawki lintingu (rzutowanie na float do number_format)

## 📚 Gdzie szukać szczegółów
- Indeks dokumentacji: `docs/README.md`
- E-maile: `docs/system-wiadomosci-email.md`
- Cron i automatyzacje: `docs/zadania-cron.md`, `docs/zadania_cron_projekt.md`, `docs/nowa_struktura_cron.md`
- Płatności: `docs/system_przypomnien_platnosci.md`
- Konfiguracja produkcji (kolejka): `docs/konfiguracja-kolejki-produkcja.md`
- Import maili (IMAP): `docs/konfiguracja-import-maili.md`
- Uruchomienie: `docs/instrukcja-uruchomienia.md`

---
Status: PRODUKCYJNY ✅


