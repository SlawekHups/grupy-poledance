<?php

namespace App\Filament\Admin\Resources\UserMailMessageResource\Pages;

use App\Filament\Admin\Resources\UserMailMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserMailMessages extends ListRecords
{
    protected static string $resource = UserMailMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
} 