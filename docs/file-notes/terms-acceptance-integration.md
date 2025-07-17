# Notatki do integracji akceptacji regulaminu z modelem Term

**Data wygenerowania listy:** 2025-07-17 20:32:14

## Najważniejsze zadania i zmiany

1. **Migracja bazy danych:**
   - Dodano kolumnę `terms_accepted_at` do tabeli `users` (jednorazowa akceptacja regulaminu przez użytkownika).

2. **Middleware wymuszający akceptację regulaminu:**
   - Użytkownik nie ma dostępu do panelu, dopóki nie zaakceptuje regulaminu.
   - Po akceptacji uzyskuje pełny dostęp.

3. **Strona akceptacji regulaminu:**
   - Utworzono stronę Filament (`TermsAcceptancePage`) widoczną tylko, gdy użytkownik nie zaakceptował regulaminu.
   - Po akceptacji zapisuje się data i następuje przekierowanie do dashboardu.

4. **Integracja z modelem Term (panel admina):**
   - Treść regulaminu pobierana jest dynamicznie z modelu Term (wszystkie aktywne regulaminy).
   - Administrator może zarządzać regulaminami w panelu admina (TermResource).
   - Użytkownik widzi zawsze aktualne wersje regulaminu do akceptacji.

5. **Wygląd i UX:**
   - Strona akceptacji regulaminu jest szeroka, responsywna, z dużym paddingiem i czytelną prezentacją treści.
   - Każdy regulamin wyświetlany z nazwą i treścią, w kolejności od najstarszego do najnowszego.
   - Przycisk „Akceptuję regulamin” na dole strony.

6. **Bezpieczeństwo:**
   - Użytkownik nie może cofnąć ani edytować akceptacji regulaminu samodzielnie.
   - Tylko administrator może zmieniać status akceptacji lub treść regulaminu. 