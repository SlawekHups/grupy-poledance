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
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Finanse';
    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return \App\Services\NavigationBadgeCacheService::getPaymentBadge();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $unpaidCount = \App\Services\NavigationBadgeCacheService::getPaymentBadge();
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
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name', fn (Builder $query) => $query->where('role', '!=', 'admin'))
                    ->label('Użytkownik')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $user = \App\Models\User::find($state);
                            if ($user) {
                                $set('amount', number_format((float) $user->amount, 2));
                            }
                        }
                    }),

                Forms\Components\Select::make('month')
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

                Forms\Components\TextInput::make('amount')
                    ->label('Kwota (PLN)')
                    ->numeric()
                    ->required()
                    ->step(0.01)
                    ->suffix('zł'),

                Forms\Components\Toggle::make('paid')
                    ->label('Opłacone')
                    ->default(false)
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $set('payment_link', null);
                        }
                    }),

                Forms\Components\TextInput::make('payment_link')
                    ->label('Link do płatności')
                    ->url()
                    ->prefix('https://')
                    ->visible(fn (callable $get) => !$get('paid'))
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('notes')
                    ->label('Notatki')
                    ->placeholder('Dodatkowe informacje o płatności')
                    ->columnSpanFull()
                    ->rows(3),

                Forms\Components\DateTimePicker::make('updated_at')
                    ->label('Data zapłaty')
                    ->displayFormat('d.m.Y H:i')
                    ->disabled()
                    ->dehydrated(false)
                    ->visible(fn ($record) => $record && $record->paid)
                    ->helperText('Data ostatniej aktualizacji płatności')
                    ->columnSpanFull(),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->contentGrid([
                'sm' => 1,
                'md' => 1,
                'lg' => 1,
                'xl' => 1,
            ])
            ->recordUrl(fn ($record) => route('filament.admin.resources.payments.edit', ['record' => $record]))
            ->recordClasses(function ($record) {
                $classes = ['rounded-xl border bg-white shadow-sm hover:shadow-md transition'];
                // Kolor obramowania/tła wg statusu płatności
                if ($record->paid) {
                    $classes[] = 'hover:bg-green-50';
                    $classes[] = 'border-green-200';
                } else {
                    $classes[] = 'hover:bg-red-50';
                    $classes[] = 'border-red-200';
                }
                return implode(' ', $classes);
            })
            ->columns([
                Tables\Columns\Layout\Panel::make([
                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\Layout\Split::make([
                            Tables\Columns\TextColumn::make('user.name')
                                ->label('Użytkownik')
                                ->searchable()
                                ->sortable()
                                ->weight('bold')
                                ->size('sm')
                                ->extraAttributes(['class' => 'text-sm sm:text-base md:text-lg lg:text-xl']),
                            Tables\Columns\TextColumn::make('paid')
                                ->label('Status')
                                ->badge()
                                ->formatStateUsing(fn (bool $state): string => $state ? 'Opłacone' : 'Nieopłacone')
                                ->color(fn (bool $state): string => $state ? 'success' : 'warning')
                                ->alignRight(),
                        ])->extraAttributes(['class' => 'justify-between items-start']),

                        Tables\Columns\Layout\Split::make([
                            Tables\Columns\TextColumn::make('month')
                                ->label('Miesiąc')
                                ->formatStateUsing(fn (string $state): string => mb_strtoupper(Carbon::createFromFormat('Y-m', $state)->translatedFormat('F Y')))
                                ->sortable()
                                ->extraAttributes(['class' => 'text-xs sm:text-sm']),
                            Tables\Columns\TextColumn::make('amount')
                                ->label('Kwota')
                                ->money('pln')
                                ->alignRight()
                                ->sortable()
                                ->extraAttributes(['class' => 'text-xs sm:text-sm font-medium']),
                        ])->extraAttributes(['class' => 'justify-between items-center']),

                        Tables\Columns\Layout\Split::make([
                            Tables\Columns\TextColumn::make('payment_date_label')
                                ->label('Data zapłaty')
                                ->icon('heroicon-o-calendar')
                                ->state(fn ($record): string => $record->paid && $record->updated_at ? \Carbon\Carbon::parse($record->updated_at)->format('d.m.Y') : '—')
                                ->color(fn ($record): string => $record->paid ? 'success' : 'danger')
                                ->extraAttributes(['class' => 'text-xs sm:text-sm']),
                            Tables\Columns\TextColumn::make('')
                                ->label('')
                                ->state('')
                                ->extraAttributes(['class' => 'hidden sm:block']),
                        ])->extraAttributes(['class' => 'justify-between items-center']),

                        Tables\Columns\TextColumn::make('notes')
                            ->label('Notatki')
                            ->wrap()
                            ->limit(80)
                            ->extraAttributes(['class' => 'text-xs sm:text-sm text-gray-600']),
                    ])->space(2),
                ])->extraAttributes(function ($record) {
                    $classes = ['p-3 sm:p-4', 'border-l-4'];
                    if ($record->paid) {
                        $classes[] = 'border-l-green-400';
                    } else {
                        $classes[] = 'border-l-red-400';
                    }
                    return ['class' => implode(' ', $classes)];
                }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('paid')
                    ->label('Status płatności')
                    ->options([
                        '1' => 'Opłacone',
                        '0' => 'Nieopłacone',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'] !== null,
                            fn (Builder $query): Builder => $query->where('paid', (bool) $data['value']),
                        );
                    }),
                Tables\Filters\SelectFilter::make('month')
                    ->label('Miesiąc (pole month)')
                    ->options(function () {
                        $options = [];
                        for ($i = -12; $i <= 12; $i++) {
                            $date = now()->addMonths($i)->startOfMonth();
                            $options[$date->format('Y-m')] = mb_strtoupper($date->translatedFormat('F Y'), 'UTF-8');
                        }
                        return $options;
                    })
                    ->placeholder('Wybierz miesiąc')
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            ($data['value'] ?? null),
                            fn (Builder $q, string $value): Builder => $q->where('month', $value),
                        );
                    }),
                Tables\Filters\Filter::make('updated_at')
                    ->label('Data aktualizacji')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('Od'),
                        Forms\Components\DatePicker::make('to')->label('Do'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'] ?? null, fn (Builder $q, $date): Builder => $q->whereDate('updated_at', '>=', $date))
                            ->when($data['to'] ?? null, fn (Builder $q, $date): Builder => $q->whereDate('updated_at', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('toggle_paid')
                        ->label(fn (Payment $record) => $record->paid ? 'Oznacz jako nieopłacone' : 'Oznacz jako opłacone')
                        ->icon(fn (Payment $record) => $record->paid ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                        ->color(fn (Payment $record) => $record->paid ? 'warning' : 'success')
                        ->tooltip(fn (Payment $record) => $record->paid ? 'Zmień status na nieopłacone' : 'Zmień status na opłacone')
                        ->requiresConfirmation()
                        ->modalHeading(fn (Payment $record) => $record->paid ? 'Oznaczyć płatność jako nieopłaconą?' : 'Oznaczyć płatność jako opłaconą?')
                        ->modalSubmitActionLabel(fn (Payment $record) => $record->paid ? 'Oznacz jako nieopłacone' : 'Oznacz jako opłacone')
                        ->action(function (Payment $record) {
                            $newPaidState = ! $record->paid;
                            $update = ['paid' => $newPaidState];
                            if ($newPaidState) {
                                $update['payment_link'] = null; // po opłaceniu link nie jest potrzebny
                            }
                            $record->update($update);

                            \Filament\Notifications\Notification::make()
                                ->title($newPaidState ? 'Oznaczono jako opłacone' : 'Oznaczono jako nieopłacone')
                                ->body("Użytkownik: {$record->user->name} | Miesiąc: {$record->month}")
                                ->{$newPaidState ? 'success' : 'warning'}()
                                ->send();
                        }),
                ])
                    ->button()
                    ->label('Actions')
                    ->icon('heroicon-o-cog-6-tooth'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_paid')
                        ->label('Oznacz jako opłacone')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Oznaczyć wybrane płatności jako opłacone?')
                        ->modalDescription('Wszystkie zaznaczone płatności zostaną oznaczone jako opłacone. Linki do płatności zostaną wyczyszczone.')
                        ->modalSubmitActionLabel('Oznacz jako opłacone')
                        ->deselectRecordsAfterCompletion()
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $updated = 0;
                            foreach ($records as $record) {
                                /** @var Payment $record */
                                if (!$record->paid) {
                                    $record->update([
                                        'paid' => true,
                                        'payment_link' => null,
                                    ]);
                                    $updated++;
                                }
                            }
                            \Filament\Notifications\Notification::make()
                                ->title('Zaktualizowano status płatności')
                                ->body("Oznaczono jako opłacone: {$updated}")
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\BulkAction::make('mark_unpaid')
                        ->label('Oznacz jako nieopłacone')
                        ->icon('heroicon-o-x-circle')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Oznaczyć wybrane płatności jako nieopłacone?')
                        ->modalDescription('Wszystkie zaznaczone płatności zostaną oznaczone jako nieopłacone.')
                        ->modalSubmitActionLabel('Oznacz jako nieopłacone')
                        ->deselectRecordsAfterCompletion()
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $updated = 0;
                            foreach ($records as $record) {
                                /** @var Payment $record */
                                if ($record->paid) {
                                    $record->update([
                                        'paid' => false,
                                    ]);
                                    $updated++;
                                }
                            }
                            \Filament\Notifications\Notification::make()
                                ->title('Zaktualizowano status płatności')
                                ->body("Oznaczono jako nieopłacone: {$updated}")
                                ->warning()
                                ->send();
                        }),
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
            ->with(['user'])
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
