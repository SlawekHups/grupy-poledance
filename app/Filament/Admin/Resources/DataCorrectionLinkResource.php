<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\DataCorrectionLinkResource\Pages;
use App\Models\DataCorrectionLink;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DataCorrectionLinkResource extends Resource
{
    protected static ?string $model = DataCorrectionLink::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';
    
    protected static ?string $navigationLabel = 'Linki do poprawy danych';
    
    protected static ?string $modelLabel = 'Link do poprawy danych';
    
    protected static ?string $pluralModelLabel = 'Linki do poprawy danych';
    
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Użytkownik')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required()
                    ->disabled(fn ($record) => $record !== null),
                    
                Forms\Components\TextInput::make('token')
                    ->label('Token')
                    ->disabled()
                    ->dehydrated(false)
                    ->helperText('Token jest generowany automatycznie'),
                    
                Forms\Components\DateTimePicker::make('expires_at')
                    ->label('Wygasa')
                    ->required()
                    ->native(false)
                    ->default(now()->addHours(24)),
                    
                Forms\Components\Toggle::make('used')
                    ->label('Użyty')
                    ->disabled()
                    ->dehydrated(false),
                    
                Forms\Components\DateTimePicker::make('used_at')
                    ->label('Data użycia')
                    ->disabled()
                    ->dehydrated(false),
                    
                Forms\Components\CheckboxList::make('allowed_fields')
                    ->label('Pola do edycji')
                    ->options([
                        'name' => 'Imię i nazwisko (zawsze dostępne)',
                        'email' => 'Email',
                        'phone' => 'Telefon',
                        'address' => 'Adres',
                        'city' => 'Miasto',
                        'postal_code' => 'Kod pocztowy',
                    ])
                    ->columns(2)
                    ->default(['name', 'email', 'phone'])
                    ->required()
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Zawsze dodaj 'name' do zaznaczonych pól
                        if (!in_array('name', $state ?? [])) {
                            $state = array_merge($state ?? [], ['name']);
                            $set('allowed_fields', $state);
                        }
                    }),
                    
                Forms\Components\Textarea::make('notes')
                    ->label('Notatki')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->contentGrid([
                'sm' => 1,
                'md' => 1,
                'xl' => 1,
            ])
            ->recordClasses('rounded-xl border bg-white shadow-sm hover:shadow-md transition hover:bg-gray-50')
            ->columns([
                Tables\Columns\Layout\Panel::make([
                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\Layout\Split::make([
                            Tables\Columns\TextColumn::make('user.name')
                                ->label('Użytkownik')
                                ->searchable()
                                ->sortable()
                                ->weight('bold')
                                ->limit(40),
                            Tables\Columns\TextColumn::make('expires_at')
                                ->label('Wygasa')
                                ->dateTime('d.m.Y H:i')
                                ->sortable()
                                ->color(fn ($record) => $record->expires_at < now() ? 'danger' : 'success')
                                ->alignRight(),
                        ])->extraAttributes(['class' => 'justify-between items-start']),

                        Tables\Columns\Layout\Split::make([
                            Tables\Columns\TextColumn::make('user.email')
                                ->label('Email użytkownika')
                                ->searchable()
                                ->sortable()
                                ->limit(30),
                            Tables\Columns\TextColumn::make('used')
                                ->label('Status')
                                ->badge()
                                ->color(fn ($state) => $state ? 'success' : 'danger')
                                ->formatStateUsing(fn ($state) => $state ? 'Użyty' : 'Nie użyty')
                                ->alignRight(),
                        ])->extraAttributes(['class' => 'justify-between items-center']),

                        Tables\Columns\Layout\Split::make([
                            Tables\Columns\TextColumn::make('allowed_fields')
                                ->label('Pola do edycji')
                                ->badge()
                                ->separator(', ')
                                ->formatStateUsing(function ($state) {
                                    $labels = [
                                        'name' => 'Imię',
                                        'email' => 'Email',
                                        'phone' => 'Telefon',
                                        'address' => 'Adres',
                                        'city' => 'Miasto',
                                        'postal_code' => 'Kod',
                                    ];
                                    return collect($state)->map(fn($field) => $labels[$field] ?? $field)->join(', ');
                                }),
                            Tables\Columns\TextColumn::make('token')
                                ->label('Token')
                                ->limit(8)
                                ->copyable()
                                ->tooltip('Kliknij aby skopiować')
                                ->alignRight(),
                        ])->extraAttributes(['class' => 'justify-between items-center']),

                        Tables\Columns\TextColumn::make('used_at')
                            ->label('Data użycia')
                            ->dateTime('d.m.Y H:i')
                            ->sortable()
                            ->placeholder('Nie użyty')
                            ->extraAttributes(['class' => 'text-sm text-gray-600']),
                    ]),
                ]),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('used')
                    ->label('Status')
                    ->trueLabel('Użyte')
                    ->falseLabel('Nie użyte')
                    ->native(false),
                    
                Tables\Filters\Filter::make('expired')
                    ->label('Wygasłe')
                    ->query(fn (Builder $query): Builder => $query->where('expires_at', '<', now()))
                    ->toggle(),
                    
                Tables\Filters\Filter::make('valid')
                    ->label('Ważne')
                    ->query(fn (Builder $query): Builder => $query->where('expires_at', '>', now())->where('used', false))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('view_form')
                        ->label('Zobacz formularz')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->url(fn ($record) => route('data-correction', $record->token))
                        ->openUrlInNewTab()
                        ->visible(fn ($record) => $record->isValid()),
                        
                    Tables\Actions\Action::make('copy_link')
                        ->label('Kopiuj link')
                        ->icon('heroicon-o-clipboard')
                        ->color('info')
                        ->modalHeading('Kopiuj link do poprawy danych')
                        ->modalDescription('Kliknij przycisk poniżej, aby skopiować link do schowka')
                        ->modalContent(function ($record) {
                            $url = route('data-correction', $record->token);
                            return view('filament.admin.resources.data-correction-link-resource.modals.copy-link', [
                                'url' => $url,
                                'token' => $record->token,
                                'record' => $record
                            ]);
                        })
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Zamknij')
                        ->visible(fn ($record) => $record->isValid()),
                        
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
                ->label('Akcje')
                ->icon('heroicon-o-ellipsis-vertical')
                ->size('sm')
                ->color('gray')
                ->button()
                ->extraAttributes(['class' => 'w-full sm:w-auto'])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ])
                ->extraAttributes(['class' => 'flex flex-col sm:flex-row gap-2 w-full sm:w-auto'])
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListDataCorrectionLinks::route('/'),
            'create' => Pages\CreateDataCorrectionLink::route('/create'),
            'edit' => Pages\EditDataCorrectionLink::route('/{record}/edit'),
        ];
    }
}
