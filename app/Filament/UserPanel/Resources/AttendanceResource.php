<?php

namespace App\Filament\UserPanel\Resources;

use App\Filament\UserPanel\Resources\AttendanceResource\Pages;
use App\Models\Attendance;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;


class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;
    protected static ?string $navigationLabel = 'Moja obecność';
    protected static ?string $navigationGroup = 'Panel użytkownika';
    protected static ?int $navigationSort = 40;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->contentGrid([
                'md' => 1,
                'xl' => 1,
            ])
            ->recordClasses('rounded-xl border bg-white shadow-sm hover:shadow-md transition hover:bg-gray-50')
            ->columns([
                Tables\Columns\Layout\Panel::make([
                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\Layout\Split::make([
                            Tables\Columns\TextColumn::make('date')
                                ->label('Data')
                                ->date('d.m.Y')
                                ->weight('bold'),
                            Tables\Columns\IconColumn::make('present')
                                ->label('Obecny?')
                                ->boolean()
                                ->trueIcon('heroicon-o-check-circle')
                                ->falseIcon('heroicon-o-x-circle')
                                ->alignRight(),
                        ])->extraAttributes(['class' => 'justify-between items-center']),

                        Tables\Columns\Layout\Split::make([
                            Tables\Columns\TextColumn::make('note')
                                ->label('Notatka')
                                ->limit(80)
                                ->wrap(),
                        ]),
                    ])->space(2),
                ])->extraAttributes(['class' => 'p-4']),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                SelectFilter::make('present')
                    ->label('Status obecności')
                    ->options([
                        '1' => 'Obecny',
                        '0' => 'Nieobecny',
                    ]),
                Filter::make('date')
                    ->label('Data')
                    ->form([
                        DatePicker::make('date')->label('Wybierz datę'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['date'], fn($query, $date) =>
                            $query->whereDate('date', $date));
                    }),

            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendances::route('/'),
        ];
    }
    // Blokady akcji
    public static function canCreate(): bool
    {
        return false;
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
