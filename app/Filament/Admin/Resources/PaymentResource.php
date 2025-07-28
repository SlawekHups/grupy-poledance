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

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Finanse';
    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('paid', false)
            ->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $unpaidCount = static::getModel()::where('paid', false)->count();
        return $unpaidCount > 0 ? 'danger' : 'success';
    }

    public static function getNavigationColor(): ?string
    {
        return static::getModel()::where('paid', false)->exists() 
            ? 'danger'
            : null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label('Użytkownik')
                    ->relationship(
                        'user',
                        'name',
                        fn (Builder $query) => $query->whereNot('role', 'admin')->orderBy('name')
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $user = \App\Models\User::find($state);
                            if ($user) {
                                $set('amount', number_format($user->amount, 2));
                            }
                        }
                    }),

                Select::make('month')
                    ->label('Miesiąc')
                    ->options(function() {
                        $options = [];
                        for ($i = -12; $i <= 12; $i++) {
                            $date = now()->addMonths($i)->startOfMonth();
                            $options[$date->format('Y-m')] = mb_strtoupper($date->translatedFormat('F Y'), 'UTF-8');
                        }
                        return $options;
                    })
                    ->default(now()->format('Y-m'))
                    ->required(),

                TextInput::make('amount')
                    ->label('Kwota (PLN)')
                    ->numeric()
                    ->suffix('zł')
                    ->required()
                    ->step(0.01)
                    ->default(0),

                TextInput::make('payment_link')
                    ->label('Link do płatności')
                    ->url()
                    ->prefix('https://')
                    ->columnSpanFull(),

                Toggle::make('paid')
                    ->label('Opłacone')
                    ->default(false),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Użytkownik')
                    ->searchable()
                    ->url(fn($record) => route('filament.admin.resources.users.edit', $record->user_id)),
                TextColumn::make('month')
                    ->label('Miesiąc')
                    ->searchable(),
                TextColumn::make('amount')
                    ->label('Kwota')
                    ->money('PLN')
                    ->searchable(),
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
                Tables\Filters\Filter::make('updated_at')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Od'),
                        Forms\Components\DatePicker::make('to')
                            ->label('Do'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('updated_at', '>=', $date),
                            )
                            ->when(
                                $data['to'],
                                fn (Builder $query, $date): Builder => $query->whereDate('updated_at', '<=', $date),
                            );
                    })
                    ->label('Data aktualizacji'),
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
    public static function getModelLabel(): string
    {
        return 'Płatności';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Płatność';
    }
}
