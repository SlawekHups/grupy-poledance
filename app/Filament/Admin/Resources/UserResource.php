<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AddressRelationManagerResource\RelationManagers\AddressesRelationManager;
use App\Filament\Admin\Resources\UserResource\Pages;
use App\Filament\Admin\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Components\MarkdownEditor;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Livewire\WithFileUploads;
use Filament\Notifications\Notification;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Użytkownicy i Grupy';
    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)
            ->whereNot('role', 'admin')
            ->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function getNavigationColor(): ?string
    {
        return static::getModel()::where('created_at', '>=', now()->subDays(7))
            ->whereNot('role', 'admin')
            ->exists()
            ? 'info'
            : null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Imię i nazwisko')
                    ->required()
                    ->minLength(3)
                    ->maxLength(45)
                    ->validationMessages([
                        'minLength' => 'Imię musi mieć minimum 3 znaki.',
                        'maxLength' => 'Imię może mieć maksymalnie 10 znaków.',
                    ]),
                TextInput::make('email')
                    ->label('E-mail')
                    ->required()
                    ->email()
                    ->rules([
                        fn($context, $record) => \Illuminate\Validation\Rule::unique('users', 'email')->ignore($record?->id),
                    ])
                    ->validationMessages([
                        'unique' => 'Ten e-mail już istnieje w systemie.',
                    ]),

                TextInput::make('phone')
                    ->label('Telefon')
                    ->tel()
                    ->required()
                    ->minLength(9)
                    ->maxLength(9)
                    ->rule(function () {
                        return \Illuminate\Validation\Rule::unique('users', 'phone')->ignore(request()->route('record'));
                    })
                    ->validationMessages([
                        'unique' => 'Ten numer telefonu już istnieje w systemie.',
                        'min' => 'Numer telefonu musi mieć co najmniej 9 cyfr.',
                        'max' => 'Numer telefonu nie może mieć więcej niż 15 znaków.',
                    ])
                    ->dehydrated(true)
                    ->afterStateHydrated(function (\Filament\Forms\Components\TextInput $component, $state) {
                        if (str_starts_with($state, '+48')) {
                            $component->state(substr($state, 3));
                        }
                    })
                    ->dehydrateStateUsing(function ($state) {
                        $number = preg_replace('/\D/', '', $state);
                        if (strlen($number) === 9) {
                            return '+48' . $number;
                        } elseif (str_starts_with($number, '48') && strlen($number) === 11) {
                            return '+' . $number;
                        } elseif (str_starts_with($number, '+48') && strlen($number) === 12) {
                            return $number;
                        }
                        return $state;
                    }),
                DatePicker::make('joined_at')->label('Data zapisu'),

                Select::make('group_id')
                    ->label('Grupa')
                    ->relationship('group', 'name')
                    ->searchable()
                    ->preload(),

                TextInput::make('password')
                    ->password()
                    ->label('Hasło')
                    ->required(fn(string $context) => $context === 'create')
                    ->dehydrated(fn($state) => filled($state))
                    ->dehydrateStateUsing(fn($state) => bcrypt($state))
                    ->maxLength(255),
                TextInput::make('amount')
                    ->label('Kwota miesięczna (zł)')
                    ->numeric()
                    ->required()
                    ->default(200),
                Forms\Components\DateTimePicker::make('terms_accepted_at')
                    ->label('Akceptacja regulaminu')
                    ->nullable()
                    ->default(null)
                    ->dehydrated(true)
                    ->dehydrateStateUsing(fn($state) => $state ?: null),
                    Toggle::make('is_active')->label('Aktywny'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Imię i nazwisko')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Telefon')
                    ->searchable(),
                TextColumn::make('group.name')
                    ->label('Grupa')
                    ->searchable(),
                TextColumn::make('amount')
                    ->label('Kwota (PLN)')
                    ->suffix(' zł')
                    ->searchable(),
                BooleanColumn::make('is_active')
                    ->label('Aktywny'),
                Tables\Columns\BooleanColumn::make('terms_accepted_at')
                    ->label('Regulamin zaakceptowany')
                    ->trueIcon('heroicon-o-document-check')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->state(fn($record) => !is_null($record->terms_accepted_at)),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->options([
                        'user' => 'Użytkownik',
                        'admin' => 'Administrator',
                    ])
                    ->label('Rola'),
                Tables\Filters\SelectFilter::make('group_id')
                    ->relationship('group', 'name')
                    ->label('Grupa')
                    ->searchable()
                    ->preload(),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('Wszystkie')
                    ->trueLabel('Aktywny')
                    ->falseLabel('Nieaktywny'),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Od'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Do'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->label('Data utworzenia'),
            ])
            ->headerActions([
                Action::make('export')
                    ->label('Eksportuj użytkowników')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action('exportUsers')
                    ->color('success'),
                Action::make('import')
                    ->label('Importuj użytkowników')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->modalHeading('Importuj użytkowników')
                    ->modalDescription('Wybierz plik CSV do importu')
                    ->form([
                        Forms\Components\FileUpload::make('file')
                            ->label('Plik CSV')
                            ->acceptedFileTypes(['text/csv', 'text/plain', '.csv'])
                            ->required(),
                        Forms\Components\Select::make('duplicateAction')
                            ->label('Duplikaty (email)')
                            ->options([
                                'skip' => 'Pomiń',
                                'update' => 'Aktualizuj',
                            ])
                            ->default('skip'),
                    ])
                    ->action(function (array $data) {
                        if (!isset($data['file'])) {
                            Notification::make()
                                ->title('Błąd')
                                ->body('Wybierz plik CSV')
                                ->danger()
                                ->send();
                            return;
                        }

                        $filePath = $data['file'];
                        $action = $data['duplicateAction'] ?? 'skip';
                        $added = $updated = $skipped = 0;
                        
                        // Debug - sprawdź ścieżkę pliku
                        \Illuminate\Support\Facades\Log::info('File path:', ['path' => $filePath]);
                        \Illuminate\Support\Facades\Log::info('File exists:', ['exists' => file_exists($filePath)]);
                        \Illuminate\Support\Facades\Log::info('Storage path:', ['storage' => storage_path('app/public/' . $filePath)]);
                        
                        try {
                            // Sprawdź czy plik istnieje
                            if (!file_exists($filePath)) {
                                // Spróbuj z storage path
                                $storagePath = storage_path('app/public/' . $filePath);
                                if (file_exists($storagePath)) {
                                    $filePath = $storagePath;
                                } else {
                                    Notification::make()
                                        ->title('Błąd')
                                        ->body('Plik nie istnieje. Ścieżka: ' . $filePath)
                                        ->danger()
                                        ->send();
                                    return;
                                }
                            }
                            
                            $handle = fopen($filePath, 'r');
                            $header = fgetcsv($handle);
                            
                            while (($row = fgetcsv($handle)) !== false) {
                                $rowData = array_combine($header, $row);
                                
                                // Nie importuj adminów
                                if (isset($rowData['role']) && $rowData['role'] === 'admin') {
                                    $skipped++;
                                    continue;
                                }
                                
                                // Obsługa pustych dat
                                if (empty($rowData['joined_at'])) {
                                    $rowData['joined_at'] = now()->format('Y-m-d');
                                }
                                
                                $user = \App\Models\User::where('email', $rowData['email'])->first();
                                
                                if ($user) {
                                    // Nie nadpisuj admina
                                    if ($user->role === 'admin') {
                                        $skipped++;
                                        continue;
                                    }
                                    
                                    if ($action === 'update') {
                                        $user->update(collect($rowData)->except(['password', 'role'])->toArray());
                                        $updated++;
                                    } else {
                                        $skipped++;
                                    }
                                } else {
                                    $rowData['password'] = \Illuminate\Support\Facades\Hash::make(str()->random(12));
                                    \App\Models\User::create(collect($rowData)->except(['id', 'role'])->toArray());
                                    $added++;
                                }
                            }
                            
                            fclose($handle);
                            Notification::make()
                                ->title('Import zakończony')
                                ->body("Dodano: $added, Zaktualizowano: $updated, Pominięto: $skipped")
                                ->success()
                                ->send();
                            
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Błąd podczas importu')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->color('warning')
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            AddressesRelationManager::class,
            \App\Filament\Admin\Resources\UserResource\RelationManagers\PaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
