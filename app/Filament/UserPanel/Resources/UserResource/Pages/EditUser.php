<?php

namespace App\Filament\UserPanel\Resources\UserResource\Pages;

use App\Filament\UserPanel\Resources\UserResource;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getMaxContentWidth(): ?string
    {
        // Węższa szerokość dla lepszej czytelności na mobile
        return '4xl';
    }
}
