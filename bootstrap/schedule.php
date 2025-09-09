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
    ->at('09:00')
    ->timezone('Europe/Warsaw')
    ->withoutOverlapping()
    ->runInBackground()
    ->description('Wysyłanie przypomnień o płatnościach do użytkowników z zaległościami (1x dziennie przed zajęciami)')
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::info('Zadanie: Przypomnienia o płatnościach - Ukończone pomyślnie');
    })
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('Zadanie: Przypomnienia o płatnościach - Błąd wykonania');
    });

// Wysyłka dziennego digestu do administratora - codziennie o 09:00
Schedule::command('payments:send-admin-digest')
    ->daily()
    ->at('09:00')
    ->timezone('Europe/Warsaw')
    ->withoutOverlapping()
    ->runInBackground()
    ->description('Wysyła dzienne zestawienie zaległości płatniczych administratorowi (grupy z dzisiejszego dnia)')
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::info('Zadanie: Admin Payment Digest - Ukończone pomyślnie');
    })
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('Zadanie: Admin Payment Digest - Błąd wykonania');
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

// Sprawdzanie brakujących płatności - co 2 minuty
Schedule::command('payments:generate-missing')
    ->everyTwoMinutes()
    ->withoutOverlapping()
    ->runInBackground()
    ->description('Sprawdzanie i generowanie brakujących płatności (co 2 minuty)')
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::info('Zadanie: Sprawdzanie płatności - Ukończone pomyślnie');
    })
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('Zadanie: Sprawdzanie płatności - Błąd wykonania');
    });

// Sprawdzanie i przypominanie o zaproszeniach - co 1 minutę
Schedule::command('users:check-invitations')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground()
    ->description('Sprawdzanie użytkowników bez hasła i wygasłych linków zaproszeń (co 1 minutę)')
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::info('Zadanie: Sprawdzanie zaproszeń - Ukończone pomyślnie');
    })
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('Zadanie: Sprawdzanie zaproszeń - Błąd wykonania');
    });

// Sprawdzanie wygasłych tokenów resetowania haseł - co godzinę
Schedule::command('passwords:check-expired')
    ->hourly()
    ->withoutOverlapping()
    ->runInBackground()
    ->description('Sprawdzanie i oznaczanie wygasłych tokenów resetowania haseł (co godzinę)')
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::info('Zadanie: Sprawdzanie wygasłych tokenów - Ukończone pomyślnie');
    })
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('Zadanie: Sprawdzanie wygasłych tokenów - Błąd wykonania');
    });

// Aktualizacja statusów grup - co 5 minut
Schedule::command('groups:update-statuses')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->runInBackground()
    ->description('Aktualizacja statusów grup na podstawie liczby członków (co 5 minut)')
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::info('Zadanie: Aktualizacja statusów grup - Ukończone pomyślnie');
    })
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('Zadanie: Aktualizacja statusów grup - Błąd wykonania');
    });

// ============================================================================
// KOMUNIKACJA - MAILE I IMPORT
// ============================================================================

// Import maili przychodzących - co 2 minuty
Schedule::command('mails:import-incoming')
    ->everyTwoMinutes()
    ->withoutOverlapping()
    ->runInBackground()
    ->description('Import maili przychodzących z serwera IMAP (co 2 minuty)')
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
| ZADANIA CO 1 MINUTĘ:
| - Sprawdzanie zaproszeń i wygasłych linków
| - Przetwarzanie kolejki
|
| ZADANIA CO 2 MINUTY:
| - Sprawdzanie brakujących płatności
| - Import maili przychodzących
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
