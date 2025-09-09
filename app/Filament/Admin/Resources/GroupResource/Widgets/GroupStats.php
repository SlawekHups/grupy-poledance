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
        // Podsumowania grup
        $fullGroups = Group::query()
            ->where('status', 'full')
            ->count();

        $withSpaceGroups = Group::query()
            ->where('status', 'active')
            ->get()
            ->filter(fn ($g) => $g->hasSpace())
            ->count();

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

            // Grupy pełne
            Card::make('Grupy pełne', $fullGroups)
                ->icon('heroicon-o-rectangle-stack')
                ->color('danger')
                ->description('Grupy o zapełnionym limicie'),

            // Grupy z wolnymi miejscami
            Card::make('Grupy z miejscem', $withSpaceGroups)
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->description('Grupy aktywne z wolnymi miejscami'),

        ];
    }

    public static function getWidgets(): array
    {
        return [
            \App\Filament\Admin\Resources\GroupResource\Widgets\GroupStats::class,
        ];
    }
}
