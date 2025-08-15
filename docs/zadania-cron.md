## 🔄 Krok 1: Dodaj komendę do crona (raz w miesiącu)

Aby co miesiąc automatycznie generować płatności dla aktywnych użytkowników, dodaj następujący wpis do crontaba:

```
0 1 1 * * cd /ścieżka/do/projektu && php artisan payments:generate >> /dev/null 2>&1
```

**Opis:**

- `0 1 1 * *` – uruchamia zadanie o 01:00 w nocy, pierwszego dnia każdego miesiąca.
- `cd /ścieżka/do/projektu` – przechodzi do katalogu głównego Twojego projektu Laravel.
- `php artisan payments:generate` – wykonuje komendę Artisan odpowiedzialną za generowanie płatności.
- `>> /dev/null 2>&1` – przekierowuje zarówno standardowe wyjście, jak i błędy do `/dev/null`, aby zapobiec wysyłaniu e-maili z wynikami zadania cron.

**Uwaga:** Upewnij się, że ścieżka `/ścieżka/do/projektu` jest poprawna i wskazuje na katalog główny Twojego projektu Laravel.

---

Jeśli chcesz, aby płatności były generowane codziennie (np. w przypadku dodania nowych użytkowników w ciągu miesiąca), możesz zmienić harmonogram na:

```
0 1 * * * cd /ścieżka/do/projektu && php artisan payments:generate >> /dev/null 2>&1
```

Ten wpis uruchomi zadanie codziennie o 01:00 w nocy.

---

**Jak dodać zadanie cron:**

1. Otwórz terminal na serwerze.
2. Wpisz komendę:

   ```
   crontab -e
   ```

3. W edytorze, który się otworzy, dodaj odpowiedni wpis cron na końcu pliku.
4. Zapisz i zamknij edytor.

Po zapisaniu, cron automatycznie zacznie wykonywać zadanie zgodnie z ustalonym harmonogramem.