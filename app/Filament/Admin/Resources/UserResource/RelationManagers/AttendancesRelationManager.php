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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Group;
use App\Models\Attendance;

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
        return $table
            ->recordTitleAttribute('date')
            ->columns([
                TextColumn::make('date')
                    ->label('Data')
                    ->date('d.m.Y')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('group.name')
                    ->label('Grupa')
                    ->searchable()
                    ->sortable(),

                BooleanColumn::make('present')
                    ->label('Obecny')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('note')
                    ->label('Notatka')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),
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
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Edytuj'),
                Tables\Actions\DeleteAction::make()
                    ->label('Usuń'),
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
