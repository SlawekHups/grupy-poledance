<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserMailMessageResource\Pages;
use App\Models\UserMailMessage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserMailMessageResource extends Resource
{
    protected static ?string $model = UserMailMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'Komunikacja';

    protected static ?string $navigationLabel = 'Wiadomości Email';

    protected static ?int $navigationSort = 3;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user_id')
                    ->label('ID Użytkownika')
                    ->numeric()
                    ->required(),
                Forms\Components\Select::make('direction')
                    ->options([
                        'in' => 'Odebrana',
                        'out' => 'Wysłana',
                    ])
                    ->label('Kierunek')
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required(),
                Forms\Components\TextInput::make('subject')
                    ->label('Temat')
                    ->required(),
                Forms\Components\Textarea::make('content')
                    ->label('Treść')
                    ->rows(10)
                    ->required()
                    ->default(''),
                Forms\Components\DateTimePicker::make('sent_at')
                    ->label('Data wysłania/odebrania')
                    ->required()
                    ->default(now()),
                Forms\Components\KeyValue::make('headers')
                    ->label('Nagłówki')
                    ->keyLabel('Nazwa')
                    ->valueLabel('Wartość')
                    ->disabled(),
                Forms\Components\TextInput::make('message_id')
                    ->label('ID Wiadomości')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('direction')
                    ->label('Kierunek')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'in' => 'success',
                        'out' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'in' => 'Odebrana',
                        'out' => 'Wysłana',
                        default => 'Nieznany',
                    }),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subject')
                    ->label('Temat')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('content')
                    ->label('Treść')
                    ->limit(50),
                Tables\Columns\TextColumn::make('sent_at')
                    ->label('Data')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                // Tables\Filters\SelectFilter::make('direction')
                //     ->options([
                //         'in' => 'Odebrane',
                //         'out' => 'Wysłane',
                //     ])
                //     ->label('Kierunek'),
                // Tables\Filters\SelectFilter::make('user_id')
                //     ->relationship('user', 'name')
                //     ->label('Użytkownik')
                //     ->searchable()
                //     ->preload(),
                Tables\Filters\Filter::make('sent_at')
                    ->form([
                        Forms\Components\DatePicker::make('sent_from')
                            ->label('Od'),
                        Forms\Components\DatePicker::make('sent_until')
                            ->label('Do'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['sent_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('sent_at', '>=', $date),
                            )
                            ->when(
                                $data['sent_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('sent_at', '<=', $date),
                            );
                    })
                    ->label('Data wysłania/odebrania'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\Action::make('reply')
                //     ->label('Odpowiedz')
                //     ->icon('heroicon-o-reply')
                //     ->color('info')
                //     ->visible(fn (UserMailMessage $record) => $record->direction === 'in')
                //     ->url(fn (UserMailMessage $record) => "mailto:{$record->email}?subject=Re: {$record->subject}")
                //     ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sent_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->forRegisteredUsers()
            ->without('user'); // Nie ładuj relacji user
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
            'index' => Pages\ListUserMailMessages::route('/'),
            'create' => Pages\CreateUserMailMessage::route('/create'),
            'view' => Pages\ViewUserMailMessage::route('/{record}'),
            'edit' => Pages\EditUserMailMessage::route('/{record}/edit'),
        ];
    }
} 