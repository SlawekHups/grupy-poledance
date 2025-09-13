# 🎨 Wytyczne stylowania w Filament

## Problem
Filament ma własne style CSS z wysoką specyficznością, które nadpisują standardowe klasy Tailwind CSS. Dlatego często trzeba używać `!important` w inline styles.

## Rozwiązania

### 1. **Konfiguracja Tailwind** (`tailwind.config.js`)
```javascript
export default {
  important: true, // Wymuś wszystkie klasy Tailwind
  content: [
    './resources/**/*.blade.php',
    './app/Filament/**/*.php',
  ],
  // ... reszta konfiguracji
}
```

### 2. **Custom CSS** (`resources/css/app.css`)
```css
/* Wymuś odstępy dla komponentów Filament */
.fi-section {
    margin-bottom: 3rem !important;
}

.fi-card {
    margin-bottom: 2rem !important;
    padding: 1.5rem !important;
}

.fi-table th,
.fi-table td {
    padding: 1rem 1.5rem !important;
}
```

### 3. **Użyj klas Filament zamiast Tailwind**
```html
<!-- Zamiast -->
<div class="mb-12 bg-white p-4">

<!-- Użyj -->
<div class="fi-section fi-card">
```

### 4. **Custom spacing utilities**
```html
<!-- Użyj custom klas z app.css -->
<div class="spacing-section">
<div class="spacing-card">
<div class="spacing-table">
```

## Najlepsze praktyki

### ✅ **DO:**
- Używaj `important: true` w Tailwind config
- Twórz custom CSS dla komponentów Filament
- Używaj klas Filament gdy to możliwe
- Testuj na mobile i desktop

### ❌ **NIE:**
- Nie używaj `!important` w każdym miejscu
- Nie mieszaj Tailwind z Filament bez potrzeby
- Nie ignoruj dark mode
- Nie zapominaj o responsywności

## Przykłady

### Karta z odstępami
```html
<!-- Stary sposób -->
<div class="mb-12 bg-white p-4" style="margin-bottom: 3rem !important;">

<!-- Nowy sposób -->
<div class="fi-card spacing-card">
```

### Tabela z odstępami
```html
<!-- Stary sposób -->
<table class="min-w-full" style="margin-top: 2rem !important;">
    <th style="padding: 1rem 1.5rem !important;">

<!-- Nowy sposób -->
<table class="fi-table spacing-table">
    <th class="spacing-table-cell">
```

## Debugowanie

### Sprawdź style w DevTools
1. Otwórz DevTools (F12)
2. Sprawdź Computed styles
3. Zobacz które style są nadpisywane

### Sprawdź specyficzność CSS
```css
/* Niska specyficzność */
.mb-12 { margin-bottom: 3rem; }

/* Wysoka specyficzność (Filament) */
.fi-section .fi-section-content { margin-bottom: 1rem; }

/* Najwyższa specyficzność */
.mb-12 { margin-bottom: 3rem !important; }
```

## Aktualizacja po zmianach

Po dodaniu nowych stylów:
```bash
php artisan view:clear
php artisan cache:clear
npm run build  # lub npm run dev
```

## Notatki
- Filament używa klas z prefiksem `fi-`
- Własne style CSS mają wyższy priorytet niż Tailwind
- Zawsze testuj na mobile i desktop
- Uwzględnij dark mode w stylach
