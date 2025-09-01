<?php

namespace App\Filament\UserPanel\Resources\UserMailMessageResource\Pages;

use App\Filament\UserPanel\Resources\UserMailMessageResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;

class ViewUserMailMessage extends ViewRecord
{
    protected static string $resource = UserMailMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Powrót')
                ->icon('heroicon-o-arrow-left')
                ->color('warning')
                ->url(UserMailMessageResource::getUrl('index')),
        ];
    }

    protected function getFooterActions(): array
    {
        return [
            Actions\Action::make('back-footer')
                ->label('Powrót')
                ->icon('heroicon-o-arrow-left')
                ->color('warning')
                ->url(UserMailMessageResource::getUrl('index')),
        ];
    }
} 