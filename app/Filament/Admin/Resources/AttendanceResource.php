<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AttendanceResource\Pages;
use App\Filament\Admin\Resources\AttendanceResource\RelationManagers;
use App\Models\Attendance;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['group_id']) && !empty($data['user_id'])) {
            $user = \App\Models\User::find($data['user_id']);
            if ($user && $user->group_id) {
                $data['group_id'] = $user->group_id;
            }
        }
        return $data;
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label('Użytkownik')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Pobieramy grupę usera i ustawiamy automatycznie group_id
                        $user = \App\Models\User::find($state);
                        if ($user && $user->group_id) {
                            $set('group_id', $user->group_id);
                        }
                    }),

                Select::make('group_id')
                    ->label('Grupa')
                    ->relationship('group', 'name')
                    ->disabled() // nie pozwalamy ręcznie zmieniać
                    ->required(),

                DatePicker::make('date')
                    ->label('Data zajęć')
                    ->default(now()) // domyślnie dziś, można zmienić
                    ->required(),
                TextInput::make('note')->label('Notatka')->nullable(),
                Toggle::make('present')->label('Obecny?'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')
                    ->label('Data')
                    ->sortable()
                    ->date()
                    ->date('d-m-Y'),
                TextColumn::make('user.name')
                    ->label('Użytkownik')
                    ->sortable(),
                TextColumn::make('group.name')
                    ->label('Grupa')
                    ->sortable(),
                TextColumn::make('note')
                    ->label('Notatka'),
                BooleanColumn::make('present')->label('Obecny?'),

            ])
            ->filters([
                // Filtrowanie po dacie (zakres dat)
                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('Od'),
                        Forms\Components\DatePicker::make('to')->label('Do'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['from'], fn($q, $date) => $q->whereDate('date', '>=', $date))
                            ->when($data['to'], fn($q, $date) => $q->whereDate('date', '<=', $date));
                    }),

                // Filtrowanie po użytkowniku
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Użytkownik')
                    ->relationship('user', 'name')
                    ->searchable(),

                // Filtrowanie po grupie
                Tables\Filters\SelectFilter::make('group_id')
                    ->label('Grupa')
                    ->relationship('group', 'name')
                    ->searchable(),

                // Filtrowanie po obecności
                Tables\Filters\TernaryFilter::make('present')
                    ->label('Obecność')
                    ->trueLabel('Obecny')
                    ->falseLabel('Nieobecny'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }
    public static function getModelLabel(): string
    {
        return 'Obecności';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Obecność';
    }
}
