<?php

namespace App\Filament\Admin\Resources\PreRegistrationResource\Widgets;

use App\Models\PreRegistration;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PreRegistrationStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $total = PreRegistration::count();
        $valid = PreRegistration::valid()->count();
        $used = PreRegistration::where('used', true)->count();
        $expired = PreRegistration::expired()->count();
        
        return [
            Stat::make('Wszystkie pre-rejestracje', $total)
                ->description('Łączna liczba')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
                
            Stat::make('Ważne tokeny', $valid)
                ->description('Dostępne do użycia')
                ->descriptionIcon('heroicon-m-clock')
                ->color('success'),
                
            Stat::make('Użyte', $used)
                ->description('Wypełnione formularze')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('info'),
                
            Stat::make('Wygasłe', $expired)
                ->description('Nieaktywne tokeny')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}
