# 🚀 Szybki Start - System Przypomnień o Płatnościach

## Co zostało stworzone

✅ **Komenda artisan** `payments:send-reminders` - automatyczne wysyłanie przypomnień  
✅ **Klasa email** `PaymentReminderMail` - profesjonalne szablony przypomnień  
✅ **Szablon Blade** `payment-reminder.blade.php` - piękny design emaili  
✅ **Akcja w panelu admin** - ręczne wysyłanie przypomnień  
✅ **Harmonogram zadań** `bootstrap/schedule.php` - automatyczne uruchamianie  
✅ **Dokumentacja** - kompletny opis systemu  

## 🧪 Testowanie systemu

### **1. Sprawdź czy komenda działa:**
```bash
# Tryb testowy - pokazuje co zostanie wysłane
php artisan payments:send-reminders --dry-run

# Rzeczywiste wysłanie (tylko jeśli masz użytkowników z zaległościami)
php artisan payments:send-reminders
```

### **2. Sprawdź w panelu admin:**
- Idź do **Użytkownicy** → wybierz użytkownika z zaległościami
- Kliknij **"Actions"** → **"Wyślij przypomnienie o płatności"**
- Potwierdź i sprawdź czy email został wysłany

### **3. Sprawdź logi:**
```bash
tail -f storage/logs/laravel.log
```

## ⚙️ Konfiguracja na serwerze

### **Dodaj do crontab:**
```bash
crontab -e

# Dodaj tę linię:
* * * * * cd /ścieżka/do/twojego/projektu && php artisan schedule:run >> /dev/null 2>&1
```

### **Dla cPanel/WHM:**
1. **cPanel** → **Cron Jobs**
2. **Command:** `cd /home/username/domains/twoja-domena.pl/public_html && php artisan schedule:run`
3. **Common Settings:** `Every Minute`

## 📅 Harmonogram działania

| Dzień | Godzina | Co się dzieje |
|-------|---------|---------------|
| **Poniedziałek** | 9:00 | Sprawdza grupy poniedziałkowe, wysyła przypomnienia |
| **Wtorek** | 9:00 | Sprawdza grupy wtorkowe, wysyła przypomnienia |
| **Środa** | 9:00 | Sprawdza grupy środowe, wysyła przypomnienia |
| **Czwartek** | 9:00 | Sprawdza grupy czwartkowe, wysyła przypomnienia |
| **Piątek** | 9:00 | Sprawdza grupy piątkowe, wysyła przypomnienia |
| **Sobota/Niedziela** | - | Brak akcji (nie ma grup weekendowych) |

## 🔍 Jak system wykrywa grupy

System automatycznie parsuje nazwy grup:
- ✅ **"Poniedziałek 18:00"** → wykryje jako grupę poniedziałkową
- ✅ **"Wtorek 19:00"** → wykryje jako grupę wtorkową  
- ✅ **"Środa 20:00"** → wykryje jako grupę środową
- ❌ **"Grupa A"** → nie zostanie wykryta (brak dnia tygodnia)

## 📧 Przykłady emaili

### **Przypomnienie bieżące:**
```
Temat: "Przypomnienie o płatności za Styczeń 2025 - Grupa Poniedziałek 18:00"
Treść: Informacja o płatności za bieżący miesiąc
```

### **Przypomnienie o zaległościach:**
```
Temat: "PILNE: Zaległości w płatnościach - Grupa Poniedziałek 18:00"
Treść: Lista wszystkich zaległych miesięcy + kwoty + ostrzeżenia
```

## 🚨 Rozwiązywanie problemów

### **Problem: "Command not found"**
```bash
# Sprawdź czy komenda jest zarejestrowana
php artisan list | grep payments
```

### **Problem: Błędy wysyłania emaili**
```bash
# Sprawdź konfigurację mail
php artisan tinker
Mail::raw('test', function($msg) { $msg->to('test@example.com')->subject('test'); });
```

### **Problem: Cron nie działa**
```bash
# Sprawdź czy cron jest aktywny
crontab -l

# Sprawdź logi cron
tail -f /var/log/cron
```

## 📊 Monitoring i logi

### **Szukanie w logach:**
```bash
# Wszystkie wysłane przypomnienia
grep "Wysłano przypomnienie o płatności" storage/logs/laravel.log

# Błędy wysyłania
grep "Błąd wysyłania przypomnienia" storage/logs/laravel.log

# Ręczne wysłania
grep "Ręcznie wysłano przypomnienie" storage/logs/laravel.log
```

### **Przykładowe logi:**
```
[2025-01-20 09:00:01] local.INFO: Wysłano przypomnienie o płatności {"user_id":5,"user_email":"user@example.com","group":"Poniedziałek 18:00","unpaid_count":2,"total_amount":400}
```

## 🎯 Następne kroki

1. **Przetestuj system** w trybie `--dry-run`
2. **Skonfiguruj cron** na serwerze
3. **Sprawdź czy emaile są wysyłane** w poniedziałek o 9:00
4. **Monitoruj logi** przez pierwsze dni
5. **Dostosuj treść emaili** jeśli potrzeba

## 📞 Wsparcie

Jeśli coś nie działa:
1. Sprawdź logi aplikacji
2. Sprawdź logi cron  
3. Uruchom komendę ręcznie
4. Sprawdź konfigurację mail
5. Sprawdź uprawnienia na serwerze

---

**🎉 Gratulacje! Masz w pełni automatyczny system przypomnień o płatnościach!**
