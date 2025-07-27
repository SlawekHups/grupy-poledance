<?php

namespace App\Filament\Admin\Resources\AttendanceResource\Pages;

use App\Filament\Admin\Resources\AttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Admin\Resources\AttendanceResource\Widgets\AttendanceStats;
use App\Filament\Admin\Resources\AttendanceResource\Widgets\AttendanceGroupChart;
use App\Filament\Admin\Resources\AttendanceResource\Widgets\TopAttendersChart;
use App\Filament\Admin\Resources\AttendanceResource\Widgets\MonthlyTrendChart;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            AttendanceStats::class,
            AttendanceGroupChart::class,
            TopAttendersChart::class,
            MonthlyTrendChart::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return [
            'default' => 3,
            'sm' => 3,
            'md' => 3,
            'lg' => 3,
            'xl' => 3,
            '2xl' => 3,
        ];
    }
}
