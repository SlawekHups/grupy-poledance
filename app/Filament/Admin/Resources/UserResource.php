<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages;
use App\Filament\Admin\Resources\UserResource\RelationManagers;
use App\Models\User;
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

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),
                TextInput::make('email')->email()->required(),

                TextInput::make('phone')->label('Telefon'),
                TextInput::make('address')->label('Adres'),
                DatePicker::make('joined_at')->label('Data zapisu'),

                Select::make('group_id')
                    ->label('Grupa')
                    ->relationship('group', 'name')
                    ->searchable()
                    ->preload(),

                // 👇 Pole hasła z logiką bcrypt 
                TextInput::make('password')
                    ->password()
                    ->label('Hasło')
                    ->required(fn(string $context) => $context === 'create')
                    ->dehydrated(fn($state) => filled($state)) // tylko gdy coś wpisano
                    ->dehydrateStateUsing(fn($state) => bcrypt($state)) // zawsze hashuj
                    ->maxLength(255),
                Toggle::make('is_active')->label('Aktywny'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->searchable(),
                TextColumn::make('name')->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('phone')->label('Telefon')->searchable(),
                TextColumn::make('group.name')->label('Grupa'),
                BooleanColumn::make('is_active')->label('Aktywny'),
            ])
            ->filters([
                //
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
