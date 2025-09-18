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
            Actions\Action::make('back_to_list')
                ->label('Powrót do listy')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(fn () => route('filament.admin.resources.data-correction-links.index'))
                ->button(),
            Actions\DeleteAction::make(),
        ];
    }
}
