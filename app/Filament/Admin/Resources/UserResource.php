<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AddressRelationManagerResource\RelationManagers\AddressesRelationManager;
use App\Filament\Admin\Resources\UserResource\Pages;
use App\Filament\Admin\Resources\UserResource\RelationManagers;
use App\Models\User;
use App\Models\Group;
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
use Filament\Forms\Components\Textarea;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Livewire\WithFileUploads;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

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
            ->bulkActions([
                Tables\Actions\BulkAction::make('reset_passwords')
                    ->label('Resetuj hasła')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Resetuj hasła użytkowników')
                    ->modalDescription('Czy na pewno chcesz zresetować hasła wybranych użytkowników? Wszyscy otrzymają nowe zaproszenia do ustawienia haseł.')
                    ->modalSubmitActionLabel('Tak, resetuj hasła')
                    ->modalCancelActionLabel('Anuluj')
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Powód resetowania (opcjonalnie)')
                            ->placeholder('Np. Masowe resetowanie haseł dla wszystkich użytkowników')
                            ->rows(3),
                    ])
                    ->action(function (Collection $records, array $data) {
                        $admin = Auth::user();
                        $resetCount = 0;
                        
                        foreach ($records as $record) {
                            if ($record->password && $record->is_active) {
                                // Wyślij event resetowania hasła
                                \App\Events\PasswordResetRequested::dispatch(
                                    $record, 
                                    $admin, 
                                    $data['reason'] ?? '', 
                                    'bulk'
                                );
                                $resetCount++;
                            }
                        }
                        
                        if ($resetCount > 0) {
                            Notification::make()
                                ->title('Hasła zostały zresetowane')
                                ->body("Zresetowano hasła dla {$resetCount} użytkowników. Wszyscy otrzymają nowe zaproszenia.")
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Brak użytkowników do resetowania')
                                ->body('Wybrani użytkownicy nie mają haseł lub nie są aktywni.')
                                ->warning()
                                ->send();
                        }
                    })
                    ->deselectRecordsAfterCompletion(),
            ])
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
                    ->label('Regulamin')
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
                    ->icon('heroicon-o-arrow-up-tray')
                    ->modalHeading('Eksportuj użytkowników')
                    ->modalDescription('Wybierz typ eksportu')
                    ->form([
                        Forms\Components\Select::make('exportType')
                            ->label('Typ eksportu')
                            ->options([
                                'active' => 'Tylko aktywni użytkownicy',
                                'all' => 'Wszyscy użytkownicy',
                            ])
                            ->default('active')
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $exportType = $data['exportType'] ?? 'active';
                        
                        // Generuj CSV i zwróć jako download
                        $filename = 'users_export_' . $exportType . '_' . now()->format('Ymd_His') . '.csv';
                        
                        // Eksportuj użytkowników w zależności od typu
                        $query = \App\Models\User::where('role', 'user');
                        
                        if ($exportType === 'active') {
                            $query->where('is_active', true);
                        }
                        
                        $users = $query->select(['name', 'email', 'phone', 'group_id', 'amount', 'joined_at', 'is_active'])->get();

                        $callback = function() use ($users) {
                            $handle = fopen('php://output', 'w');
                            fputcsv($handle, ['name', 'email', 'phone', 'group_id', 'amount', 'joined_at', 'is_active']);
                            foreach ($users as $user) {
                                fputcsv($handle, [
                                    $user->name,
                                    $user->email,
                                    $user->phone,
                                    $user->group_id,
                                    $user->amount,
                                    $user->joined_at,
                                    $user->is_active ? 1 : 0,
                                ]);
                            }
                            fclose($handle);
                        };

                        $headers = [
                            'Content-Type' => 'text/csv',
                            'Content-Disposition' => "attachment; filename=\"$filename\"",
                        ];

                        return response()->stream($callback, 200, $headers);
                    })
                    ->color('success'),
                Action::make('import')
                    ->label('Importuj użytkowników')
                    ->icon('heroicon-o-arrow-down-tray')
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
                            
                            // Sprawdź czy plik został otwarty
                            if (!$handle) {
                                Notification::make()
                                    ->title('Błąd')
                                    ->body('Nie można otworzyć pliku CSV')
                                    ->danger()
                                    ->send();
                                return;
                            }
                            
                            $header = fgetcsv($handle);
                            
                            // Sprawdź czy nagłówek został odczytany
                            if (!$header) {
                                Notification::make()
                                    ->title('Błąd')
                                    ->body('Nie można odczytać nagłówka CSV')
                                    ->danger()
                                    ->send();
                                fclose($handle);
                                return;
                            }
                            
                            // Debug - sprawdź nagłówek
                            \Illuminate\Support\Facades\Log::info('CSV Header:', ['header' => $header]);
                            
                            while (($row = fgetcsv($handle)) !== false) {
                                // Debug - sprawdź każdy wiersz
                                \Illuminate\Support\Facades\Log::info('CSV Row:', ['row' => $row, 'header_count' => count($header), 'row_count' => count($row)]);
                                
                                // Sprawdź czy liczba kolumn odpowiada liczbie wartości
                                if (count($header) !== count($row)) {
                                    \Illuminate\Support\Facades\Log::info('Skipping row - column count mismatch', ['header_count' => count($header), 'row_count' => count($row)]);
                                    $skipped++;
                                    continue;
                                }
                                
                                // Sprawdź czy wiersz nie jest pusty
                                if (empty(array_filter($row))) {
                                    \Illuminate\Support\Facades\Log::info('Skipping empty row');
                                    $skipped++;
                                    continue;
                                }
                                
                                try {
                                    $rowData = array_combine($header, $row);
                                    \Illuminate\Support\Facades\Log::info('Row data created:', ['rowData' => $rowData]);
                                } catch (\Exception $e) {
                                    \Illuminate\Support\Facades\Log::error('Array combine error:', ['error' => $e->getMessage(), 'header' => $header, 'row' => $row, 'header_count' => count($header), 'row_count' => count($row)]);
                                    $skipped++;
                                    continue;
                                }
                                
                                // Nie importuj adminów
                                if (isset($rowData['role']) && $rowData['role'] === 'admin') {
                                    \Illuminate\Support\Facades\Log::info('Skipping admin user:', ['email' => $rowData['email'] ?? 'no email']);
                                    $skipped++;
                                    continue;
                                }
                                
                                // Sprawdź czy email jest wymagany
                                if (empty($rowData['email'])) {
                                    \Illuminate\Support\Facades\Log::info('Skipping row - empty email');
                                    $skipped++;
                                    continue;
                                }
                                
                                // Sprawdź czy nazwa jest wymagana
                                if (empty($rowData['name'])) {
                                    \Illuminate\Support\Facades\Log::info('Skipping row - empty name', ['email' => $rowData['email']]);
                                    $skipped++;
                                    continue;
                                }
                                
                                \Illuminate\Support\Facades\Log::info('Processing user:', ['email' => $rowData['email'], 'name' => $rowData['name']]);
                                
                                // Ustaw domyślne wartości tylko dla pustych pól
                                $rowData['phone'] = $rowData['phone'] ?? '';
                                $rowData['group_id'] = !empty($rowData['group_id']) ? (int)$rowData['group_id'] : null;
                                
                                // Zachowaj oryginalną wartość amount z CSV (nie konwertuj na int)
                                if (empty($rowData['amount'])) {
                                    $rowData['amount'] = 200; // domyślna wartość tylko jeśli puste
                                } else {
                                    $rowData['amount'] = (float)$rowData['amount']; // konwertuj na float aby zachować grosze
                                }
                                
                                $rowData['joined_at'] = !empty($rowData['joined_at']) ? $rowData['joined_at'] : now()->format('Y-m-d');
                                
                                // Zachowaj oryginalną wartość is_active z CSV
                                if (empty($rowData['is_active'])) {
                                    $rowData['is_active'] = 0; // domyślna wartość tylko jeśli puste
                                } else {
                                    $rowData['is_active'] = (bool)(int)$rowData['is_active']; // konwertuj na boolean
                                }
                                
                                \Illuminate\Support\Facades\Log::info('Final row data:', ['rowData' => $rowData]);
                                
                                $user = \App\Models\User::where('email', $rowData['email'])->first();
                                
                                if ($user) {
                                    // Nie nadpisuj admina
                                    if ($user->role === 'admin') {
                                        $skipped++;
                                        continue;
                                    }
                                    
                                    if ($action === 'update') {
                                        try {
                                            $user->update(collect($rowData)->except(['password', 'role'])->toArray());
                                            $updated++;
                                        } catch (\Exception $e) {
                                            $skipped++;
                                            \Illuminate\Support\Facades\Log::error('Update error for user ' . $rowData['email'] . ': ' . $e->getMessage());
                                        }
                                    } else {
                                        $skipped++;
                                    }
                                } else {
                                    try {
                                        $rowData['password'] = \Illuminate\Support\Facades\Hash::make(str()->random(12));
                                        $userData = collect($rowData)->except(['id', 'role'])->toArray();
                                        \Illuminate\Support\Facades\Log::info('Creating user with data:', ['userData' => $userData]);
                                        
                                        // Sprawdź czy wszystkie wymagane pola są obecne
                                        if (empty($userData['name']) || empty($userData['email'])) {
                                            \Illuminate\Support\Facades\Log::error('Missing required fields:', ['userData' => $userData]);
                                            $skipped++;
                                            continue;
                                        }
                                        
                                        $newUser = \App\Models\User::create($userData);
                                        \Illuminate\Support\Facades\Log::info('User created successfully:', ['user_id' => $newUser->id, 'email' => $newUser->email]);
                                        $added++;
                                    } catch (\Exception $e) {
                                        $skipped++;
                                        \Illuminate\Support\Facades\Log::error('Create error for user ' . $rowData['email'] . ': ' . $e->getMessage(), [
                                            'exception' => $e,
                                            'rowData' => $rowData,
                                            'trace' => $e->getTraceAsString()
                                        ]);
                                    }
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
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('send_message')
                        ->label('Wyślij wiadomość')
                        ->icon('heroicon-o-chat-bubble-left-right')
                        ->color('success')
                        ->size('sm')
                        ->tooltip('Wyślij wiadomość email do użytkownika')
                        ->visible(fn (User $record) => $record->is_active)
                        ->form([
                            Forms\Components\TextInput::make('subject')
                                ->label('Temat')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\RichEditor::make('content')
                                ->label('Treść wiadomości')
                                ->required()
                                ->toolbarButtons([
                                    'bold',
                                    'italic',
                                    'link',
                                    'bulletList',
                                    'orderedList',
                                ])
                                ->columnSpanFull(),
                        ])
                        ->action(function (User $record, array $data) {
                            // Wyślij email
                            \Illuminate\Support\Facades\Mail::to($record->email)->send(
                                new \App\Mail\UserMessageMail($record, $data['subject'], $data['content'])
                            );
                            
                            Notification::make()
                                ->title('Wiadomość wysłana')
                                ->body("Wiadomość została wysłana do użytkownika {$record->name} na adres: {$record->email}")
                                ->success()
                                ->send();
                        })
                        ->modalHeading(fn (User $record) => "Wyślij wiadomość do {$record->name}")
                        ->modalDescription(fn (User $record) => "Wiadomość zostanie wysłana na adres: {$record->email}")
                        ->modalSubmitActionLabel('Wyślij wiadomość'),
                    Tables\Actions\Action::make('send_payment_reminder')
                        ->label('Wyślij przypomnienie o płatności')
                        ->icon('heroicon-o-currency-dollar')
                        ->color('warning')
                        ->size('sm')
                        ->tooltip('Wyślij przypomnienie o zaległych płatnościach')
                        ->visible(fn (User $record) => $record->is_active)
                        ->action(function (User $record) {
                            try {
                                // Sprawdź zaległości w płatnościach
                                $unpaidPayments = \App\Models\Payment::where('user_id', $record->id)
                                    ->where('paid', false)
                                    ->where('month', '<=', now()->format('Y-m'))
                                    ->orderBy('month', 'asc')
                                    ->get();
                                
                                if ($unpaidPayments->isEmpty()) {
                                    Notification::make()
                                        ->title('Brak zaległości')
                                        ->body("Użytkownik {$record->name} nie ma zaległych płatności")
                                        ->warning()
                                        ->send();
                                    return;
                                }
                                
                                // Przygotuj dane do przypomnienia
                                $group = $record->group;
                                $totalAmount = $unpaidPayments->sum('amount');
                                $currentMonth = now()->format('Y-m');
                                $currentMonthPayment = $unpaidPayments->where('month', $currentMonth)->first();
                                
                                if ($currentMonthPayment) {
                                    $reminderType = 'bieżący';
                                    $subject = "Przypomnienie o płatności za {$currentMonth} - Grupa {$group->name}";
                                } else {
                                    $reminderType = 'zaległy';
                                    $subject = "PILNE: Zaległości w płatnościach - Grupa {$group->name}";
                                }
                                
                                // Generuj treść przypomnienia
                                $content = self::generateReminderContent($record, $group, $unpaidPayments, $totalAmount, $reminderType);
                                
                                // Wyślij email
                                \Illuminate\Support\Facades\Mail::to($record->email)->send(
                                    new \App\Mail\PaymentReminderMail($record, $subject, $content)
                                );
                                
                                Notification::make()
                                    ->title('Przypomnienie wysłane')
                                    ->body("Przypomnienie o płatnościach zostało wysłane do użytkownika {$record->name}")
                                    ->success()
                                    ->send();
                                
                                // Zaloguj wysłanie
                                \Illuminate\Support\Facades\Log::info("Ręcznie wysłano przypomnienie o płatności", [
                                    'user_id' => $record->id,
                                    'user_email' => $record->email,
                                    'group' => $group->name,
                                    'unpaid_count' => $unpaidPayments->count(),
                                    'total_amount' => $totalAmount
                                ]);
                                
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Błąd')
                                    ->body("Błąd podczas wysyłania przypomnienia: " . $e->getMessage())
                                    ->danger()
                                    ->send();
                                
                                \Illuminate\Support\Facades\Log::error("Błąd ręcznego wysyłania przypomnienia o płatności", [
                                    'user_id' => $record->id,
                                    'error' => $e->getMessage()
                                ]);
                            }
                        })
                        ->modalHeading(fn (User $record) => "Wyślij przypomnienie o płatnościach")
                        ->modalDescription(fn (User $record) => "System sprawdzi zaległości w płatnościach i wyśle odpowiednie przypomnienie na adres: {$record->email}")
                        ->modalSubmitActionLabel('Wyślij przypomnienie')
                        ->requiresConfirmation()
                        ->modalDescription(fn (User $record) => "Czy na pewno chcesz wysłać przypomnienie o płatnościach do użytkownika {$record->name}?\n\nSystem automatycznie:\n• Sprawdzi wszystkie zaległe płatności\n• Wygeneruje odpowiednią treść przypomnienia\n• Wyśle email na adres: {$record->email}"),
                    Tables\Actions\Action::make('reset_password')
                        ->label('Resetuj hasło')
                        ->icon('heroicon-o-key')
                        ->color('warning')
                        ->size('sm')
                        ->tooltip('Usuń hasło użytkownika i wyślij nowe zaproszenie (tylko dla aktywnych użytkowników)')
                        ->visible(fn (User $record) => $record->password && $record->is_active)
                        ->requiresConfirmation()
                        ->modalHeading('Resetuj hasło użytkownika')
                        ->modalDescription('Czy na pewno chcesz zresetować hasło użytkownika? Użytkownik otrzyma nowe zaproszenie do ustawienia hasła.')
                        ->modalSubmitActionLabel('Tak, resetuj hasło')
                        ->modalCancelActionLabel('Anuluj')
                        ->form([
                            Forms\Components\Textarea::make('reason')
                                ->label('Powód resetowania (opcjonalnie)')
                                ->placeholder('Np. Użytkownik zgłosił problem z logowaniem')
                                ->rows(3),
                        ])
                        ->action(function (User $record, array $data) {
                            $admin = Auth::user();
                            
                            \Illuminate\Support\Facades\Log::info('Akcja reset hasła uruchomiona', [
                                'user_id' => $record->id,
                                'user_email' => $record->email,
                                'admin_id' => $admin->id,
                                'action_type' => 'single'
                            ]);
                            
                            // Wyślij event resetowania hasła
                            \App\Events\PasswordResetRequested::dispatch(
                                $record, 
                                $admin, 
                                $data['reason'] ?? '', 
                                'single'
                            );
                            
                            Notification::make()
                                ->title('Hasło zostało zresetowane')
                                ->body("Użytkownik {$record->name} otrzyma nowe zaproszenie do ustawienia hasła.")
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\Action::make('export_user')
                        ->label('Eksportuj do CSV')
                        ->icon('heroicon-o-arrow-up-tray')
                        ->color('info')
                        ->size('sm')
                        ->tooltip('Eksportuj dane użytkownika do pliku CSV')
                        ->url(fn (User $record) => route('admin.export-user-csv', ['user' => $record->id]))
                        ->openUrlInNewTab(),

                ])
                    ->button()
                    ->label('Actions')
                    ->icon('heroicon-o-cog-6-tooth'),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('resend_invitation')
                        ->label('Wyślij zaproszenie')
                        ->icon('heroicon-o-envelope')
                        ->color('info')
                        ->size('sm')
                        ->tooltip('Wyślij ponownie link do ustawienia hasła (tylko dla aktywnych użytkowników)')
                        ->visible(fn (User $record) => !$record->password && $record->is_active)
                        ->action(function (User $record) {
                            \App\Events\UserInvited::dispatch($record);
                            Notification::make()
                                ->title('Zaproszenie wysłane')
                                ->body("Link do ustawienia hasła został wysłany na adres: {$record->email}")
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Wyślij ponownie zaproszenie')
                        ->modalDescription(fn (User $record) => "Czy na pewno chcesz wysłać ponownie link do ustawienia hasła dla aktywnego użytkownika {$record->name}?")
                        ->modalSubmitActionLabel('Wyślij zaproszenie'),
                ])
                    ->button()
                    ->label('Zaproszenia')
                    ->icon('heroicon-o-envelope')
                    ->color('info'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('resend_invitations')
                        ->label('Wyślij zaproszenia')
                        ->icon('heroicon-o-envelope')
                        ->color('info')
                        ->tooltip('Wyślij ponownie linki do ustawienia hasła dla aktywnych użytkowników bez hasła')
                        ->deselectRecordsAfterCompletion()
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $sentCount = 0;
                            $skippedCount = 0;
                            $inactiveCount = 0;
                            
                            foreach ($records as $record) {
                                if (!$record->is_active) {
                                    $inactiveCount++;
                                    continue;
                                }
                                
                                if (!$record->password) {
                                    \App\Events\UserInvited::dispatch($record);
                                    $sentCount++;
                                } else {
                                    $skippedCount++;
                                }
                            }
                            
                            $message = "Wysłano {$sentCount} zaproszeń";
                            if ($skippedCount > 0) {
                                $message .= " (pominięto {$skippedCount} użytkowników z już ustawionym hasłem)";
                            }
                            if ($inactiveCount > 0) {
                                $message .= " (pominięto {$inactiveCount} nieaktywnych użytkowników)";
                            }
                            
                            Notification::make()
                                ->title('Zaproszenia wysłane')
                                ->body($message)
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Wyślij ponownie zaproszenia')
                        ->modalDescription('Czy na pewno chcesz wysłać ponownie linki do ustawienia hasła dla aktywnych użytkowników bez hasła?')
                        ->modalSubmitActionLabel('Wyślij zaproszenia'),

                    Tables\Actions\BulkAction::make('send_messages')
                        ->label('Wyślij wiadomości')
                        ->icon('heroicon-o-chat-bubble-left-right')
                        ->color('success')
                        ->tooltip('Wyślij wiadomość email do wybranych użytkowników')
                        ->form([
                            Forms\Components\TextInput::make('subject')
                                ->label('Temat')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\RichEditor::make('content')
                                ->label('Treść wiadomości')
                                ->required()
                                ->toolbarButtons([
                                    'bold',
                                    'italic',
                                    'link',
                                    'bulletList',
                                    'orderedList',
                                ])
                                ->columnSpanFull(),
                        ])
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records, array $data) {
                            $sentCount = 0;
                            $skippedCount = 0;
                            
                            foreach ($records as $record) {
                                if (!$record->is_active) {
                                    $skippedCount++;
                                    continue;
                                }
                                
                                try {
                                    \Illuminate\Support\Facades\Mail::to($record->email)->send(
                                        new \App\Mail\UserMessageMail($record, $data['subject'], $data['content'])
                                    );
                                    $sentCount++;
                                } catch (\Exception $e) {
                                    $skippedCount++;
                                    \Illuminate\Support\Facades\Log::error("Błąd wysyłania wiadomości do {$record->email}: " . $e->getMessage());
                                }
                            }
                            
                            $message = "Wysłano {$sentCount} wiadomości";
                            if ($skippedCount > 0) {
                                $message .= " (pominięto {$skippedCount} nieaktywnych użytkowników)";
                            }
                            
                            Notification::make()
                                ->title('Wiadomości wysłane')
                                ->body($message)
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion()
                        ->modalHeading('Wyślij wiadomości do użytkowników')
                        ->modalDescription('Wiadomość zostanie wysłana do wszystkich wybranych aktywnych użytkowników.')
                        ->modalSubmitActionLabel('Wyślij wiadomości'),
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
            'edit' => Pages\EditUser::route('/{record}'),
        ];
    }
    
    /**
     * Generuje treść przypomnienia o płatnościach
     */
    private static function generateReminderContent(User $user, Group $group, $unpaidPayments, $totalAmount, $reminderType): string
    {
        $currentMonth = now()->format('Y-m');
        $currentMonthName = now()->translatedFormat('F Y');
        
        $content = "<h2>Cześć {$user->name}!</h2>";
        
        if ($reminderType === 'bieżący') {
            $content .= "<p>Przypominamy o płatności za <strong>{$currentMonthName}</strong> w grupie <strong>{$group->name}</strong>.</p>";
        } else {
            $content .= "<p><strong>PILNE:</strong> Masz zaległości w płatnościach za następujące miesiące:</p>";
            $monthsList = $unpaidPayments->map(function ($payment) {
                $date = \Carbon\Carbon::createFromFormat('Y-m', $payment->month);
                return $date->translatedFormat('F Y');
            })->implode(', ');
            $content .= "<ul><li>{$monthsList}</li></ul>";
        }
        
        $content .= "<div class='payment-summary'>";
        $content .= "<h3>Podsumowanie zaległości:</h3>";
        $content .= "<ul>";
        $content .= "<li>Liczba nieopłaconych miesięcy: <strong>{$unpaidPayments->count()}</strong></li>";
        $content .= "<li>Łączna kwota do zapłaty: <strong>{$totalAmount} zł</strong></li>";
        $content .= "</ul>";
        $content .= "</div>";
        
        $content .= "<div class='payment-details'>";
        $content .= "<h3>Szczegóły zaległości:</h3>";
        $content .= "<table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>";
        $content .= "<thead><tr style='background-color: #f3f4f6;'>";
        $content .= "<th style='padding: 10px; border: 1px solid #d1d5db; text-align: left;'>Miesiąc</th>";
        $content .= "<th style='padding: 10px; border: 1px solid #d1d5db; text-align: right;'>Kwota</th>";
        $content .= "</tr></thead><tbody>";
        
        foreach ($unpaidPayments as $payment) {
            $monthName = \Carbon\Carbon::createFromFormat('Y-m', $payment->month)->translatedFormat('F Y');
            $content .= "<tr>";
            $content .= "<td style='padding: 10px; border: 1px solid #d1d5db;'>{$monthName}</td>";
            $content .= "<td style='padding: 10px; border: 1px solid #d1d5db; text-align: right;'>{$payment->amount} zł</td>";
            $content .= "</tr>";
        }
        
        $content .= "</tbody></table>";
        $content .= "</div>";
        
        $content .= "<div class='action-required'>";
        $content .= "<h3>Co dalej?</h3>";
        $content .= "<p>Prosimy o uregulowanie zaległości w najbliższym możliwym terminie.</p>";
        
        if ($reminderType === 'zaległy') {
            $content .= "<p><strong>Uwaga:</strong> Długotrwałe zaległości mogą skutkować zawieszeniem uczestnictwa w zajęciach.</p>";
        }
        
        $content .= "</div>";
        
        $content .= "<div class='contact-info'>";
        $content .= "<p>Jeśli masz pytania lub chcesz ustalić plan spłaty, skontaktuj się z nami:</p>";
        $content .= "<ul>";
        $content .= "<li>Email: " . config('app.payment_reminder_email') . "</li>";
        $content .= "<li>Telefon: " . config('app.payment_reminder_phone') . "</li>";
        $content .= "</ul>";
        $content .= "</div>";
        
        $content .= "<p>Dziękujemy za zrozumienie!</p>";
        $content .= "<p><em>Zespół " . config('app.payment_reminder_company_name') . "</em></p>";
        
        return $content;
    }
    
    /**
     * Generuje zawartość CSV dla eksportu użytkownika
     */
    private static function generateCsvContent(User $record): string
    {
        $csvContent = "name,email,phone,group_id,amount,joined_at,is_active\n";
        $csvContent .= "\"{$record->name}\",\"{$record->email}\",\"{$record->phone}\",\"{$record->group_id}\",\"{$record->amount}\",\"{$record->joined_at}\",\"" . ($record->is_active ? 1 : 0) . "\"\n";
        
        return $csvContent;
    }
}
