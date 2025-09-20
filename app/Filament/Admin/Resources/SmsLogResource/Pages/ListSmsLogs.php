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
            \Filament\Actions\Action::make('refresh_balance')
                ->label('Odśwież saldo')
                ->icon('heroicon-o-arrow-path')
                ->color('info')
                ->action(function () {
                    // Wyczyść cache salda
                    cache()->forget('sms_api_balance');
                    
                    $smsService = new \App\Services\SmsService();
                    $balance = $smsService->getBalance();
                    
                    if ($balance !== null) {
                        \Filament\Notifications\Notification::make()
                            ->title('Saldo SMS API zaktualizowane')
                            ->body("Dostępne środki: " . number_format($balance, 2) . " PLN (" . number_format($balance, 0) . " punktów)")
                            ->success()
                            ->send();
                    } else {
                        \Filament\Notifications\Notification::make()
                            ->title('Błąd pobierania salda')
                            ->body('Nie udało się pobrać salda SMS API. Sprawdź logi.')
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            SmsStatsWidget::class,
        ];
    }
}
