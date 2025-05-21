<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PaymentResource\Pages;
use App\Filament\Admin\Resources\PaymentResource\RelationManagers;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Carbon\Carbon;


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
                    ->disabled(fn(?Payment $record) => $record !== null)
                    ->reactive() // pozwala reagować na zmianę
                    ->afterStateUpdated(function ($state, callable $set) {
                        $user = \App\Models\User::find($state);
                        if ($user) {
                            $set('amount', $user->amount); // ustaw kwotę z User
                        }
                    }),

                Select::make('month')
                    ->label('Miesiąc')
                    ->options(
                        collect(range(-12, 12))->mapWithKeys(function ($i) {
                            $date = now()->addMonths($i);
                            return [
                                $date->format('Y-m') => mb_strtoupper($date->translatedFormat('F Y'), 'UTF-8'), // np. "maj 2025"
                            ];
                        })->toArray()
                    )
                    ->default(now()->format('Y-m'))
                    ->required(),

                TextInput::make('amount')
                    ->label('Kwota (PLN)')
                    ->numeric()
                    ->suffix('zł')
                    ->step(0.01)
                    ->required(),

                TextInput::make('payment_link')
                    ->label('Link do płatności')
                    ->url()
                    ->prefix('https://'),

                Toggle::make('paid')
                    ->label('Opłacone'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Użytkownik')
                    ->searchable()
                    ->url(fn($record) => route('filament.admin.resources.users.edit', $record->user_id)),
                TextColumn::make('month')->label('Miesiąc'),
                TextColumn::make('amount')->label('Kwota')->money('PLN'),
                TextColumn::make(name: 'updated_at')->label('Data_zapłaty'),
                BooleanColumn::make('paid')
                    ->label('Opłacone')
                    ->trueIcon('heroicon-o-shield-check')
                    ->falseIcon('heroicon-o-currency-dollar')
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('paid')
                    ->label('Status płatności')
                    ->trueLabel('Opłacone')
                    ->falseLabel('Nieopłacone')
                    ->default(null),
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
    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $user = \App\Models\User::find($data['user_id']);
        if ($user) {
            $data['amount'] = $user->amount; // kopiujemy na stałe do płatności
        }

        return $data;
    }
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->orderBy('paid') // false (0) na górze, true (1) niżej
            ->orderByDesc('updated_at'); // dodatkowo sortuj wg daty
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
    public static function getTabs(): array
    {
        return [
            'Wszystkie' => Payment::query(),
            'Opłacone' => Payment::query()->where('paid', true),
            'Nieopłacone' => Payment::query()->where('paid', false),
        ];
    }
}
