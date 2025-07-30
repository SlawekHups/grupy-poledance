<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Form;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Podstawowe dane')
                        ->description('Wprowadź podstawowe dane użytkownika')
                        ->schema([
                            TextInput::make('name')
                                ->label('Imię i nazwisko')
                                ->required()
                                ->minLength(3)
                                ->maxLength(45)
                                ->validationMessages([
                                    'required' => 'To pole jest wymagane',
                                    'min' => 'Minimalna długość to 3 znaki',
                                    'max' => 'Maksymalna długość to 45 znaków',
                                ]),
                            TextInput::make('email')
                                ->label('E-mail')
                                ->required()
                                ->email()
                                ->unique('users', 'email')
                                ->validationMessages([
                                    'required' => 'To pole jest wymagane',
                                    'email' => 'Podaj prawidłowy adres email',
                                    'unique' => 'Ten email jest już zajęty',
                                ]),
                            TextInput::make('password')
                                ->label('Hasło')
                                ->password()
                                ->required()
                                ->minLength(8)
                                ->dehydrateStateUsing(fn($state) => bcrypt($state))
                                ->validationMessages([
                                    'required' => 'To pole jest wymagane',
                                    'min' => 'Hasło musi mieć minimum 8 znaków',
                                ]),
                        ])->columns(2),

                    Step::make('Dane kontaktowe')
                        ->description('Dodaj informacje kontaktowe')
                        ->schema([
                            TextInput::make('phone')
                                ->label('Telefon')
                                ->tel()
                                ->required()
                                ->minLength(9)
                                ->maxLength(9)
                                ->dehydrateStateUsing(function ($state) {
                                    if (!$state) return null;
                                    $number = preg_replace('/\\D/', '', $state);
                                    if (strlen($number) === 9) {
                                        return '+48' . $number;
                                    }
                                    return $state;
                                }),
                            TextInput::make('amount')
                                ->label('Kwota miesięczna (zł)')
                                ->numeric()
                                ->required()
                                ->default(200),
                            DatePicker::make('joined_at')
                                ->label('Data zapisu')
                                ->default(now()),
                        ])->columns(2),

                    Step::make('Grupa')
                        ->description('Wybierz grupę dla użytkownika')
                        ->schema([
                            Select::make('group_id')
                                ->label('Grupa')
                                ->relationship('group', 'name')
                                ->searchable()
                                ->preload()
                                ->createOptionForm([
                                    TextInput::make('name')
                                        ->label('Nazwa grupy')
                                        ->required(),
                                    TextInput::make('description')
                                        ->label('Opis')
                                        ->nullable(),
                                ]),
                        ]),

                    Step::make('Ustawienia')
                        ->description('Skonfiguruj dodatkowe opcje')
                        ->schema([
                            Toggle::make('is_active')
                                ->label('Użytkownik aktywny')
                                ->default(true),
                        ])->columns(2),
                ])->columnSpanFull()
            ]);
    }
}