# 📞 Konfiguracja Danych Kontaktowych - System Przypomnień

## 🔍 Gdzie są ustawione dane kontaktowe

Dane kontaktowe w systemie przypomnień o płatnościach są obecnie zahardkodowane w kodzie. Oto jak je skonfigurować:

## ⚙️ Konfiguracja przez plik .env

### **Dodaj do pliku `.env`:**
```bash
# Dane kontaktowe dla przypomnień o płatnościach
PAYMENT_REMINDER_EMAIL=biuro@twoja-domena.pl
PAYMENT_REMINDER_PHONE=+48 123 456 789
PAYMENT_REMINDER_COMPANY_NAME=Twoja Nazwa Firmy
PAYMENT_REMINDER_WEBSITE=twoja-domena.pl
```

### **Przykład dla Twojej firmy:**
```bash
PAYMENT_REMINDER_EMAIL=biuro@ozonek.pl
PAYMENT_REMINDER_PHONE=+48 534 598 534
PAYMENT_REMINDER_COMPANY_NAME=Grupy Poledance
PAYMENT_REMINDER_WEBSITE=ozonek.pl
```

## 🏗️ Struktura plików do zmiany

### **1. Komenda `SendPaymentReminders`:**
```php
// app/Console/Commands/SendPaymentReminders.php
// Linie ~240-241 - dane kontaktowe
```

### **2. Akcja w `UserResource`:**
```php
// app/Filament/Admin/Resources/UserResource.php
// Metoda generateReminderContent() - dane kontaktowe
```

### **3. Szablon email:**
```php
// resources/views/emails/payment-reminder.blade.php
// Dane kontaktowe w stopce
```

## 🚀 Jak zmienić dane kontaktowe

### **Opcja 1: Przez plik .env (ZALECANE)**
1. Edytuj plik `.env` w głównym katalogu projektu
2. Dodaj/zmień dane kontaktowe
3. Uruchom: `php artisan config:cache`

### **Opcja 2: Bezpośrednio w kodzie**
1. Edytuj plik `app/Console/Commands/SendPaymentReminders.php`
2. Znajdź linie z danymi kontaktowymi
3. Zmień na swoje dane
4. Uruchom: `php artisan config:cache`

### **Opcja 3: Przez panel admin (PRZYSZŁOŚĆ)**
- Można dodać stronę ustawień w panelu admin
- Dane kontaktowe będą edytowalne przez interfejs

## 📋 Aktualne dane w systemie

```
Email: biuro@ozonek.pl
Telefon: +48 534 598 534
Nazwa firmy: Grupy Poledance
```

## 🔄 Po zmianie danych

### **1. Wyczyść cache:**
```bash
php artisan config:clear
php artisan cache:clear
```

### **2. Przetestuj:**
```bash
# Tryb testowy
php artisan payments:send-reminders --dry-run

# Sprawdź czy nowe dane są używane
```

### **3. Sprawdź logi:**
```bash
tail -f storage/logs/laravel.log
```

## 📝 Przykład zmiany danych

### **Z:**
```bash
PAYMENT_REMINDER_EMAIL=biuro@ozonek.pl
PAYMENT_REMINDER_PHONE=+48 534 598 534
```

### **Na:**
```bash
PAYMENT_REMINDER_EMAIL=kontakt@twoja-firma.pl
PAYMENT_REMINDER_PHONE=+48 999 888 777
```

## ⚠️ Uwagi

- **Nie commituj** pliku `.env` do Git (powinien być w `.gitignore`)
- **Zawsze** wyczyść cache po zmianie konfiguracji
- **Przetestuj** system po każdej zmianie
- **Zachowaj** kopię zapasową danych kontaktowych

## 🆘 Wsparcie

Jeśli masz problemy ze zmianą danych kontaktowych:
1. Sprawdź czy plik `.env` jest poprawnie sformatowany
2. Uruchom `php artisan config:clear`
3. Sprawdź logi aplikacji
4. Przetestuj w trybie `--dry-run`
