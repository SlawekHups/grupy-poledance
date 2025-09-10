# Instrukcja użytkowania systemu pre-rejestracji

## Opis systemu

System pre-rejestracji pozwala administratorom na generowanie krótkoterminowych linków (30 minut) dla potencjalnych użytkowników. Osoby te mogą wypełnić podstawowe dane (imię, telefon, email), które są zapisywane w systemie. Następnie administrator może skonwertować te pre-rejestracje na pełnych użytkowników i wysłać im standardowe zaproszenia do grup.

## Dostęp do systemu

1. Zaloguj się do panelu administratora
2. Przejdź do sekcji **"Pre-rejestracje"** w menu głównym
3. Zostaniesz przekierowany do listy wszystkich pre-rejestracji

## Generowanie linków pre-rejestracji

### Pojedynczy link

1. W sekcji pre-rejestracji kliknij przycisk **"Generuj link"** (ikona plus)
2. Wypełnij formularz:
   - **Imię i nazwisko**: (opcjonalne - można zostawić puste)
   - **Email**: (opcjonalne - można zostawić puste)
   - **Telefon**: (opcjonalne - można zostawić puste)
3. Kliknij **"Generuj"**
4. Link zostanie utworzony i wyświetli się w tabeli

### Wiele linków naraz

1. Kliknij przycisk **"Generuj linki"** w górnej części strony
2. Wprowadź liczbę linków do wygenerowania (zalecane 7-10)
3. Kliknij **"Generuj"**
4. Wszystkie linki zostaną utworzone i wyświetlą się w tabeli

## Kopiowanie linków

### Pojedynczy link

1. W tabeli pre-rejestracji znajdź interesujący Cię link
2. Kliknij przycisk **"Kopiuj link"** (ikona schowka) w kolumnie akcji
3. W otwartym oknie modalnym:
   - Link będzie widoczny w polu tekstowym
   - Kliknij przycisk **"📋 Kopiuj"** (szary przycisk)
   - Przycisk zmieni się na zielony z "✅ Skopiowano!" na 2 sekundy
   - Link zostanie skopiowany do schowka

### Wszystkie ważne linki

1. Kliknij przycisk **"Kopiuj wszystkie linki"** w górnej części strony
2. W otwartym oknie modalnym:
   - Zobaczysz listę wszystkich ważnych linków
   - Kliknij przycisk **"📋 Kopiuj wszystkie"** (szary przycisk)
   - Przycisk zmieni się na zielony z "✅ Skopiowano!" na 2 sekundy
   - Wszystkie linki zostaną skopiowane do schowka (jeden pod drugim)

## Wysyłanie linków

Skopiowane linki możesz wysłać przez:
- **Wiadomości SMS**
- **Messenger** (Facebook, WhatsApp)
- **Email**
- **Inne komunikatory**

### Przykład wiadomości:

```
Cześć! 👋

Zapraszam Cię do zapisania się na zajęcia pole dance! 

Kliknij w link poniżej i wypełnij podstawowe dane:
[WSTAW SKOPIOWANY LINK]

Link jest ważny przez 30 minut.

Pozdrawiam!
```

## Zarządzanie pre-rejestracjami

### Statusy pre-rejestracji

- **Ważna** (zielona ikona): Link jest aktywny i można z niego korzystać
- **Użyta** (pomarańczowa ikona): Link został już wykorzystany
- **Wygasła** (czerwona ikona): Link wygasł (po 30 minutach)

### Filtrowanie

Możesz filtrować pre-rejestracje według:
- **Statusu** (wszystkie, ważne, użyte, wygasłe)
- **Daty utworzenia**
- **Danych użytkownika** (imię, email, telefon)

### Edycja pre-rejestracji

1. Kliknij przycisk **"Edytuj"** (ikona ołówka) w kolumnie akcji
2. Zmodyfikuj dane w formularzu
3. Kliknij **"Zapisz"**

### Usuwanie pre-rejestracji

1. Kliknij przycisk **"Usuń"** (ikona kosza) w kolumnie akcji
2. Potwierdź usunięcie w oknie dialogowym

## Konwersja na pełnego użytkownika

### Krok 1: Wybór pre-rejestracji

1. Znajdź pre-rejestrację, którą chcesz skonwertować
2. Kliknij przycisk **"Konwertuj na użytkownika"** (ikona użytkownika)

### Krok 2: Przypisanie grup

1. W formularzu konwersji wybierz grupy dla nowego użytkownika
2. Możesz przypisać użytkownika do wielu grup jednocześnie
3. Kliknij **"Konwertuj"**

### Krok 3: Automatyczne zaproszenie

- System automatycznie utworzy pełne konto użytkownika
- Wyśle standardowe zaproszenie email z linkiem do rejestracji
- Użytkownik będzie mógł uzupełnić pozostałe dane i ustawić hasło

## Monitorowanie i statystyki

### Widget statystyk

Na stronie pre-rejestracji widzisz widget z:
- **Łączna liczba** pre-rejestracji
- **Ważne linki** (aktywne)
- **Użyte linki** (wykorzystane)
- **Wygasłe linki** (nieaktywne)

### Historia działań

Wszystkie operacje są logowane:
- Generowanie linków
- Konwersje na użytkowników
- Edycje i usunięcia

## Najlepsze praktyki

### 1. Planowanie linków
- Generuj 7-10 linków na raz
- Używaj ich w ciągu 30 minut
- Generuj nowe linki gdy poprzednie wygasną

### 2. Komunikacja z klientami
- Wyjaśnij, że link jest ważny tylko 30 minut
- Poproś o szybkie wypełnienie formularza
- Bądź gotowy do konwersji zaraz po wypełnieniu

### 3. Organizacja pracy
- Regularnie sprawdzaj nowe pre-rejestracje
- Konwertuj je na użytkowników w ciągu 24 godzin
- Usuwaj wygasłe linki, aby zachować porządek

## Rozwiązywanie problemów

### Link nie działa
- Sprawdź czy nie minęło 30 minut
- Sprawdź status w tabeli pre-rejestracji
- Wygeneruj nowy link

### Nie można skopiować linku
- Upewnij się, że przeglądarka obsługuje kopiowanie do schowka
- Spróbuj skopiować link ręcznie z pola tekstowego
- Sprawdź czy nie ma blokady JavaScript

### Brak emaila po konwersji
- Sprawdź logi systemu
- Upewnij się, że email użytkownika jest poprawny
- Sprawdź konfigurację poczty w systemie

## Wsparcie techniczne

W przypadku problemów:
1. Sprawdź logi systemu w sekcji "Logi"
2. Skontaktuj się z administratorem systemu
3. Opisz dokładnie problem i kroki, które do niego doprowadziły

---

**Ostatnia aktualizacja**: {{ date('d.m.Y H:i') }}
**Wersja systemu**: 1.0
