<?php

namespace App\Filament\Admin\Resources\FileResource\Pages;

use App\Filament\Admin\Resources\FileResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateFile extends CreateRecord
{
    protected static string $resource = FileResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Debug: sprawdź dane przed przetworzeniem
        \Log::info('=== CREATE FILE - Data before processing ===', [
            'file' => $data['file'] ?? 'NOT SET',
            'name' => $data['name'] ?? 'NOT SET',
            'original_name' => $data['original_name'] ?? 'NOT SET',
            'mime_type' => $data['mime_type'] ?? 'NOT SET',
            'size' => $data['size'] ?? 'NOT SET'
        ]);
        
        // Ustaw uploaded_by na ID aktualnie zalogowanego użytkownika
        $data['uploaded_by'] = auth()->id();
        
        // Ustaw ścieżkę pliku
        if (isset($data['file'])) {
            $data['path'] = $data['file'];
            unset($data['file']);
        }

        // Ustaw domyślną nazwę jeśli użytkownik zostawił puste pole
        if (empty($data['name']) && !empty($data['original_name'])) {
            \Log::info('User left name empty, setting from original_name:', [
                'original_name' => $data['original_name'],
                'current_name' => $data['name'] ?? 'EMPTY'
            ]);
            $data['name'] = $data['original_name'];
        }
        
        // Walidacja - upewnij się że nazwa nie jest pusta
        if (empty($data['name'])) {
            throw new \Exception('Nazwa pliku jest wymagana. Zostaw puste pole aby użyć oryginalnej nazwy pliku.');
        }
        
        // Upewnij się, że original_name jest ustawione
        if (empty($data['original_name']) && !empty($data['name'])) {
            $data['original_name'] = $data['name'];
        }
        
        \Log::info('Final name and original_name:', [
            'name' => $data['name'] ?? 'NOT SET',
            'original_name' => $data['original_name'] ?? 'NOT SET'
        ]);

        // Upewnij się, że original_name jest ustawione
        if (empty($data['original_name']) && !empty($data['name'])) {
            $data['original_name'] = $data['name'];
        }

        // Upewnij się, że mime_type jest ustawione
        if (empty($data['mime_type'])) {
            $originalName = $data['original_name'] ?? $data['name'] ?? '';
            if ($originalName) {
                $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
                $mimeTypes = [
                    // Obrazy
                    'jpg' => 'image/jpeg',
                    'jpeg' => 'image/jpeg',
                    'png' => 'image/png',
                    'gif' => 'image/gif',
                    'webp' => 'image/webp',
                    'svg' => 'image/svg+xml',
                    'bmp' => 'image/bmp',
                    'tiff' => 'image/tiff',
                    'ico' => 'image/x-icon',
                    // Dokumenty PDF
                    'pdf' => 'application/pdf',
                    // Dokumenty Microsoft Office
                    'doc' => 'application/msword',
                    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'xls' => 'application/vnd.ms-excel',
                    'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'ppt' => 'application/vnd.ms-powerpoint',
                    'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                    // Tekst
                    'txt' => 'text/plain',
                    'csv' => 'text/csv',
                    'html' => 'text/html',
                    'htm' => 'text/html',
                    'css' => 'text/css',
                    'js' => 'text/javascript',
                    'json' => 'application/json',
                    'xml' => 'application/xml',
                    // Bazy danych
                    'sql' => 'application/sql',
                    'db' => 'application/x-sqlite3',
                    // Archiwa
                    'zip' => 'application/zip',
                    'rar' => 'application/x-rar-compressed',
                    '7z' => 'application/x-7z-compressed',
                    'gz' => 'application/gzip',
                    'tar' => 'application/x-tar',
                    // Wideo
                    'mp4' => 'video/mp4',
                    'avi' => 'video/x-msvideo',
                    'mov' => 'video/quicktime',
                    'wmv' => 'video/x-ms-wmv',
                    'webm' => 'video/webm',
                    'mkv' => 'video/x-matroska',
                    // Audio
                    'mp3' => 'audio/mpeg',
                    'wav' => 'audio/wav',
                    'ogg' => 'audio/ogg',
                    'm4a' => 'audio/mp4',
                    'flac' => 'audio/flac',
                    // Inne
                    'log' => 'text/plain',
                    'md' => 'text/markdown',
                    'rtf' => 'application/rtf',
                    'sh' => 'application/x-sh',
                    'doc' => 'application/msword',
                ];
                $data['mime_type'] = $mimeTypes[$extension] ?? 'application/octet-stream';
            } else {
                $data['mime_type'] = 'application/octet-stream';
            }
        }

        // Upewnij się, że size jest ustawione
        if (empty($data['size']) || $data['size'] === 0) {
            // Spróbuj pobrać rozmiar pliku z dysku
            if (isset($data['path'])) {
                try {
                    $filePath = Storage::disk('admin_files')->path($data['path']);
                    if (file_exists($filePath)) {
                        $data['size'] = filesize($filePath);
                    } else {
                        $data['size'] = 0;
                    }
                } catch (\Exception $e) {
                    $data['size'] = 0;
                }
            } else {
                $data['size'] = 0;
            }
        }

        // Debug: sprawdź dane po przetworzeniu
        \Log::info('File upload data after processing:', $data);

        return $data;
    }

    protected function afterCreate(): void
    {
        // Utwórz symbolic link jeśli nie istnieje
        if (!file_exists(public_path('admin-files'))) {
            \Artisan::call('storage:link');
        }
    }
}
