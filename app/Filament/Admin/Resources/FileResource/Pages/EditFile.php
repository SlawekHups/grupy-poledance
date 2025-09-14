<?php

namespace App\Filament\Admin\Resources\FileResource\Pages;

use App\Filament\Admin\Resources\FileResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditFile extends EditRecord
{
    protected static string $resource = FileResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Jeśli nie wybrano nowego pliku, usuń pole 'file' z danych
        if (empty($data['file'])) {
            unset($data['file']);
        } else {
            // Jeśli wybrano nowy plik, ustaw ścieżkę
            $data['path'] = $data['file'];
            unset($data['file']);
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('download')
                ->label('Pobierz plik')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    $filePath = Storage::disk('admin_files')->path($this->record->path);
                    $originalName = $this->record->original_name;
                    
                    if (file_exists($filePath)) {
                        return response()->download($filePath, $originalName);
                    }
                    
                    throw new \Exception('Plik nie istnieje: ' . $filePath);
                }),

            Actions\DeleteAction::make()
                ->before(function () {
                    // Usuń plik z dysku
                    if ($this->record->exists()) {
                        Storage::disk('admin_files')->delete($this->record->path);
                    }
                }),
        ];
    }
}
