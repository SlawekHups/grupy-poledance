# System Plików Administratora - Kompletna Dokumentacja

## Przegląd

System plików administratora to zaawansowana funkcjonalność pozwalająca administratorom na zarządzanie plikami w aplikacji Laravel z panelem Filament. System umożliwia upload, pobieranie, organizację i udostępnianie plików z pełną kontrolą dostępu i profesjonalnym interfejsem użytkownika.

## 🚀 Główne Funkcjonalności

### 1. Upload i Zarządzanie Plikami
- **Upload plików** z zachowaniem oryginalnych nazw
- **Zamiana plików** z automatycznym usuwaniem starych
- **Podgląd obrazów** w czasie rzeczywistym
- **Ikony plików** dla różnych typów (emoji)
- **Walidacja** rozmiaru i typu plików

### 2. Organizacja i Kategoryzacja
- **7 kategorii** plików (Ogólne, Dokumenty, Obrazy, Wideo, Audio, Archiwa, Kopie zapasowe)
- **Opisy plików** z ograniczeniem do 1000 znaków
- **Automatyczne wykrywanie** typu MIME
- **Szukanie i filtrowanie** plików

### 3. Kontrola Dostępu
- **Pliki prywatne** - dostęp tylko dla administratorów
- **Pliki publiczne** - dostęp przez link
- **Automatyczne kopiowanie** linków publicznych
- **Bezpieczne pobieranie** z oryginalnymi nazwami

## 📁 Obsługiwane Formaty Plików

### Obrazy
- **PNG, JPEG, JPG** - z podglądem miniatur
- **GIF, WebP, BMP, SVG** - z ikonami
- **Miniaturki** automatycznie generowane

### Dokumenty
- **PDF** 📄 - z ikoną dokumentu
- **Word** 📝 - DOC, DOCX
- **Excel** 📊 - XLS, XLSX
- **PowerPoint** 📊 - PPT, PPTX
- **Tekst** 📄 - TXT, CSV, MD, LOG

### Archiwa i Skrypty
- **ZIP** 📦 - archiwa
- **Shell Scripts** ⚙️ - SH, BASH
- **Style Files** 🔧 - pliki stylów

### Ograniczenia
- **Maksymalny rozmiar**: 10MB
- **Automatyczna walidacja** typu MIME
- **Bezpieczne rozszerzenia** - ochrona przed szkodliwymi plikami

## 🎨 Interfejs Użytkownika

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

### Formularz Edycji (Nowy Layout)
```
┌─ Sekcja "Plik" ─────────────────────────┐
│ [Upload pliku - pełna szerokość]        │
│ [Podgląd obrazka] [Informacje o pliku]  │
└─────────────────────────────────────────┘

┌─ Sekcja "Informacje o pliku" ──────────┐
│ [Nazwa pliku] [Oryginalna nazwa]       │
│ [Kategoria] [Rozmiar]                  │
│ [Opis - pełna szerokość]               │
└─────────────────────────────────────────┘

┌─ Sekcja "Ustawienia" ──────────────────┐
│ [☐ Plik publiczny]                     │
└─────────────────────────────────────────┘

[⬅️ Powrót] [⬇️ Pobierz] [🗑️ Usuń] [💾 Zapisz]
```

## 🔧 Funkcje Zaawansowane

### Zamiana Plików z Konfirmacją
1. **Wybierz nowy plik** w edycji
2. **JavaScript alert** - "Czy zastąpić obecny plik?"
3. **Automatyczne usuwanie** starego pliku
4. **Aktualizacja metadanych** (nazwa, rozmiar, typ)
5. **Przekierowanie** do tabeli po zapisaniu

### Inteligentne Nazewnictwo
```php
// Logika nazewnictwa:
if (empty($user_name)) {
    $name = pathinfo($original_name, PATHINFO_FILENAME);
} else {
    $name = $user_name;
}
```

### Bezpieczne URL-e
```php
// HTTPS wymuszony dla wszystkich linków:
public function getUrlAttribute(): string
{
    return secure_url('admin-files/' . $urlPath);
}
```

## 🗂️ Struktura Plików

### Katalogi
```
storage/app/admin-files/
├── uploads/                 # Pliki użytkowników
│   ├── 01K546QY0WDHZ7ZECJC5W6SQ6.png
│   ├── raport-miesieczny-2024.pdf
│   └── backup-systemu.zip
└── thumbnails/              # Miniaturki (jeśli potrzebne)
    └── uploads/
        └── 01K546QY0WDHZ7ZECJC5W6SQ6.png
```

### Symlink Publiczny
```bash
# Automatycznie tworzony:
public/admin-files -> storage/app/admin-files
```

## 🛠️ Implementacja Techniczna

### Model File
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
    
    // Accessory dla bezpiecznych URL-i
    public function getUrlAttribute(): string
    public function getThumbnailUrlAttribute(): string
    public function getIconAttribute(): string
}
```

### Filament Resource
```php
class FileResource extends Resource
{
    // Formularz z sekcjami i gridami
    public static function form(Form $form): Form
    
    // Tabela z ikonami i skróconymi nazwami
    public static function table(Table $table): Table
    
    // Strony CRUD
    public static function getPages(): array
}
```

### Route Publiczny
```php
// routes/web.php
Route::get('/admin-files/{path}', [FileController::class, 'download'])
    ->where('path', '.*');

Route::get('/admin-files/thumbnails/{path}', [FileController::class, 'thumbnail'])
    ->where('path', '.*');
```

## 📊 Baza Danych

### Tabela `files`
```sql
CREATE TABLE files (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    path VARCHAR(255) NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    size BIGINT UNSIGNED NOT NULL,
    category VARCHAR(50) DEFAULT 'general',
    description TEXT NULL,
    uploaded_by BIGINT UNSIGNED NOT NULL,
    is_public BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_category (category),
    INDEX idx_is_public (is_public),
    INDEX idx_uploaded_by (uploaded_by),
    FOREIGN KEY (uploaded_by) REFERENCES users(id)
);
```

## 🎯 Workflow Użytkownika

### 1. Dodawanie Nowego Pliku
```
1. Kliknij "Utwórz plik"
2. Wybierz plik z dysku
3. System automatycznie wypełnia:
   - Oryginalną nazwę
   - Nazwę (bez rozszerzenia)
   - Rozmiar i typ MIME
4. Opcjonalnie zmień kategorię i dodaj opis
5. Ustaw czy ma być publiczny
6. Kliknij "Utwórz"
7. Automatyczne przekierowanie do tabeli
```

### 2. Edycja Pliku
```
1. Kliknij "Edytuj" w tabeli
2. Widzisz:
   - Podgląd obrazka (dla obrazów)
   - Informacje o pliku (dla innych typów)
   - Formularz z metadanymi
   - Przełącznik publiczny na dole
3. Opcjonalnie zmień plik:
   - Wybierz nowy plik
   - Potwierdź zamianę w alert
   - Stary plik zostanie usunięty
4. Kliknij "Zapisz"
5. Automatyczne przekierowanie do tabeli
```

### 3. Pobieranie Pliku
```
Jako administrator:
1. Kliknij "Pobierz" w tabeli lub edycji
2. Plik pobiera się z oryginalną nazwą

Link publiczny:
1. Ustaw plik jako publiczny
2. Kliknij "Kopiuj link" w tabeli
3. Link zostaje skopiowany do schowka
4. Udostępnij link innym osobom
```

## 🔍 Debugowanie i Logi

### Szczegółowe Logi
```php
// Logi uploadu:
=== File upload afterStateUpdated ===
state: uploads/68c6ca109624c.csv
original_name: raport-miesięczny-2024.csv

// Logi tworzenia:
=== CREATE FILE - Data before processing ===
file: uploads/68c6ca109624c.csv
name: raport-miesięczny-2024
mime_type: text/csv
size: 400

// Logi edycji:
=== ZMIANA PLIKU W EDYCJI ===
stary_plik: uploads/old-file.pdf
nowy_plik: uploads/new-file.pdf
```

### Monitorowanie
```bash
# Monitoruj logi w czasie rzeczywistym:
tail -f storage/logs/laravel.log | grep -A 5 -B 5 "File"

# Sprawdź pliki w katalogu:
ls -la storage/app/admin-files/uploads/

# Sprawdź symlink:
ls -la public/admin-files
```

## 🚨 Rozwiązywanie Problemów

### 1. Błąd "Plik nie istnieje"
**Przyczyna**: Plik został usunięty z dysku, ale rekord pozostał w bazie
**Rozwiązanie**:
```bash
# Sprawdź czy plik istnieje:
ls -la storage/app/admin-files/uploads/

# Sprawdź rekord w bazie:
php artisan tinker
>>> App\Models\File::find(123)->path
```

### 2. Błąd formatowania rozmiaru
**Przyczyna**: `SQLSTATE[01000]: Warning: 1265 Data truncated for column 'size'`
**Rozwiązanie**: Sprawdź czy pole `size` używa `round()` zamiast `number_format()`

### 3. Brak miniatur obrazów
**Przyczyna**: Problem z route thumbnails lub symlink
**Rozwiązanie**:
```bash
# Sprawdź route:
php artisan route:list | grep admin-files

# Sprawdź symlink:
ls -la public/admin-files
```

### 4. Upload nie działa
**Przyczyna**: Problemy z uprawnieniami lub konfiguracją
**Rozwiązanie**:
```bash
# Sprawdź uprawnienia:
chmod -R 755 storage/app/admin-files/
chown -R www-data:www-data storage/app/admin-files/

# Sprawdź konfigurację:
php artisan config:cache
php artisan route:cache
```

## 🎨 Customizacja

### Dodanie Nowych Typów Plików
```php
// W FileResource.php, sekcja ikon:
elseif (strpos($record->mime_type, 'application/your-type') === 0) {
    $icon = '🔧'; // Wybierz emoji
}
```

### Zmiana Kategorii
```php
// W formularzu:
Forms\Components\Select::make('category')
    ->options([
        'general' => 'Ogólne',
        'documents' => 'Dokumenty',
        'your-category' => 'Twoja kategoria', // Dodaj nową
    ])
```

### Modyfikacja Ograniczeń
```php
// W FileUpload:
Forms\Components\FileUpload::make('file')
    ->maxSize(20480) // Zmień na 20MB
    ->acceptedFileTypes(['image/*', 'application/pdf', 'your-type/*'])
```

## 📈 Wydajność i Optymalizacja

### Cache
```bash
# Wyczyść cache po zmianach:
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
```

### Monitoring Rozmiaru
```bash
# Sprawdź rozmiar katalogu plików:
du -sh storage/app/admin-files/

# Znajdź największe pliki:
find storage/app/admin-files/ -type f -exec ls -lh {} \; | sort -k5 -hr | head -10
```

### Backup
```bash
# Backup plików:
tar -czf backup-files-$(date +%Y%m%d).tar.gz storage/app/admin-files/

# Backup bazy danych:
php artisan backup:run
```

## 🔮 Przyszłe Usprawnienia

### Planowane Funkcje
- [ ] **Podgląd PDF** w przeglądarce
- [ ] **Kompresja obrazów** automatyczna
- [ ] **Wersjonowanie plików** z historią
- [ ] **Automatyczne tagi** na podstawie zawartości
- [ ] **Wyszukiwanie pełnotekstowe** w plikach
- [ ] **Integracja z chmurą** (AWS S3, Google Drive)
- [ ] **Analiza użycia plików** z statystykami
- [ ] **Automatyczne kopie zapasowe** na zewnętrzne dyski

### Optymalizacje UI/UX
- [ ] **Drag & drop** interface dla uploadu
- [ ] **Progress bar** dla dużych plików
- [ ] **Lazy loading** dla dużych list
- [ ] **Bulk operations** (masowe operacje)
- [ ] **Quick preview** bez otwierania edycji
- [ ] **Keyboard shortcuts** dla częstych operacji

### Bezpieczeństwo
- [ ] **Antywirus scanning** uploadowanych plików
- [ ] **Watermarking** dla obrazów
- [ ] **Audit log** wszystkich operacji
- [ ] **Rate limiting** dla uploadu
- [ ] **IP whitelisting** dla dostępu publicznego

## 📞 Wsparcie Techniczne

### Kontakt
- **Email**: admin@grupy-poledance.test
- **Dokumentacja**: `/docs/system-plikow-administratora.md`
- **Logi**: `storage/logs/laravel.log`

### Przydatne Komendy
```bash
# Sprawdź status systemu:
php artisan about

# Wyczyść cache:
php artisan optimize:clear

# Sprawdź konfigurację:
php artisan config:show filesystems.disks.admin_files

# Sprawdź route:
php artisan route:list | grep admin-files
```

---

**Ostatnia aktualizacja**: 14 września 2025  
**Wersja**: 2.0  
**Autor**: System Administrator  
**Status**: ✅ Aktywny i w pełni funkcjonalny