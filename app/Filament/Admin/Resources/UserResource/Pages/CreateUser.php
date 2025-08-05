<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Events\UserInvited;
use App\Filament\Admin\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Form;
use Filament\Forms\Components\Grid;
use Filament\Notifications\Notification;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)
                    ->schema([
                        // Lewa kolumna: główne dane użytkownika
                        Section::make('Dane użytkownika')
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
                                TextInput::make('phone')
                                    ->label('Telefon')
                                    ->tel()
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
                                Select::make('group_id')
                                    ->label('Grupa')
                                    ->relationship('group', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->default(1)
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->label('Nazwa grupy')
                                            ->required(),
                                        TextInput::make('description')
                                            ->label('Opis')
                                            ->nullable(),
                                    ]),
                            ])->columns(2),

                        // Prawa kolumna: ustawienia
                        Grid::make()
                            ->schema([
                                Section::make('Ustawienia')
                                    ->schema([
                                        Toggle::make('is_active')
                                            ->label('Użytkownik aktywny')
                                            ->default(false),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    protected function afterCreate(): void
    {
        // Wyślij zaproszenie do użytkownika
        UserInvited::dispatch($this->record);

        Notification::make()
            ->title('Użytkownik został utworzony')
            ->body('Zaproszenie zostało wysłane na adres: ' . $this->record->email)
            ->success()
            ->send();
    }
}