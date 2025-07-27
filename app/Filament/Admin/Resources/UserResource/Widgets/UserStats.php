<?php

namespace App\Filament\Admin\Resources\UserResource\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserStats extends BaseWidget
{
    protected static ?string $pollingInterval = null;
    
    protected function getStats(): array
    {
        // Podstawowe liczniki
        $totalUsers = User::where('role', 'user')->count();
        $activeUsers = User::where('role', 'user')->where('is_active', true)->count();
        $inactiveUsers = $totalUsers - $activeUsers;

        return [
            Stat::make('Wszyscy użytkownicy', $totalUsers)
                ->description('Łączna liczba użytkowników')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('Aktywni użytkownicy', $activeUsers)
                ->description('Liczba aktywnych użytkowników')
                ->descriptionIcon('heroicon-m-user')
                ->color('success'),

            Stat::make('Nieaktywni użytkownicy', $inactiveUsers)
                ->description('Liczba nieaktywnych użytkowników')
                ->descriptionIcon('heroicon-m-user-minus')
                ->color('danger'),
        ];
    }
} 