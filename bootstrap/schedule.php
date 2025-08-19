<?php

use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Laravel 11 Task Scheduler - Uproszczona Wersja
|--------------------------------------------------------------------------
|
| Ten plik zawiera wszystkie zadania cron w projekcie.
| Wszystkie zadania są zorganizowane według kategorii
| i łatwe w zarządzaniu.
|
| Kategorie zadań:
| - Finanse (płatności, przypomnienia)
| - Komunikacja (maile, import)
| - System (kolejka, nieudane zadania)
| - Monitoring (logi)
| - Backup (backup, czyszczenie)
| - Cache (cache, optymalizacja)
|
*/

// ============================================================================
// FINANSE - PŁATNOŚCI I PRZYPOMNIENIA
// ============================================================================

// Wysyłanie przypomnień o płatnościach - codziennie o 8:00 (przed zajęciami)
Schedule::command('payments:send-reminders')
    ->daily()
    ->at('08:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->description('Wysyłanie przypomnień o płatnościach do użytkowników z zaległościami (1x dziennie przed zajęciami)')
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::info('Zadanie: Przypomnienia o płatnościach - Ukończone pomyślnie');
    })
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('Zadanie: Przypomnienia o płatnościach - Błąd wykonania');
    });

// Generowanie miesięcznych płatności - pierwszego dnia każdego miesiąca o 6:00
Schedule::command('payments:generate')
    ->monthly()
    ->at('06:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->description('Generowanie płatności dla aktywnych użytkowników na nowy miesiąc')
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::info('Zadanie: Generowanie płatności - Ukończone pomyślnie');
    })
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('Zadanie: Generowanie płatności - Błąd wykonania');
    });

// Sprawdzanie brakujących płatności - co 2 godziny
Schedule::command('payments:generate-missing')
    ->everyTwoHours()
    ->withoutOverlapping()
    ->runInBackground()
    ->description('Sprawdzanie i generowanie brakujących płatności dla użytkowników dodanych w trakcie miesiąca (co 2h)')
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::info('Zadanie: Sprawdzanie brakujących płatności - Ukończone pomyślnie');
    })
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('Zadanie: Sprawdzanie brakujących płatności - Błąd wykonania');
    });

// Sprawdzanie i przypominanie o zaproszeniach - co 6 godzin
Schedule::command('users:check-invitations')
    ->everySixHours()
    ->withoutOverlapping()
    ->runInBackground()
    ->description('Sprawdzanie użytkowników bez hasła i wysyłanie przypomnień o zaproszeniach (co 6h)')
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::info('Zadanie: Sprawdzanie zaproszeń - Ukończone pomyślnie');
    })
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('Zadanie: Sprawdzanie zaproszeń - Błąd wykonania');
    });

// ============================================================================
// KOMUNIKACJA - MAILE I IMPORT
// ============================================================================

// Import maili przychodzących - co 15 minut
Schedule::command('mails:import-incoming --days=7')
    ->everyFifteenMinutes()
    ->withoutOverlapping()
    ->runInBackground()
    ->description('Import maili przychodzących z serwera IMAP (co 15 minut)')
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::info('Zadanie: Import maili - Ukończone pomyślnie');
    })
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('Zadanie: Import maili - Błąd wykonania');
    });

// ============================================================================
// SYSTEM - KOLEJKA I CACHE
// ============================================================================

// Przetwarzanie kolejki - co minutę (szybka odpowiedź)
Schedule::command('queue:work --stop-when-empty --max-time=60')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground()
    ->description('Przetwarzanie zadań w kolejce (co minutę dla szybkiej odpowiedzi)')
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::info('Zadanie: Przetwarzanie kolejki - Ukończone pomyślnie');
    })
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('Zadanie: Przetwarzanie kolejki - Błąd wykonania');
    });

// Sprawdzanie nieudanych zadań w kolejce - codziennie o 7:00
Schedule::command('queue:failed --force')
    ->daily()
    ->at('07:00')
    ->description('Sprawdzanie nieudanych zadań w kolejce')
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::info('Zadanie: Sprawdzanie nieudanych zadań - Ukończone pomyślnie');
    });

// ============================================================================
// MONITORING I LOGI
// ============================================================================

// Czyszczenie starych logów - co tydzień w niedzielę o 2:00
Schedule::command('log:clear')
    ->weekly()
    ->sundays()
    ->at('02:00')
    ->description('Czyszczenie starych plików logów')
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::info('Zadanie: Czyszczenie logów - Ukończone pomyślnie');
    });

// ============================================================================
// BACKUP I BEZPIECZEŃSTWO
// ============================================================================

// Backup bazy danych - codziennie o 3:00
Schedule::command('backup:run')
    ->daily()
    ->at('03:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->description('Backup bazy danych')
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::info('Zadanie: Backup bazy danych - Ukończone pomyślnie');
    })
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('Zadanie: Backup bazy danych - Błąd wykonania');
    });

// Czyszczenie starych backupów - co tydzień w sobotę o 4:00
Schedule::command('backup:clean')
    ->weekly()
    ->saturdays()
    ->at('04:00')
    ->description('Czyszczenie starych backupów')
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::info('Zadanie: Czyszczenie backupów - Ukończone pomyślnie');
    });

// ============================================================================
// CACHE I OPTYMALIZACJA
// ============================================================================

// Czyszczenie cache - codziennie o 5:00
Schedule::command('cache:clear')
    ->daily()
    ->at('05:00')
    ->description('Czyszczenie cache aplikacji')
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::info('Zadanie: Czyszczenie cache - Ukończone pomyślnie');
    });

// Optymalizacja autoloadera - co tydzień w niedzielę o 1:00
Schedule::command('optimize:clear')
    ->weekly()
    ->sundays()
    ->at('01:00')
    ->description('Optymalizacja autoloadera i cache')
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::info('Zadanie: Optymalizacja - Ukończone pomyślnie');
    });

// ============================================================================
// INFORMACJE O ZADAŃ (tylko w development)
// ============================================================================

if (app()->environment('local', 'development')) {
    Schedule::command('schedule:list-all')
        ->daily()
        ->at('06:00')
        ->description('Lista wszystkich zaplanowanych zadań (tylko dev)');
}

/*
|--------------------------------------------------------------------------
| PODSUMOWANIE ZADAŃ
|--------------------------------------------------------------------------
|
| ZADANIA DZIENNE:
| - 03:00 - Backup bazy danych
| - 05:00 - Czyszczenie cache
| - 07:00 - Sprawdzanie nieudanych zadań w kolejce
| - 08:00 - Import maili przychodzących
| - 09:00 - Przypomnienia o płatnościach (pon-pt)
|
| ZADANIA CO 5 MINUT:
| - Przetwarzanie kolejki
|
| ZADANIA TYGODNIOWE:
| - Sobota 04:00 - Czyszczenie backupów
| - Niedziela 01:00 - Optymalizacja autoloadera
| - Niedziela 02:00 - Czyszczenie logów
|
| ZADANIA MIESIĘCZNE:
| - 1. dnia 06:00 - Generowanie płatności
|
| KONFIGURACJA CRON:
| * * * * * cd /ścieżka/do/projektu && php artisan schedule:run
|
| SPRAWDZENIE ZADAŃ:
| php artisan schedule:list-all
| php artisan schedule:list
|
*/
