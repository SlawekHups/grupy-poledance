<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Filament\Resources\PaymentResource\RelationManagers;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label('Użytkownik')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->disabled(fn(?Payment $record) => $record !== null),

                TextInput::make('month')
                    ->label('Miesiąc')
                    ->placeholder('np. 2025-05')
                    ->required(),

                TextInput::make('amount')
                    ->label('Kwota (PLN)')
                    ->numeric()
                    ->suffix('zł')
                    ->step(0.01),

                TextInput::make('payment_link')
                    ->label('Link do płatności')
                    ->url()
                    ->prefix('https://'),

                Toggle::make('paid')->label('Opłacone'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('Użytkownik')->searchable(),
                TextColumn::make('month')->label('Miesiąc'),
                TextColumn::make('amount')->label('Kwota')->money('PLN'),
                BooleanColumn::make('paid')->label('Opłacone'),
                TextColumn::make(name: 'updated_at')->label('Data_zapłaty'),

            ])
            ->filters([
                //
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
