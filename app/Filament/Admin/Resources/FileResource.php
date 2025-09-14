<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\FileResource\Pages;
use App\Models\File;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

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
                    ->live()
                    ->storeFileNamesIn('original_name')
                    ->helperText(fn ($record) => $record ? 'Zostaw puste aby zachować obecny plik' : 'Wybierz plik do przesłania')
                    ->afterStateUpdated(function ($state, $set, $get) {
                        \Log::info('=== File upload afterStateUpdated ===', [
                            'state' => $state,
                            'original_name' => $get('original_name'),
                            'file_exists' => $state ? file_exists(storage_path('app/admin-files/' . $state)) : false
                        ]);
                        
                        if ($state && $get('original_name')) {
                            // Ustaw nazwę pliku tylko jeśli pole jest puste
                            $currentName = $get('name');
                            if (empty($currentName)) {
                                $set('name', $get('original_name'));
                                \Log::info('Setting name from original (field was empty):', ['name' => $get('original_name')]);
                            } else {
                                \Log::info('Keeping user-provided name:', ['name' => $currentName]);
                            }
                            
                            // Ustaw MIME type i rozmiar
                            $filePath = storage_path('app/admin-files/' . $state);
                            if (file_exists($filePath)) {
                                $mimeType = mime_content_type($filePath);
                                $set('mime_type', $mimeType);
                                
                                $fileSize = filesize($filePath);
                                $set('size', $fileSize);
                                
                                \Log::info('File metadata set:', [
                                    'mime_type' => $mimeType,
                                    'size' => $fileSize
                                ]);
                            }
                        }
                    }),

                Forms\Components\TextInput::make('name')
                    ->label('Nazwa pliku')
                    ->maxLength(255)
                    ->live()
                    ->placeholder('Wpisz swoją nazwę lub zostaw puste dla automatycznej nazwy pliku')
                    ->helperText('Zostaw puste aby użyć oryginalnej nazwy pliku, lub wpisz swoją nazwę.')
                    ->afterStateUpdated(function ($state, $set, $get) {
                        \Log::info('Name field updated:', ['name' => $state]);
                    }),

                Forms\Components\TextInput::make('original_name')
                    ->label('Oryginalna nazwa')
                    ->disabled()
                    ->dehydrated(true)
                    ->live()
                    ->afterStateUpdated(function ($state, $set, $get) {
                        if ($state) {
                            \Log::info('Original name updated:', ['original_name' => $state]);
                            $fileName = pathinfo($state, PATHINFO_FILENAME);
                            \Log::info('Setting name field:', ['fileName' => $fileName]);
                            $set('name', $fileName);
                        }
                    }),

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
                Tables\Columns\IconColumn::make('icon')
                    ->label('')
                    ->getStateUsing(function ($record) {
                        return $record->icon;
                    })
                    ->size(Tables\Columns\IconColumn\IconColumnSize::Large),

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

                Tables\Columns\TextColumn::make('public_url')
                    ->label('Link publiczny')
                    ->getStateUsing(function ($record) {
                        if ($record->is_public) {
                            return 'Kopiuj link';
                        }
                        return 'Prywatny';
                    })
                    ->color(fn ($record) => $record->is_public ? 'success' : 'gray')
                    ->icon(fn ($record) => $record->is_public ? 'heroicon-o-link' : 'heroicon-o-lock-closed')
                    ->url(fn ($record) => $record->is_public ? '#' : null)
                    ->openUrlInNewTab(false)
                    ->extraAttributes(fn ($record) => $record->is_public ? [
                        'onclick' => "
                            navigator.clipboard.writeText('{$record->url}');
                            return false;
                        "
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