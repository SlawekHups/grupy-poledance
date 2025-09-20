<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SmsLogResource\Pages;
use App\Filament\Admin\Resources\SmsLogResource\RelationManagers;
use App\Models\SmsLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SmsLogResource extends Resource
{
    protected static ?string $model = SmsLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    
    protected static ?string $navigationLabel = 'Logi SMS';
    
    protected static ?string $modelLabel = 'Log SMS';
    
    protected static ?string $pluralModelLabel = 'Logi SMS';
    
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->required()
                    ->maxLength(20),
                Forms\Components\Textarea::make('message')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('type')
                    ->required()
                    ->maxLength(50)
                    ->default('general'),
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\TextInput::make('message_id')
                    ->maxLength(100),
                Forms\Components\TextInput::make('cost')
                    ->numeric()
                    ->prefix('$'),
                Forms\Components\Textarea::make('error_message')
                    ->columnSpanFull(),
                Forms\Components\DateTimePicker::make('sent_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('phone')
                    ->label('Numer telefonu')
                    ->searchable()
                    ->copyable()
                    ->tooltip('Kliknij aby skopiować'),
                    
                Tables\Columns\TextColumn::make('type')
                    ->label('Typ')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pre_registration' => 'success',
                        'password_reset' => 'warning',
                        'payment_reminder' => 'info',
                        'data_correction' => 'gray',
                        default => 'primary',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pre_registration' => 'Pre-rejestracja',
                        'password_reset' => 'Reset hasła',
                        'payment_reminder' => 'Przypomnienie',
                        'data_correction' => 'Poprawa danych',
                        'test' => 'Test',
                        default => ucfirst($state),
                    }),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'sent' => 'success',
                        'error' => 'danger',
                        'pending' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'sent' => 'Wysłany',
                        'error' => 'Błąd',
                        'pending' => 'Oczekujący',
                        default => ucfirst($state),
                    }),
                    
                Tables\Columns\TextColumn::make('message')
                    ->label('Wiadomość')
                    ->limit(50)
                    ->tooltip(function (SmsLog $record): string {
                        return $record->message;
                    }),
                    
                Tables\Columns\TextColumn::make('message_id')
                    ->label('ID SMS')
                    ->searchable()
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('cost')
                    ->label('Koszt')
                    ->money('PLN')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('sent_at')
                    ->label('Wysłany')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Utworzony')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'sent' => 'Wysłany',
                        'error' => 'Błąd',
                        'pending' => 'Oczekujący',
                    ]),
                    
                Tables\Filters\SelectFilter::make('type')
                    ->label('Typ')
                    ->options([
                        'pre_registration' => 'Pre-rejestracja',
                        'password_reset' => 'Reset hasła',
                        'payment_reminder' => 'Przypomnienie',
                        'data_correction' => 'Poprawa danych',
                        'test' => 'Test',
                        'general' => 'Ogólny',
                    ]),
                    
                Tables\Filters\Filter::make('sent_today')
                    ->label('Wysłane dziś')
                    ->query(fn (Builder $query): Builder => $query->whereDate('sent_at', today())),
                    
                Tables\Filters\Filter::make('errors_only')
                    ->label('Tylko błędy')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'error')),
            ])
            ->actions([
                Tables\Actions\Action::make('check_balance')
                    ->label('Sprawdź saldo')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('info')
                    ->action(function () {
                        $smsService = new \App\Services\SmsService();
                        $balance = $smsService->getBalance();
                        
                        if ($balance !== null) {
                            \Filament\Notifications\Notification::make()
                                ->title('Saldo SMS API')
                                ->body("Dostępne środki: " . number_format($balance, 2) . " PLN (" . number_format($balance, 0) . " punktów)")
                                ->success()
                                ->send();
                        } else {
                            \Filament\Notifications\Notification::make()
                                ->title('Błąd pobierania salda')
                                ->body('Nie udało się pobrać salda SMS API. Sprawdź logi.')
                                ->danger()
                                ->send();
                        }
                    }),
                Tables\Actions\ViewAction::make()
                    ->modalHeading('Szczegóły SMS')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Zamknij')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('phone')
                            ->label('Numer telefonu')
                            ->disabled(),
                        \Filament\Forms\Components\Select::make('type')
                            ->label('Typ SMS')
                            ->disabled()
                            ->options([
                                'pre_registration' => 'Pre-rejestracja',
                                'password_reset' => 'Reset hasła',
                                'payment_reminder' => 'Przypomnienie',
                                'data_correction' => 'Poprawa danych',
                                'test' => 'Test',
                                'general' => 'Ogólny',
                            ]),
                        \Filament\Forms\Components\Select::make('status')
                            ->label('Status')
                            ->disabled()
                            ->options([
                                'sent' => 'Wysłany',
                                'error' => 'Błąd',
                                'pending' => 'Oczekujący',
                            ]),
                        \Filament\Forms\Components\Textarea::make('message')
                            ->label('Wiadomość')
                            ->disabled()
                            ->rows(6),
                        \Filament\Forms\Components\TextInput::make('message_id')
                            ->label('ID SMS')
                            ->disabled(),
                        \Filament\Forms\Components\TextInput::make('cost')
                            ->label('Koszt')
                            ->disabled()
                            ->suffix('PLN')
                            ->placeholder('0.17 PLN (standardowy koszt)'),
                        \Filament\Forms\Components\Textarea::make('error_message')
                            ->label('Błąd')
                            ->disabled()
                            ->visible(fn ($record) => !empty($record->error_message))
                            ->rows(3),
                        \Filament\Forms\Components\DateTimePicker::make('sent_at')
                            ->label('Data wysłania')
                            ->disabled(),
                        \Filament\Forms\Components\DateTimePicker::make('created_at')
                            ->label('Data utworzenia')
                            ->disabled(),
                    ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListSmsLogs::route('/'),
            // Usunięto create i edit - logi SMS są tylko do odczytu
        ];
    }
}
