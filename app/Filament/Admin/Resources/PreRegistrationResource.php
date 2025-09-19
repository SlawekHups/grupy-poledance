<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PreRegistrationResource\Pages;
use App\Filament\Admin\Resources\PreRegistrationResource\RelationManagers;
use App\Models\PreRegistration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PreRegistrationResource extends Resource
{
    protected static ?string $model = PreRegistration::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    
    protected static ?string $navigationLabel = 'Pre-rejestracje';
    
    protected static ?string $modelLabel = 'Pre-rejestracja';
    
    protected static ?string $pluralModelLabel = 'Pre-rejestracje';
    
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Imię i nazwisko')
                    ->maxLength(255)
                    ->placeholder('Opcjonalne - można dodać później'),
                    
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->maxLength(255)
                    ->placeholder('Opcjonalne - można dodać później'),
                    
                Forms\Components\TextInput::make('phone')
                    ->label('Telefon')
                    ->tel()
                    ->maxLength(20)
                    ->placeholder('Opcjonalne - można dodać później'),
                    
                Forms\Components\TextInput::make('token')
                    ->label('Token')
                    ->disabled()
                    ->dehydrated(false)
                    ->helperText('Token jest generowany automatycznie'),
                    
                Forms\Components\DateTimePicker::make('expires_at')
                    ->label('Wygasa')
                    ->required()
                    ->native(false),
                    
                Forms\Components\Toggle::make('used')
                    ->label('Użyty')
                    ->disabled()
                    ->dehydrated(false),
                    
                Forms\Components\DateTimePicker::make('used_at')
                    ->label('Data użycia')
                    ->disabled()
                    ->dehydrated(false),
                    
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
                            Tables\Columns\TextColumn::make('name')
                                ->label('Imię i nazwisko')
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
                            Tables\Columns\TextColumn::make('email')
                                ->label('Email')
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
                            Tables\Columns\TextColumn::make('phone')
                                ->label('Telefon')
                                ->searchable()
                                ->limit(20),
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
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('view_form')
                        ->label('Zobacz formularz')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->url(fn ($record) => route('pre-register', $record->token))
                        ->openUrlInNewTab()
                        ->visible(fn ($record) => $record->isValid()),
                        
                    Tables\Actions\Action::make('copy_link')
                        ->label('Kopiuj link')
                        ->icon('heroicon-o-clipboard')
                        ->color('info')
                        ->modalHeading('Kopiuj link pre-rejestracji')
                        ->modalDescription('Kliknij przycisk poniżej, aby skopiować link do schowka')
                        ->modalContent(function ($record) {
                            $url = route('pre-register', $record->token);
                            return view('filament.admin.resources.pre-registration-resource.modals.copy-link-simple', [
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
                        ->modalHeading('Wyślij SMS z linkiem pre-rejestracji')
                        ->modalDescription(fn ($record) => $record->phone ? "SMS zostanie wysłany na numer: {$record->phone}" : 'Wprowadź numer telefonu, na który ma zostać wysłany SMS')
                        ->form([
                            \Filament\Forms\Components\TextInput::make('phone')
                                ->label('Numer telefonu')
                                ->tel()
                                ->required()
                                ->default(fn ($record) => $record->phone ?: '')
                                ->placeholder('Wprowadź numer telefonu')
                                ->helperText('Numer w formacie: 123456789'),
                            \Filament\Forms\Components\Textarea::make('custom_message')
                                ->label('Niestandardowa wiadomość (opcjonalne)')
                                ->rows(3)
                                ->placeholder('Pozostaw puste, aby użyć domyślnego szablonu z linkiem')
                                ->helperText('Jeśli zostawisz puste, zostanie użyty domyślny szablon SMS z linkiem pre-rejestracji'),
                            \Filament\Forms\Components\Placeholder::make('link_preview')
                                ->label('Podgląd linku')
                                ->content(fn ($record) => 'Link: ' . route('pre-register', $record->token))
                                ->columnSpanFull(),
                        ])
                        ->action(function ($record, array $data) {
                            try {
                                $smsService = new \App\Services\SmsService();
                                $url = route('pre-register', $record->token);
                                
                                // Jeśli podano niestandardową wiadomość, dodaj do niej link
                                if (!empty($data['custom_message'])) {
                                    $messageWithLink = $data['custom_message'] . "\n\nLink: " . $url;
                                    $result = $smsService->sendCustomMessage($data['phone'], $messageWithLink);
                                } else {
                                    // Użyj domyślnego szablonu pre-rejestracji
                                    $result = $smsService->sendPreRegistrationLink($data['phone'], $url);
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
                        
                    Tables\Actions\Action::make('convert_to_user')
                        ->label('Konwertuj na użytkownika')
                        ->icon('heroicon-o-user-plus')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Konwersja na użytkownika')
                        ->modalDescription('Wybierz grupę dla nowego użytkownika. Możesz zostawić puste, aby przypisać później.')
                        ->form([
                            \Filament\Forms\Components\Select::make('group_id')
                                ->label('Grupa')
                                ->options(\App\Models\Group::all()->pluck('name', 'id'))
                                ->searchable()
                                ->placeholder('Wybierz grupę (opcjonalne)')
                                ->helperText('Możesz przypisać użytkownika do grupy teraz lub później'),
                        ])
                        ->action(function ($record, array $data) {
                            try {
                                $user = $record->convertToUser($data['group_id'] ?? null);
                                
                                \Filament\Notifications\Notification::make()
                                    ->title('Konwersja zakończona')
                                    ->body("Użytkownik {$user->name} został utworzony" . 
                                          ($data['group_id'] ? " i przypisany do grupy" : ""))
                                    ->success()
                                    ->send();
                                    
                            } catch (\Exception $e) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Błąd konwersji')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        })
                        ->visible(fn ($record) => $record->canConvertToUser()),
                        
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
                    Tables\Actions\BulkAction::make('send_sms_bulk')
                        ->label('Wyślij SMS (masowo)')
                        ->icon('heroicon-o-chat-bubble-left-right')
                        ->color('success')
                        ->modalHeading('Masowe wysyłanie SMS')
                        ->modalDescription('Wyślij SMS z linkami pre-rejestracji do wybranych osób')
                        ->form([
                            \Filament\Forms\Components\Textarea::make('custom_message')
                                ->label('Niestandardowa wiadomość (opcjonalne)')
                                ->rows(3)
                                ->placeholder('Pozostaw puste, aby użyć domyślnego szablonu')
                                ->helperText('Jeśli zostawisz puste, zostanie użyty domyślny szablon SMS dla każdej osoby'),
                        ])
                        ->action(function ($records, array $data) {
                            $smsService = new \App\Services\SmsService();
                            $successCount = 0;
                            $errorCount = 0;
                            
                            foreach ($records as $record) {
                                if (!$record->isValid() || empty($record->phone)) {
                                    $errorCount++;
                                    continue;
                                }
                                
                                try {
                                    $url = route('pre-register', $record->token);
                                    
                                    if (!empty($data['custom_message'])) {
                                        $messageWithLink = $data['custom_message'] . "\n\nLink: " . $url;
                                        $result = $smsService->sendCustomMessage($record->phone, $messageWithLink);
                                    } else {
                                        $result = $smsService->sendPreRegistrationLink($record->phone, $url);
                                    }
                                    
                                    if ($result) {
                                        $successCount++;
                                    } else {
                                        $errorCount++;
                                    }
                                    
                                } catch (\Exception $e) {
                                    $errorCount++;
                                }
                            }
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Masowe wysyłanie SMS zakończone')
                                ->body("Wysłano: {$successCount}, Błędy: {$errorCount}")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                        
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListPreRegistrations::route('/'),
            'create' => Pages\CreatePreRegistration::route('/create'),
            'edit' => Pages\EditPreRegistration::route('/{record}/edit'),
        ];
    }
}
