<?php

namespace App\Filament\UserPanel\Resources;

use App\Filament\UserPanel\Resources\UserMailMessageResource\Pages;
use App\Models\UserMailMessage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class UserMailMessageResource extends Resource
{
    protected static ?string $model = UserMailMessage::class;

    protected static ?string $navigationGroup = 'Komunikacja';

    protected static ?string $navigationLabel = 'Moje Wiadomości';

    protected static ?int $navigationSort = 3;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('user_id', Auth::id())->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('direction')
                    ->options([
                        'in' => 'Odebrana',
                        'out' => 'Wysłana',
                    ])
                    ->label('Kierunek')
                    ->disabled(),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->disabled(),
                Forms\Components\TextInput::make('subject')
                    ->label('Temat')
                    ->disabled(),
                Forms\Components\Textarea::make('content')
                    ->label('Treść')
                    ->rows(10)
                    ->disabled(),
                Forms\Components\DateTimePicker::make('sent_at')
                    ->label('Data wysłania/odebrania')
                    ->disabled(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Szczegóły')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('direction')
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
                        TextEntry::make('email')
                            ->label('Email'),
                        TextEntry::make('subject')
                            ->label('Temat')
                            ->columnSpanFull(),
                        TextEntry::make('sent_at')
                            ->label('Data')
                            ->dateTime('d.m.Y H:i'),
                    ]),
                Section::make('Treść wiadomości')
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('content')
                            ->label('')
                            ->html()
                            ->formatStateUsing(function (?string $state) {
                                if (!$state) {
                                    return new HtmlString('<em>Brak treści</em>');
                                }
                                $decoded = html_entity_decode($state, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                                $looksLikeHtml = Str::contains($decoded, ['</', '<br', '<p', '<div', '<span', '<ul', '<ol', '<li', '<strong', '<em', '<a ', '<table', '<style', '<img', '<h1', '<h2', '<h3']);
                                if (!$looksLikeHtml) {
                                    $decoded = preg_replace('/[^\n\r\{\}]+\{[\s\S]*?\}/m', '', $decoded);
                                    $decoded = preg_replace('/\n{3,}/', "\n\n", $decoded);
                                }
                                $html = $looksLikeHtml ? $decoded : Str::markdown($decoded);
                                return new HtmlString($html);
                            })
                            ->extraAttributes(['class' => 'prose max-w-none whitespace-pre-wrap break-words']),
                    ]),
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
                Tables\Columns\TextColumn::make('sent_at')
                    ->label('Data')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('direction')
                    ->options([
                        'in' => 'Odebrane',
                        'out' => 'Wysłane',
                    ])
                    ->label('Kierunek'),
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
                Tables\Actions\Action::make('reply')
                    ->label('Odpowiedz')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('info')
                    ->visible(fn (UserMailMessage $record) => $record->direction === 'in')
                    ->url(fn (UserMailMessage $record) => "mailto:" . config('mail.from.address') . "?subject=Re: {$record->subject}&body=Odpowiedź na wiadomość od: {$record->email}%0D%0A%0D%0A")
                    ->openUrlInNewTab()
                    ->tooltip('Odpowiedz administratorowi systemu'),
            ])
            ->defaultSort('sent_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $userId = \Illuminate\Support\Facades\Auth::id();
        
        // Debug - loguj ID zalogowanego użytkownika
        \Illuminate\Support\Facades\Log::info('UserPanel UserMailMessage - zalogowany użytkownik ID:', ['user_id' => $userId]);
        
        $query = parent::getEloquentQuery()
            ->where('user_id', $userId); // Bezpośrednie filtrowanie po user_id
        
        // Debug - loguj zapytanie SQL
        \Illuminate\Support\Facades\Log::info('UserPanel UserMailMessage - SQL query:', [
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings(),
            'user_id' => $userId
        ]);
        
        return $query;
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
            'view' => Pages\ViewUserMailMessage::route('/{record}'),
        ];
    }
} 