<?php

use Illuminate\Support\Facades\Schedule;

// Harmonogram zadań cron dla Laravel 11
// Ten plik jest automatycznie ładowany przez Laravel

// Wysyłanie przypomnień o płatnościach - codziennie od poniedziałku do piątku o 9:00
Schedule::command('payments:send-reminders')
    ->weekdays()
    ->at('09:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->description('Wysyłanie przypomnień o płatnościach do użytkowników z zaległościami');

// Generowanie miesięcznych płatności - pierwszego dnia każdego miesiąca o 6:00
Schedule::command('payments:generate')
    ->monthly()
    ->at('06:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->description('Generowanie płatności dla aktywnych użytkowników na nowy miesiąc');

// Import maili przychodzących - codziennie o 8:00
Schedule::command('mails:import-incoming --days=30')
    ->daily()
    ->at('08:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->description('Import maili przychodzących z serwera IMAP');

// Przetwarzanie kolejki - co 5 minut
Schedule::command('queue:work --stop-when-empty --max-time=300')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->runInBackground()
    ->description('Przetwarzanie zadań w kolejce');
