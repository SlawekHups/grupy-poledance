<?php

namespace App\Filament\Admin\Resources\AddressRelationManagerResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AddressesRelationManager extends RelationManager
{
    protected static string $relationship = 'addresses';

    public function form(Form $form): Form
    {

        return $form->schema([
            TextInput::make('type')->label('Typ adresu'),
            TextInput::make('street')->required()->label('Ulica'),
            TextInput::make('city')->required()->label('Miasto'),
            TextInput::make('postal_code')->required()->label('Kod pocztowy'),

        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('street')
            ->columns([
                TextColumn::make('type')->label('Typ'),
                TextColumn::make('street')->label('Ulica'),
                TextColumn::make('city')->label('Miasto'),
                TextColumn::make('postal_code')->label('Kod pocztowy'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
