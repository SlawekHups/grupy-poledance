<?php

namespace App\Filament\Admin\Resources\GroupResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Filament\Support\Colors\Color;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';
    protected static ?string $title = 'Płatności';
    protected static ?string $modelLabel = 'płatność';
    protected static ?string $pluralModelLabel = 'płatności';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Użytkownik')
                    ->searchable()
                    ->getSearchResultsUsing(function (string $search) {
                        $group = $this->getOwnerRecord();
                        if (!$group) return [];
                        $users = $group->members()
                            ->where(function ($q) use ($search) {
                                $q->where('users.name', 'like', "%{$search}%")
                                  ->orWhere('users.email', 'like', "%{$search}%")
                                  ->orWhere('users.phone', 'like', "%{$search}%");
                            })
                            ->select('users.id', 'users.name', 'users.phone')
                            ->orderBy('users.name')
                            ->limit(50)
                            ->get()
                            ->mapWithKeys(function ($u) {
                                $label = $u->name . (!empty($u->phone) ? ' (' . $u->phone . ')' : '');
                                return [$u->id => $label];
                            })
                            ->toArray();
                        return $users ?? [];
                    })
                    ->getOptionLabelUsing(function ($value) {
                        $u = \App\Models\User::find($value);
                        if (!$u) return $value;
                        return $u->name . (!empty($u->phone) ? ' (' . $u->phone . ')' : '');
                    })
                    ->label('Użytkownik')
                    ->required()
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
            ])
            ->columns(3);
    }

    protected function getTableQuery(): Builder
    {
        // Pokaż płatności użytkowników należących do tej grupy (pivot members)
        $owner = $this->getOwnerRecord();
        return \App\Models\Payment::query()
            ->whereIn('user_id', $owner->members()->select('users.id'));
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Użytkownik')
                    ->searchable()
                    ->sortable()
                    ->url(fn ($record) => route('filament.admin.resources.users.edit', $record->user))
                    ->color('primary'),

                Tables\Columns\TextColumn::make('month')
                    ->label('Miesiąc')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Kwota')
                    ->money('PLN')
                    ->sortable(),

                Tables\Columns\IconColumn::make('paid')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor(Color::Green)
                    ->falseColor(Color::Red),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Data aktualizacji')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('month', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('paid')
                    ->label('Status płatności')
                    ->options([
                        '1' => 'Opłacone',
                        '0' => 'Nieopłacone',
                    ]),

                Tables\Filters\Filter::make('month')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Od'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Do'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->where('month', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->where('month', '<=', $date),
                            );
                    }),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Dodaj płatność')
                    ->using(fn (array $data) => \App\Models\Payment::create($data))
                    ->mutateFormDataUsing(function (array $data) {
                        $user = \App\Models\User::find($data['user_id']);
                        if ($user) {
                            $data['amount'] = $user->amount;
                        }
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\Action::make('togglePaid')
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
                            $newPaid = ! $record->paid;
                            $update = ['paid' => $newPaid];
                            if ($newPaid) {
                                $update['payment_link'] = null;
                            }
                            $record->update($update);
                        }),
                ])
                    ->button()
                    ->label('Actions')
                    ->icon('heroicon-o-cog-6-tooth'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('markAsPaid')
                        ->label('Oznacz jako opłacone')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Oznaczyć wybrane płatności jako opłacone?')
                        ->modalDescription('Wszystkie zaznaczone płatności zostaną oznaczone jako opłacone. Linki do płatności zostaną wyczyszczone.')
                        ->modalSubmitActionLabel('Oznacz jako opłacone')
                        ->deselectRecordsAfterCompletion()
                        ->action(function ($records) {
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
                            \Filament\Notifications\Notification::make()
                                ->title('Zaktualizowano status płatności')
                                ->body("Oznaczono jako opłacone: {$updated}")
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\BulkAction::make('markAsUnpaid')
                        ->label('Oznacz jako nieopłacone')
                        ->icon('heroicon-o-x-circle')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Oznaczyć wybrane płatności jako nieopłacone?')
                        ->modalDescription('Wszystkie zaznaczone płatności zostaną oznaczone jako nieopłacone.')
                        ->modalSubmitActionLabel('Oznacz jako nieopłacone')
                        ->deselectRecordsAfterCompletion()
                        ->action(function ($records) {
                            $updated = 0;
                            foreach ($records as $record) {
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
                ]),
            ]);
    }
} 