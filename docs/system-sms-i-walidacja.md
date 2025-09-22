# System SMS i Walidacja - Dokumentacja

Data: 2025-09-22

## 📱 System SMS API

### Integracja z SMS API Poland
- **Wysyłanie SMS-ów** z aplikacji przez SMS API Poland
- **Logowanie** wszystkich wysłanych wiadomości
- **Automatyczne obliczanie kosztów** (0,17 PLN za SMS)
- **Sprawdzanie salda konta** w czasie rzeczywistym

### Funkcjonalności SMS
- **Pre-rejestracja** - wysyłanie linków rejestracyjnych przez SMS
- **Reset hasła** - SMS z linkiem do resetu hasła
- **Przypomnienia płatności** - SMS z informacją o zaległościach
- **Poprawa danych** - SMS z linkiem do korekty danych
- **Testy SMS** - możliwość testowania wysyłania

### Panel administracyjny SMS
- **Logi SMS** (`/admin/sms-logs`) w grupie "Ustawienia"
- **Szczegóły SMS** - modal z pełnymi informacjami o wiadomości
- **Statystyki** - dzienne, tygodniowe, miesięczne podsumowania
- **Saldo konta** - automatyczne sprawdzanie i ostrzeżenia o niskim saldzie
- **Kolorowe wskaźniki** - status wysłania, typ SMS, poziom salda

### Konfiguracja SMS API
```php
// config/smsapi.php
'pricing' => [
    'cost_per_sms' => 0.17, // Koszt wysłania 1 SMS w PLN
    'currency' => 'PLN',
],
'templates' => [
    'pre_registration' => 'Witaj! Oto link do rejestracji: {link}',
    'password_reset' => 'Link do resetu hasła: {link}',
    'payment_reminder' => 'Przypomnienie: Zaległość {amount} zł do {due_date}. Zapłać online: {link}',
    'data_correction' => 'Witaj! Oto link do poprawy danych: {link}',
],
```

### Komendy artisan
```bash
# Sprawdź saldo SMS API
php artisan sms:balance
```

## 🔍 Walidacja formularzy

### Walidacja telefonu
- **Format**: 9 cyfr (opcjonalnie z +48)
- **Przykłady**: `123456789`, `+48123456789`, `48123456789`
- **Regex**: `/^(\+?48)?[0-9]{9}$/`
- **Komunikat błędu**: "Numer telefonu musi zawierać 9 cyfr (np. 123456789 lub +48123456789)."

### Walidacja kodu pocztowego
- **Format**: XX-XXX (np. 12-345)
- **Regex**: `/^\d{2}-\d{3}$/`
- **Komunikat błędu**: "Kod pocztowy musi być w formacie XX-XXX (np. 12-345)."

### Formularz poprawy danych
- **Uproszczony** - usunięto pola adres, miasto, kod pocztowy
- **Pola**: tylko nazwa, email, telefon
- **Powód**: te dane użytkownik uzupełnia w swoim panelu po rejestracji
- **Walidacja**: pełna walidacja wszystkich pól z podświetlaniem błędów

## 📱 Mobile menu i nawigacja

### Naprawione problemy
- **Przewijanie menu** - dodano `max-height` i `overflow-y-auto`
- **Custom scrollbar** - ładniejszy pasek przewijania z obsługą dark mode
- **Wszystkie linki** - kompletne menu mobilne dla admin i user
- **Logi SMS** - dodane do sekcji "Ustawienia" w mobile menu

### Style przewijania
```css
.mobile-menu-scroll {
    -webkit-overflow-scrolling: touch;
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 #f1f5f9;
}
```

## 🗂️ Struktura plików

### SMS
```
app/
├── Services/
│   └── SmsService.php
├── Models/
│   └── SmsLog.php
├── Filament/Admin/Resources/
│   └── SmsLogResource.php
└── Console/Commands/
    └── CheckSmsBalanceCommand.php

database/migrations/
└── create_sms_logs_table.php

config/
└── smsapi.php
```

### Walidacja
```
app/Filament/UserPanel/Pages/
└── OnboardingWizard.php

app/Http/Controllers/
└── DataCorrectionController.php

resources/views/
├── data-correction/form.blade.php
└── filament/user/mobile-top-nav.blade.php
```

## 🔧 Rozwiązywane problemy

### SMS API
- ✅ Integracja z SMS API Poland
- ✅ Automatyczne logowanie SMS-ów
- ✅ Obliczanie kosztów
- ✅ Sprawdzanie salda konta
- ✅ Statystyki i widgety

### Walidacja
- ✅ Walidacja formatu telefonu (polski)
- ✅ Walidacja kodu pocztowego (XX-XXX)
- ✅ Podświetlanie błędów (czerwone ramki)
- ✅ Placeholdery z przykładami
- ✅ Uproszczenie formularza poprawy danych

### Mobile menu
- ✅ Naprawione przewijanie
- ✅ Custom scrollbar
- ✅ Wszystkie linki podłączone
- ✅ Logi SMS w menu

## 📊 Monitoring

### Logi SMS
- **Lokalizacja**: `/admin/sms-logs`
- **Filtry**: status, typ, data, błędy
- **Statystyki**: dzienne, tygodniowe, miesięczne
- **Saldo**: automatyczne sprawdzanie co 5 minut (cache)

### Komendy
```bash
# Sprawdź saldo SMS API
php artisan sms:balance

# Sprawdź logi SMS
tail -f storage/logs/laravel.log | grep "SMS"
```

---

**Status**: ✅ PRODUKCYJNY - Wszystkie funkcjonalności działają poprawnie
