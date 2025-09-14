# System Plików Administratora

## Przegląd

System plików administratora to zaawansowana funkcjonalność pozwalająca administratorom na zarządzanie plikami w aplikacji. System umożliwia upload, pobieranie, organizację i udostępnianie plików z pełną kontrolą dostępu.

## Funkcjonalności

### 1. Upload Plików

#### Obsługiwane formaty:
- **Obrazy**: JPG, JPEG, PNG, GIF, WebP
- **Dokumenty**: PDF, DOC, DOCX, TXT, MD
- **Archiwa**: ZIP, RAR, 7Z
- **Dane**: CSV, SQL, XML, JSON
- **Skrypty**: SH, BAT, PS1
- **Inne**: MP3, MP4, AVI, MOV

#### Ograniczenia:
- Maksymalny rozmiar pliku: 10MB
- Automatyczne wykrywanie typu MIME
- Walidacja rozszerzeń plików

### 2. Zarządzanie Nazwami Plików

#### Inteligentne wypełnianie:
- **Puste pole**: Automatycznie używa oryginalnej nazwy pliku
- **Wpisana nazwa**: Zachowuje nazwę wprowadzoną przez użytkownika
- **Fallback**: Jeśli brak oryginalnej nazwy, używa nazwy użytkownika

#### Przykład:
```
Oryginalny plik: "raport-miesięczny-2024.pdf"
Pole puste → Automatycznie: "raport-miesięczny-2024.pdf"
Wpisane: "Mój raport" → Zachowuje: "Mój raport"
```

### 3. Organizacja Plików

#### Kategorie:
- **Ogólne** (general) - domyślna
- **Dokumenty** (documents)
- **Obrazy** (images)
- **Filmy** (videos)
- **Audio** (audio)
- **Archiwa** (archives)
- **Kopie zapasowe** (backups)

#### Struktura katalogów:
```
storage/app/admin-files/
└── uploads/
    ├── 01K546QY0WDHZ7ZECJC5W6SQ6.png
    ├── 68c6ca109624c.csv
    └── raport-miesięczny-2024.pdf
```

### 4. System Dostępu

#### Poziomy dostępu:
- **Prywatny** - dostęp tylko dla administratorów
- **Publiczny** - dostęp przez link publiczny

#### Linki publiczne:
- Format: `https://domena.com/admin-files/filename.ext`
- Pobieranie z oryginalną nazwą pliku
- Automatyczne kopiowanie do schowka

### 5. Interfejs Użytkownika

#### Tabela plików:
- **Ikona pliku** - automatyczna na podstawie typu
- **Nazwa pliku** - edytowalna
- **Rozmiar** - formatowany (KB, MB, GB)
- **Oryginalna nazwa** - tylko do odczytu
- **Kategoria** - wybierana z listy
- **Typ MIME** - automatyczny
- **Status publiczny** - przełącznik
- **Link publiczny** - "Kopiuj link" / "Prywatny"
- **Data utworzenia** - automatyczna
- **Data aktualizacji** - automatyczna

#### Akcje:
- **Pobierz** - pobieranie z oryginalną nazwą
- **Edytuj** - modyfikacja metadanych
- **Usuń** - usunięcie pliku i rekordu

### 6. Bezpieczeństwo

#### Walidacja:
- Sprawdzanie rozmiaru pliku
- Walidacja typu MIME
- Ochrona przed uploadem szkodliwych plików

#### Autoryzacja:
- Dostęp tylko dla administratorów
- Logowanie wszystkich operacji
- Śledzenie kto przesłał plik

## Implementacja Techniczna

### 1. Model File

```php
class File extends Model
{
    protected $fillable = [
        'name', 'original_name', 'path', 'mime_type', 
        'size', 'category', 'description', 'uploaded_by', 'is_public'
    ];
    
    protected $casts = [
        'is_public' => 'boolean',
        'size' => 'integer',
    ];
}
```

### 2. Struktura Bazy Danych

```sql
CREATE TABLE files (
    id BIGINT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    path VARCHAR(255) NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    size BIGINT NOT NULL,
    category VARCHAR(50) DEFAULT 'general',
    description TEXT NULL,
    uploaded_by BIGINT NOT NULL,
    is_public BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### 3. Konfiguracja Storage

```php
// config/filesystems.php
'admin_files' => [
    'driver' => 'local',
    'root' => storage_path('app/admin-files'),
    'url' => env('APP_URL').'/admin-files',
    'visibility' => 'public',
    'throw' => false,
],
```

### 4. Route Publiczny

```php
// routes/web.php
Route::get('/admin-files/{path}', function ($path) {
    $file = File::where('path', 'uploads/' . $path)->first();
    
    if (!$file || !$file->is_public) {
        abort(404);
    }
    
    $filePath = Storage::disk('admin_files')->path($file->path);
    return response()->download($filePath, $file->original_name);
})->where('path', '.*');
```

## Użycie

### 1. Upload Pliku

1. Przejdź do sekcji "Pliki" w panelu administratora
2. Kliknij "Utwórz plik"
3. Wybierz plik z dysku
4. Wypełnij metadane (opcjonalnie)
5. Kliknij "Utwórz"

### 2. Pobieranie Pliku

#### Jako administrator:
1. W tabeli plików kliknij ikonę "Pobierz"
2. Plik zostanie pobrany z oryginalną nazwą

#### Link publiczny:
1. Ustaw plik jako publiczny
2. Kliknij "Kopiuj link" w tabeli
3. Link zostanie skopiowany do schowka
4. Udostępnij link innym osobom

### 3. Edycja Pliku

1. Kliknij "Edytuj" w tabeli plików
2. Zmodyfikuj metadane (nazwa, kategoria, opis)
3. Opcjonalnie zmień plik
4. Kliknij "Zapisz"

## Debugowanie

### Logi

System generuje szczegółowe logi w `storage/logs/laravel.log`:

```
=== File upload afterStateUpdated ===
state: uploads/68c6ca109624c.csv
original_name: raport-miesięczny-2024.csv
file_exists: true

=== CREATE FILE - Data before processing ===
file: uploads/68c6ca109624c.csv
name: raport-miesięczny-2024.csv
original_name: raport-miesięczny-2024.csv
mime_type: text/csv
size: 400
```

### Monitorowanie

```bash
# Monitoruj logi w czasie rzeczywistym
tail -f storage/logs/laravel.log | grep -A 5 -B 5 "File upload"
```

## Rozwiązywanie Problemów

### 1. Plik nie pobiera się

**Problem**: Błąd "Plik nie istnieje"
**Rozwiązanie**: 
- Sprawdź czy plik istnieje w `storage/app/admin-files/uploads/`
- Sprawdź ścieżkę w bazie danych
- Sprawdź uprawnienia do katalogu

### 2. Nieprawidłowa nazwa pliku

**Problem**: Pobierany plik ma wygenerowaną nazwę zamiast oryginalnej
**Rozwiązanie**:
- Sprawdź czy `original_name` jest zapisane w bazie
- Sprawdź logi `afterStateUpdated`
- Sprawdź czy `storeFileNamesIn('original_name')` jest ustawione

### 3. Upload nie działa

**Problem**: Plik nie jest zapisywany
**Rozwiązanie**:
- Sprawdź uprawnienia do katalogu `storage/app/admin-files/`
- Sprawdź konfigurację `admin_files` w `filesystems.php`
- Sprawdź logi `afterStateUpdated`

## Przyszłe Usprawnienia

### Planowane funkcje:
- [ ] Podgląd plików w przeglądarce
- [ ] Kompresja obrazów
- [ ] Wersjonowanie plików
- [ ] Automatyczne tagi
- [ ] Wyszukiwanie pełnotekstowe
- [ ] Integracja z chmurą (AWS S3, Google Drive)
- [ ] Automatyczne kopie zapasowe
- [ ] Analiza użycia plików

### Optymalizacje:
- [ ] Lazy loading dla dużych list
- [ ] Cache metadanych plików
- [ ] Asynchroniczny upload
- [ ] Progress bar dla uploadu
- [ ] Drag & drop interface

## Wsparcie

W przypadku problemów z systemem plików:

1. Sprawdź logi w `storage/logs/laravel.log`
2. Sprawdź uprawnienia do katalogów
3. Sprawdź konfigurację storage
4. Skontaktuj się z administratorem systemu

---

**Ostatnia aktualizacja**: 14 września 2025  
**Wersja**: 1.0  
**Autor**: System Administrator
