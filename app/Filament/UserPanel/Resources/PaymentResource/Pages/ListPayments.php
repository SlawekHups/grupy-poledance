<?php

namespace App\Filament\UserPanel\Resources\PaymentResource\Pages;

use App\Filament\UserPanel\Resources\PaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // brak akcji tworzenia
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\UserPanel\Widgets\PaymentsKpis::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 1;
    }
}
