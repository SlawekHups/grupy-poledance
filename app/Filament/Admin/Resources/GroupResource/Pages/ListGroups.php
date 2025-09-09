<?php

namespace App\Filament\Admin\Resources\GroupResource\Pages;

use App\Filament\Admin\Resources\GroupResource;
use App\Filament\Admin\Resources\GroupResource\Widgets\GroupStats;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGroups extends ListRecords
{
    protected static string $resource = GroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('info')
                ->label('Opis grup')
                ->icon('heroicon-o-information-circle')
                ->color('info')
                ->modalHeading('Opis systemu grup i widżetów')
                ->modalContent(view('filament.admin.pages.groups-info'))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Zamknij'),
            Actions\CreateAction::make(),
        ];
    }
    protected function getHeaderWidgets(): array
    {
        return [
            GroupStats::class,
        ];
    }
}
