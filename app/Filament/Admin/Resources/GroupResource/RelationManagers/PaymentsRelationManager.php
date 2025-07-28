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
                    ->relationship(
                        'user',
                        'name',
                        fn (Builder $query) => $query->where('group_id', $this->getOwnerRecord()->id)
                    )
                    ->required()
                    ->searchable()
                    ->preload(),

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
                    ->suffix('zł')
                    ->required()
                    ->default(function ($livewire) {
                        return number_format($livewire->getOwnerRecord()->users()->first()?->amount ?? 0, 2);
                    }),

                Forms\Components\Toggle::make('paid')
                    ->label('Opłacone')
                    ->default(false),

                Forms\Components\TextInput::make('payment_link')
                    ->label('Link do płatności')
                    ->url()
                    ->prefix('https://')
                    ->columnSpanFull(),
            ])
            ->columns(3);
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
                    ->mutateFormDataUsing(function (array $data) {
                        $user = \App\Models\User::find($data['user_id']);
                        if ($user) {
                            $data['amount'] = $user->amount;
                        }
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('togglePaid')
                    ->label(fn($record) => $record->paid ? 'Oznacz jako nieopłacone' : 'Oznacz jako opłacone')
                    ->icon(fn($record) => $record->paid ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn($record) => $record->paid ? 'danger' : 'success')
                    ->action(function ($record) {
                        $record->update(['paid' => !$record->paid]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('markAsPaid')
                        ->label('Oznacz jako opłacone')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['paid' => true])),
                    Tables\Actions\BulkAction::make('markAsUnpaid')
                        ->label('Oznacz jako nieopłacone')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['paid' => false])),
                ]),
            ]);
    }
} 