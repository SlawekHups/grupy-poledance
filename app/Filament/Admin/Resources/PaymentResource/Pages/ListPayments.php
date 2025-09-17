<?php

namespace App\Filament\Admin\Resources\PaymentResource\Pages;

use App\Filament\Admin\Resources\PaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Admin\Resources\PaymentResource\Widgets\PaymentStats;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('info')
                ->label('Opis widżetów')
                ->icon('heroicon-o-information-circle')
                ->color('info')
                ->modalHeading('Opis widżetów statystyk płatności')
                ->modalContent(view('filament.admin.pages.payment-stats-info'))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Zamknij'),
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }
}
