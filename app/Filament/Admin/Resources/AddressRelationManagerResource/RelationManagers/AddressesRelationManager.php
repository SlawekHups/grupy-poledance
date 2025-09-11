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
            ->contentGrid([
                'md' => 1,
                'lg' => 1,
                'xl' => 1,
            ])
            ->recordUrl(fn ($record) => null)
            ->recordClasses('rounded-xl border bg-white shadow-sm hover:shadow-md transition hover:bg-gray-50')
            ->recordAction('edit')
            ->recordTitleAttribute('street')
            ->reorderable(false)
            ->paginated(false)
            ->defaultSort('street', 'asc')
            ->columns([
                Tables\Columns\Layout\Panel::make([
                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\Layout\Split::make([
                            Tables\Columns\TextColumn::make('type')
                                ->label('Typ')
                                ->badge()
                                ->color('info')
                                ->alignLeft(),
                            Tables\Columns\TextColumn::make('postal_code')
                                ->label('Kod')
                                ->badge()
                                ->color('secondary')
                                ->alignRight(),
                        ])->extraAttributes(['class' => 'justify-between items-start']),

                        Tables\Columns\TextColumn::make('street')
                            ->label('')
                            ->weight('bold')
                            ->extraAttributes(['class' => 'text-lg mb-2']),

                        Tables\Columns\TextColumn::make('city')
                            ->label('')
                            ->extraAttributes(['class' => 'text-gray-600']),
                    ])->space(1),
                ])->extraAttributes(['class' => 'p-3']),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->label('Edytuj'),
                    Tables\Actions\DeleteAction::make()
                        ->label('Usuń'),
                ])
                    ->icon('heroicon-o-cog-6-tooth')
                    ->button()
                    ->label('Akcje'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Dodaj adres'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
