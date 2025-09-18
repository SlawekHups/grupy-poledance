<?php

namespace App\Filament\Admin\Resources\DataCorrectionLinkResource\Pages;

use App\Filament\Admin\Resources\DataCorrectionLinkResource;
use App\Models\DataCorrectionLink;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDataCorrectionLink extends CreateRecord
{
    protected static string $resource = DataCorrectionLinkResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Generuj token automatycznie
        $data['token'] = DataCorrectionLink::generateToken();
        
        // Ustaw domyślne pola do edycji jeśli nie wybrano
        if (empty($data['allowed_fields'])) {
            $data['allowed_fields'] = ['email', 'phone', 'name'];
        }
        
        return $data;
    }
}
