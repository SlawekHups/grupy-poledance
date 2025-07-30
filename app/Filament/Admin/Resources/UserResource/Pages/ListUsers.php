<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Admin\Resources\UserResource\Widgets\UserStats;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modal()
                ->steps([
                    \Filament\Forms\Components\Wizard\Step::make('Podstawowe dane')
                        ->description('Wprowadź podstawowe dane użytkownika')
                        ->schema([
                            \Filament\Forms\Components\TextInput::make('name')
                                ->label('Imię i nazwisko')
                                ->required()
                                ->minLength(3)
                                ->maxLength(45),
                            \Filament\Forms\Components\TextInput::make('email')
                                ->label('E-mail')
                                ->required()
                                ->email()
                                ->unique('users', 'email'),
                            \Filament\Forms\Components\TextInput::make('password')
                                ->label('Hasło')
                                ->password()
                                ->required()
                                ->minLength(8)
                                ->dehydrateStateUsing(fn($state) => bcrypt($state)),
                        ])
                        ->columns(2),
                    \Filament\Forms\Components\Wizard\Step::make('Dane kontaktowe')
                        ->description('Dodaj informacje kontaktowe')
                        ->schema([
                            \Filament\Forms\Components\TextInput::make('phone')
                                ->label('Telefon')
                                ->tel()
                                ->minLength(9)
                                ->maxLength(15)
                                ->dehydrateStateUsing(function ($state) {
                                    $number = preg_replace('/\\D/', '', $state);
                                    if (strlen($number) === 9) {
                                        return '+48' . $number;
                                    }
                                    return $state;
                                }),
                            \Filament\Forms\Components\TextInput::make('address')
                                ->label('Adres'),
                            \Filament\Forms\Components\DatePicker::make('joined_at')
                                ->label('Data zapisu')
                                ->default(now()),
                        ]),
                    \Filament\Forms\Components\Wizard\Step::make('Grupa')
                        ->description('Wybierz grupę dla użytkownika')
                        ->schema([
                            \Filament\Forms\Components\Select::make('group_id')
                                ->label('Grupa')
                                ->relationship('group', 'name')
                                ->searchable()
                                ->preload()
                                ->createOptionForm([
                                    \Filament\Forms\Components\TextInput::make('name')
                                        ->label('Nazwa grupy')
                                        ->required(),
                                    \Filament\Forms\Components\TextInput::make('description')
                                        ->label('Opis')
                                        ->nullable(),
                                ]),
                        ]),
                    \Filament\Forms\Components\Wizard\Step::make('Ustawienia')
                        ->description('Skonfiguruj dodatkowe opcje')
                        ->schema([
                            \Filament\Forms\Components\Toggle::make('is_active')
                                ->label('Użytkownik aktywny')
                                ->default(true),
                        ]),
                ]),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            UserStats::class,
        ];
    }
}
