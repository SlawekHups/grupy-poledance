<?php

namespace App\Filament\Admin\Resources\FileResource\Pages;

use App\Filament\Admin\Resources\FileResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class EditFile extends EditRecord
{
    protected static string $resource = FileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('download')
                ->label('Pobierz plik')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->visible(fn () => $this->record->path && Storage::disk('admin_files')->exists($this->record->path))
                ->action(function () {
                    $filePath = Storage::disk('admin_files')->path($this->record->path);
                    $originalName = $this->record->original_name;
                    
                    if (file_exists($filePath)) {
                        return response()->download($filePath, $originalName);
                    }
                    
                    throw new \Exception('Plik nie istnieje: ' . $filePath);
                }),

            Actions\Action::make('delete_file')
                ->label('Usuń obecny plik')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Usuń plik')
                ->modalDescription('Czy na pewno chcesz usunąć obecny plik? Ta operacja nie może zostać cofnięta.')
                ->action(function () {
                    \Log::info('=== USUWANIE PLIKU PRZEZ PRZYCISK ===', [
                        'file_path' => $this->record->path
                    ]);
                    
                    // Usuń plik z dysku
                    if (Storage::disk('admin_files')->exists($this->record->path)) {
                        Storage::disk('admin_files')->delete($this->record->path);
                        \Log::info('Plik usunięty z dysku:', ['path' => $this->record->path]);
                    }
                    
                    // Usuń cały rekord z bazy danych
                    $this->record->delete();
                    
                    \Log::info('Rekord usunięty z bazy danych:', ['id' => $this->record->id]);
                    
                    // Przekieruj do strony tworzenia nowego pliku
                    return redirect()->route('filament.admin.resources.files.create');
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

    public function mount(int | string $record): void
    {
        parent::mount($record);
        
        // Dodaj JavaScript do obsługi konfirmacji zamiany pliku
        $this->js('
            document.addEventListener("DOMContentLoaded", function() {
                const fileInput = document.querySelector("input[type=file]");
                if (fileInput) {
                    fileInput.addEventListener("change", function(e) {
                        if (e.target.files.length > 0 && "' . ($this->record->path ? 'true' : 'false') . '" === "true") {
                            const fileName = e.target.files[0].name;
                            const currentFile = "' . addslashes($this->record->original_name) . '";
                            
                            const confirmed = confirm(
                                "Czy na pewno chcesz zastąpić obecny plik \"" + currentFile + "\" nowym plikiem \"" + fileName + "\"?\n\n" +
                                "Stary plik zostanie automatycznie usunięty."
                            );
                            
                            if (!confirmed) {
                                // Jeśli użytkownik anulował, wyczyść input
                                e.target.value = "";
                                // Wyczyść pola formularza
                                const nameField = document.querySelector("input[name=\"data[name]\"]");
                                const originalNameField = document.querySelector("input[name=\"data[original_name]\"]");
                                if (nameField) nameField.value = "";
                                if (originalNameField) originalNameField.value = "";
                            }
                        }
                    });
                }
            });
        ');
    }



    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Jeśli wybrano nowy plik, ustaw dane z nowego pliku
        if (!empty($data['file'])) {
            \Log::info('=== ZMIANA PLIKU W EDYCJI ===', [
                'stary_plik' => $this->record->path,
                'nowy_plik' => $data['file']
            ]);

            // Usuń stary plik z dysku przed ustawieniem nowego
            if ($this->record->path && Storage::disk('admin_files')->exists($this->record->path)) {
                Storage::disk('admin_files')->delete($this->record->path);
                \Log::info('Stary plik usunięty z dysku:', ['path' => $this->record->path]);
            }

            // Ustaw nową ścieżkę
            $data['path'] = $data['file'];
            
            // Ustaw original_name z nowego pliku
            $data['original_name'] = basename($data['file']);
            
            // Ustaw name z original_name (bez rozszerzenia)
            $data['name'] = pathinfo($data['original_name'], PATHINFO_FILENAME);
            
            // Pobierz informacje o nowym pliku
            $newFilePath = Storage::disk('admin_files')->path($data['file']);
            if (file_exists($newFilePath)) {
                $data['size'] = filesize($newFilePath);
                $data['mime_type'] = mime_content_type($newFilePath);
            }
            
            \Log::info('=== ZAKTUALIZOWANE DANE ===', [
                'path' => $data['path'],
                'original_name' => $data['original_name'],
                'name' => $data['name'],
                'size' => $data['size'] ?? 'unknown',
                'mime_type' => $data['mime_type'] ?? 'unknown'
            ]);
        }
        
        // Usuń pole 'file' z danych (nie zapisujemy go do bazy)
        unset($data['file']);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterSave(): void
    {
        // Plik został już usunięty w mutateFormDataBeforeSave
        \Log::info('=== PO ZAPISANIU ===', [
            'new_path' => $this->record->path,
            'file_replaced' => true
        ]);
    }
}
