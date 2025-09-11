<?php

namespace App\Filament\Admin\Resources\UserMailMessageResource\Pages;

use App\Filament\Admin\Resources\UserMailMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewUserMailMessage extends ViewRecord
{
    protected static string $resource = UserMailMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Powrót do listy')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(fn () => static::getResource()::getUrl('index')),
            Actions\EditAction::make(),
        ];
    }
} 