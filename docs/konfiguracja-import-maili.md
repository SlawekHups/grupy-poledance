# Konfiguracja Automatycznego Importu Wiadomości Przychodzących

## Status
✅ **Import działa poprawnie** - zaimportowano 1 wiadomość od `slawek@hups.pl`

## Konfiguracja IMAP
Dodano konfigurację IMAP do `config/mail.php`:
```php
'imap' => [
    'host' => env('MAIL_IMAP_HOST', env('MAIL_HOST')),
    'port' => env('MAIL_IMAP_PORT', 993),
    'username' => env('MAIL_IMAP_USERNAME', env('MAIL_USERNAME')),
    'password' => env('MAIL_IMAP_PASSWORD', env('MAIL_PASSWORD')),
    'encryption' => env('MAIL_IMAP_ENCRYPTION', 'ssl'),
],
```

Dodano zmienne do `.env`:
```env
MAIL_IMAP_HOST=mail.hupsnet.pl
MAIL_IMAP_PORT=993
MAIL_IMAP_USERNAME=info@hupsnet.pl
MAIL_IMAP_PASSWORD=B@belek84
MAIL_IMAP_ENCRYPTION=ssl
```

## Komenda Importu
```bash
php artisan mails:import-incoming --days=7
```

## Automatyczne Uruchamianie (Cron)

### Opcja 1: Dodaj do crona
```bash
# Otwórz edytor crona
crontab -e

# Dodaj linię (sprawdza co 15 minut)
*/15 * * * * cd /Users/slawek/Herd/grupy-poledance && php artisan mails:import-incoming --days=1 >> /dev/null 2>&1
```

### Opcja 2: Sprawdź czy cron działa
```bash
# Sprawdź aktywny cron
crontab -l

# Przetestuj komendę ręcznie
php artisan mails:import-incoming --days=1
```

## Sprawdzenie Działania

### 1. Sprawdź wiadomości w bazie
```bash
php artisan tinker --execute="use App\\Models\\UserMailMessage; echo 'Przychodzące: ' . UserMailMessage::where('direction', 'in')->count();"
```

### 2. Sprawdź w panelu admina
- Przejdź do `/admin/user-mail-messages`
- Filtruj po "Przychodzące" (direction = in)

### 3. Sprawdź w panelu użytkownika
- Użytkownik `slawek@hups.pl` powinien widzieć swoje wiadomości w `/user/user-mail-messages`

## Rozwiązywanie Problemów

### Problem: Brak wiadomości przychodzących
1. Sprawdź czy użytkownik ma email w bazie
2. Sprawdź logi: `tail -f storage/logs/laravel.log`
3. Przetestuj połączenie IMAP ręcznie

### Problem: Treść zakodowana
- Naprawiono dekodowanie quoted-printable w `ImportIncomingMails.php`

### Problem: Duplikaty wiadomości
- Komenda sprawdza `message_id` przed importem
- Używa `uniqid()` jako fallback

## Następne Kroki
1. ✅ Skonfiguruj cron dla automatycznego importu
2. ✅ Przetestuj wysyłanie wiadomości do użytkowników
3. ✅ Sprawdź czy wiadomości są widoczne w panelach 