<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use App\Filament\Admin\Resources\UserResource\Widgets\UserIndividualStats;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    public function mount(int | string $record): void
    {
        parent::mount($record);
        
        // Ustaw domyślną zakładkę na "Płatność" (index 1)
        $this->activeRelationManager = 1;
    }

    public function getActiveTab(): ?int
    {
        // Jeśli nie ma aktywnej zakładki, ustaw domyślnie na "Płatność"
        return $this->activeRelationManager ?? 1;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }
}
