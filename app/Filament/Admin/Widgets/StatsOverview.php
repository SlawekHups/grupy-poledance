<?php

namespace App\Filament\Admin\Widgets;

use App\Models\User;
use App\Models\Payment;
use App\Models\Group;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class StatsOverview extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            // 👤 Użytkownicy
            Card::make('Liczba użytkowników', User::count())
                ->icon('heroicon-o-users')
                ->color('primary')
                ->description('Ostatni dodany: ' . User::latest('created_at')->first()?->created_at->format('d.m.Y')),

            Card::make('Nowi użytkownicy (7 dni)', User::where('created_at', '>=', now()->subDays(7))->count())
                ->icon('heroicon-o-user-plus')
                ->color('success')
                ->description('Ostatni: ' . optional(User::latest()->first())->created_at->format('d.m.Y')),

            // 💳 Płatności
            Card::make('Łączna liczba płatności', Payment::count())
                ->icon('heroicon-o-banknotes')
                ->color('gray')
                ->description('Wszystkie rekordy płatności'),

            Card::make('Wpłaty (30 dni)', Payment::where('paid', true)->where('updated_at', '>=', now()->subDays(30))->sum('amount') . ' zł')
                ->icon('heroicon-o-currency-euro')
                ->color('success')
                ->description('Suma wpłat z ostatnich 30 dni'),

            Card::make('Zaległości', Payment::where('paid', false)->count())
                ->icon('heroicon-o-exclamation-circle')
                ->color('danger')
                ->description('Nieopłacone faktury')
                ->url(route('filament.admin.resources.payments.index'))
                ->extraAttributes(['class' => 'cursor-pointer']),

            Card::make('Podsumowanie płatności', '')
                ->icon('heroicon-o-banknotes')
                ->color('info')
                ->description(
                    'Wpłaty (30 dni): ' . Payment::where('paid', true)->where('updated_at', '>=', now()->subDays(30))->sum('amount') . ' zł' . PHP_EOL .
                    'Zaległości: ' . Payment::where('paid', false)->count()
                ),

            // 📂 Grupy
            Card::make('Liczba grup', Group::count())
                ->icon('heroicon-o-folder')
                ->color('warning')
                ->description('Wszystkie zarejestrowane grupy'),
        ];
    }
}