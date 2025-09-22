# Funkcja: Wyślij w Messengerze - Pre-rejestracje

## Opis
Dodano funkcjonalność udostępniania linków pre-rejestracji przez Messenger (Facebook) w zakładce Pre-rejestracje.

## Funkcjonalności

### 1. Pojedyncze udostępnianie
- **Lokalizacja:** Akcje w tabeli Pre-rejestracji → "Wyślij w Messengerze"
- **Ikona:** `heroicon-o-chat-bubble-left-ellipsis`
- **Kolor:** Primary (niebieski)

### 2. Masowe udostępnianie
- **Lokalizacja:** Akcje masowe → "Wyślij w Messengerze (masowo)"
- **Ikona:** `heroicon-o-chat-bubble-left-ellipsis`
- **Kolor:** Primary (niebieski)

## Opcje udostępniania

### 1. Aplikacja Messenger
- **URL:** `fb-messenger://share?link={encodedUrl}`
- **Działanie:** Próbuje otworzyć aplikację Messenger na urządzeniu mobilnym
- **Fallback:** Po 800ms automatycznie przełącza na web Messenger

### 2. Web Messenger
- **URL:** `https://www.facebook.com/dialog/send?link={encodedUrl}&app_id={APP_ID}&redirect_uri={APP_URL}`
- **Działanie:** Otwiera Messenger w przeglądarce
- **Wymagania:** `META_APP_ID` i `APP_URL` w konfiguracji

### 3. Chat strony (opcjonalny)
- **URL:** `https://m.me/{PAGE_USERNAME}?ref={context}`
- **Działanie:** Rozpoczyna czat z stroną na Facebooku
- **Wymagania:** `META_PAGE_USERNAME` w konfiguracji

## Konfiguracja

### Zmienne środowiskowe (.env)
```env
# Meta/Facebook Configuration
META_APP_ID=your_facebook_app_id
META_PAGE_USERNAME=your_page_username
```

### Konfiguracja w services.php
```php
'meta' => [
    'app_id' => env('META_APP_ID'),
    'page_username' => env('META_PAGE_USERNAME'),
],
```

## Pliki

### 1. PreRegistrationResource.php
- **Lokalizacja:** `app/Filament/Admin/Resources/PreRegistrationResource.php`
- **Zmiany:** Dodano akcje `send_messenger` i `send_messenger_bulk`

### 2. Widoki modali
- **Pojedyncze:** `resources/views/filament/admin/resources/pre-registration-resource/modals/send-messenger.blade.php`
- **Masowe:** `resources/views/filament/admin/resources/pre-registration-resource/modals/send-messenger-bulk.blade.php`

### 3. Konfiguracja
- **Services:** `config/services.php` - dodano sekcję `meta`

## Funkcjonalności JavaScript

### 1. openMessengerApp()
- Próbuje otworzyć aplikację Messenger
- Fallback do web po 800ms

### 2. openWebMessenger()
- Otwiera web Messenger z parametrami
- Obsługuje brak konfiguracji APP_ID

### 3. openPageChat()
- Otwiera czat ze stroną (jeśli skonfigurowany)

### 4. copyAllLinks() (masowe)
- Kopiuje wszystkie linki do schowka
- Obsługuje fallback dla starszych przeglądarek

## Wymagania

### 1. Facebook App ID
- Wymagany dla web Messenger
- Opcjonalny dla aplikacji mobilnej

### 2. Page Username
- Opcjonalny dla czatu ze stroną
- Format: `your_page_username` (bez @)

### 3. APP_URL
- Używany jako redirect_uri dla web Messenger
- Pobierany z `config('app.url')`

## Bezpieczeństwo

### 1. Walidacja URL
- Wszystkie URL są kodowane przez `urlencode()`
- Sprawdzanie czy link jest ważny (`$record->isValid()`)

### 2. Fallback
- Graceful degradation przy braku konfiguracji
- Obsługa błędów JavaScript

## Testowanie

### 1. Test aplikacji
1. Skonfiguruj `META_APP_ID` i `META_PAGE_USERNAME`
2. Otwórz zakładkę Pre-rejestracje
3. Kliknij "Wyślij w Messengerze" na dowolnej pre-rejestracji
4. Przetestuj wszystkie opcje udostępniania

### 2. Test masowy
1. Zaznacz kilka pre-rejestracji
2. Kliknij "Wyślij w Messengerze (masowo)"
3. Przetestuj wszystkie opcje

### 3. Test bez konfiguracji
1. Usuń zmienne META_* z .env
2. Sprawdź czy funkcjonalność działa z fallback

## Uwagi

### 1. Kompatybilność
- Działa na urządzeniach mobilnych i desktop
- Obsługuje wszystkie nowoczesne przeglądarki

### 2. UX
- Intuicyjny interfejs z ikonami
- Jasne instrukcje dla użytkownika
- Feedback wizualny przy kopiowaniu

### 3. Wydajność
- Minimalne obciążenie serwera
- JavaScript wykonuje się po stronie klienta
- Brak dodatkowych zapytań do API
