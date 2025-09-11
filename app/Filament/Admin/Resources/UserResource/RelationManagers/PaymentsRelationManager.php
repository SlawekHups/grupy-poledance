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

                TextInput::make('amount')
                    ->label('Kwota (PLN)')
                    ->numeric()
                    ->suffix('zł')
                    ->default(function ($livewire) {
                        // Pobierz kwotę z modelu użytkownika
                        return number_format((float) ($livewire->getOwnerRecord()->amount ?? 0), 2);
                    })
                    ->required()
                    ->step(0.01),

                TextInput::make('payment_link')
                    ->label('Link do płatności')
                    ->url()
                    ->prefix('https://')
                    ->columnSpanFull(),

                Toggle::make('paid')
                    ->label('Opłacone')
                    ->default(false),
            ])
            ->columns(2);
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->contentGrid([
                'md' => 1,
                'lg' => 2,
                'xl' => 3,
            ])
            ->recordUrl(fn ($record) => null)
            ->recordClasses('rounded-xl border bg-white shadow-sm hover:shadow-md transition hover:bg-gray-50')
            ->recordAction('edit')
            ->recordTitleAttribute('month')
            ->reorderable(false)
            ->paginated(false)
            ->defaultSort('month', 'desc')
            ->columns([
                Tables\Columns\Layout\Panel::make([
                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\Layout\Split::make([
                            Tables\Columns\TextColumn::make('month')
                                ->label('Miesiąc')
                                ->formatStateUsing(function (string $state): string {
                                    return mb_strtoupper(\Carbon\Carbon::parse($state)->translatedFormat('F Y'), 'UTF-8');
                                })
                                ->weight('bold')
                                ->sortable(),
                            Tables\Columns\TextColumn::make('paid')
                                ->label('Status')
                                ->badge()
                                ->formatStateUsing(fn (bool $state): string => $state ? 'Opłacone' : 'Nieopłacone')
                                ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                                ->alignRight(),
                        ])->extraAttributes(['class' => 'justify-between items-start']),

                        Tables\Columns\Layout\Split::make([
                            Tables\Columns\TextColumn::make('amount')
                                ->label('Kwota')
                                ->money('PLN')
                                ->weight('bold'),
                        ])->extraAttributes(['class' => 'justify-between items-center']),

                        Tables\Columns\TextColumn::make('updated_at')
                            ->label('Data zapłaty')
                            ->dateTime('d.m.Y H:i')
                            ->formatStateUsing(fn (?string $state, $record) => $record->paid ? \Carbon\Carbon::parse($state)->format('d.m.Y H:i') : 'Brak')
                            ->extraAttributes(['class' => 'text-sm text-gray-600']),
                    ])->space(2),
                ])->extraAttributes(['class' => 'p-4']),
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
                Tables\Actions\ActionGroup::make([
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
                    ->button()
                    ->label('Actions')
                    ->icon('heroicon-o-cog-6-tooth'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
                                if (!$record->paid) {
                                    $record->update([
                                        'paid' => true,
                                        'payment_link' => null,
                                    ]);
                                    $updated++;
                                }
                            }
                            Notification::make()
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
                                if ($record->paid) {
                                    $record->update([
                                        'paid' => false,
                                    ]);
                                    $updated++;
                                }
                            }
                            Notification::make()
                                ->title('Zaktualizowano status płatności')
                                ->body("Oznaczono jako nieopłacone: {$updated}")
                                ->warning()
                                ->send();
                        }),
                ]),
            ]);
    }
}
