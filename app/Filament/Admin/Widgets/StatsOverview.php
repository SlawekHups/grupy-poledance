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

        // 👥 Liczba użytkowników w każdej grupie (pivot: members) + kolory wg zajętości i tooltipy
        foreach (Group::all() as $group) {
            $userCount = $group->members()->where('users.role', 'user')->count();
            $capacity = (int) ($group->max_size ?? 0);
            $color = 'primary';
            $description = 'Liczba przypisanych użytkowników';
            $title = "Status: {$group->status}\nBrak ustawionego limitu miejsc";

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
                ->extraAttributes(['class' => 'cursor-pointer', 'title' => $title]);
        }

        return $cards;
    }
}
