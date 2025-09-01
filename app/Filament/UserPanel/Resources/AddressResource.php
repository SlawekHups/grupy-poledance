<?php

namespace App\Filament\UserPanel\Resources;

use App\Filament\UserPanel\Resources\AddressResource\Pages;
use App\Filament\UserPanel\Resources\AddressResource\RelationManagers;
use App\Models\Address;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class AddressResource extends Resource
{
    protected static ?string $model = Address::class;

    protected static ?string $navigationLabel = 'Adres';
    protected static ?string $navigationGroup = 'Panel użytkownika';
    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('user_id')->default(fn() => Auth::id()),
                TextInput::make(name: 'type')->required(),
                TextInput::make('street'),
                TextInput::make('postal_code'),
                TextInput::make('city'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->contentGrid([
                'md' => 1,
                'xl' => 1,
            ])
            ->recordUrl(fn($record) => route('filament.user.resources.addresses.edit', ['record' => $record]))
            ->recordClasses('rounded-xl border bg-white shadow-sm hover:shadow-md transition hover:bg-gray-50')
            ->columns([
                Tables\Columns\Layout\Panel::make([
                    Tables\Columns\Layout\Stack::make([
                        TextColumn::make(name: 'type')
                            ->label('Nazwa')
                            ->weight('bold'),
                        Tables\Columns\Layout\Split::make([
                            TextColumn::make('street')->label('Ulica')->wrap(),
                        ]),
                        Tables\Columns\Layout\Split::make([
                            TextColumn::make('postal_code')->label('Kod'),
                            TextColumn::make('city')->label('Miasto')->alignRight(),
                        ])->extraAttributes(['class' => 'justify-between']),
                        Tables\Columns\ViewColumn::make('edit_button')
                            ->label('Edycja')
                            ->view('filament.user.widgets.edit-address-button')
                            ->extraAttributes(['class' => 'mt-2']),
                    ])->space(2),
                ])->extraAttributes(['class' => 'p-4']),
            ])
            ->filters([
                //
            ])
            ->actions([
                // klik w kafelek prowadzi do edycji
            ])
            ->bulkActions([
                // brak
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id());
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAddresses::route('/'),
            'create' => Pages\CreateAddress::route('/create'),
            'edit' => Pages\EditAddress::route('/{record}/edit'),
        ];
    }
    public static function getModelLabel(): string
    {
        return 'Adresu';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Adresy';
    }
}
