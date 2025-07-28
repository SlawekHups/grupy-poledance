# System Zarządzania Zajęciami - Plan Implementacji

## 1. Model Danych

### 1.1 Nowe Modele
- [ ] `Lesson` (Zajęcia)
  ```php
  - id
  - group_id (relacja z Group)
  - title (tytuł zajęć)
  - description (opis/plan zajęć)
  - date (data zajęć)
  - created_at
  - updated_at
  - published_at (możliwość zapisania jako szkic)
  - status (draft/published)
  - created_by (relacja z User - admin)
  ```

- [ ] `LessonAttachment` (Załączniki do zajęć)
  ```php
  - id
  - lesson_id (relacja z Lesson)
  - file_path
  - file_name
  - file_type
  - file_size
  - created_at
  ```

- [ ] `LessonTemplate` (Szablony zajęć)
  ```php
  - id
  - title
  - description
  - created_by
  - created_at
  - updated_at
  ```

## 2. Funkcjonalności Administracyjne

### 2.1 Zarządzanie Zajęciami w Grupie
- [ ] Dodawanie nowych zajęć dla grupy
- [ ] Edycja istniejących zajęć
- [ ] Usuwanie zajęć
- [ ] Publikowanie/wycofywanie publikacji
- [ ] Kopiowanie zajęć do innych grup
- [ ] System szablonów zajęć
- [ ] Załączanie plików (zdjęcia, PDF, etc.)
- [ ] Podgląd historii zajęć grupy

### 2.2 Centralna Zarządzanie Zajęciami
- [ ] Lista wszystkich zajęć z filtrowaniem po grupach
- [ ] Masowe dodawanie zajęć dla wielu grup
- [ ] Kalendarz zajęć
- [ ] System tagów dla łatwiejszego organizowania
- [ ] Wyszukiwarka zajęć
- [ ] Sortowanie i filtrowanie

### 2.3 Szablony i Automatyzacja
- [ ] Tworzenie szablonów zajęć
- [ ] Zapisywanie często używanych opisów
- [ ] Szybkie kopiowanie zajęć między grupami
- [ ] Automatyczne powiadomienia o nowych zajęciach

## 3. Interface Użytkownika

### 3.1 Panel Administratora - Widok Grupy
- [ ] Nowa zakładka "Zajęcia" w szczegółach grupy
- [ ] Lista zajęć grupy z sortowaniem
- [ ] Formularz dodawania/edycji zajęć
- [ ] Podgląd załączników
- [ ] Historia zmian
- [ ] Statystyki zajęć

### 3.2 Panel Administratora - Centralne Zarządzanie
- [ ] Nowa sekcja "Zajęcia" w głównym menu
- [ ] Dashboard z przeglądem zajęć
- [ ] Kalendarz wszystkich zajęć
- [ ] Masowy edytor zajęć
- [ ] System szablonów
- [ ] Statystyki i raporty

### 3.3 Widgety i Komponenty
- [ ] Widget nadchodzących zajęć
- [ ] Kalendarz zajęć
- [ ] Szybkie akcje
- [ ] Powiadomienia o zmianach

## 4. Implementacja Techniczna

### 4.1 Backend
- [ ] Migracje bazy danych dla nowych modeli
- [ ] Kontrolery Filament dla zasobów
- [ ] Relacje Eloquent
- [ ] Polityki dostępu
- [ ] Walidacja danych
- [ ] System cache'owania

### 4.2 Frontend
- [ ] Formularze Filament
- [ ] Komponenty Livewire
- [ ] Edytor WYSIWYG dla opisów
- [ ] Upload plików
- [ ] Interaktywny kalendarz
- [ ] Dynamiczne filtry

## 5. Dodatkowe Funkcje

### 5.1 Powiadomienia
- [ ] Powiadomienia email o nowych zajęciach
- [ ] Powiadomienia w aplikacji
- [ ] Przypomnienia o nadchodzących zajęciach
- [ ] System subskrypcji powiadomień

### 5.2 Eksport/Import
- [ ] Eksport zajęć do PDF
- [ ] Eksport do kalendarza (iCal)
- [ ] Import z szablonów
- [ ] Masowy import z Excel

### 5.3 Integracje
- [ ] Integracja z kalendarzem Google
- [ ] Integracja z systemem obecności
- [ ] Możliwość udostępniania zajęć

## 6. Proponowany Interfejs

### 6.1 Widok Grupy
```
[Grupa XYZ]
├── Informacje
├── Użytkownicy
├── Obecności
└── Zajęcia
    ├── Lista zajęć
    │   ├── Filtrowanie po dacie
    │   ├── Wyszukiwanie
    │   └── Sortowanie
    ├── Kalendarz
    └── Szablony
```

### 6.2 Centralne Zarządzanie
```
[Zajęcia]
├── Dashboard
├── Wszystkie zajęcia
├── Kalendarz
├── Szablony
└── Ustawienia
```

## 7. Kolejność Implementacji

1. Podstawowa struktura
   - Migracje bazy danych
   - Modele i relacje
   - Podstawowe kontrolery

2. Zarządzanie w grupach
   - Lista zajęć grupy
   - Dodawanie/edycja zajęć
   - Podstawowe filtrowanie

3. Centralne zarządzanie
   - Lista wszystkich zajęć
   - Filtrowanie po grupach
   - Masowe operacje

4. Zaawansowane funkcje
   - System szablonów
   - Załączniki
   - Kalendarz
   - Powiadomienia

5. Optymalizacje
   - Cache
   - Wydajność
   - UX usprawnienia

## 8. Sugestie Dodatkowe

1. **System Kategorii Zajęć**
   - Podział na typy zajęć
   - Kolorowanie według kategorii
   - Filtrowanie po kategoriach

2. **System Poziomów Trudności**
   - Oznaczanie poziomu trudności zajęć
   - Filtrowanie po poziomie
   - Statystyki postępu grupy

3. **Notatki Prywatne**
   - Możliwość dodawania prywatnych notatek do zajęć
   - Widoczne tylko dla administratorów
   - Historia zmian

4. **System Celów**
   - Definiowanie celów dla grupy
   - Śledzenie postępu
   - Raporty osiągnięć

5. **Feedback od Uczestników**
   - Możliwość zbierania opinii
   - Ankiety po zajęciach
   - Statystyki zadowolenia

## 9. Bezpieczeństwo

1. **Kontrola Dostępu**
   - Role i uprawnienia
   - Logowanie akcji
   - Zabezpieczenie załączników

2. **Backup Danych**
   - Automatyczne kopie zapasowe
   - Historia zmian
   - System przywracania

## 10. Wsparcie Mobilne

1. **Responsywny Design**
   - Dostosowanie do urządzeń mobilnych
   - Szybki dostęp do kluczowych funkcji
   - Optymalizacja obrazów

2. **Offline Access**
   - Podstawowa funkcjonalność offline
   - Synchronizacja po połączeniu
   - Cache kluczowych danych 