# Deploy checklist – Filament Admin

## Konfiguracja panelu admin
- Guard: upewnij się, że panel używa `web`
  - PanelProvider: `->authGuard('web')`
- Dostęp (auth): przez middleware, bez `->auth(fn...)` (Filament 3)
  - Egzekwuje `App\Http\Middleware\EnsureIsAdmin` (rola `admin`)
- Middleware: standardowe Laravel/Filament (cookies/session/CSRF/bindings)
  - Brak wymuszania verified/terms/rodo w panelu admin
- Auth middleware:
  - `Filament\Http\Middleware\Authenticate`
  - `App\Http\Middleware\EnsureIsAdmin`

## Zmienne środowiskowe
- Usuń/wyłącz tryby diagnostyczne po teście:
  - `FILAMENT_DIAG_ALLOW_ALL=false` (lub brak zmiennej)

## Komendy po wdrożeniu
```bash
php artisan migrate --force
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Test powdrożeniowy (akceptacja)
- Zaloguj: `admin@hups.pl` (poprawne hasło)
- Oczekiwane: HTTP 200, dashboard /admin bez 403
- Jeśli NOK: sprawdź logi (`storage/logs/laravel.log`), sesję/cookie, CSRF

## Uwagi
- Jeżeli w przyszłości dodasz wymogi verified/terms/rodo – wprowadź wyjątki dla roli `admin` w odpowiednich middleware, aby nie blokować panelu admin.
