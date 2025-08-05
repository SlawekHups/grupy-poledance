# Konfiguracja kolejki na produkcji

## Problem
Zaproszenia email są dodawane do kolejki, ale nie są automatycznie przetwarzane na serwerze produkcyjnym.

## Rozwiązanie: Konfiguracja Cron Job

### 1. Sprawdź aktualny cron
```bash
crontab -l
```

### 2. Edytuj cron
```bash
crontab -e
```

### 3. Dodaj wpis dla kolejki Laravel
```bash
# Uruchamiaj worker kolejki co minutę
* * * * * cd /ścieżka/do/projektu && php artisan queue:work --timeout=60 --tries=3 --stop-when-empty >> /dev/null 2>&1
```

### 4. Alternatywnie - użyj supervisora (zalecane dla większych projektów)

#### Instalacja supervisor:
```bash
# Ubuntu/Debian
sudo apt-get install supervisor

# CentOS/RHEL
sudo yum install supervisor
```

#### Konfiguracja:
Utwórz plik `/etc/supervisor/conf.d/laravel-worker.conf`:

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /ścieżka/do/projektu/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/ścieżka/do/projektu/storage/logs/worker.log
stopwaitsecs=3600
```

#### Uruchom supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

### 5. Sprawdź czy działa
```bash
# Sprawdź zadania w kolejce
php artisan tinker --execute="echo 'Zadania w kolejce: ' . \Illuminate\Support\Facades\DB::table('jobs')->count();"

# Sprawdź nieudane zadania
php artisan queue:failed

# Sprawdź logi workera (jeśli używasz supervisora)
tail -f storage/logs/worker.log
```

## Testowanie

### 1. Utwórz testowego użytkownika
- Przejdź do panelu admin
- Dodaj nowego użytkownika (bez hasła)
- Sprawdź czy email został wysłany

### 2. Sprawdź logi
```bash
# Sprawdź logi aplikacji
tail -f storage/logs/laravel.log

# Sprawdź logi maila (jeśli skonfigurowane)
tail -f /var/log/mail.log
```

## Uwagi

- **Cron**: Proste rozwiązanie, ale może powodować duplikaty
- **Supervisor**: Zalecane dla produkcji, lepsze zarządzanie procesami
- **Timeout**: Ustaw odpowiedni timeout (60-300 sekund)
- **Tries**: Liczba prób przed oznaczeniem jako nieudane (3-5)
- **Logi**: Zawsze włącz logowanie dla debugowania

## Troubleshooting

### Problem: Zadania nie są przetwarzane
```bash
# Sprawdź czy worker działa
ps aux | grep "queue:work"

# Uruchom worker ręcznie
php artisan queue:work --verbose
```

### Problem: Duplikaty emaili
- Użyj supervisora zamiast crona
- Sprawdź czy nie ma wielu workerów

### Problem: Timeout
- Zwiększ `--timeout` w komendzie
- Sprawdź konfigurację SMTP 