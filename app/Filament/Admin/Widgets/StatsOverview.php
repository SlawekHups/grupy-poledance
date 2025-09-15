<?php

namespace App\Filament\Admin\Widgets;

use App\Models\User;
use App\Models\Payment;
use App\Models\Group;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Collection;

class StatsOverview extends BaseWidget
{
    protected function getCards(): array
    {
        $cards = [
            // 💳 Płatności
            Card::make('Łączna liczba opłaconych płatności', Payment::where('paid', true)->count())
                ->icon('heroicon-o-banknotes')
                ->color('success')
                ->description('Wszystkie opłacone płatności')
                ->url(route('filament.admin.resources.payments.index', [
                    'tableFilters[paid][value]' => 'true'
                ]))
                ->extraAttributes(['class' => 'cursor-pointer']),

            Card::make('Suma wpłat (30 dni)', 
                Payment::query()
                    ->where('paid', true)
                    ->whereDate('updated_at', '>=', now()->subDays(30))
                    ->whereDate('updated_at', '<=', now())
                    ->sum('amount') . ' zł'
            )
                ->icon('heroicon-o-currency-euro')
                ->color('success')
                ->description('Suma opłaconych płatności z ostatnich 30 dni')
                ->url(route('filament.admin.resources.payments.index', [
                    'tableFilters[paid][value]' => 'true',
                    'tableFilters[updated_at][from]' => now()->subDays(30)->format('Y-m-d'),
                    'tableFilters[updated_at][to]' => now()->format('Y-m-d'),
                ]))
                ->extraAttributes(['class' => 'cursor-pointer']),

            Card::make('Zaległości', Payment::where('paid', false)->count())
                ->icon('heroicon-o-exclamation-circle')
                ->color('danger')
                ->description('Zaległości: ' . Payment::where('paid', false)->count())
                ->url(route('filament.admin.resources.payments.index', ['tableFilters[paid][value]' => false]))
                ->extraAttributes(['class' => 'cursor-pointer']),

            Card::make(
                'Podsumowanie płatności za dany rok.',
                Payment::where('paid', true)
                    ->where('updated_at', '>=', now()->startOfYear())
                    ->sum('amount') . ' zł'
            )
                ->icon('heroicon-o-banknotes')
                ->color('success')
                ->description('Suma wpłat w tym roku')
                ->url(route('filament.admin.resources.payments.index', ['tableFilters[paid][value]' => true]))
                ->extraAttributes(['class' => 'cursor-pointer']),
        ];

        // 👥 Liczba użytkowników w każdej grupie (pivot: members) + kolory wg zajętości i tooltipy + kolorowe obramowania dla dni tygodnia
        foreach (Group::all() as $group) {
            $userCount = $group->members()->where('users.role', 'user')->count();
            $capacity = (int) ($group->max_size ?? 0);
            $color = 'primary';
            $description = 'Liczba przypisanych użytkowników';
            $title = "Status: {$group->status}\nBrak ustawionego limitu miejsc";

            // 🎨 Określ kolor obramowania i tła na podstawie dnia tygodnia
            $borderColor = $this->getDayBorderColor($group->name);
            $backgroundColor = $this->getDayBackgroundColor($group->name);

            if ($capacity > 0) {
                $fillPct = (int) round(($userCount / max($capacity, 1)) * 100);
                if ($userCount === 0) {
                    $color = 'gray';
                } elseif ($userCount >= $capacity) {
                    $color = 'danger';
                } elseif ($fillPct >= 80) {
                    $color = 'warning';
                } elseif ($fillPct >= 50) {
                    $color = 'success';
                } else {
                    $color = 'secondary';
                }
                $description = "Zajętość: {$userCount}/{$capacity} ({$fillPct}%)";
                $title = "Limit miejsc: {$capacity}\nStatus: {$group->status}\nWolne miejsca: " . max(0, $capacity - $userCount);
            } else {
                // Brak ustawionego limitu – prosta kolorystyka
                $color = $userCount === 0 ? 'gray' : 'primary';
            }

            $cards[] = Card::make("Grupa: {$group->name}", $userCount)
                ->icon('heroicon-o-user-group')
                ->color($color)
                ->description($description)
                ->url(route('filament.admin.resources.groups.edit', ['record' => $group->id]))
                ->extraAttributes([
                    'class' => 'cursor-pointer', 
                    'title' => $title,
                    'style' => "border-left: 4px solid {$borderColor}; background-color: {$backgroundColor};"
                ]);
        }

        return $cards;
    }

    /**
     * 🎨 Zwraca kolor obramowania na podstawie dnia tygodnia w nazwie grupy
     */
    private function getDayBorderColor(string $groupName): string
    {
        $groupName = mb_strtolower($groupName);
        
        // Kolory dla każdego dnia tygodnia
        $dayColors = [
            'poniedziałek' => '#ef4444', // Czerwony
            'wtorek' => '#f97316',       // Pomarańczowy  
            'środa' => '#eab308',        // Żółty
            'czwartek' => '#22c55e',     // Zielony
            'piątek' => '#06b6d4',       // Cyjan
            'sobota' => '#8b5cf6',       // Fioletowy
            'niedziela' => '#ec4899',    // Różowy
        ];
        
        // Sprawdź który dzień tygodnia występuje w nazwie
        foreach ($dayColors as $day => $color) {
            if (str_contains($groupName, $day)) {
                return $color;
            }
        }
        
        // Domyślny kolor dla grup bez określonego dnia (np. "Bez grupy")
        return '#6b7280'; // Szary
    }

    /**
     * 🎨 Zwraca jasniejszy kolor tła na podstawie dnia tygodnia w nazwie grupy
     */
    private function getDayBackgroundColor(string $groupName): string
    {
        $groupName = mb_strtolower($groupName);
        
        // Jasniejsze kolory tła dla każdego dnia tygodnia (z przezroczystością)
        $dayBackgroundColors = [
            'poniedziałek' => 'rgba(239, 68, 68, 0.1)',   // Jasny czerwony
            'wtorek' => 'rgba(249, 115, 22, 0.1)',        // Jasny pomarańczowy  
            'środa' => 'rgba(234, 179, 8, 0.1)',          // Jasny żółty
            'czwartek' => 'rgba(34, 197, 94, 0.1)',       // Jasny zielony
            'piątek' => 'rgba(6, 182, 212, 0.1)',         // Jasny cyjan
            'sobota' => 'rgba(139, 92, 246, 0.1)',        // Jasny fioletowy
            'niedziela' => 'rgba(236, 72, 153, 0.1)',     // Jasny różowy
        ];
        
        // Sprawdź który dzień tygodnia występuje w nazwie
        foreach ($dayBackgroundColors as $day => $color) {
            if (str_contains($groupName, $day)) {
                return $color;
            }
        }
        
        // Domyślny kolor dla grup bez określonego dnia (np. "Bez grupy")
        return 'rgba(107, 114, 128, 0.1)'; // Jasny szary
    }
}
