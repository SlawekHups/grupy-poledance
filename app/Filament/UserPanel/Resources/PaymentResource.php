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
            ->contentGrid([
                'md' => 1,
                'xl' => 1,
            ])
            ->recordClasses('rounded-xl border bg-white shadow-sm hover:shadow-md transition hover:bg-gray-50')
            ->columns([
                Tables\Columns\Layout\Panel::make([
                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\Layout\Split::make([
                            TextColumn::make('month')
                                ->label('Miesiąc')
                                ->weight('bold'),
                            TextColumn::make('amount')
                                ->label('Kwota')
                                ->money('PLN')
                                ->weight('bold')
                                ->color(fn($record) => ($record && $record->paid) ? 'success' : 'danger')
                                ->alignRight(),
                        ])->extraAttributes(['class' => 'justify-between items-center']),

                        Tables\Columns\Layout\Split::make([
                            TextColumn::make('updated_at')
                                ->label('Data_zapłaty')
                                ->dateTime('Y-m-d H:i'),
                            BooleanColumn::make('paid')
                                ->label('Opłacone')
                                ->alignRight(),
                        ])->extraAttributes(['class' => 'justify-between items-center']),

                        Tables\Columns\ViewColumn::make('pay_button')
                            ->label('Płatność Online')
                            ->view('filament.user.widgets.pay-button')
                            ->visible(fn($record) => ($record && $record->payment_link && !$record->paid))
                            ->extraAttributes(['class' => 'mt-2']),
                    ])->space(2),
                ])->extraAttributes(['class' => 'p-4']),
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
