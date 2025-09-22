<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\DataCorrectionLinkResource\Pages;
use App\Models\DataCorrectionLink;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DataCorrectionLinkResource extends Resource
{
    protected static ?string $model = DataCorrectionLink::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';
    
    protected static ?string $navigationLabel = 'Linki do poprawy danych';
    
    protected static ?string $modelLabel = 'Link do poprawy danych';
    
    protected static ?string $pluralModelLabel = 'Linki do poprawy danych';
    
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Użytkownik')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required()
                    ->disabled(fn ($record) => $record !== null),
                    
                Forms\Components\TextInput::make('token')
                    ->label('Token')
                    ->disabled()
                    ->dehydrated(false)
                    ->helperText('Token jest generowany automatycznie'),
                    
                Forms\Components\DateTimePicker::make('expires_at')
                    ->label('Wygasa')
                    ->required()
                    ->native(false)
                    ->default(now()->addHours(24)),
                    
                Forms\Components\Toggle::make('used')
                    ->label('Użyty')
                    ->disabled()
                    ->dehydrated(false),
                    
                Forms\Components\DateTimePicker::make('used_at')
                    ->label('Data użycia')
                    ->disabled()
                    ->dehydrated(false),
                    
                Forms\Components\CheckboxList::make('allowed_fields')
                    ->label('Pola do edycji')
                    ->options([
                        'name' => 'Imię i nazwisko (zawsze dostępne)',
                        'email' => 'Email',
                        'phone' => 'Telefon',
                    ])
                    ->columns(1)
                    ->default(['name', 'email', 'phone'])
                    ->required()
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Zawsze dodaj 'name' do zaznaczonych pól
                        if (!in_array('name', $state ?? [])) {
                            $state = array_merge($state ?? [], ['name']);
                            $set('allowed_fields', $state);
                        }
                    }),
                    
                Forms\Components\Textarea::make('notes')
                    ->label('Notatki')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->contentGrid([
                'sm' => 1,
                'md' => 1,
                'xl' => 1,
            ])
            ->recordClasses('rounded-xl border bg-white shadow-sm hover:shadow-md transition hover:bg-gray-50')
            ->columns([
                Tables\Columns\Layout\Panel::make([
                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\Layout\Split::make([
                            Tables\Columns\TextColumn::make('user.name')
                                ->label('Użytkownik')
                                ->searchable()
                                ->sortable()
                                ->weight('bold')
                                ->limit(40),
                            Tables\Columns\TextColumn::make('expires_at')
                                ->label('Wygasa')
                                ->dateTime('d.m.Y H:i')
                                ->sortable()
                                ->color(fn ($record) => $record->expires_at < now() ? 'danger' : 'success')
                                ->alignRight(),
                        ])->extraAttributes(['class' => 'justify-between items-start']),

                        Tables\Columns\Layout\Split::make([
                            Tables\Columns\TextColumn::make('user.email')
                                ->label('Email użytkownika')
                                ->searchable()
                                ->sortable()
                                ->limit(30),
                            Tables\Columns\TextColumn::make('used')
                                ->label('Status')
                                ->badge()
                                ->color(fn ($state) => $state ? 'success' : 'danger')
                                ->formatStateUsing(fn ($state) => $state ? 'Użyty' : 'Nie użyty')
                                ->alignRight(),
                        ])->extraAttributes(['class' => 'justify-between items-center']),

                        Tables\Columns\Layout\Split::make([
                            Tables\Columns\TextColumn::make('allowed_fields')
                                ->label('Pola do edycji')
                                ->badge()
                                ->separator(', ')
                                ->formatStateUsing(function ($state) {
                                    $labels = [
                                        'name' => 'Imię',
                                        'email' => 'Email',
                                        'phone' => 'Telefon',
                                    ];
                                    return collect($state)->map(fn($field) => $labels[$field] ?? $field)->join(', ');
                                }),
                            Tables\Columns\TextColumn::make('token')
                                ->label('Token')
                                ->limit(8)
                                ->copyable()
                                ->tooltip('Kliknij aby skopiować')
                                ->alignRight(),
                        ])->extraAttributes(['class' => 'justify-between items-center']),

                        Tables\Columns\TextColumn::make('used_at')
                            ->label('Data użycia')
                            ->dateTime('d.m.Y H:i')
                            ->sortable()
                            ->placeholder('Nie użyty')
                            ->extraAttributes(['class' => 'text-sm text-gray-600']),
                    ]),
                ]),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('used')
                    ->label('Status')
                    ->trueLabel('Użyte')
                    ->falseLabel('Nie użyte')
                    ->native(false),
                    
                Tables\Filters\Filter::make('expired')
                    ->label('Wygasłe')
                    ->query(fn (Builder $query): Builder => $query->where('expires_at', '<', now()))
                    ->toggle(),
                    
                Tables\Filters\Filter::make('valid')
                    ->label('Ważne')
                    ->query(fn (Builder $query): Builder => $query->where('expires_at', '>', now())->where('used', false))
                    ->toggle(),
                    
                Tables\Filters\Filter::make('old')
                    ->label('Stare (30+ dni)')
                    ->query(fn (Builder $query): Builder => $query->where('created_at', '<', now()->subDays(30)))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('view_form')
                        ->label('Zobacz formularz')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->url(fn ($record) => route('data-correction', $record->token))
                        ->openUrlInNewTab()
                        ->visible(fn ($record) => $record->isValid()),
                        
                    Tables\Actions\Action::make('copy_link')
                        ->label('Kopiuj link')
                        ->icon('heroicon-o-clipboard')
                        ->color('info')
                        ->modalHeading('Kopiuj link do poprawy danych')
                        ->modalDescription('Kliknij przycisk poniżej, aby skopiować link do schowka')
                        ->modalContent(function ($record) {
                            $url = route('data-correction', $record->token);
                            return view('filament.admin.resources.data-correction-link-resource.modals.copy-link', [
                                'url' => $url,
                                'token' => $record->token,
                                'record' => $record
                            ]);
                        })
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Zamknij')
                        ->visible(fn ($record) => $record->isValid()),
                        
                    Tables\Actions\Action::make('send_sms')
                        ->label('Wyślij SMS')
                        ->icon('heroicon-o-chat-bubble-left-right')
                        ->color('success')
                        ->modalHeading('Wyślij SMS z linkiem do poprawy danych')
                        ->modalDescription(fn ($record) => $record->user->phone ? "SMS zostanie wysłany na numer: {$record->user->phone}" : 'Wprowadź numer telefonu, na który ma zostać wysłany SMS')
                        ->form([
                            \Filament\Forms\Components\TextInput::make('phone')
                                ->label('Numer telefonu')
                                ->tel()
                                ->required()
                                ->default(fn ($record) => $record->user->phone ?: '')
                                ->placeholder('Wprowadź numer telefonu')
                                ->helperText('Numer w formacie: 123456789')
                                ->rules([
                                    'required',
                                    'string',
                                    'min:9',
                                    'max:15',
                                    'regex:/^(\+?[0-9]{9,15})$/',
                                ])
                                ->validationMessages([
                                    'required' => 'Numer telefonu jest wymagany',
                                    'min' => 'Numer telefonu musi mieć co najmniej 9 cyfr',
                                    'max' => 'Numer telefonu może mieć maksymalnie 15 cyfr',
                                    'regex' => 'Numer telefonu musi zawierać 9-15 cyfr i opcjonalnie + na początku',
                                ]),
                            \Filament\Forms\Components\Textarea::make('custom_message')
                                ->label('Niestandardowa wiadomość (opcjonalne)')
                                ->rows(3)
                                ->placeholder('Pozostaw puste, aby użyć domyślnego szablonu z linkiem')
                                ->helperText('Jeśli zostawisz puste, zostanie użyty domyślny szablon SMS z linkiem do poprawy danych'),
                            \Filament\Forms\Components\Placeholder::make('link_preview')
                                ->label('Podgląd linku')
                                ->content(fn ($record) => 'Link: ' . route('data-correction', $record->token))
                                ->columnSpanFull(),
                        ])
                        ->action(function ($record, array $data) {
                            try {
                                $smsService = new \App\Services\SmsService();
                                $url = route('data-correction', $record->token);
                                
                                // Jeśli podano niestandardową wiadomość, dodaj do niej link
                                if (!empty($data['custom_message'])) {
                                    $messageWithLink = $data['custom_message'] . "\n\nLink: " . $url;
                                    $result = $smsService->sendCustomMessage($data['phone'], $messageWithLink);
                                } else {
                                    // Użyj domyślnego szablonu poprawy danych
                                    $result = $smsService->sendDataCorrectionLink($data['phone'], $url);
                                }
                                
                                if ($result) {
                                    \Filament\Notifications\Notification::make()
                                        ->title('SMS wysłany pomyślnie')
                                        ->body("SMS został wysłany na numer {$data['phone']}")
                                        ->success()
                                        ->send();
                                } else {
                                    \Filament\Notifications\Notification::make()
                                        ->title('Błąd wysyłania SMS')
                                        ->body('Nie udało się wysłać SMS. Sprawdź logi.')
                                        ->danger()
                                        ->send();
                                }
                                
                            } catch (\Exception $e) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Błąd wysyłania SMS')
                                    ->body('Błąd: ' . $e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        })
                        ->visible(fn ($record) => $record->isValid()),
                        
                    Tables\Actions\Action::make('send_email')
                        ->label('Wyślij Email')
                        ->icon('heroicon-o-envelope')
                        ->color('info')
                        ->modalHeading('Wyślij Email z linkiem do poprawy danych')
                        ->modalDescription(fn ($record) => $record->user->email ? "Email zostanie wysłany na adres: {$record->user->email}" : 'Wprowadź adres email, na który ma zostać wysłany email')
                        ->form([
                            \Filament\Forms\Components\TextInput::make('email')
                                ->label('Adres email')
                                ->email()
                                ->required()
                                ->default(fn ($record) => $record->user->email ?: '')
                                ->placeholder('Wprowadź adres email')
                                ->helperText('Adres email w formacie: user@example.com')
                                ->rules([
                                    'required',
                                    'email',
                                    'max:255',
                                ])
                                ->validationMessages([
                                    'required' => 'Adres email jest wymagany',
                                    'email' => 'Adres email musi być prawidłowy',
                                    'max' => 'Adres email może mieć maksymalnie 255 znaków',
                                ]),
                            \Filament\Forms\Components\Textarea::make('custom_message')
                                ->label('Niestandardowa wiadomość (opcjonalne)')
                                ->rows(3)
                                ->placeholder('Pozostaw puste, aby użyć domyślnego szablonu z linkiem')
                                ->helperText('Jeśli zostawisz puste, zostanie użyty domyślny szablon email z linkiem do poprawy danych'),
                            \Filament\Forms\Components\Placeholder::make('link_preview')
                                ->label('Podgląd linku')
                                ->content(fn ($record) => 'Link: ' . route('data-correction', $record->token))
                                ->columnSpanFull(),
                        ])
                        ->action(function ($record, array $data) {
                            try {
                                $emailService = new \App\Services\EmailService();
                                $url = route('data-correction', $record->token);
                                
                                // Jeśli podano niestandardową wiadomość, użyj jej
                                if (!empty($data['custom_message'])) {
                                    $result = $emailService->sendCustomEmailWithLink(
                                        $data['email'],
                                        'Link do poprawy danych - Grupy Poledance',
                                        $data['custom_message'],
                                        $url
                                    );
                                } else {
                                    // Użyj domyślnego szablonu poprawy danych
                                    $result = $emailService->sendDataCorrectionLink($data['email'], $url);
                                }
                                
                                if ($result) {
                                    \Filament\Notifications\Notification::make()
                                        ->title('Email wysłany pomyślnie')
                                        ->body("Email został wysłany na adres {$data['email']}")
                                        ->success()
                                        ->send();
                                } else {
                                    \Filament\Notifications\Notification::make()
                                        ->title('Błąd wysyłania Email')
                                        ->body('Nie udało się wysłać email. Sprawdź logi.')
                                        ->danger()
                                        ->send();
                                }
                                
                            } catch (\Exception $e) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Błąd wysyłania Email')
                                    ->body('Błąd: ' . $e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        })
                        ->visible(fn ($record) => $record->isValid()),
                        
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
                ->label('Akcje')
                ->icon('heroicon-o-ellipsis-vertical')
                ->size('sm')
                ->color('gray')
                ->button()
                ->extraAttributes(['class' => 'w-full sm:w-auto'])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    Tables\Actions\BulkAction::make('delete_used')
                        ->label('Usuń użyte')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Usuń użyte linki')
                        ->modalDescription('Czy na pewno chcesz usunąć wszystkie zaznaczone użyte linki?')
                        ->modalSubmitActionLabel('Tak, usuń użyte')
                        ->action(function ($records) {
                            $count = $records->where('used', true)->count();
                            $records->where('used', true)->each->delete();
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Użyte linki usunięte')
                                ->body("Usunięto {$count} użytych linków")
                                ->success()
                                ->send();
                        }),
                        
                    Tables\Actions\BulkAction::make('delete_expired')
                        ->label('Usuń przeterminowane')
                        ->icon('heroicon-o-clock')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Usuń przeterminowane linki')
                        ->modalDescription('Czy na pewno chcesz usunąć wszystkie zaznaczone przeterminowane linki?')
                        ->modalSubmitActionLabel('Tak, usuń przeterminowane')
                        ->action(function ($records) {
                            $count = $records->where('expires_at', '<', now())->count();
                            $records->where('expires_at', '<', now())->each->delete();
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Przeterminowane linki usunięte')
                                ->body("Usunięto {$count} przeterminowanych linków")
                                ->success()
                                ->send();
                        }),
                        
                    Tables\Actions\BulkAction::make('delete_old')
                        ->label('Usuń stare (30+ dni)')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Usuń stare linki')
                        ->modalDescription('Czy na pewno chcesz usunąć wszystkie zaznaczone linki starsze niż 30 dni?')
                        ->modalSubmitActionLabel('Tak, usuń stare')
                        ->action(function ($records) {
                            $cutoffDate = now()->subDays(30);
                            $count = $records->where('created_at', '<', $cutoffDate)->count();
                            $records->where('created_at', '<', $cutoffDate)->each->delete();
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Stare linki usunięte')
                                ->body("Usunięto {$count} starych linków")
                                ->success()
                                ->send();
                        }),
                ])
                ->extraAttributes(['class' => 'flex flex-col sm:flex-row gap-2 w-full sm:w-auto'])
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListDataCorrectionLinks::route('/'),
            'create' => Pages\CreateDataCorrectionLink::route('/create'),
            'edit' => Pages\EditDataCorrectionLink::route('/{record}/edit'),
        ];
    }
}
