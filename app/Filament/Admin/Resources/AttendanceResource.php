<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AttendanceResource\Pages;
use App\Filament\Admin\Resources\AttendanceResource\Widgets;
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

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'Użytkownicy i Grupy';
    protected static ?int $navigationSort = 3;
    protected static ?string $modelLabel = 'Obecność';
    protected static ?string $pluralModelLabel = 'Obecności';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereDate('created_at', today())
            ->where('present', true)
            ->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function getNavigationColor(): ?string
    {
        return static::getModel()::whereDate('created_at', today())
            ->where('present', true)
            ->exists()
            ? 'success'
            : null;
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
                        $user = \App\Models\User::find($state);
                        if ($user && $user->group_id) {
                            $set('group_id', $user->group_id);
                        }
                    }),

                Select::make('group_id')
                    ->label('Grupa')
                    ->relationship('group', 'name')
                    ->disabled()
                    ->required(),

                DatePicker::make('date')
                    ->label('Data zajęć')
                    ->default(now())
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
                    ->date('d-m-Y')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('Użytkownik')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('group.name')
                    ->label('Grupa')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('note')
                    ->label('Notatka')
                    ->searchable(),
                BooleanColumn::make('present')
                    ->label('Obecny?'),
            ])
            ->filters([
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

                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Użytkownik')
                    ->relationship('user', 'name')
                    ->searchable(),

                Tables\Filters\SelectFilter::make('group_id')
                    ->label('Grupa')
                    ->relationship('group', 'name')
                    ->searchable(),

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

    public static function getWidgets(): array
    {
        return [
            Widgets\AttendanceStats::class,
            Widgets\AttendanceGroupChart::class,
            Widgets\TopAttendersChart::class,
            Widgets\MonthlyTrendChart::class,
        ];
    }
}
