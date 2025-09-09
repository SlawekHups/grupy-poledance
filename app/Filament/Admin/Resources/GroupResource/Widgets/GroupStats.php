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
        // Pobierz wszystkie grupy z liczbą członków
        $groups = Group::withCount('members')->get();
        
        // Grupy pełne (liczba członków >= max_size)
        $fullGroups = $groups->filter(fn ($g) => $g->members_count >= $g->max_size)->count();
        
        // Grupy z wolnymi miejscami (liczba członków < max_size)
        $withSpaceGroups = $groups->filter(fn ($g) => $g->members_count < $g->max_size)->count();

        return [
            Card::make('Łączna liczba użytkowników', User::where('role', 'user')->count())
                ->icon('heroicon-o-users')
                ->color('success')
                ->description('Wszyscy użytkownicy w systemie')
                ->url(route('filament.admin.resources.users.index')),

            // 📂 Grupy
            Card::make('Liczba grup', Group::count())
                ->icon('heroicon-o-folder')
                ->color('warning')
                ->description('Wszystkie zarejestrowane grupy')
                ->url(route('filament.admin.resources.groups.index')),

            // Użytkownicy bez przypisanej grupy (brak wpisu w pivot group_user)
            Card::make('Bez grupy', User::where('role', 'user')
                ->whereDoesntHave('groups')
                ->count())
                ->icon('heroicon-o-user-group')
                ->color('warning')
                ->description('Użytkownicy nieprzypisani do grup')
                ->url(route('filament.admin.resources.users.index', ['tableFilters[no_groups][value]' => '1'])),

            // Grupy pełne
            Card::make('Grupy pełne', $fullGroups)
                ->icon('heroicon-o-rectangle-stack')
                ->color('danger')
                ->description('Grupy o zapełnionym limicie')
                ->url(route('filament.admin.resources.groups.index', ['tableFilters[status][value]' => 'full'])),

            // Grupy z wolnymi miejscami
            Card::make('Grupy z miejscem', $withSpaceGroups)
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->description('Grupy z wolnymi miejscami')
                ->url(route('filament.admin.resources.groups.index', ['tableFilters[status][value]' => 'active'])),

        ];
    }

    public static function getWidgets(): array
    {
        return [
            \App\Filament\Admin\Resources\GroupResource\Widgets\GroupStats::class,
        ];
    }
}
