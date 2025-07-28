<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\GroupResource\Pages;
use App\Filament\Admin\Resources\GroupResource\RelationManagers;
use App\Models\Group;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Resources\GroupResource\RelationManagers\UsersRelationManager;
use Illuminate\Database\Eloquent\Model;

class GroupResource extends Resource
{
    protected static ?string $model = Group::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Użytkownicy i Grupy';
    protected static ?int $navigationSort = 2;
    protected static ?string $modelLabel = 'grupa';
    protected static ?string $pluralModelLabel = 'grupy';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nazwa')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Nazwa grupy')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('description')
                            ->label('Opis')
                            ->maxLength(30)
                            ->placeholder('Krótki opis grupy')
                            ->helperText(fn ($state) => strlen($state) . '/30 znaków')
                            ->live()
                            ->columnSpanFull(),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'active' => 'Aktywna',
                                        'inactive' => 'Nieaktywna',
                                        'full' => 'Pełna',
                                    ])
                                    ->default('active'),

                                Forms\Components\TextInput::make('max_size')
                                    ->label('Maksymalna liczba uczestników')
                                    ->numeric()
                                    ->minValue(1)
                                    ->default(7),
                            ]),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nazwa')
                    ->searchable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Opis')
                    ->searchable()
                    ->limit(30)
                    ->tooltip(function (Model $record): ?string {
                        return $record->description;
                    }),

                Tables\Columns\TextColumn::make('users_count')
                    ->label('Liczba uczestników')
                    ->counts('users')
                    ->description(fn (Group $record) => "{$record->users()->count()}/{$record->max_size}")
                    ->color(fn (Group $record) => 
                        $record->users()->count() >= $record->max_size ? 'danger' : 
                        ($record->users()->count() >= $record->max_size * 0.8 ? 'warning' : 'success')
                    )
                    ->alignCenter()
                    ->size('lg'),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'danger' => 'inactive',
                        'warning' => 'full',
                        'success' => 'active',
                    ]),

                Tables\Columns\TextColumn::make('lessons_count')
                    ->label('Liczba zadań')
                    ->counts('lessons')
                    ->url(fn ($record) => route('filament.admin.resources.groups.edit', [
                        'record' => $record,
                        'activeRelationManager' => 1
                    ]))
                    ->color('primary'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktywna',
                        'inactive' => 'Nieaktywna',
                        'full' => 'Pełna',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            RelationManagers\UsersRelationManager::class,
            RelationManagers\LessonsRelationManager::class,
            RelationManagers\PaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGroups::route('/'),
            'create' => Pages\CreateGroup::route('/create'),
            'edit' => Pages\EditGroup::route('/{record}/edit'),
        ];
    }
    public static function getWidgets(): array
    {
        return [
            \App\Filament\Admin\Resources\GroupResource\Widgets\GroupStats::class,

        ];
    }
    public static function getModelLabel(): string
    {
        return 'Grupy';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Grupy';
    }
}
