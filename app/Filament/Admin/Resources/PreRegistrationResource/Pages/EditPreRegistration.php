<?php

namespace App\Filament\Admin\Resources\PreRegistrationResource\Pages;

use App\Filament\Admin\Resources\PreRegistrationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPreRegistration extends EditRecord
{
    protected static string $resource = PreRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back_to_list')
                ->label('Powrót do tabeli')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(fn () => static::getResource()::getUrl('index'))
                ->tooltip('Powrót do listy pre-rejestracji'),
            Actions\DeleteAction::make(),
        ];
    }
}
