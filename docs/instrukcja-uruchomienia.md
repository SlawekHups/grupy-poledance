# Instrukcja uruchomienia projektu na serwerze zewnętrznym

## 1. Wymagania serwera
- PHP 8.2 lub nowszy (zalecane 8.3)
- Rozszerzenia PHP: `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`
- Composer (najlepiej globalnie)
- Node.js + npm (do budowania assetów)
- Serwer WWW: Apache/Nginx
- Baza danych: MySQL/MariaDB/PostgreSQL
- Dostęp SSH do serwera

## 2. Klonowanie repozytorium
```bash
git clone <TWOJE_REPOZYTORIUM_GIT> projekt
cd projekt
```

## 3. Konfiguracja środowiska
- Skopiuj plik `.env.example` do `.env`:
  ```bash
  cp .env.example .env
  ```
- Ustaw dane bazy danych, maila, domeny, itp. w `.env`:
  - `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
  - `APP_URL`, `MAIL_*`, itp.
- Wygeneruj klucz aplikacji:
  ```bash
  php artisan key:generate
  ```

## 4. Instalacja zależności
- PHP:
  ```bash
  composer install --no-dev --optimize-autoloader
  ```
- JS/CSS:
  ```bash
  npm install
  npm run build
  ```

## 5. Uprawnienia
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```
*(użyj odpowiedniego użytkownika serwera, np. `www-data` lub `apache`)*

## 6. Migracje i seedy
```bash
php artisan migrate --force
php artisan db:seed --force
```

## 7. Link do storage (uploady)
```bash
php artisan storage:link
```

## 8. Cache i optymalizacja
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 9. Ustawienia wirtualnego hosta (przykład Nginx)
```nginx
server {
    server_name twoja-domena.pl;
    root /ścieżka/do/projektu/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

## 10. Cron (np. zadania cykliczne)
Dodaj do crontaba:
```
* * * * * cd /ścieżka/do/projektu && php artisan schedule:run >> /dev/null 2>&1
```

## 11. Certyfikat SSL
- Skonfiguruj SSL (np. Let’s Encrypt) według dokumentacji serwera.

## 12. Uruchomienie/restart serwera WWW
- Zrestartuj serwer WWW po zmianach:
  ```bash
  sudo systemctl restart nginx
  # lub
  sudo systemctl restart apache2
  ```

## 13. Typowe błędy i wskazówki
- Sprawdź logi: `storage/logs/laravel.log`
- Sprawdź uprawnienia do katalogów
- Upewnij się, że `.env` jest poprawnie skonfigurowany
- Jeśli assety się nie ładują: `npm run build` i sprawdź uprawnienia do `public/build`
- Jeśli nie działa storage: sprawdź `php artisan storage:link` i uprawnienia

---
**Projekt gotowy do działania!** 