# Debug 403 Error na Produkcji - Przewodnik

## 🔍 **Najczęstsze przyczyny błędu 403 na produkcji:**

### 1. **Maintenance Mode (Tryb konserwacji)**
```bash
# Sprawdź czy aplikacja jest w trybie konserwacji
APP_ENV=production php artisan down
APP_ENV=production php artisan up

# Sprawdź status
APP_ENV=production php artisan config:show app.maintenance
```

### 2. **Problemy z uprawnieniami plików**
```bash
# Ustaw poprawne uprawnienia
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod -R 755 public/
chown -R www-data:www-data storage/
chown -R www-data:www-data bootstrap/cache/
```

### 3. **Problemy z konfiguracją serwera**
```bash
# Sprawdź konfigurację nginx/apache
# Upewnij się że DocumentRoot wskazuje na folder public/
# Sprawdź czy .htaccess jest dostępny (Apache)
```

### 4. **Problemy z bazą danych**
```bash
# Sprawdź połączenie z bazą
APP_ENV=production php artisan migrate:status
APP_ENV=production php artisan db:show
```

### 5. **Problemy z cache**
```bash
# Wyczyść wszystkie cache
APP_ENV=production php artisan config:clear
APP_ENV=production php artisan route:clear
APP_ENV=production php artisan view:clear
APP_ENV=production php artisan cache:clear
```

### 6. **Problemy z sesjami**
```bash
# Sprawdź konfigurację sesji
APP_ENV=production php artisan config:show session
# Upewnij się że tabela sessions istnieje
APP_ENV=production php artisan migrate:status
```

## 🛠️ **Jak testować na serwerze produkcyjnym:**

### **Krok 1: Sprawdź logi**
```bash
# Sprawdź logi Laravel
tail -f storage/logs/laravel.log

# Sprawdź logi serwera
tail -f /var/log/nginx/error.log
tail -f /var/log/apache2/error.log
```

### **Krok 2: Testuj konfigurację**
```bash
# Sprawdź czy aplikacja się uruchamia
APP_ENV=production php artisan serve --host=0.0.0.0 --port=8000

# Testuj połączenie z bazą
APP_ENV=production php artisan tinker
>>> DB::connection()->getPdo();
```

### **Krok 3: Sprawdź uprawnienia**
```bash
# Sprawdź uprawnienia do zapisu
ls -la storage/
ls -la bootstrap/cache/
ls -la public/

# Sprawdź czy użytkownik serwera ma dostęp
sudo -u www-data ls -la storage/
sudo -u www-data touch storage/test.txt
```

### **Krok 4: Testuj routing**
```bash
# Sprawdź czy routes działają
APP_ENV=production php artisan route:list
APP_ENV=production php artisan route:cache
```

## 🚨 **Najczęstsze błędy 403:**

1. **403 Forbidden - Directory listing denied**
   - Problem: Serwer próbuje wyświetlić katalog zamiast pliku
   - Rozwiązanie: Sprawdź DocumentRoot w konfiguracji serwera

2. **403 Forbidden - Access denied**
   - Problem: Brak uprawnień do plików
   - Rozwiązanie: Ustaw poprawne uprawnienia (755/644)

3. **403 Forbidden - Maintenance mode**
   - Problem: Aplikacja w trybie konserwacji
   - Rozwiązanie: `php artisan up`

4. **403 Forbidden - CSRF token mismatch**
   - Problem: Błędny token CSRF
   - Rozwiązanie: Sprawdź konfigurację sesji i cache

## 🔧 **Szybkie rozwiązania:**

```bash
# 1. Wyczyść wszystko
APP_ENV=production php artisan optimize:clear

# 2. Ustaw uprawnienia
chmod -R 755 storage/ bootstrap/cache/ public/
chown -R www-data:www-data storage/ bootstrap/cache/

# 3. Sprawdź konfigurację
APP_ENV=production php artisan config:show app
APP_ENV=production php artisan config:show database

# 4. Testuj aplikację
APP_ENV=production php artisan serve --host=0.0.0.0 --port=8000
```

## 📋 **Checklist przed wdrożeniem:**

- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `APP_URL` ustawiony na prawdziwą domenę
- [ ] Baza danych skonfigurowana
- [ ] Uprawnienia plików ustawione (755/644)
- [ ] Cache wyczyszczony
- [ ] Migracje wykonane
- [ ] Konfiguracja serwera poprawna
- [ ] Logi sprawdzone
