<?php

namespace App\Filament\UserPanel\Resources;

use App\Filament\UserPanel\Resources\AttendanceResource\Pages;
use App\Models\Attendance;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Moja obecność';
    protected static ?string $navigationGroup = 'Moje konto';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Data')
                    ->date('d.m.Y')
                    ->sortable(),
                Tables\Columns\IconColumn::make('present')
                    ->boolean()
                    ->label('Obecny?'),
                Tables\Columns\TextColumn::make('note')
                    ->label('Notatka')
                    ->limit(40)
                    ->wrap(),
            ])
            ->defaultSort('date', 'desc');
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
}
