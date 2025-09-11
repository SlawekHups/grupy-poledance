<?php

namespace App\Filament\Admin\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Placeholder;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\BooleanColumn;
use App\Models\Attendance;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Group;

class AttendancesRelationManager extends RelationManager
{
    protected static string $relationship = 'attendances';

    protected static ?string $title = 'Obecność';

    protected static ?string $modelLabel = 'obecność';

    protected static ?string $pluralModelLabel = 'obecności';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('date')
                    ->label('Data')
                    ->required()
                    ->default(now())
                    ->native(false),

                Select::make('group_id')
                    ->label('Grupa')
                    ->relationship('group', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->visible(fn () => $this->ownerRecord->groups->count() > 1)
                    ->helperText(fn () => $this->ownerRecord->groups->count() > 1 
                        ? 'Wybierz grupę dla której chcesz dodać obecność' 
                        : null),

                Forms\Components\Placeholder::make('group_info')
                    ->label('Grupa')
                    ->content(fn () => $this->ownerRecord->groups->count() === 1 
                        ? $this->ownerRecord->groups->first()->name 
                        : 'Użytkownik nie ma przypisanej grupy')
                    ->visible(fn () => $this->ownerRecord->groups->count() <= 1),

                Toggle::make('present')
                    ->label('Obecny')
                    ->default(true)
                    ->required(),

                Textarea::make('note')
                    ->label('Notatka')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        \Illuminate\Support\Facades\Log::info('AttendancesRelationManager table method called');
        return $table
            ->recordTitleAttribute('date')
            ->contentGrid([
                'md' => 1,
                'lg' => 1,
                'xl' => 1,
            ])
            ->recordUrl(fn ($record) => null)
            ->recordClasses('rounded-xl border bg-white shadow-sm hover:shadow-md transition hover:bg-gray-50')
            ->recordAction('edit')
            ->recordTitleAttribute('date')
            ->reorderable(false)
            ->paginated(false)
            ->defaultSort('date', 'desc')
            ->columns([
                Tables\Columns\Layout\Panel::make([
                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\Layout\Split::make([
                            Tables\Columns\TextColumn::make('date')
                                ->label('Data')
                                ->date('d.m.Y')
                                ->weight('bold')
                                ->sortable(),
                            Tables\Columns\TextColumn::make('present')
                                ->label('Obecność')
                                ->badge()
                                ->formatStateUsing(fn ($state) => $state ? 'Obecny' : 'Nieobecny')
                                ->color(fn ($state) => $state ? 'success' : 'warning')
                                ->alignRight(),
                        ])->extraAttributes(['class' => 'justify-between items-start']),

                        Tables\Columns\Layout\Split::make([
                            Tables\Columns\TextColumn::make('group.name')
                                ->label('Grupa')
                                ->badge()
                                ->color('info')
                                ->searchable(),
                        ])->extraAttributes(['class' => 'justify-between items-center']),

                        Tables\Columns\TextColumn::make('note')
                            ->label('Notatka')
                            ->wrap()
                            ->extraAttributes(['class' => 'text-sm text-gray-600']),
                    ])->space(2),
                ])->extraAttributes(['class' => 'p-4']),
            ])
            ->filters([
                SelectFilter::make('present')
                    ->label('Status obecności')
                    ->options([
                        true => 'Obecny',
                        false => 'Nieobecny',
                    ]),

                SelectFilter::make('group_id')
                    ->label('Grupa')
                    ->relationship('group', 'name')
                    ->searchable()
                    ->preload(),

                Filter::make('date_range')
                    ->form([
                        DatePicker::make('from')
                            ->label('Od'),
                        DatePicker::make('until')
                            ->label('Do'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->label('Edytuj'),
                    Tables\Actions\Action::make('togglePresent')
                        ->label(fn (Attendance $record): string => $record->present ? 'Oznacz jako nieobecny' : 'Oznacz jako obecny')
                        ->icon(fn (Attendance $record): string => $record->present ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                        ->color(fn (Attendance $record): string => $record->present ? 'warning' : 'success')
                        ->action(function (Attendance $record) {
                            $record->update(['present' => !$record->present]);
                            \Filament\Notifications\Notification::make()
                                ->title('Status obecności zaktualizowany')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Zmiana statusu obecności')
                        ->modalDescription(fn (Attendance $record): string => 
                            $record->present 
                                ? 'Czy na pewno chcesz oznaczyć jako nieobecny?' 
                                : 'Czy na pewno chcesz oznaczyć jako obecny?'
                        ),
                    Tables\Actions\DeleteAction::make()
                        ->label('Usuń'),
                ])
                    ->icon('heroicon-o-cog-6-tooth')
                    ->button()
                    ->label('Akcje'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Dodaj obecność')
                    ->mutateFormDataUsing(function (array $data): array {
                        // Jeśli użytkownik ma tylko jedną grupę, automatycznie ją ustaw
                        if ($this->ownerRecord->groups->count() === 1) {
                            $data['group_id'] = $this->ownerRecord->groups->first()->id;
                        }
                        return $data;
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Usuń zaznaczone'),
                ]),
            ])
            ->defaultSort('date', 'desc')
            ->emptyStateHeading('Brak obecności')
            ->emptyStateDescription('Ten użytkownik nie ma jeszcze żadnych zapisów obecności.')
            ->emptyStateIcon('heroicon-o-calendar-days');
    }
}
