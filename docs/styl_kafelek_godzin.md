# Style Kafelków Godzin - Instrukcja Użycia

## 📁 Lokalizacja pliku
```
resources/css/calendar-hour-tiles.css
```

## 🎯 Jak wykorzystać w innych zakładkach

### 1. Dodaj style CSS (dwa sposoby)

#### Sposób 1: Wbudowane style (zalecane)
```blade
<style>
    /* Skopiuj style z calendar-widget.blade.php */
    .calendar-hour-tile { /* style kafelka */ }
    .calendar-hour-1800 { /* kolor dla 18:00 */ }
    /* itd... */
</style>
```

#### Sposób 2: Zewnętrzny plik CSS
```php
// Skopiuj plik do public/css/
cp resources/css/calendar-hour-tiles.css public/css/

// Dodaj link w layout
<link rel="stylesheet" href="{{ asset('css/calendar-hour-tiles.css') }}">
```

### 2. Podstawowe użycie w Blade
```blade
<!-- Pojedynczy kafelek godziny -->
<a href="#" class="calendar-hour-tile calendar-hour-1800">
    <div class="text-center">
        <div class="hour-time">18:00</div>
        <div class="hour-name">Nazwa grupy</div>
        <div class="hour-badge">5/10 osób</div>
    </div>
</a>

<!-- Grid kafelków -->
<div class="calendar-hours-grid calendar-hours-grid-4">
    @foreach($groups as $group)
        @php
            $hour = $group->time; // np. "18:00"
            $hourFormatted = str_replace(':', '', $hour);
        @endphp
        <a href="{{ route('group.edit', $group->id) }}" 
           class="calendar-hour-tile calendar-hour-{{ $hourFormatted }}">
            <div class="text-center">
                <div class="hour-time">{{ $hour }}</div>
                <div class="hour-name">{{ $group->name }}</div>
                <div class="hour-badge">{{ $group->members_count }}/{{ $group->max_size }}</div>
            </div>
        </a>
    @endforeach
</div>
```

## 🎨 Dostępne kolory godzin

### Główne godziny (18:00-22:00)
- **18:00** - Cyjan (`calendar-hour-1800`)
- **19:00** - Czerwony (`calendar-hour-1900`)
- **20:00** - Pomarańczowy (`calendar-hour-2000`)
- **21:00** - Żółty (`calendar-hour-2100`)
- **22:00** - Zielony (`calendar-hour-2200`)

### Wszystkie godziny (00:00-23:00)
- **00:00** - Indigo, **01:00** - Lime, **02:00** - Amber
- **03:00** - Dark Red, **04:00** - Purple, **05:00** - Sky
- **06:00** - Emerald, **07:00** - Amber, **08:00** - Yellow
- **09:00** - Red, **10:00** - Violet, **11:00** - Blue
- **12:00** - Cyan, **13:00** - Green, **14:00** - Teal
- **15:00** - Lime, **16:00** - Blue, **17:00** - Purple
- **18:00** - Cyan, **19:00** - Red, **20:00** - Orange
- **21:00** - Yellow, **22:00** - Green, **23:00** - Teal

## 📱 Grid Layout

### Dostępne klasy grid:
- `.calendar-hours-grid-2` - 2 kolumny
- `.calendar-hours-grid-3` - 3 kolumny  
- `.calendar-hours-grid-4` - 4 kolumny
- `.calendar-hours-grid-6` - 6 kolumn

### Responsywność:
- **Desktop** - wybrana liczba kolumn
- **Mobile (640px-)** - 2 kolumny
- **Mobile (480px-)** - 1 kolumna

## 🔧 Przykłady użycia

### W Filament Resource
```php
// W metodzie render() lub view
public function render()
{
    return view('filament.resources.my-resource.view', [
        'groups' => $this->getGroups()
    ]);
}
```

### W Blade view
```blade
<div class="calendar-hours-grid calendar-hours-grid-3">
    @foreach($groups as $group)
        @php
            $hour = substr($group->name, -5); // "18:00"
            $hourFormatted = str_replace(':', '', $hour);
        @endphp
        <a href="{{ route('groups.edit', $group->id) }}" 
           class="calendar-hour-tile calendar-hour-{{ $hourFormatted }}">
            <div class="text-center">
                <div class="hour-time">{{ $hour }}</div>
                <div class="hour-name">{{ $group->name }}</div>
                <div class="hour-badge">{{ $group->members_count }}/{{ $group->max_size }}</div>
            </div>
        </a>
    @endforeach
</div>
```

## 💡 Jak przypomnieć o stylu

### Opcje przypomnienia:
1. **"Użyj stylu kafelków godzin z kalendarza"**
2. **"Zastosuj calendar-hour-tile style"**
3. **"Chcę kafelki jak w kalendarzu"**
4. **"Użyj gradientowych kafelków godzin"**

### Konkretne przypomnienia:
- **"Użyj calendar-hour-tile dla kafelków"**
- **"Dodaj calendar-hours-grid dla layoutu"**
- **"Zastosuj kolory godzin z CSS"**
- **"Użyj calendar-hour-1800 dla 18:00"**

## 🎯 Korzyści
- ✅ **Spójność** - te same kolory w całej aplikacji
- ✅ **Responsywność** - automatyczne dostosowanie
- ✅ **Łatwość użycia** - proste klasy CSS
- ✅ **Profesjonalny wygląd** - gradienty i efekty
- ✅ **Wymuszone style** - `!important` zapewnia działanie
