<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AddressRelationManagerResource\RelationManagers\AddressesRelationManager;
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

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Użytkownicy i Grupy';
    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)
            ->whereNot('role', 'admin')
            ->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Imię i nazwisko')
                    ->required()
                    ->minLength(3)
                    ->maxLength(45)
                    ->validationMessages([
                        'minLength' => 'Imię musi mieć minimum 3 znaki.',
                        'maxLength' => 'Imię może mieć maksymalnie 10 znaków.',
                    ]),
                TextInput::make('email')
                    ->label('E-mail')
                    ->required()
                    ->email()
                    ->rules([
                        fn($context, $record) => \Illuminate\Validation\Rule::unique('users', 'email')->ignore($record?->id),
                    ])
                    ->validationMessages([
                        'unique' => 'Ten e-mail już istnieje w systemie.',
                    ]),

                TextInput::make('phone')
                    ->label('Telefon')
                    ->tel()
                    ->required()
                    ->minLength(9)
                    ->maxLength(9)
                    ->rule(function () {
                        return \Illuminate\Validation\Rule::unique('users', 'phone')->ignore(request()->route('record'));
                    })
                    ->validationMessages([
                        'unique' => 'Ten numer telefonu już istnieje w systemie.',
                        'min' => 'Numer telefonu musi mieć co najmniej 9 cyfr.',
                        'max' => 'Numer telefonu nie może mieć więcej niż 15 znaków.',
                    ])
                    ->dehydrated(true)
                    ->afterStateHydrated(function (\Filament\Forms\Components\TextInput $component, $state) {
                        if (str_starts_with($state, '+48')) {
                            $component->state(substr($state, 3));
                        }
                    })
                    ->dehydrateStateUsing(function ($state) {
                        $number = preg_replace('/\D/', '', $state);
                        if (strlen($number) === 9) {
                            return '+48' . $number;
                        } elseif (str_starts_with($number, '48') && strlen($number) === 11) {
                            return '+' . $number;
                        } elseif (str_starts_with($number, '+48') && strlen($number) === 12) {
                            return $number;
                        }
                        return $state;
                    }),
                DatePicker::make('joined_at')->label('Data zapisu'),

                Select::make('group_id')
                    ->label('Grupa')
                    ->relationship('group', 'name')
                    ->searchable()
                    ->preload(),

                TextInput::make('password')
                    ->password()
                    ->label('Hasło')
                    ->required(fn(string $context) => $context === 'create')
                    ->dehydrated(fn($state) => filled($state))
                    ->dehydrateStateUsing(fn($state) => bcrypt($state))
                    ->maxLength(255),
                TextInput::make('amount')
                    ->label('Kwota miesięczna (zł)')
                    ->numeric()
                    ->required()
                    ->default(200),
                Forms\Components\DateTimePicker::make('terms_accepted_at')
                    ->label('Akceptacja regulaminu')
                    ->nullable()
                    ->default(null)
                    ->dehydrated(true)
                    ->dehydrateStateUsing(fn($state) => $state ?: null),
                    Toggle::make('is_active')->label('Aktywny'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Imię i nazwisko')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Telefon')
                    ->searchable(),
                TextColumn::make('group.name')
                    ->label('Grupa')
                    ->searchable(),
                TextColumn::make('amount')
                    ->label('Kwota (PLN)')
                    ->suffix(' zł')
                    ->searchable(),
                BooleanColumn::make('is_active')
                    ->label('Aktywny'),
                Tables\Columns\BooleanColumn::make('terms_accepted_at')
                    ->label('Regulamin zaakceptowany')
                    ->trueIcon('heroicon-o-document-check')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->state(fn($record) => !is_null($record->terms_accepted_at)),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->options([
                        'user' => 'Użytkownik',
                        'admin' => 'Administrator',
                    ])
                    ->label('Rola'),
                Tables\Filters\SelectFilter::make('group_id')
                    ->relationship('group', 'name')
                    ->label('Grupa')
                    ->searchable()
                    ->preload(),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('Wszystkie')
                    ->trueLabel('Aktywny')
                    ->falseLabel('Nieaktywny'),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Od'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Do'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->label('Data utworzenia'),
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
            AddressesRelationManager::class,
            \App\Filament\Admin\Resources\UserResource\RelationManagers\PaymentsRelationManager::class,
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
