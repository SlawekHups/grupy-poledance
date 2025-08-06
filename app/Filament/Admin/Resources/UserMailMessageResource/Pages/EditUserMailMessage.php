<?php

namespace App\Filament\Admin\Resources\UserMailMessageResource\Pages;

use App\Filament\Admin\Resources\UserMailMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserMailMessage extends EditRecord
{
    protected static string $resource = UserMailMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
} 