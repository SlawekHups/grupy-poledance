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
            // 👤 Użytkownicy (tylko zwykli użytkownicy, bez administratorów)
            Card::make('Liczba użytkowników', User::where('role', 'user')->count())
                ->icon('heroicon-o-users')
                ->color('success')
                ->description('Ostatni dodany: ' . User::where('role', 'user')->latest('created_at')->first()?->created_at->format('d.m.Y'))
                ->url(route('filament.admin.resources.users.index', ['tableFilters[role][value]' => 'user']))
                ->extraAttributes(['class' => 'cursor-pointer']),

            Card::make('Nowi użytkownicy (7 dni)', User::where('role', 'user')->where('created_at', '>=', now()->subDays(7))->count())
                ->icon('heroicon-o-user-plus')
                ->color('success')
                ->description('Ostatni: ' . optional(User::where('role', 'user')->latest()->first())->created_at->format('d.m.Y'))
                ->url(route('filament.admin.resources.users.index', [
                    'tableFilters[role]' => 'user',
                    'tableFilters[created_at][created_from]' => now()->subDays(7)->format('Y-m-d'),
                    'tableFilters[created_at][created_until]' => now()->format('Y-m-d'),
                ]))
                ->extraAttributes(['class' => 'cursor-pointer']),

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

            // 📂 Grupy
            Card::make('Liczba grup', Group::count())
                ->icon('heroicon-o-folder')
                ->color('warning')
                ->description('Wszystkie zarejestrowane grupy'),

            Card::make('Grupa Poniedziałek 20:00 testowa recznie dodana', \App\Models\User::where('group_id', 4)->count())
                ->icon('heroicon-o-user-group')
                ->color('warning')
                ->description('Liczba użytkowników w tej grupie'),
        ];

        // 👥 Liczba użytkowników w każdej grupie
        foreach (Group::all() as $group) {
            $userCount = $group->users()->where('role', 'user')->count();
            $color = 'success';
            if ($userCount === 0) {
                $color = 'danger';
            } elseif ($userCount >= 1 && $userCount <= 6) {
                $color = 'purple';
            }
            $cards[] = Card::make("Grupa: {$group->name}", $userCount)
                ->icon('heroicon-o-user-group')
                ->color($color)
                ->description('Liczba przypisanych użytkowników');
        }

        return $cards;
    }
}
