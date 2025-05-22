<?php

namespace App\Filament\Admin\Resources\UserResource\RelationManagers;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Forms;
use Filament\Notifications\Notification;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments'; // nazwa relacji w modelu User

    protected static ?string $title = 'Płatności użytkownika';

    public function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                TextInput::make('month')
                    ->label('Miesiąc')
                    ->disabled(),
                TextInput::make('amount')
                    ->label('Kwota (PLN)')
                    ->numeric()
                    ->suffix('zł'),
                Toggle::make('paid')->label('Opłacone'),
                TextInput::make('payment_link')
                    ->label('Link do płatności')
                    ->url()
                    ->prefix('https://'),
            ]);
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('month')->label('Miesiąc'),
                TextColumn::make('amount')->label('Kwota')->money('PLN'),
                BooleanColumn::make('paid')->label('Opłacone')
                    ->trueIcon('heroicon-o-shield-check')
                    ->falseIcon('heroicon-o-currency-dollar'),
                TextColumn::make('updated_at')->label('Data_zapłaty')->dateTime('d.m.Y H:i'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('paid')
                    ->label('Status płatności')
                    ->trueLabel('Opłacone')
                    ->falseLabel('Nieopłacone'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                // Dodaj przycisk "Oznacz jako opłacone/nieopłacone"
                Action::make('togglePaid')
                    ->label(fn($record) => $record->paid ? 'Oznacz jako nieopłacone' : 'Oznacz jako opłacone')
                    ->icon(fn($record) => $record->paid ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn($record) => $record->paid ? 'warning' : 'success')
                    ->requiresConfirmation()
                    ->modalHeading('Potwierdź zmianę statusu płatności')
                    ->modalDescription(
                        fn($record) => $record->paid
                            ? 'Czy na pewno oznaczyć tę płatność jako NIEOPŁACONĄ?'
                            : 'Czy na pewno oznaczyć tę płatność jako OPŁACONĄ?'
                    )
                    ->action(function ($record) {
                        $record->paid = !$record->paid;
                        if ($record->paid) {
                            $record->payment_link = null;
                            $record->save();

                            // Wyślij notyfikację o usunięciu linku
                            Notification::make()
                                ->success()
                                ->title('Link do płatności został usunięty')
                                ->body('Oznaczyłeś płatność jako opłaconą. Link został automatycznie wyczyszczony.')
                                ->send();
                        } else {
                            $record->save();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
