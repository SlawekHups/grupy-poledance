<?php

namespace App\Filament\Admin\Resources\DataCorrectionLinkResource\Pages;

use App\Filament\Admin\Resources\DataCorrectionLinkResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDataCorrectionLink extends EditRecord
{
    protected static string $resource = DataCorrectionLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
