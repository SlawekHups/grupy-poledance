<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cron Jobs Configuration
    |--------------------------------------------------------------------------
    |
    | Ten plik zawiera konfigurację wszystkich zadań cron w projekcie.
    | Wszystkie zadania są zorganizowane według kategorii i mają
    | szczegółowe opisy oraz opcje konfiguracyjne.
    |
    */

    'enabled' => env('CRON_ENABLED', true),

    'timezone' => env('CRON_TIMEZONE', 'UTC'),

    /*
    |--------------------------------------------------------------------------
    | Kategorie zadań cron
    |--------------------------------------------------------------------------
    */

    'categories' => [
        'finances' => [
            'name' => 'Finanse',
            'description' => 'Zadania związane z płatnościami i finansami',
            'icon' => '💰',
        ],
        'communication' => [
            'name' => 'Komunikacja',
            'description' => 'Zadania związane z mailami i komunikacją',
            'icon' => '📧',
        ],
        'system' => [
            'name' => 'System',
            'description' => 'Zadania systemowe i kolejka',
            'icon' => '⚙️',
        ],
        'monitoring' => [
            'name' => 'Monitoring',
            'description' => 'Zadania monitoringu i logów',
            'icon' => '📊',
        ],
        'backup' => [
            'name' => 'Backup',
            'description' => 'Zadania backupu i bezpieczeństwa',
            'icon' => '💾',
        ],
        'cache' => [
            'name' => 'Cache',
            'description' => 'Zadania cache i optymalizacji',
            'icon' => '🚀',
        ],
        'reports' => [
            'name' => 'Raporty',
            'description' => 'Zadania raportów i statystyk',
            'icon' => '📈',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Konfiguracja zadań
    |--------------------------------------------------------------------------
    */

    'jobs' => [
        'finances' => [
            'payments_send_reminders' => [
                'command' => 'payments:send-reminders',
                'schedule' => 'weekdays',
                'time' => '09:00',
                'description' => 'Wysyłanie przypomnień o płatnościach',
                'enabled' => true,
                'options' => [
                    'without_overlapping' => true,
                    'run_in_background' => true,
                    'max_time' => 300,
                ],
            ],
            'payments_generate' => [
                'command' => 'payments:generate',
                'schedule' => 'monthly',
                'time' => '06:00',
                'description' => 'Generowanie płatności miesięcznych',
                'enabled' => true,
                'options' => [
                    'without_overlapping' => true,
                    'run_in_background' => true,
                ],
            ],
        ],

        'communication' => [
            'mails_import_incoming' => [
                'command' => 'mails:import-incoming',
                'schedule' => 'daily',
                'time' => '08:00',
                'description' => 'Import maili przychodzących',
                'enabled' => true,
                'arguments' => ['--days=30'],
                'options' => [
                    'without_overlapping' => true,
                    'run_in_background' => true,
                ],
            ],
        ],

        'system' => [
            'queue_work' => [
                'command' => 'queue:work',
                'schedule' => 'every_five_minutes',
                'description' => 'Przetwarzanie kolejki',
                'enabled' => true,
                'arguments' => [
                    '--stop-when-empty',
                    '--max-time=300'
                ],
                'options' => [
                    'without_overlapping' => true,
                    'run_in_background' => true,
                ],
            ],
            'queue_failed' => [
                'command' => 'queue:failed',
                'schedule' => 'daily',
                'time' => '07:00',
                'description' => 'Sprawdzanie nieudanych zadań',
                'enabled' => true,
                'arguments' => ['--force'],
            ],
        ],

        'monitoring' => [
            'log_clear' => [
                'command' => 'log:clear',
                'schedule' => 'weekly',
                'day' => 'sunday',
                'time' => '02:00',
                'description' => 'Czyszczenie starych logów',
                'enabled' => true,
            ],
        ],

        'backup' => [
            'backup_run' => [
                'command' => 'backup:run',
                'schedule' => 'daily',
                'time' => '03:00',
                'description' => 'Backup bazy danych',
                'enabled' => true,
                'options' => [
                    'without_overlapping' => true,
                    'run_in_background' => true,
                ],
            ],
            'backup_clean' => [
                'command' => 'backup:clean',
                'schedule' => 'weekly',
                'day' => 'saturday',
                'time' => '04:00',
                'description' => 'Czyszczenie starych backupów',
                'enabled' => true,
            ],
        ],

        'cache' => [
            'cache_clear' => [
                'command' => 'cache:clear',
                'schedule' => 'daily',
                'time' => '05:00',
                'description' => 'Czyszczenie cache',
                'enabled' => true,
            ],
            'optimize_clear' => [
                'command' => 'optimize:clear',
                'schedule' => 'weekly',
                'day' => 'sunday',
                'time' => '01:00',
                'description' => 'Optymalizacja autoloadera',
                'enabled' => true,
            ],
        ],

        'reports' => [
            'reports_generate_daily' => [
                'command' => 'reports:generate-daily',
                'schedule' => 'daily',
                'time' => '23:00',
                'description' => 'Generowanie raportu dziennego',
                'enabled' => false, // Wyłączone - brak implementacji
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Opcje globalne
    |--------------------------------------------------------------------------
    */

    'global_options' => [
        'log_success' => true,
        'log_failure' => true,
        'notify_on_failure' => env('CRON_NOTIFY_ON_FAILURE', false),
        'notification_email' => env('CRON_NOTIFICATION_EMAIL', 'admin@example.com'),
        'max_execution_time' => 3600, // 1 godzina
        'memory_limit' => '512M',
    ],

    /*
    |--------------------------------------------------------------------------
    | Harmonogram dzienny
    |--------------------------------------------------------------------------
    */

    'daily_schedule' => [
        '00:00' => [],
        '01:00' => ['optimize_clear' => 'niedziela'],
        '02:00' => ['log_clear' => 'niedziela'],
        '03:00' => ['backup_run'],
        '04:00' => ['backup_clean' => 'sobota'],
        '05:00' => ['cache_clear'],
        '06:00' => ['payments_generate'],
        '07:00' => ['queue_failed'],
        '08:00' => ['mails_import_incoming'],
        '09:00' => ['payments_send_reminders' => 'pon-pt'],
        '10:00' => [],
        '11:00' => [],
        '12:00' => [],
        '13:00' => [],
        '14:00' => [],
        '15:00' => [],
        '16:00' => [],
        '17:00' => [],
        '18:00' => [],
        '19:00' => [],
        '20:00' => [],
        '21:00' => [],
        '22:00' => [],
        '23:00' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Zadania co 5 minut
    |--------------------------------------------------------------------------
    */

    'every_five_minutes' => [
        'queue_work',
    ],

    /*
    |--------------------------------------------------------------------------
    | Zadania co godzinę
    |--------------------------------------------------------------------------
    */

    'hourly' => [
        // Brak zadań co godzinę
    ],

    /*
    |--------------------------------------------------------------------------
    | Zadania tygodniowe
    |--------------------------------------------------------------------------
    */

    'weekly' => [
        'saturday' => [
            '01:00' => [],
            '04:00' => ['backup_clean'],
        ],
        'sunday' => [
            '01:00' => ['optimize_clear'],
            '02:00' => ['log_clear'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Zadania miesięczne
    |--------------------------------------------------------------------------
    */

    'monthly' => [
        '01' => [
            '03:00' => ['backup_run'],
            '06:00' => ['payments_generate'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Zadania tylko w dni robocze
    |--------------------------------------------------------------------------
    */

    'weekdays' => [
        '09:00' => ['payments_send_reminders'],
    ],
];
