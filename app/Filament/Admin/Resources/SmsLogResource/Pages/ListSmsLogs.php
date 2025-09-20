<?php

namespace App\Filament\Admin\Resources\SmsLogResource\Pages;

use App\Filament\Admin\Resources\SmsLogResource;
use App\Filament\Admin\Resources\SmsLogResource\Widgets\SmsStatsWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSmsLogs extends ListRecords
{
    protected static string $resource = SmsLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Usunięto CreateAction - logi SMS są tworzone automatycznie
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            SmsStatsWidget::class,
        ];
    }
}
