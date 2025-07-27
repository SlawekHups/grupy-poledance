<?php

namespace App\Filament\Admin\Resources\AttendanceResource\Widgets;

use App\Models\Attendance;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class AttendanceStats extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $stats = Attendance::query()
            ->where('present', true)
            ->select([
                DB::raw('COUNT(*) as total_count'),
                DB::raw('SUM(CASE WHEN DATE(date) = CURDATE() THEN 1 ELSE 0 END) as today_count'),
                DB::raw('SUM(CASE WHEN DATE(date) = DATE_SUB(CURDATE(), INTERVAL 1 DAY) THEN 1 ELSE 0 END) as yesterday_count')
            ])
            ->first();

        return [
            Stat::make('Wszystkie obecności', $stats->total_count)
                ->description('Łączna liczba obecności')
                ->icon('heroicon-o-user-group')
                ->color('success')
                ->url(route('filament.admin.resources.attendances.index', [
                    'tableFilters[present][value]' => 'true'
                ]))
                ->extraAttributes(['class' => 'cursor-pointer']),

            Stat::make('Dzisiaj', $stats->today_count)
                ->description('Obecności dzisiaj')
                ->icon('heroicon-o-calendar')
                ->color('info')
                ->url(route('filament.admin.resources.attendances.index', [
                    'tableFilters[present][value]' => 'true',
                    'tableFilters[date][from]' => today()->format('Y-m-d'),
                    'tableFilters[date][to]' => today()->format('Y-m-d'),
                ]))
                ->extraAttributes(['class' => 'cursor-pointer']),

            Stat::make('Wczoraj', $stats->yesterday_count)
                ->description('Obecności wczoraj')
                ->icon('heroicon-o-calendar')
                ->color('warning')
                ->url(route('filament.admin.resources.attendances.index', [
                    'tableFilters[present][value]' => 'true',
                    'tableFilters[date][from]' => today()->subDay()->format('Y-m-d'),
                    'tableFilters[date][to]' => today()->subDay()->format('Y-m-d'),
                ]))
                ->extraAttributes(['class' => 'cursor-pointer']),
        ];
    }
} 