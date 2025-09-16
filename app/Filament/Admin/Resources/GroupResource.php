<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\GroupResource\Pages;
use App\Filament\Admin\Resources\GroupResource\RelationManagers;
use App\Models\Group;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Resources\GroupResource\RelationManagers\UsersRelationManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

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
                // Lokalny przełącznik trybu edycji utrzymywany w stanie Livewire
                Forms\Components\Hidden::make('_editMode')
                    ->default(false)
                    ->afterStateHydrated(function (Get $get, callable $set) {
                        // Ustaw tylko przy pierwszym załadowaniu, aby utrzymać wartość w kolejnych żądaniach Livewire
                        if ($get('_editMode') === null || $get('_editMode') === false) {
                            if (request()->boolean('edit', false) || request()->routeIs('*.create')) {
                                $set('_editMode', true);
                            }
                        }
                    })
                    ->dehydrated(false),

                Forms\Components\View::make('filament.admin.groups.group-summary')
                    ->visible(fn (Get $get) => ! (bool) $get('_editMode') && !request()->routeIs('*.create'))
                    ->columnSpanFull(),
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nazwa')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Nazwa grupy')
                            ->columnSpanFull()
                            ->disabled(fn (Get $get) => ! (bool) $get('_editMode') && !request()->routeIs('*.create')),

                        Forms\Components\TextInput::make('description')
                            ->label('Opis')
                            ->maxLength(30)
                            ->placeholder('Krótki opis grupy')
                            ->helperText(fn ($state) => strlen($state) . '/30 znaków')
                            ->live()
                            ->columnSpanFull()
                            ->disabled(fn (Get $get) => ! (bool) $get('_editMode') && !request()->routeIs('*.create')),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'active' => 'Aktywna',
                                        'inactive' => 'Nieaktywna',
                                        'full' => 'Pełna',
                                    ])
                                    ->default('active')
                                    ->disabled(fn (Get $get) => ! (bool) $get('_editMode') && !request()->routeIs('*.create')),

                                Forms\Components\TextInput::make('max_size')
                                    ->label('Maksymalna liczba uczestników')
                                    ->numeric()
                                    ->minValue(1)
                                    ->default(7)
                                    ->disabled(fn (Get $get) => ! (bool) $get('_editMode') && !request()->routeIs('*.create')),
                            ]),
                    ])
                    ->columns(2)
                    ->visible(fn (Get $get) => (bool) $get('_editMode') || request()->routeIs('*.create')),
            ]);
    }

    public static function table(Table $table): Table
    {
        // Tabela wyłączona - używamy tylko widżetów
        return $table
            ->columns([])
            ->paginated(false)
            ->searchable(false)
            ->filters([])
            ->actions([])
            ->bulkActions([])
            ->headerActions([])
            ->emptyStateHeading('Grupy wyświetlane w widżetach poniżej')
            ->emptyStateDescription('Kliknij na grupę w widżecie "Grupy według dni tygodnia" aby przejść do edycji.')
            ->emptyStateIcon('heroicon-o-calendar-days');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\UsersRelationManager::class,
            RelationManagers\LessonsRelationManager::class,
            RelationManagers\PaymentsRelationManager::class,
            RelationManagers\AttendancesRelationManager::class,
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
