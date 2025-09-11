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
                ->color('primary')
                ->url(route('filament.admin.resources.pre-registrations.index')),
                
            Stat::make('Ważne tokeny', $valid)
                ->description('Dostępne do użycia')
                ->descriptionIcon('heroicon-m-clock')
                ->color('success')
                ->url(route('filament.admin.resources.pre-registrations.index', ['tableFilters[valid][isActive]' => '1'])),
                
            Stat::make('Użyte', $used)
                ->description('Wypełnione formularze')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('info')
                ->url(route('filament.admin.resources.pre-registrations.index', ['tableFilters[used][value]' => 'true'])),
                
            Stat::make('Wygasłe', $expired)
                ->description('Nieaktywne tokeny')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger')
                ->url(route('filament.admin.resources.pre-registrations.index', ['tableFilters[expired][isActive]' => '1'])),
        ];
    }
}
