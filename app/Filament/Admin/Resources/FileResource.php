<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\FileResource\Pages;
use App\Models\File;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;

class FileResource extends Resource
{
    protected static ?string $model = File::class;

    protected static ?string $navigationIcon = 'heroicon-o-document';

    protected static ?string $navigationLabel = 'Pliki';

    protected static ?string $modelLabel = 'Plik';

    protected static ?string $pluralModelLabel = 'Pliki';

    protected static ?string $navigationGroup = 'Zarządzanie';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Sekcja główna - plik i podgląd
                Forms\Components\Section::make('Plik')
                    ->schema([
                        Forms\Components\FileUpload::make('file')
                            ->label('Wybierz plik')
                            ->required(fn ($record) => $record === null) // Wymagane tylko przy tworzeniu
                            ->maxSize(10240) // 10MB
                            ->disk('admin_files')
                            ->directory('uploads')
                            ->visibility('public')
                            ->preserveFilenames() // PROFESJONALNE: zachowaj oryginalne nazwy plików
                            ->helperText(fn ($record) => $record ? 'Kliknij "Wybierz plik" aby zastąpić obecny plik' : 'Wybierz plik do przesłania')
                            ->afterStateUpdated(function ($state, $set, $get) {
                                \Log::info('=== File upload afterStateUpdated ===', [
                                    'state' => $state
                                ]);
                                
                                if ($state) {
                                    // Tylko ustaw nazwy - metadata zostanie ustawiona w mutateFormDataBeforeSave
                                    $originalName = basename($state);
                                    $set('original_name', $originalName);
                                    
                                    $fileName = pathinfo($originalName, PATHINFO_FILENAME);
                                    $set('name', $fileName);
                                    
                                    \Log::info('Set names from uploaded file:', [
                                        'original_name' => $originalName,
                                        'name' => $fileName
                                    ]);
                                }
                            })
                            ->columnSpanFull(),

                        // Podgląd w kolumnach
                        Forms\Components\Grid::make(2)
                            ->schema([
                                // Podgląd obrazka dla plików graficznych
                                Forms\Components\Placeholder::make('image_preview')
                                    ->label('Podgląd obrazka')
                                    ->content(function ($record) {
                                        if (!$record || !$record->mime_type || !$record->path) {
                                            return new \Illuminate\Support\HtmlString('<p class="text-gray-500 text-sm">Brak podglądu</p>');
                                        }
                                        
                                        // Sprawdź czy to obrazek - pokaż oryginalny obraz
                                        if (strpos($record->mime_type, 'image/') === 0) {
                                            $imageUrl = $record->thumbnail_url;
                                            
                                            return new \Illuminate\Support\HtmlString(
                                                '<div class="mt-2">' .
                                                '<img src="' . $imageUrl . '" alt="Podgląd" class="max-w-xs max-h-48 object-contain border border-gray-300 rounded-lg shadow-sm" style="max-width: 300px; max-height: 192px;" />' .
                                                '<p class="text-xs text-gray-500 mt-1">Typ: ' . $record->mime_type . 
                                                ($record->size ? ' | Rozmiar: ' . number_format($record->size / 1024, 1) . ' KB' : '') .
                                                '</p>' .
                                                '</div>'
                                            );
                                        }
                                        
                                        return null;
                                    })
                                    ->visible(function ($record) {
                                        return $record && $record->mime_type && $record->path && strpos($record->mime_type, 'image/') === 0;
                                    }),

                                // Informacje o pliku dla plików niegraficznych
                                Forms\Components\Placeholder::make('file_info')
                                    ->label('Informacje o pliku')
                                    ->content(function ($record) {
                                        if (!$record || !$record->mime_type || !$record->path) {
                                            return new \Illuminate\Support\HtmlString('<p class="text-gray-500 text-sm">Brak informacji</p>');
                                        }
                                        
                                        $extension = strtolower(pathinfo($record->original_name, PATHINFO_EXTENSION));
                                        $fileType = 'Nieznany typ';
                                        $icon = '📄';
                                        
                                        // Określ typ pliku i ikonę
                                        if ($record->mime_type === 'application/pdf' || $extension === 'pdf') {
                                            $fileType = 'PDF';
                                            $icon = '📄';
                                        } elseif (strpos($record->mime_type, 'application/msword') === 0 || strpos($record->mime_type, 'application/vnd.openxmlformats-officedocument.wordprocessingml') === 0 || in_array($extension, ['doc', 'docx'])) {
                                            $fileType = 'Word Document';
                                            $icon = '📝';
                                        } elseif (strpos($record->mime_type, 'application/vnd.ms-excel') === 0 || strpos($record->mime_type, 'application/vnd.openxmlformats-officedocument.spreadsheetml') === 0 || in_array($extension, ['xls', 'xlsx'])) {
                                            $fileType = 'Excel Spreadsheet';
                                            $icon = '📊';
                                        } elseif ($record->mime_type === 'application/zip' || $extension === 'zip') {
                                            $fileType = 'ZIP Archive';
                                            $icon = '📦';
                                        } elseif (strpos($record->mime_type, 'text/') === 0 || in_array($extension, ['txt', 'csv', 'log', 'md'])) {
                                            $fileType = 'Text File';
                                            $icon = '📄';
                                        } elseif (strpos($record->mime_type, 'application/x-sh') === 0 || $extension === 'sh') {
                                            $fileType = 'Shell Script';
                                            $icon = '⚙️';
                                        } elseif (strpos($record->mime_type, 'application/octet-stream') === 0 || $extension === 'style') {
                                            $fileType = 'Style File';
                                            $icon = '🔧';
                                        } elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'])) {
                                            $fileType = 'Image';
                                            $icon = '🖼️';
                                        }
                                        
                                        return new \Illuminate\Support\HtmlString(
                                            '<div class="mt-2 p-4 bg-gray-50 border border-gray-200 rounded-lg">' .
                                            '<div class="flex items-center space-x-2">' .
                                            '<span class="text-2xl">' . $icon . '</span>' .
                                            '<div>' .
                                            '<p class="text-sm font-medium text-gray-900">' . $record->original_name . '</p>' .
                                            '<p class="text-xs text-gray-500">' . $fileType . ' • ' . $record->mime_type . 
                                            ($record->size ? ' • ' . number_format($record->size / 1024, 1) . ' KB' : '') .
                                            '</p>' .
                                            '</div>' .
                                            '</div>' .
                                            '</div>'
                                        );
                                    })
                                    ->visible(function ($record) {
                                        return $record && $record->mime_type && $record->path && strpos($record->mime_type, 'image/') !== 0;
                                    }),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columns(1),

                // Sekcja informacji o pliku
                Forms\Components\Section::make('Informacje o pliku')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nazwa pliku')
                                    ->maxLength(255)
                                    ->placeholder('Wpisz swoją nazwę lub zostaw puste dla automatycznej nazwy pliku'),

                                Forms\Components\TextInput::make('original_name')
                                    ->label('Oryginalna nazwa')
                                    ->maxLength(255)
                                    ->disabled()
                                    ->dehydrated(),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('category')
                                    ->label('Kategoria')
                                    ->options([
                                        'general' => 'Ogólne',
                                        'documents' => 'Dokumenty',
                                        'images' => 'Obrazy',
                                        'videos' => 'Wideo',
                                        'audio' => 'Audio',
                                        'archives' => 'Archiwa',
                                        'backups' => 'Kopie zapasowe',
                                    ])
                                    ->default('general'),

                                Forms\Components\TextInput::make('size')
                                    ->label('Rozmiar (KB)')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->formatStateUsing(fn ($state) => $state ? round($state / 1024, 1) : 0),
                            ]),

                        Forms\Components\Textarea::make('description')
                            ->label('Opis')
                            ->maxLength(1000)
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(1),

                // Sekcja ustawień na dole formularza
                Forms\Components\Section::make('Ustawienia')
                    ->schema([
                        Forms\Components\Toggle::make('is_public')
                            ->label('Plik publiczny')
                            ->helperText('Czy plik ma być dostępny publicznie przez link')
                            ->default(false)
                            ->inline(false),
                    ])
                    ->compact()
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('thumbnail')
                    ->label('')
                    ->getStateUsing(function ($record) {
                        if ($record->mime_type && strpos($record->mime_type, 'image/') === 0) {
                            // Dla obrazków - pokaż miniaturkę
                            $thumbnailUrl = $record->thumbnail_url;
                            return new \Illuminate\Support\HtmlString(
                                '<div style="width: 48px; height: 48px; border: 1px solid #ccc; border-radius: 4px; overflow: hidden;">' .
                                '<img src="' . $thumbnailUrl . '" alt="Podgląd" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src=\'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDgiIGhlaWdodD0iNDgiIHZpZXdCb3g9IjAgMCA0OCA0OCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjQ4IiBoZWlnaHQ9IjQ4IiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik0yNCAzNkMzMC42Mjc0IDM2IDM2IDMwLjYyNzQgMzYgMjRDMzYgMTcuMzcyNiAzMC42Mjc0IDEyIDI0IDEyQzE3LjM3MjYgMTIgMTIgMTcuMzcyNiAxMiAyNEMxMiAzMC42Mjc0IDE3LjM3MjYgMzYgMjQgMzYiIHN0cm9rZT0iIzlDQTNBRiIgc3Ryb2tlLXdpZHRoPSIyIiBzdHJva2UtbGluZWNhcD0icm91bmQiIHN0cm9rZS1saW5lam9pbj0icm91bmQiLz4KPC9zdmc+Cg==\'" />' .
                                '</div>'
                            );
                        } else {
                            // Dla nie-obrazków - pokaż emoji ikonę
                            $extension = strtolower(pathinfo($record->original_name, PATHINFO_EXTENSION));
                            $icon = '📄'; // Default icon
                            
                            // Określ ikonę na podstawie typu MIME i rozszerzenia
                            if ($record->mime_type === 'application/pdf' || $extension === 'pdf') {
                                $icon = '📄';
                            } elseif (strpos($record->mime_type, 'application/msword') === 0 || strpos($record->mime_type, 'application/vnd.openxmlformats-officedocument.wordprocessingml') === 0 || in_array($extension, ['doc', 'docx'])) {
                                $icon = '📝';
                            } elseif (strpos($record->mime_type, 'application/vnd.ms-excel') === 0 || strpos($record->mime_type, 'application/vnd.openxmlformats-officedocument.spreadsheetml') === 0 || in_array($extension, ['xls', 'xlsx'])) {
                                $icon = '📊';
                            } elseif ($record->mime_type === 'application/zip' || $extension === 'zip') {
                                $icon = '📦';
                            } elseif (strpos($record->mime_type, 'text/') === 0 || in_array($extension, ['txt', 'csv', 'log', 'md'])) {
                                $icon = '📄';
                            } elseif (strpos($record->mime_type, 'application/vnd.ms-powerpoint') === 0 || strpos($record->mime_type, 'application/vnd.openxmlformats-officedocument.presentationml') === 0 || in_array($extension, ['ppt', 'pptx'])) {
                                $icon = '📊';
                            } elseif (strpos($record->mime_type, 'application/x-sh') === 0 || $extension === 'sh') {
                                $icon = '⚙️';
                            } elseif (strpos($record->mime_type, 'application/octet-stream') === 0 || $extension === 'style') {
                                $icon = '🔧';
                            } elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'])) {
                                $icon = '🖼️';
                            }
                            
                            return new \Illuminate\Support\HtmlString(
                                '<div class="flex items-center justify-center w-12 h-12 bg-gray-50 border border-gray-200 rounded-lg">' .
                                '<span class="text-2xl">' . $icon . '</span>' .
                                '</div>'
                            );
                        }
                    })
                    ->html(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nazwa pliku')
                    ->searchable()
                    ->sortable()
                    ->limit(25)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 25 ? $state : null;
                    }),

                Tables\Columns\TextColumn::make('size')
                    ->label('Rozmiar')
                    ->formatStateUsing(function ($state) {
                        $bytes = $state;
                        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
                        
                        for ($i = 0; $bytes > 1024; $i++) {
                            $bytes /= 1024;
                        }
                        
                        return round($bytes, 2) . ' ' . $units[$i];
                    })
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('original_name')
                    ->label('Oryginalna nazwa')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 30 ? $state : null;
                    }),

                Tables\Columns\TextColumn::make('category')
                    ->label('Kategoria')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'general' => 'gray',
                        'documents' => 'blue',
                        'images' => 'green',
                        'videos' => 'purple',
                        'audio' => 'orange',
                        'archives' => 'red',
                        'backups' => 'amber',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('mime_type')
                    ->label('Typ MIME')
                    ->searchable()
                    ->sortable()
                    ->limit(20)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 20 ? $state : null;
                    }),

                Tables\Columns\IconColumn::make('is_public')
                    ->label('Publiczny')
                    ->boolean()
                    ->trueIcon('heroicon-o-eye')
                    ->falseIcon('heroicon-o-eye-slash')
                    ->trueColor('success')
                    ->falseColor('gray'),

                Tables\Columns\TextColumn::make('public_link')
                    ->label('Link publiczny')
                    ->getStateUsing(function ($record) {
                        if ($record->is_public) {
                            return 'Kopiuj link';
                        }
                        return 'Prywatny';
                    })
                    ->color(fn ($record) => $record->is_public ? 'success' : 'gray')
                    ->icon(fn ($record) => $record->is_public ? 'heroicon-o-link' : 'heroicon-o-lock-closed')
                    ->extraAttributes(fn ($record) => $record->is_public ? [
                        'onclick' => "navigator.clipboard.writeText('" . ($record->is_public ? url('admin-files/' . str_replace('uploads/', '', $record->path)) : '') . "'); return false;",
                        'style' => 'cursor: pointer;'
                    ] : [])
                    ->tooltip(fn ($record) => $record->is_public ? 'Kliknij aby skopiować link' : 'Plik prywatny'),


                Tables\Columns\TextColumn::make('created_at')
                    ->label('Utworzono')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Zaktualizowano')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Kategoria')
                    ->options([
                        'general' => 'Ogólne',
                        'documents' => 'Dokumenty',
                        'images' => 'Obrazy',
                        'videos' => 'Filmy',
                        'audio' => 'Audio',
                        'archives' => 'Archiwa',
                        'backups' => 'Kopie zapasowe',
                    ]),

                Tables\Filters\TernaryFilter::make('is_public')
                    ->label('Publiczny')
                    ->trueLabel('Tylko publiczne')
                    ->falseLabel('Tylko prywatne')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('Pobierz')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function ($record) {
                        $filePath = Storage::disk('admin_files')->path($record->path);
                        $originalName = $record->original_name;
                        
                        if (file_exists($filePath)) {
                            return response()->download($filePath, $originalName);
                        }
                        
                        throw new \Exception('Plik nie istnieje: ' . $filePath);
                    }),

                Tables\Actions\EditAction::make(),

                Tables\Actions\DeleteAction::make()
                    ->before(function ($record) {
                        // Usuń plik z dysku
                        if ($record->exists()) {
                            Storage::disk('admin_files')->delete($record->path);
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            // Usuń pliki z dysku
                            foreach ($records as $record) {
                                if ($record->exists()) {
                                    Storage::disk('admin_files')->delete($record->path);
                                }
                            }
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFiles::route('/'),
            'create' => Pages\CreateFile::route('/create'),
            'edit' => Pages\EditFile::route('/{record}/edit'),
        ];
    }


    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->latest();
    }
}