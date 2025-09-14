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
                    }),


                // Informacja o obecnym pliku (tylko przy edycji)
                Forms\Components\Placeholder::make('current_file_info')
                    ->label('Obecny plik')
                    ->content(function ($record) {
                        if ($record && $record->path) {
                            return new \Illuminate\Support\HtmlString(
                                '<div class="bg-blue-50 border border-blue-200 rounded-lg p-4">' .
                                '<div class="flex items-center">' .
                                '<svg class="w-5 h-5 text-blue-500 mr-2" fill="currentColor" viewBox="0 0 20 20">' .
                                '<path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>' .
                                '</svg>' .
                                '<span class="text-blue-800 font-medium">' . $record->original_name . '</span>' .
                                '</div>' .
                                '<p class="text-blue-600 text-sm mt-1">Aby wgrać nowy plik, wybierz plik powyżej. Zostaniesz poproszony o potwierdzenie zamiany.</p>' .
                                '</div>'
                            );
                        }
                        return null;
                    })
                    ->visible(fn ($record) => $record !== null && $record->path),


                // Podgląd obrazka dla plików graficznych
                Forms\Components\Placeholder::make('image_preview')
                    ->label('Podgląd obrazka')
                    ->content(function ($record) {
                        if (!$record || !$record->mime_type || !$record->path) {
                            return new \Illuminate\Support\HtmlString('<p class="text-gray-500 text-sm">Brak podglądu</p>');
                        }
                        
                        // Sprawdź czy to obrazek
                        if (strpos($record->mime_type, 'image/') !== 0) {
                            return new \Illuminate\Support\HtmlString('<p class="text-gray-500 text-sm">Podgląd dostępny tylko dla obrazków</p>');
                        }
                        
                        // Użyj URL dla miniatur (działa dla wszystkich obrazków)
                        $imageUrl = $record->thumbnail_url;
                        
                        return new \Illuminate\Support\HtmlString(
                            '<div class="mt-2">' .
                            '<img src="' . $imageUrl . '" alt="Podgląd" class="max-w-xs max-h-48 object-contain border border-gray-300 rounded-lg shadow-sm" style="max-width: 300px; max-height: 192px;" />' .
                            '<p class="text-xs text-gray-500 mt-1">Typ: ' . $record->mime_type . 
                            ($record->size ? ' | Rozmiar: ' . number_format($record->size / 1024, 1) . ' KB' : '') .
                            '</p>' .
                            '</div>'
                        );
                    })
                    ->visible(function ($record) {
                        return $record && $record->mime_type && strpos($record->mime_type, 'image/') === 0;
                    }),

                Forms\Components\TextInput::make('name')
                    ->label('Nazwa pliku')
                    ->maxLength(255)
                    ->placeholder('Wpisz swoją nazwę lub zostaw puste dla automatycznej nazwy pliku')
                    ->helperText('Zostaw puste aby użyć oryginalnej nazwy pliku, lub wpisz swoją nazwę.'),

                Forms\Components\TextInput::make('original_name')
                    ->label('Oryginalna nazwa')
                    ->disabled()
                    ->dehydrated(true),

                Forms\Components\TextInput::make('mime_type')
                    ->label('Typ MIME')
                    ->disabled()
                    ->dehydrated(true), // Zmieniono na true żeby było zapisywane

                Forms\Components\TextInput::make('size')
                    ->label('Rozmiar (bajty)')
                    ->disabled()
                    ->dehydrated(true), // Zmieniono na true żeby było zapisywane

                Forms\Components\Select::make('category')
                    ->label('Kategoria')
                    ->options([
                        'general' => 'Ogólne',
                        'documents' => 'Dokumenty',
                        'images' => 'Obrazy',
                        'videos' => 'Filmy',
                        'audio' => 'Audio',
                        'archives' => 'Archiwa',
                        'backups' => 'Kopie zapasowe',
                    ])
                    ->default('general')
                    ->required(),

                Forms\Components\Toggle::make('is_public')
                    ->label('Publiczny')
                    ->helperText('Czy plik ma być dostępny publicznie')
                    ->default(false),

                Forms\Components\Textarea::make('description')
                    ->label('Opis')
                    ->maxLength(1000)
                    ->rows(3),
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
                            \Log::info('Generating thumbnail for file ' . $record->id . ': ' . $thumbnailUrl);
                            return new \Illuminate\Support\HtmlString(
                                '<div style="width: 48px; height: 48px; border: 1px solid #ccc; border-radius: 4px; overflow: hidden;">' .
                                '<img src="' . $thumbnailUrl . '" alt="Podgląd" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src=\'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDgiIGhlaWdodD0iNDgiIHZpZXdCb3g9IjAgMCA0OCA0OCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjQ4IiBoZWlnaHQ9IjQ4IiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik0yNCAzNkMzMC42Mjc0IDM2IDM2IDMwLjYyNzQgMzYgMjRDMzYgMTcuMzcyNiAzMC42Mjc0IDEyIDI0IDEyQzE3LjM3MjYgMTIgMTIgMTcuMzcyNiAxMiAyNEMxMiAzMC42Mjc0IDE3LjM3MjYgMzYgMjQgMzYiIHN0cm9rZT0iIzlDQTNBRiIgc3Ryb2tlLXdpZHRoPSIyIiBzdHJva2UtbGluZWNhcD0icm91bmQiIHN0cm9rZS1saW5lam9pbj0icm91bmQiLz4KPC9zdmc+Cg==\'" />' .
                                '</div>'
                            );
                        } else {
                            // Dla nie-obrazków - pokaż ikonę
                            return new \Illuminate\Support\HtmlString(
                                '<div class="flex items-center justify-center w-12 h-12 bg-gray-100 rounded text-gray-500">' .
                                '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">' .
                                $record->icon .
                                '</svg>' .
                                '</div>'
                            );
                        }
                    })
                    ->html(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nazwa pliku')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('size')
                    ->label('Pojemność')
                    ->formatStateUsing(function ($state) {
                        $bytes = $state;
                        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
                        
                        for ($i = 0; $bytes > 1024; $i++) {
                            $bytes /= 1024;
                        }
                        
                        return round($bytes, 2) . ' ' . $units[$i];
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('original_name')
                    ->label('Oryginalna nazwa')
                    ->searchable()
                    ->sortable()
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
                    ->sortable(),

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