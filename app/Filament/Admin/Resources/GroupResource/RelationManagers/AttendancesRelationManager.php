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
                                return $this->getOwnerRecord()->members()
                                    ->select('users.id', 'users.name')
                                    ->orderBy('users.name')
                                    ->pluck('users.name', 'users.id');
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
                Tables\Actions\Action::make('infoOdrabianie')
                    ->label('Info')
                    ->icon('heroicon-o-information-circle')
                    ->color('info')
                    ->tooltip('Jak dodać obecność spoza grupy (odrabianie)')
                    ->requiresConfirmation()
                    ->modalHeading('Informacja: Odrabianie zajęć')
                    ->modalDescription('Opcja „Dodaj obecność spoza grupy (odrabianie)” służy do oznaczenia obecności uczestnika, który przyszedł do tej grupy wyjątkowo (np. odrabianie). Nie zmienia to przypisań do grup i nie wpływa na listę członków. Wpis tworzony jest na wybraną datę, z notatką „Odrabianie”.')
                    ->modalSubmitActionLabel('OK')
                    ->action(fn () => null),
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data) {
                        // Upewnij się, że group_id jest ustawione dla relacji
                        $data['group_id'] = $this->getOwnerRecord()->id;
                        return $data;
                    }),
                Tables\Actions\Action::make('addExternalUserAttendance')
                    ->label('Dodaj obecność spoza grupy (odrabianie)')
                    ->icon('heroicon-o-user-plus')
                    ->color('danger')
                    ->form([
                        Forms\Components\Select::make('user_id')
                            ->label('Użytkownik')
                            ->searchable()
                            ->getSearchResultsUsing(function (string $search) {
                                return \App\Models\User::query()
                                    ->where('is_active', true)
                                    ->whereNot('role', 'admin')
                                    ->where(function ($q) use ($search) {
                                        $q->where('name', 'like', "%{$search}%")
                                          ->orWhere('email', 'like', "%{$search}%");
                                    })
                                    ->limit(50)
                                    ->pluck('name', 'id');
                            })
                            ->getOptionLabelUsing(fn ($value) => \App\Models\User::find($value)?->name ?? $value)
                            ->required(),

                        Forms\Components\DatePicker::make('date')
                            ->label('Data zajęć')
                            ->default(now())
                            ->required(),

                        Forms\Components\Toggle::make('present')
                            ->label('Obecny?')
                            ->default(true),

                        Forms\Components\TextInput::make('note')
                            ->label('Notatka')
                            ->placeholder('Odrabianie')
                            ->default('Odrabianie')
                            ->nullable(),
                    ])
                    ->action(function (array $data): void {
                        \App\Models\Attendance::updateOrCreate(
                            [
                                'user_id' => $data['user_id'],
                                'date' => $data['date'],
                            ],
                            [
                                'group_id' => $this->getOwnerRecord()->id,
                                'present' => (bool)($data['present'] ?? true),
                                'note' => $data['note'] ?? null,
                            ]
                        );

                        \Filament\Notifications\Notification::make()
                            ->title('Dodano obecność (spoza grupy)')
                            ->success()
                            ->send();
                    }),
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
                                        return $this->getOwnerRecord()->members()
                                            ->select('users.id', 'users.name')
                                            ->orderBy('users.name')
                                            ->pluck('users.name', 'users.id');
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
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\Action::make('toggle_present')
                        ->label(fn ($record) => $record->present ? 'Oznacz jako nieobecny' : 'Oznacz jako obecny')
                        ->icon(fn ($record) => $record->present ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                        ->color(fn ($record) => $record->present ? 'warning' : 'success')
                        ->requiresConfirmation()
                        ->modalHeading('Potwierdź zmianę obecności')
                        ->modalDescription(fn ($record) => $record->present ? 'Czy na pewno oznaczyć jako nieobecny?' : 'Czy na pewno oznaczyć jako obecny?')
                        ->action(function ($record) {
                            $record->update(['present' => ! $record->present]);
                        }),
                ])
                    ->button()
                    ->label('Actions')
                    ->icon('heroicon-o-cog-6-tooth'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('mark_present')
                        ->label('Oznacz jako obecny')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Oznaczyć zaznaczone wpisy jako obecne?')
                        ->modalSubmitActionLabel('Oznacz jako obecny')
                        ->deselectRecordsAfterCompletion()
                        ->action(function ($records) {
                            $updated = 0;
                            foreach ($records as $record) {
                                if (!$record->present) {
                                    $record->update(['present' => true]);
                                    $updated++;
                                }
                            }
                            Notification::make()
                                ->title('Zaktualizowano obecności')
                                ->body("Oznaczono jako obecnych: {$updated}")
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\BulkAction::make('mark_absent')
                        ->label('Oznacz jako nieobecny')
                        ->icon('heroicon-o-x-circle')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Oznaczyć zaznaczone wpisy jako nieobecne?')
                        ->modalSubmitActionLabel('Oznacz jako nieobecny')
                        ->deselectRecordsAfterCompletion()
                        ->action(function ($records) {
                            $updated = 0;
                            foreach ($records as $record) {
                                if ($record->present) {
                                    $record->update(['present' => false]);
                                    $updated++;
                                }
                            }
                            Notification::make()
                                ->title('Zaktualizowano obecności')
                                ->body("Oznaczono jako nieobecnych: {$updated}")
                                ->warning()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('date', 'desc');
    }
} 