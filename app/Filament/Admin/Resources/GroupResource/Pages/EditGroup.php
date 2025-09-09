<?php

namespace App\Filament\Admin\Resources\GroupResource\Pages;

use App\Filament\Admin\Resources\GroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGroup extends EditRecord
{
    protected static string $resource = GroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('enableEdit')
                ->label('Edytuj')
                ->icon('heroicon-o-pencil-square')
                ->color('info')
                ->url(fn ($record) => route('filament.admin.resources.groups.edit', ['record' => $record, 'edit' => 1]))
                ->visible(fn () => ! request()->boolean('edit', false)),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        // Po zapisie wracamy do widoku bez trybu edycji (kafelka)
        return route('filament.admin.resources.groups.edit', ['record' => $this->record->getKey()]);
    }
}
