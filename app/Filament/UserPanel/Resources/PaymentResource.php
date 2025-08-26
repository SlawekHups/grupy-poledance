<?php

namespace App\Filament\UserPanel\Resources;

use App\Filament\UserPanel\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;


class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Płatności użytkownika';
    protected static ?string $navigationGroup = 'Panel użytkownika';
    protected static ?int $navigationSort = 30;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('rowNumber')
                    ->label('Lp.')
                    ->state(function ($record, $livewire, $rowLoop) {
                        return $rowLoop->iteration;
                    }),
                TextColumn::make('month')
                    ->label('Miesiąc')
                    ->weight('bold'),
                TextColumn::make('amount')
                    ->label('Kwota')
                    ->money('PLN'),
                TextColumn::make('updated_at')
                    ->label('Data_zapłaty'),
                TextColumn::make('payment_link')
                    ->label('Płatność Online')
                    ->url(fn($record) => $record->payment_link)
                    ->openUrlInNewTab()
                    ->limit(30)
                    ->view('tables.columns.payment-link')
                    ->extraAttributes(['class' => 'text-center']),
                BooleanColumn::make('paid')
                    ->label('Opłacone'),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('paid')
                    ->label('Status płatności')
                    ->options([
                        '1' => 'Opłacone',
                        '0' => 'Nieopłacone',
                    ])
                    ->placeholder('Wszystkie')
                    ->query(function (Builder $query, array $data) {
                        $value = $data['value'] ?? ($data['paid'] ?? ($data['is'] ?? null));
                        if ($value === null || $value === '') {
                            return;
                        }
                        $query->where('paid', $value === '1');
                    }),
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
            // 'create' => Pages\CreatePayment::route('/create'),
            // 'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
    public static function canCreate(): bool
    {
        return false;
    }
    public static function getModelLabel(): string
    {
        return 'Płatność';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Płatności';
    }
}
