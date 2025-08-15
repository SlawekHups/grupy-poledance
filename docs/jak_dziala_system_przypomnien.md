# 🔍 Jak Działa System Przypomnień o Płatnościach

## 📋 Przegląd systemu

System automatycznie wysyła przypomnienia o płatnościach do użytkowników z zaległościami, bazując na harmonogramie zajęć ich grup. Działa w tle, nie wymaga interwencji administratora i jest w pełni zintegrowany z istniejącą aplikacją.

## 🏗️ Architektura systemu

```
┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐
│   Cron Server   │───▶│ Laravel Scheduler│───▶│ Artisan Command │
└─────────────────┘    └──────────────────┘    └─────────────────┘
                                │                        │
                                ▼                        ▼
                       ┌──────────────────┐    ┌─────────────────┐
                       │  Schedule File   │    │SendPaymentReminders│
                       │bootstrap/schedule│    │   Command       │
                       └──────────────────┘    └─────────────────┘
                                                        │
                                                        ▼
                       ┌──────────────────┐    ┌─────────────────┐
                       │  Email System   │◀───│ PaymentReminderMail│
                       │   (SMTP/IMAP)   │    │   Mailable      │
                       └──────────────────┘    └─────────────────┘
                                                        │
                                                        ▼
                       ┌──────────────────┐    ┌─────────────────┐
                       │   Database      │    │   Logging       │
                       │  (Users, Groups,│    │  (Laravel Log)  │
                       │   Payments)     │    │                 │
                       └──────────────────┘    └─────────────────┘
```

## 🔄 Przepływ działania

### **1. Uruchomienie przez Cron**
```bash
# Co minutę cron uruchamia:
* * * * * cd /ścieżka/do/projektu && php artisan schedule:run
```

### **2. Laravel Scheduler sprawdza harmonogram**
```php
// bootstrap/schedule.php
Schedule::command('payments:send-reminders')
    ->weekdays()           // Od poniedziałku do piątku
    ->at('09:00')
    ->withoutOverlapping()
    ->runInBackground();
```

### **3. Komenda SendPaymentReminders jest uruchamiana**
```php
// app/Console/Commands/SendPaymentReminders.php
class SendPaymentReminders extends Command
{
    protected $signature = 'payments:send-reminders {--dry-run}';
    
    public function handle()
    {
        // 1. Sprawdź jaki dzień tygodnia
        // 2. Znajdź grupy na dzisiaj
        // 3. Sprawdź użytkowników z zaległościami
        // 4. Wyślij przypomnienia
    }
}
```

## 🧠 Logika wykrywania grup

### **Mapowanie dni tygodnia:**
```php
$dayNames = [
    1 => 'Poniedziałek',  // Carbon::MONDAY
    2 => 'Wtorek',        // Carbon::TUESDAY  
    3 => 'Środa',         // Carbon::WEDNESDAY
    4 => 'Czwartek',      // Carbon::THURSDAY
    5 => 'Piątek',        // Carbon::FRIDAY
    6 => 'Sobota',        // Carbon::SATURDAY
    7 => 'Niedziela'      // Carbon::SUNDAY
];
```

### **Wykrywanie grup na dzisiaj:**
```php
$today = Carbon::now();
$currentDayOfWeek = $today->dayOfWeek; // 1-7
$currentDayName = $dayNames[$currentDayOfWeek];

// Znajdź grupy, które mają zajęcia dzisiaj
$todayGroups = Group::where('name', 'like', "{$currentDayName}%")->get();
```

### **Przykłady wykrywania:**
- **Dzisiaj poniedziałek** → szuka grup: "Poniedziałek 18:00", "Poniedziałek 19:00"
- **Dzisiaj wtorek** → szuka grup: "Wtorek 18:00", "Wtorek 19:00"
- **Dzisiaj środa** → szuka grup: "Środa 18:00", "Środa 19:00"

## 💰 Sprawdzanie zaległości w płatnościach

### **Algorytm sprawdzania:**
```php
private function getUnpaidPayments(User $user): Collection
{
    $currentMonth = Carbon::now()->format('Y-m'); // np. "2025-01"
    
    return Payment::where('user_id', $user->id)
        ->where('paid', false)                    // Tylko nieopłacone
        ->where('month', '<=', $currentMonth)     // Do bieżącego miesiąca włącznie
        ->orderBy('month', 'asc')                 // Sortuj od najstarszych
        ->get();
}
```

### **Przykład sprawdzania:**
```
Dzisiaj: 20 stycznia 2025
Bieżący miesiąc: 2025-01

Użytkownik ma nieopłacone płatności:
- 2024-11 (Listopad 2024) - 200 zł
- 2024-12 (Grudzień 2024) - 200 zł  
- 2025-01 (Styczeń 2025) - 200 zł

Łączna zaległość: 600 zł
```

## 📧 Generowanie treści emaili

### **Rozróżnianie typów zaległości:**
```php
$currentMonth = Carbon::now()->format('Y-m');
$currentMonthPayment = $unpaidPayments->where('month', $currentMonth)->first();

if ($currentMonthPayment) {
    $reminderType = 'bieżący';
    $subject = "Przypomnienie o płatności za {$currentMonth} - Grupa {$group->name}";
} else {
    $reminderType = 'zaległy';
    $subject = "PILNE: Zaległości w płatnościach - Grupa {$group->name}";
}
```

### **Struktura emaila:**
```
┌─────────────────────────────────────────────────────────────┐
│                    💰 Przypomnienie o Płatności            │
├─────────────────────────────────────────────────────────────┤
│ Cześć [Imię]!                                              │
│                                                             │
│ [Treść w zależności od typu zaległości]                    │
│                                                             │
│ ┌─ Podsumowanie zaległości ─┐                              │
│ │ • Liczba miesięcy: X      │                              │
│ │ • Łączna kwota: XXX zł    │                              │
│ └───────────────────────────┘                              │
│                                                             │
│ ┌─ Szczegółowa tabela ──────┐                              │
│ │ Miesiąc      │ Kwota      │                              │
│ │ Listopad 2024│ 200 zł     │                              │
│ │ Grudzień 2024│ 200 zł     │                              │
│ │ Styczeń 2025 │ 200 zł     │                              │
│ └───────────────────────────┘                              │
│                                                             │
│ Co dalej?                                                   │
│ [Instrukcje + ostrzeżenia]                                 │
│                                                             │
│ Dane kontaktowe                                             │
└─────────────────────────────────────────────────────────────┘
```

## 🎯 Harmonogram działania

### **Tygodniowy cykl:**
```
Poniedziałek 9:00 ──┐
                    ├─► Sprawdza grupy poniedziałkowe
                    ├─► Wysyła przypomnienia do użytkowników z zaległościami
                    └─► Loguje wszystkie operacje

Wtorek 9:00 ───────┐
                    ├─► Sprawdza grupy wtorkowe
                    ├─► Wysyła przypomnienia do użytkowników z zaległościami
                    └─► Loguje wszystkie operacje

Środa 9:00 ────────┐
                    ├─► Sprawdza grupy środowe
                    ├─► Wysyła przypomnienia do użytkowników z zaległościami
                    └─► Loguje wszystkie operacje

Czwartek 9:00 ─────┐
                    ├─► Sprawdza grupy czwartkowe
                    ├─► Wysyła przypomnienia do użytkowników z zaległościami
                    └─► Loguje wszystkie operacje

Piątek 9:00 ───────┐
                    ├─► Sprawdza grupy piątkowe
                    ├─► Wysyła przypomnienia do użytkowników z zaległościami
                    └─► Loguje wszystkie operacje

Sobota/Niedziela ──► Brak akcji (nie ma grup weekendowych)
```

## 🔧 Integracja z istniejącym systemem

### **Modele używane:**
- **User** - użytkownicy z grupami i płatnościami
- **Group** - grupy z harmonogramem zajęć
- **Payment** - płatności miesięczne z statusem opłacenia
- **UserMailMessage** - logowanie wysłanych emaili (przez istniejący system)

### **Relacje w bazie danych:**
```sql
users
├── id
├── name
├── email
├── group_id ──┐
├── amount     │
└── is_active  │
               │
groups ◄───────┘
├── id
├── name (np. "Poniedziałek 18:00")
└── description

payments
├── id
├── user_id ──┐
├── month     │
├── amount    │
├── paid      │
└── created_at│
              │
users ◄───────┘
```

## 📊 Logowanie i monitoring

### **Co jest logowane:**
```php
Log::info("Wysłano przypomnienie o płatności", [
    'user_id' => $user->id,
    'user_email' => $user->email,
    'group' => $group->name,
    'unpaid_count' => $unpaidPayments->count(),
    'total_amount' => $unpaidPayments->sum('amount')
]);
```

### **Gdzie szukać logów:**
```bash
# Wszystkie wysłane przypomnienia
grep "Wysłano przypomnienie o płatności" storage/logs/laravel.log

# Błędy wysyłania
grep "Błąd wysyłania przypomnienia" storage/logs/laravel.log

# Ręczne wysłania z panelu admin
grep "Ręcznie wysłano przypomnienie" storage/logs/laravel.log
```

## 🚨 Obsługa błędów

### **Typy błędów i ich obsługa:**
```php
try {
    // Wysyłanie emaila
    Mail::to($user->email)->send(new PaymentReminderMail(...));
    
    // Sukces - logowanie
    Log::info("Wysłano przypomnienie o płatności", [...]);
    
} catch (\Exception $e) {
    // Błąd - logowanie i kontynuacja
    Log::error("Błąd wysyłania przypomnienia", [
        'user_id' => $user->id,
        'error' => $e->getMessage()
    ]);
    
    // Kontynuuj z następnym użytkownikiem
    continue;
}
```

### **Zabezpieczenia:**
- **`withoutOverlapping()`** - zapobiega uruchomieniu wielu instancji
- **`runInBackground()`** - nie blokuje głównego procesu
- **Try-catch** - błędy nie przerywają całego procesu
- **Logowanie** - wszystkie operacje są śledzone

## 🎛️ Konfiguracja i dostosowywanie

### **Zmiana częstotliwości:**
```php
// bootstrap/schedule.php

// Codziennie o 9:00 zamiast co poniedziałek
Schedule::command('payments:send-reminders')
    ->daily()
    ->at('09:00');

// Co 2 dni
Schedule::command('payments:send-reminders')
    ->everyTwoDays()
    ->at('09:00');
```

### **Zmiana treści emaili:**
```php
// app/Console/Commands/SendPaymentReminders.php
private function generateReminderContent(...): string
{
    // Tutaj możesz dostosować treść emaila
    $content = "<h2>Cześć {$user->name}!</h2>";
    // ... reszta logiki
}
```

### **Dodanie nowych dni tygodnia:**
```php
$dayNames = [
    1 => 'Poniedziałek',
    2 => 'Wtorek', 
    3 => 'Środa',
    4 => 'Czwartek',
    5 => 'Piątek',
    6 => 'Sobota',      // ← Dodane
    7 => 'Niedziela'    // ← Dodane
];
```

## 🔍 Tryb testowy (Dry Run)

### **Jak działa:**
```bash
php artisan payments:send-reminders --dry-run
```

### **Co pokazuje:**
```
Rozpoczynam wysyłanie przypomnień o płatnościach...
TRYB TESTOWY - nie wysyłam emaili
Dzisiaj jest: Poniedziałek (1)
Znaleziono grupy na dzisiaj: Poniedziałek 18:00, Poniedziałek 19:00

Przetwarzam grupę: Poniedziałek 18:00
  Użytkownicy w grupie: 3
  ✓ Jan Kowalski - wszystkie płatności uregulowane
  ⚠ Anna Nowak - ma 2 nieopłacone płatności
    [DRY RUN] Wysłano by przypomnienie do: anna@example.com
    [DRY RUN] Temat: PILNE: Zaległości w płatnościach - Grupa Poniedziałek 18:00
    [DRY RUN] Treść: Cześć Anna! PILNE: Masz zaległości w płatnościach...

=== PODSUMOWANIE ===
Przetworzono użytkowników: 3
Wysłano przypomnień: 1
TRYB TESTOWY - żadne emaile nie zostały wysłane
```

## 📈 Wydajność i optymalizacja

### **Zapytania do bazy danych:**
```php
// 1. Znajdź grupy na dzisiaj
$todayGroups = Group::where('name', 'like', "{$currentDayName}%")->get();

// 2. Dla każdej grupy - użytkownicy
$users = User::where('group_id', $group->id)
    ->where('is_active', true)
    ->whereNot('role', 'admin')
    ->get();

// 3. Dla każdego użytkownika - nieopłacone płatności
$unpaidPayments = Payment::where('user_id', $user->id)
    ->where('paid', false)
    ->where('month', '<=', $currentMonth)
    ->orderBy('month', 'asc')
    ->get();
```

### **Optymalizacje:**
- **Eager loading** - grupy są ładowane raz
- **Batch processing** - użytkownicy są przetwarzani grupa po grupie
- **Background processing** - nie blokuje głównego procesu
- **Logging asynchroniczne** - logi nie spowalniają wysyłania

## 🔐 Bezpieczeństwo

### **Ograniczenia dostępu:**
- **Tylko aktywni użytkownicy** - `where('is_active', true)`
- **Bez administratorów** - `whereNot('role', 'admin')`
- **Walidacja danych** - sprawdzanie czy użytkownik ma grupę
- **Tryb testowy** - bezpieczne testowanie bez wysyłania emaili

### **Ochrona przed nadużyciem:**
- **`withoutOverlapping()`** - zapobiega wielokrotnemu uruchomieniu
- **Logowanie wszystkich operacji** - pełna transparentność
- **Obsługa błędów** - błędy nie przerywają procesu
- **Walidacja emaili** - sprawdzanie poprawności adresów

## 🎯 Podsumowanie działania

1. **Cron uruchamia Laravel Scheduler** co minutę
2. **Scheduler sprawdza harmonogram** i uruchamia komendy o określonych porach
3. **Komenda SendPaymentReminders** jest uruchamiana **codziennie od poniedziałku do piątku o 9:00**
4. **System wykrywa grupy** na dzisiaj (np. poniedziałkowe we wtorek, wtorkowe we wtorek)
5. **Dla każdej grupy** pobiera aktywnych użytkowników
6. **Dla każdego użytkownika** sprawdza zaległości w płatnościach
7. **Generuje odpowiedni email** (bieżący/zaległy)
8. **Wysyła email** przez system mail Laravel
9. **Loguje wszystkie operacje** do pliku log
10. **Kontynuuje z następnym użytkownikiem** aż do zakończenia

System jest w pełni automatyczny, bezpieczny i skalowalny, zapewniając regularne przypomnienia o płatnościach bez interwencji administratora! 🚀
