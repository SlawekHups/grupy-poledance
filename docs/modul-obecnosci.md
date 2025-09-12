# Moduł Obecności - Dokumentacja

## 📋 Przegląd

Moduł obecności to zaawansowany system zarządzania frekwencją w grupach zajęć poledance. Umożliwia administratorom łatwe zaznaczanie obecności uczestników, śledzenie statystyk frekwencji oraz analizę postępów grup.

## 🎯 Główne funkcjonalności

### 1. **Kalendarz Tygodniowy**
- **Wybór daty** - interaktywny kalendarz z nawigacją tygodniową
- **Wizualne oznaczenia** - wyróżnienie dzisiejszej daty i wybranej daty
- **Informacje o dacie** - wyświetlanie miesiąca, roku i aktualnej daty
- **Szybki dostęp** - przycisk "Przejdź do dziś" dla szybkiej nawigacji

### 2. **Wybór Grupy**
- **Karty grup** - wizualny wybór grupy w stylu kalendarza
- **Status grup** - kolorowe oznaczenia statusu (aktywna, pełna, nieaktywna)
- **Informacje o grupie** - nazwa, godzina, liczba wolnych miejsc
- **Automatyczny wybór** - domyślny wybór grupy na podstawie dnia tygodnia

### 3. **Zarządzanie Obecnością**
- **Przełączniki obecności** - nowoczesne przełączniki Filament w kolorze pomarańczowym
- **Zbiorowe operacje** - zaznaczanie/odznaczanie wszystkich uczestników
- **Odwracanie zaznaczenia** - szybka zmiana stanu wszystkich uczestników
- **Notatki** - możliwość dodawania notatek do każdej obecności

### 4. **Statystyki Frekwencji**
- **Karty statystyk** - kolorowe karty z liczbami obecnych/nieobecnych
- **Pasek postępu** - dynamiczny pasek z kolorami zależnymi od frekwencji
- **Procenty frekwencji** - dokładne obliczenia w czasie rzeczywistym
- **Aktualizacja na żywo** - automatyczne odświeżanie po zmianach

## 🔧 Architektura Techniczna

### **Struktura Plików**
```
app/Filament/Admin/Pages/
├── AttendanceGroupPage.php          # Główna logika strony
└── AttendanceGroupPageTest.php      # Testy jednostkowe

resources/views/filament/admin/pages/
└── attendance-group-page.blade.php  # Widok strony

app/Models/
├── Attendance.php                   # Model obecności
├── Group.php                        # Model grup
└── User.php                         # Model użytkowników
```

### **Kluczowe Metody PHP**

#### `AttendanceGroupPage.php`
```php
// Ładowanie użytkowników grupy
public function loadUsers()

// Obliczanie statystyk obecności
public function getAttendanceStats()

// Przełączanie obecności pojedynczego użytkownika
public function toggleAttendance($userId)

// Zbiorowe operacje na obecności
public function selectAll()
public function deselectAll()
public function toggleAll()

// Wybór daty i grupy
public function selectDate($date)
public function selectGroup($groupId)
public function selectWeek($weekStart)
```

## 📊 Logika Obliczania Statystyk

### **Progi Kolorów Paska Postępu**
- **≥75%** → 🟢 Zielony (`#22c55e`) - Doskonała frekwencja
- **≥60%** → 🟡 Żółty (`#eab308`) - Dobra frekwencja  
- **≥30%** → 🟠 Pomarańczowy (`#f59e0b`) - Średnia frekwencja
- **<30%** → 🔴 Czerwony (`#ef4444`) - Niska frekwencja

### **Wzór Obliczania**
```php
$total = count($this->attendances);
$present = collect($this->attendances)->where('present', true)->count();
$absent = $total - $present;
$percentage = $total > 0 ? round(($present / $total) * 100, 1) : 0;
```

## 🎨 Interfejs Użytkownika

### **Layout Responsywny**
- **Desktop** - Pełny kalendarz z kartami grup i tabelą użytkowników
- **Mobile** - Zoptymalizowane karty z przełącznikami obecności

### **Komponenty Wizualne**
- **Kalendarz tygodniowy** - 7 kafelków z dniami tygodnia
- **Karty grup** - Poziome przewijanie z 7 grupami na linię
- **Przełączniki obecności** - Pomarańczowe przełączniki Filament
- **Karty statystyk** - Kolorowe karty z ikonami i liczbami

### **Kolory i Stylowanie**
```css
/* Przełączniki obecności */
background-color: #ea580c;  /* Pomarańczowy Filament */

/* Karty statystyk */
Obecni: #22c55e (zielony)
Nieobecni: #ef4444 (czerwony)  
Frekwencja: #3b82f6 (niebieski)

/* Pasek postępu */
Dynamiczne kolory na podstawie procentów
```

## 🔄 Przepływ Danych

### **1. Inicjalizacja Strony**
```
1. Ładowanie domyślnej daty (dziś)
2. Automatyczny wybór grupy na podstawie dnia tygodnia
3. Ładowanie użytkowników grupy
4. Obliczenie statystyk obecności
5. Renderowanie interfejsu
```

### **2. Zmiana Daty/Grupy**
```
1. Użytkownik wybiera nową datę/grupę
2. Odświeżenie listy użytkowników
3. Ładowanie istniejących obecności
4. Aktualizacja statystyk
5. Re-renderowanie interfejsu
```

### **3. Zmiana Obecności**
```
1. Użytkownik klika przełącznik
2. Aktualizacja danych w $this->attendances
3. Przeliczenie statystyk
4. Aktualizacja paska postępu
5. Zapisywanie do bazy danych
```

## 💾 Baza Danych

### **Tabela `attendances`**
```sql
CREATE TABLE attendances (
    id BIGINT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    group_id BIGINT NOT NULL,
    date DATE NOT NULL,
    present BOOLEAN DEFAULT FALSE,
    note TEXT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### **Relacje**
- `Attendance` belongsTo `User`
- `Attendance` belongsTo `Group`
- `User` hasMany `Attendance`
- `Group` hasMany `Attendance`

## 🚀 Funkcje Zaawansowane

### **Automatyczny Wybór Grupy**
- **Priorytet 1**: Grupa pasująca do dnia tygodnia (np. "Poniedziałek 18:00")
- **Priorytet 2**: Pierwsza grupa z poniedziałku (jeśli brak grupy na dany dzień)
- **Priorytet 3**: Pierwsza aktywna grupa w systemie

### **Nawigacja Tygodniowa**
- **Poprzedni tydzień** - Przewijanie wstecz
- **Ten tydzień** - Powrót do aktualnego tygodnia
- **Następny tydzień** - Przewijanie w przód

### **Zbiorowe Operacje**
- **Zaznacz wszystkich** - Wszyscy jako obecni
- **Odznacz wszystkich** - Wszyscy jako nieobecni
- **Odwróć zaznaczenie** - Zmiana stanu wszystkich

## 🔧 Konfiguracja

### **Zmienne Środowiskowe**
```env
# Brak specjalnych zmiennych - używa standardowej konfiguracji Laravel
```

### **Zależności**
- Laravel 10+
- Filament 3.x
- Livewire 3.x
- Carbon (dla dat)
- Tailwind CSS (dla stylów)

## 📱 Responsywność

### **Breakpointy**
- **Mobile** (`< 768px`) - Karty użytkowników, poziome przewijanie grup
- **Desktop** (`≥ 768px`) - Tabela użytkowników, pełny kalendarz

### **Optymalizacje Mobile**
- Zwiększone obszary kliknięcia
- Uproszczone układy
- Poziome przewijanie dla grup

## 🧪 Testowanie

### **Testy Jednostkowe**
```php
// AttendanceGroupPageTest.php
public function test_can_load_users_for_group()
public function test_can_toggle_attendance()
public function test_can_calculate_stats()
public function test_auto_group_selection()
```

### **Testy Funkcjonalne**
- Wybór daty i grupy
- Przełączanie obecności
- Zbiorowe operacje
- Obliczanie statystyk
- Responsywność interfejsu

## 🐛 Znane Problemy i Rozwiązania

### **Problem: Pasek postępu nie aktualizuje kolorów**
**Rozwiązanie**: Użycie Blade `@if` zamiast JavaScript dla dynamicznych kolorów

### **Problem: Automatyczny wybór grupy nie działa**
**Rozwiązanie**: Sprawdzenie statusu grupy (`active`, `full`) w zapytaniach

### **Problem: Statystyki pokazują nieprawidłowe wartości**
**Rozwiązanie**: Synchronizacja między `$users` a `$this->attendances`

## 🔮 Planowane Usprawnienia

### **Krótkoterminowe**
- [ ] Eksport statystyk do PDF/Excel
- [ ] Powiadomienia o niskiej frekwencji
- [ ] Historia obecności użytkownika

### **Długoterminowe**
- [ ] Integracja z systemem płatności
- [ ] Automatyczne przypomnienia
- [ ] Dashboard z analityką frekwencji

## 📞 Wsparcie

W przypadku problemów z modułem obecności:
1. Sprawdź logi Laravel (`storage/logs/laravel.log`)
2. Zweryfikuj konfigurację bazy danych
3. Sprawdź uprawnienia użytkowników
4. Skontaktuj się z zespołem deweloperskim

---

**Ostatnia aktualizacja**: {{ date('Y-m-d') }}  
**Wersja**: 1.0.0  
**Autor**: Zespół Deweloperski
