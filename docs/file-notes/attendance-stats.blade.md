# Notatki do pliku: resources/views/filament/user-panel/widgets/attendance-stats.blade.php

**Data wygenerowania listy:** 2025-07-17 11:37:26

## Najważniejsze zmiany i funkcje

1. **Nowy widok widgetu statystyk obecności:**
   - Wyświetla trzy karty: Obecności, Nieobecności, Frekwencja.
   - Każda karta w innym kolorze (zielony, czerwony, niebieski) – kolory wymuszone stylem inline.

2. **Responsywny układ:**
   - Na desktopie: karty w jednym rzędzie.
   - Na mobile: karty jedna pod drugą.

3. **Wyśrodkowanie danych:**
   - Dane w kartach są wyśrodkowane zarówno poziomo, jak i pionowo (`justify-center`, `items-center`, `text-center`, `py-10`).

4. **Estetyka:**
   - Zaokrąglone rogi, cień, padding.
   - Duże liczby, czytelne tytuły.

5. **Dane dynamiczne:**
   - Liczby pobierane z kontrolera widgetu na podstawie obecności zalogowanego użytkownika. 