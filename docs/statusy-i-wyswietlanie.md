# Statusy i sposób wyświetlania w systemie

## Grupy (Group)
- Statusy: `active`, `inactive`, `full`
- Wyświetlanie: kolumna `status` jako badge
  - Kolory:
    - `active` → success (etykieta: „Aktywna”)
    - `inactive` → danger (etykieta: „Nieaktywna”)
    - `full` → warning (etykieta: „Pełna”)
- Pojemność: kolumna `users_count` z opisem `X/Y`
  - Kolory: `>= max_size` → danger; `>= 80% max_size` → warning; inaczej → success
- Filtry: Select „Status”

## Płatności (Payment)
- Status: `paid` (bool)
- Wyświetlanie:
  - Lista główna: `IconColumn`/boolean (status „Opłacone”)
  - Zakładka płatności w Grupie: `IconColumn`
    - `trueIcon='heroicon-o-check-circle'` (zielony), `falseIcon='heroicon-o-x-circle'` (czerwony)
  - Zakładka płatności Użytkownika: `BooleanColumn`
    - `trueIcon='heroicon-o-shield-check'`, `falseIcon='heroicon-o-currency-dollar'`
- Filtry: Select/Ternary „Status płatności”
- Akcje masowe: „Oznacz jako opłacone/nieopłacone” (przy opłaceniu czyści `payment_link`)

## Obecności (Attendance)
- Status: `present` (bool)
- Wyświetlanie: kolumna boolean/icon „Obecny?” (Admin i Panel Użytkownika)
- Filtry: Ternary/Select „Obecność”
- Akcje:
  - Wiersz: „Oznacz jako obecny/nieobecny”
  - Masowe: „Oznacz jako obecny/nieobecny” (potwierdzenia, notyfikacje)

## Użytkownicy (User)
- Status: `is_active` (bool)
  - Wyświetlanie: `BooleanColumn` „Aktywny”
  - Filtr: Ternary „Status”
- Regulamin: `terms_accepted_at` (pochodny bool)
  - Ikony: `trueIcon='heroicon-o-document-check'`, `falseIcon='heroicon-o-x-circle'`
  - Kolory: true → success; false → danger

## Logi resetów haseł (PasswordResetLog)
- Statusy: `pending`, `completed`, `expired`
- Wyświetlanie: `BadgeColumn status`
  - Kolory: `completed` → success; `pending` → warning; `expired` → danger
- Pole „Wygasa”: `token_expires_at`
  - Kolor daty: przeszła → danger; ≤24h → warning; inaczej → success
- Akcje:
  - Wiersz: „Wyślij ponownie zaproszenie”, „Resetuj ponownie hasło”, „Oznacz jako zakończony/wygasły”
  - Masowe: „Oznacz jako zakończone/wygasłe”
- Filtry: „Status”, „Typ resetowania” (`single`/`bulk`), „Wygasa w 24h”, „Wygasłe”

## Badge w nawigacji (skrót)
- Użytkownicy: badge = liczba aktywnych nie-adminów
  - Kolor badge: `success`
  - Kolor linku nawigacji: `info`, gdy są nowi użytkownicy w ciągu 7 dni
- Płatności: badge = liczba nieopłaconych
  - Kolor badge i linku nawigacji: `danger`, gdy istnieją nieopłacone
- Obecności: kolor linku nawigacji `success`, gdy dziś są obecności

---
Dokument odzwierciedla aktualny stan kodu (ostatnia aktualizacja: 2025-08-29).
