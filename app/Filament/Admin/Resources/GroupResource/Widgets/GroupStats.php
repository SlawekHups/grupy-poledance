<?php

namespace App\Filament\Admin\Resources\GroupResource\Widgets;

use App\Models\Group;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Card;


class GroupStats extends StatsOverviewWidget
{
    protected function getCards(): array
    {
        return [
            Card::make('Łączna liczba użytkowników', User::where('role', 'user')->count())
                ->icon('heroicon-o-users')
                ->color('success')
                ->description('Podsumowanie przypisanych do grup'),

            // 📂 Grupy
            Card::make('Liczba grup', Group::count())
                ->icon('heroicon-o-folder')
                ->color('warning')
                ->description('Wszystkie zarejestrowane grupy'),

            // Użytkownicy bez przypisanej grupy (brak wpisu w pivot group_user)
            Card::make('Bez grupy', \App\Models\User::where('role', 'user')
                ->whereDoesntHave('groups')
                ->count())
                ->icon('heroicon-o-user-group')
                ->color('warning')
                ->description('Liczba użytkowników w tej grupie'),

        ];
    }

    public static function getWidgets(): array
    {
        return [
            \App\Filament\Admin\Resources\GroupResource\Widgets\GroupStats::class,
        ];
    }
}
