<?php

namespace App\Filament\Admin\Resources\PasswordResetLogResource\Pages;

use App\Filament\Admin\Resources\PasswordResetLogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPasswordResetLog extends EditRecord
{
    protected static string $resource = PasswordResetLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
