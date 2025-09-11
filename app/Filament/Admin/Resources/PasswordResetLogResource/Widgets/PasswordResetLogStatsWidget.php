<?php

namespace App\Filament\Admin\Resources\PasswordResetLogResource\Widgets;

use App\Models\PasswordResetLog;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class PasswordResetLogStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $total = PasswordResetLog::count();
        $pending = PasswordResetLog::where('status', 'pending')->count();
        $completed = PasswordResetLog::where('status', 'completed')->count();
        $expired = PasswordResetLog::where('status', 'expired')->count();
        
        return [
            Stat::make('Wszystkie logi', $total)
                ->description('Łączna liczba resetów')
                ->descriptionIcon('heroicon-m-key')
                ->color('primary')
                ->url(route('filament.admin.resources.password-reset-logs.index')),
                
            Stat::make('Oczekujące', $pending)
                ->description('Oczekują na ustawienie hasła')
                ->descriptionIcon('heroicon-m-clock')
                ->color('violet')
                ->url(route('filament.admin.resources.password-reset-logs.index', ['tableFilters[status][value]' => 'pending'])),
                
            Stat::make('Zakończone', $completed)
                ->description('Hasło zostało ustawione')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->url(route('filament.admin.resources.password-reset-logs.index', ['tableFilters[status][value]' => 'completed'])),
                
            Stat::make('Wygasłe', $expired)
                ->description('Tokeny wygasły')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger')
                ->url(route('filament.admin.resources.password-reset-logs.index', ['tableFilters[status][value]' => 'expired'])),
        ];
    }
}
