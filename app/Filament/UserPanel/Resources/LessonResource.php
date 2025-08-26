<?php

namespace App\Filament\UserPanel\Resources;

use App\Filament\UserPanel\Resources\LessonResource\Pages;
use App\Models\Lesson;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class LessonResource extends Resource
{
    protected static ?string $model = Lesson::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Zadania (grupa)';
    protected static ?string $navigationGroup = 'Informacje';
    protected static ?int $navigationSort = 20;

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        return parent::getEloquentQuery()
            ->where('status', 'published')
            ->when($user?->group_id, fn ($q) => $q->where('group_id', $user->group_id))
            ->orderBy('date', 'asc');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')
                    ->label('Data')
                    ->date('d.m.Y')
                    ->sortable()
                    ->extraAttributes(['class' => 'whitespace-nowrap']),
                TextColumn::make('title')
                    ->label('Tytuł')
                    ->weight('bold')
                    ->searchable()
                    ->limit(80)
                    ->extraAttributes([
                        'style' => 'white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 28rem;'
                    ]),
                TextColumn::make('description')
                    ->label('Opis')
                    ->html()
                    ->extraAttributes([
                        'class' => 'prose max-w-none break-words',
                        'style' => 'white-space: normal; overflow: hidden; max-height: 200px; height: 200px; display: block; max-width: 36rem;'
                    ]),
            ])
            ->defaultSort('date', 'asc')
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Podgląd')
                    ->icon('heroicon-o-eye')
                    ->color('info'),
            ])
            ->bulkActions([
                // brak
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Zadanie')
                    ->schema([
                        TextEntry::make('title')->label('Tytuł')->weight('bold'),
                        TextEntry::make('date')->label('Data')->date('d.m.Y'),
                        TextEntry::make('description')->label('Opis')->html()->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLessons::route('/'),
            'view' => Pages\ViewLesson::route('/{record}'),
        ];
    }
}
