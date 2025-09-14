# 📁 Instrukcja Obsługi Systemu Plików - Dla Użytkowników

## 🎯 Wprowadzenie

Ten przewodnik pomoże Ci efektywnie korzystać z systemu zarządzania plikami w panelu administratora. System pozwala na bezpieczne przechowywanie, organizację i udostępnianie plików.

## 🚀 Szybki Start

### 1. Dostęp do Systemu
1. Zaloguj się do panelu administratora
2. W menu bocznym kliknij **"Pliki"**
3. Zobaczysz tabelę ze wszystkimi plikami

### 2. Dodawanie Pierwszego Pliku
1. Kliknij **"Utwórz plik"** (zielony przycisk)
2. Wybierz plik z komputera
3. System automatycznie wypełni nazwę
4. Kliknij **"Utwórz"**

## 📤 Dodawanie Plików

### Krok po Kroku

#### 1. Wybierz Plik
- **Kliknij** "Wybierz plik" lub przeciągnij plik do okna
- **Maksymalny rozmiar**: 10MB
- **Obsługiwane formaty**: obrazy, dokumenty, archiwa, skrypty

#### 2. Sprawdź Automatyczne Dane
System automatycznie wypełni:
- ✅ **Oryginalna nazwa** - nazwa pliku z komputera
- ✅ **Nazwa pliku** - bez rozszerzenia
- ✅ **Rozmiar** - w KB/MB
- ✅ **Typ MIME** - automatycznie wykryty

#### 3. Ustaw Opcjonalne Dane
- **Kategoria**: Wybierz odpowiednią (domyślnie "Ogólne")
- **Opis**: Dodaj opis pliku (maksymalnie 1000 znaków)
- **Plik publiczny**: Zaznacz jeśli chcesz udostępnić przez link

#### 4. Zapisz
- Kliknij **"Utwórz"**
- System przekieruje Cię do tabeli plików

### 💡 Wskazówki
- **Nazwa pliku**: Zostaw puste, aby użyć oryginalnej nazwy
- **Kategoria**: Pomaga w organizacji plików
- **Opis**: Przydatny dla innych użytkowników

## 📋 Przeglądanie Plików

### Tabela Plików

```
┌─────┬─────────────────┬─────────┬───────────────┬─────────┬─────────┐
│ Ikona│ Nazwa pliku     │ Rozmiar │ Oryginalna    │ Kategoria│ Publiczny│
├─────┼─────────────────┼─────────┼───────────────┼─────────┼─────────┤
│ 🖼️  │ moje-zdjecie    │ 2.1 MB  │ IMG_001.jpg   │ Obrazy  │ ✅      │
│ 📄  │ raport-pdf      │ 856 KB  │ report.pdf    │ Dokumenty│ ❌     │
│ 📦  │ backup-zip      │ 15.2 MB │ backup.zip    │ Archiwa │ ❌     │
└─────┴─────────────────┴─────────┴───────────────┴─────────┴─────────┘
```

### Ikony Plików
- 🖼️ **Obrazy** - PNG, JPEG, GIF, WebP
- 📄 **Dokumenty** - PDF, Word, Excel, PowerPoint
- 📝 **Tekst** - TXT, CSV, Markdown
- 📦 **Archiwa** - ZIP, RAR
- ⚙️ **Skrypty** - Shell scripts
- 🔧 **Inne** - Style files, konfiguracje

### Filtrowanie i Szukanie
- **Szukaj** po nazwie pliku lub oryginalnej nazwie
- **Filtruj** po kategorii
- **Sortuj** po rozmiarze, dacie, nazwie
- **Kliknij** na nagłówki kolumn aby zmienić sortowanie

## ✏️ Edycja Plików

### Modyfikacja Metadanych

#### 1. Otwórz Edycję
- Kliknij **"Edytuj"** w tabeli plików
- Zobaczysz formularz z aktualnymi danymi

#### 2. Zmień Dane
- **Nazwa pliku**: Edytuj nazwę wyświetlaną
- **Kategoria**: Zmień kategorię
- **Opis**: Dodaj lub zmień opis
- **Plik publiczny**: Włącz/wyłącz dostęp publiczny

#### 3. Zapisz Zmiany
- Kliknij **"Zapisz"**
- System przekieruje Cię do tabeli

### Zamiana Pliku

#### ⚠️ Ważne: Zamiana Pliku
Jeśli chcesz zastąpić plik nowym:

1. **Wybierz nowy plik** w sekcji "Plik"
2. **Pojawi się alert**: "Czy na pewno chcesz zastąpić obecny plik?"
3. **Kliknij "OK"** aby potwierdzić
4. **Stary plik zostanie usunięty** automatycznie
5. **Nowy plik zastąpi** stary z aktualizacją metadanych

#### 💡 Wskazówki Zamiany
- **Potwierdź zamianę** - operacja jest nieodwracalna
- **Sprawdź nazwę** - może się zmienić automatycznie
- **Zachowana kategoria** - pozostaje bez zmian

## 📥 Pobieranie Plików

### Jako Administrator

#### Z Tabeli
1. Kliknij ikonę **"Pobierz"** (⬇️) w tabeli
2. Plik pobiera się z oryginalną nazwą

#### Z Edycji
1. Otwórz edycję pliku
2. Kliknij **"Pobierz plik"** na górze
3. Plik pobiera się z oryginalną nazwą

### Linki Publiczne

#### Ustawienie Publicznego Dostępu
1. **Edytuj plik**
2. **Przewiń na dół** do sekcji "Ustawienia"
3. **Zaznacz "Plik publiczny"**
4. **Zapisz zmiany**

#### Udostępnianie Linku
1. **W tabeli** kliknij **"Kopiuj link"**
2. **Link zostanie skopiowany** do schowka
3. **Udostępnij link** innym osobom

#### Bezpieczeństwo Linków
- ✅ **Linki działają** tylko dla plików publicznych
- ✅ **Automatyczne kopiowanie** do schowka
- ✅ **Pobieranie z oryginalną nazwą**

## 🗂️ Organizacja Plików

### Kategorie

#### Dostępne Kategorie
- **Ogólne** - pliki uniwersalne (domyślna)
- **Dokumenty** - PDF, Word, Excel, PowerPoint
- **Obrazy** - zdjęcia, grafiki, ikony
- **Wideo** - filmy, nagrania
- **Audio** - muzyka, nagrania audio
- **Archiwa** - ZIP, RAR, skompresowane pliki
- **Kopie zapasowe** - backup, archiwa systemowe

#### Zmiana Kategorii
1. **Edytuj plik**
2. **Wybierz nową kategorię** z listy
3. **Zapisz zmiany**

### Opisy Plików

#### Dodawanie Opisu
- **Maksymalnie 1000 znaków**
- **Opcjonalne** - można zostawić puste
- **Pomocne** dla innych użytkowników
- **Wyszukiwalne** w systemie

## 🔍 Wyszukiwanie i Filtrowanie

### Szukanie Plików
- **Kliknij** w pole wyszukiwania
- **Wpisz** nazwę pliku lub oryginalną nazwę
- **System wyszuka** automatycznie

### Filtrowanie
- **Kliknij** na ikonę filtra
- **Wybierz kategorię** z listy
- **Zobacz** tylko pliki z tej kategorii

### Sortowanie
- **Kliknij** na nagłówek kolumny
- **Sortuj** po:
  - Nazwie pliku
  - Rozmiarze
  - Dacie utworzenia
  - Kategorii

## ⚙️ Ustawienia Zaawansowane

### Przełącznik Publiczny

#### Gdzie Znaleźć
- **Na dole formularza** w sekcji "Ustawienia"
- **Tuż przy przyciskach** Zapisz/Anuluj

#### Jak Działa
- **❌ Prywatny** - dostęp tylko dla administratorów
- **✅ Publiczny** - dostęp przez link publiczny

### Bezpieczeństwo

#### Pliki Prywatne
- **Dostęp** tylko dla zalogowanych administratorów
- **Bezpieczne** przechowywanie
- **Kontrola** nad dostępem

#### Pliki Publiczne
- **Dostęp** przez link bez logowania
- **Udostępnianie** zewnętrznym osobom
- **Kopiowanie linku** jednym kliknięciem

## 🚨 Rozwiązywanie Problemów

### Częste Problemy

#### 1. Plik się nie pobiera
**Sprawdź**:
- Czy plik jest publiczny (dla linków zewnętrznych)
- Czy jesteś zalogowany (dla plików prywatnych)
- Czy plik istnieje w systemie

**Rozwiązanie**:
- Skontaktuj się z administratorem
- Sprawdź czy link jest poprawny

#### 2. Upload nie działa
**Sprawdź**:
- Czy plik nie przekracza 10MB
- Czy format pliku jest obsługiwany
- Czy masz połączenie z internetem

**Rozwiązanie**:
- Zmniejsz rozmiar pliku
- Sprawdź format pliku
- Spróbuj ponownie

#### 3. Zamiana pliku nie działa
**Sprawdź**:
- Czy potwierdziłeś zamianę w alert
- Czy nowy plik ma odpowiedni format
- Czy masz uprawnienia do edycji

**Rozwiązanie**:
- Potwierdź zamianę w oknie dialogowym
- Sprawdź format nowego pliku

### Kontakt z Pomocą
- **Email**: admin@grupy-poledance.test
- **Logi systemu**: Dostępne dla administratorów
- **Dokumentacja techniczna**: `/docs/system-plikow-administratora.md`

## 💡 Wskazówki i Najlepsze Praktyki

### Organizacja Plików
- ✅ **Używaj opisowych nazw** plików
- ✅ **Przypisuj odpowiednie kategorie**
- ✅ **Dodawaj opisy** dla ważnych plików
- ✅ **Regularnie sprawdzaj** publiczne pliki

### Bezpieczeństwo
- ✅ **Ogranicz publiczne pliki** do minimum
- ✅ **Usuwaj niepotrzebne pliki**
- ✅ **Sprawdzaj rozmiary** plików
- ✅ **Używaj bezpiecznych nazw** plików

### Wydajność
- ✅ **Kompresuj duże pliki** przed uploadem
- ✅ **Używaj odpowiednich formatów**
- ✅ **Regularnie porządkuj** pliki
- ✅ **Sprawdzaj rozmiar** katalogu plików

## 📊 Statystyki i Monitoring

### Informacje o Plikach
- **Rozmiar pliku** - wyświetlany w KB/MB
- **Data utworzenia** - automatyczna
- **Data modyfikacji** - aktualizowana przy zmianach
- **Typ MIME** - automatycznie wykryty

### Historia Operacji
- **Logi systemu** - dostępne dla administratorów
- **Śledzenie zmian** - kto i kiedy edytował
- **Audyt bezpieczeństwa** - dostęp do plików

---

## 🎉 Podsumowanie

System plików administratora to potężne narzędzie do zarządzania plikami. Kluczowe funkcje:

- ✅ **Upload** z automatycznym nazewnictwem
- ✅ **Organizacja** w kategorie i opisy
- ✅ **Bezpieczeństwo** z kontrolą dostępu
- ✅ **Udostępnianie** przez linki publiczne
- ✅ **Zamiana plików** z automatycznym usuwaniem
- ✅ **Wyszukiwanie i filtrowanie**
- ✅ **Intuicyjny interfejs** z ikonami i podglądem

**Pamiętaj**: W przypadku problemów, skontaktuj się z administratorem systemu.

---

**Ostatnia aktualizacja**: 14 września 2025  
**Wersja**: 1.0  
**Dla użytkowników**: Panel Administratora
