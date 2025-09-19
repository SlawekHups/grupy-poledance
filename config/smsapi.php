<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SMSAPI Configuration
    |--------------------------------------------------------------------------
    |
    | Konfiguracja dla integracji z SMSAPI.pl
    |
    */

    'auth_token' => env('SMSAPI_AUTH_TOKEN'),
    'from_name' => env('SMSAPI_FROM_NAME', 'Poledance'),
    'test_mode' => env('SMSAPI_TEST_MODE', true),
    'debug' => env('SMSAPI_DEBUG', false),
    
    /*
    |--------------------------------------------------------------------------
    | Domyślne ustawienia SMS
    |--------------------------------------------------------------------------
    */
    
    'defaults' => [
        'from' => env('SMSAPI_FROM_NAME', 'Poledance'),
        'test' => env('SMSAPI_TEST_MODE', true),
        'encoding' => 'utf-8',
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Limity i bezpieczeństwo
    |--------------------------------------------------------------------------
    */
    
    'limits' => [
        'max_message_length' => 160,
        'max_recipients_per_batch' => 100,
        'rate_limit_per_minute' => 60,
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Szablony wiadomości
    |--------------------------------------------------------------------------
    */
    
    'templates' => [
        'pre_registration' => 'Witaj! Oto link do rejestracji: {link}',
        'data_correction' => 'Link do poprawy danych: {link}',
        'password_reset' => 'Link do resetu hasła: {link}',
        'payment_reminder' => 'Przypomnienie: Zaległość {amount} zł do {due_date}. Zapłać online: {link}',
        'general' => '{message}',
    ],
];
