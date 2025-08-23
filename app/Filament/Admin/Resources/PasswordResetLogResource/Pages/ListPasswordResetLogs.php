<?php

namespace App\Filament\Admin\Resources\PasswordResetLogResource\Pages;

use App\Filament\Admin\Resources\PasswordResetLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPasswordResetLogs extends ListRecords
{
    protected static string $resource = PasswordResetLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
