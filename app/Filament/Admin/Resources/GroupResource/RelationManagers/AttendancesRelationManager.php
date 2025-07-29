<?php

namespace App\Filament\Admin\Resources\GroupResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

class AttendancesRelationManager extends RelationManager
{
    protected static string $relationship = 'attendances';
    protected static ?string $title = 'Obecności';
    protected static ?string $modelLabel = 'obecność';
    protected static ?string $pluralModelLabel = 'obecności';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Użytkownik')
                            ->options(function () {
                                return $this->getOwnerRecord()->users()
                                    ->orderBy('name')
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\DatePicker::make('date')
                            ->label('Data zajęć')
                            ->default(now())
                            ->required(),

                        Forms\Components\TextInput::make('note')
                            ->label('Notatka')
                            ->nullable(),

                        Forms\Components\Toggle::make('present')
                            ->label('Obecny?')
                            ->default(true),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Użytkownik')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('date')
                    ->label('Data')
                    ->date('d.m.Y')
                    ->sortable(),

                Tables\Columns\IconColumn::make('present')
                    ->label('Obecny')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('note')
                    ->label('Notatka')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('Od'),
                        Forms\Components\DatePicker::make('to')->label('Do'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['to'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    }),

                Tables\Filters\SelectFilter::make('user')
                    ->label('Użytkownik')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('present')
                    ->label('Obecność')
                    ->placeholder('Wszystkie')
                    ->trueLabel('Obecny')
                    ->falseLabel('Nieobecny'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                Tables\Actions\Action::make('addGroupAttendance')
                    ->label('Dodaj obecności grupowe')
                    ->icon('heroicon-o-user-group')
                    ->form([
                        Forms\Components\DatePicker::make('date')
                            ->label('Data zajęć')
                            ->default(now())
                            ->required(),
                        
                        Forms\Components\TextInput::make('note')
                            ->label('Notatka dla wszystkich')
                            ->nullable(),

                        Forms\Components\Grid::make(1)
                            ->schema([
                                Forms\Components\CheckboxList::make('users')
                                    ->label('Użytkownicy')
                                    ->options(function ($record) {
                                        return $this->getOwnerRecord()->users()
                                            ->orderBy('name')
                                            ->pluck('name', 'id');
                                    })
                                    ->searchable()
                                    ->bulkToggleable()
                                    ->columns(2)
                                    ->required()
                                    ->helperText('Możesz użyć przycisku "Zaznacz wszystkie" powyżej listy'),
                            ]),
                    ])
                    ->action(function (array $data): void {
                        foreach ($data['users'] as $userId) {
                            $this->getOwnerRecord()->attendances()->create([
                                'user_id' => $userId,
                                'date' => $data['date'],
                                'note' => $data['note'],
                                'present' => true,
                            ]);
                        }

                        Notification::make()
                            ->title('Dodano obecności')
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('date', 'desc');
    }
} 