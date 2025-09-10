<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PreRegistrationResource\Pages;
use App\Filament\Admin\Resources\PreRegistrationResource\RelationManagers;
use App\Models\PreRegistration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PreRegistrationResource extends Resource
{
    protected static ?string $model = PreRegistration::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    
    protected static ?string $navigationLabel = 'Pre-rejestracje';
    
    protected static ?string $modelLabel = 'Pre-rejestracja';
    
    protected static ?string $pluralModelLabel = 'Pre-rejestracje';
    
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Imię i nazwisko')
                    ->required()
                    ->maxLength(255),
                    
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                    
                Forms\Components\TextInput::make('phone')
                    ->label('Telefon')
                    ->tel()
                    ->required()
                    ->maxLength(20),
                    
                Forms\Components\TextInput::make('token')
                    ->label('Token')
                    ->disabled()
                    ->dehydrated(false)
                    ->helperText('Token jest generowany automatycznie'),
                    
                Forms\Components\DateTimePicker::make('expires_at')
                    ->label('Wygasa')
                    ->required()
                    ->native(false),
                    
                Forms\Components\Toggle::make('used')
                    ->label('Użyty')
                    ->disabled()
                    ->dehydrated(false),
                    
                Forms\Components\DateTimePicker::make('used_at')
                    ->label('Data użycia')
                    ->disabled()
                    ->dehydrated(false),
                    
                Forms\Components\Textarea::make('notes')
                    ->label('Notatki')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('name')
                    ->label('Imię i nazwisko')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefon')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('token')
                    ->label('Token')
                    ->limit(8)
                    ->copyable()
                    ->tooltip('Kliknij aby skopiować'),
                    
                Tables\Columns\IconColumn::make('used')
                    ->label('Użyty')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                    
                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Wygasa')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->color(fn ($record) => $record->expires_at < now() ? 'danger' : 'success'),
                    
                Tables\Columns\TextColumn::make('used_at')
                    ->label('Data użycia')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->placeholder('Nie użyty'),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Utworzono')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Actions\Action::make('view_form')
                    ->label('Zobacz formularz')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn ($record) => route('pre-register', $record->token))
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => $record->isValid()),
                    
                Tables\Actions\Action::make('copy_link')
                    ->label('Kopiuj link')
                    ->icon('heroicon-o-clipboard')
                    ->color('info')
                    ->modalHeading('Kopiuj link pre-rejestracji')
                    ->modalDescription('Kliknij przycisk poniżej, aby skopiować link do schowka')
                    ->modalContent(function ($record) {
                        $url = route('pre-register', $record->token);
                        return view('filament.admin.resources.pre-registration-resource.modals.copy-link-simple', [
                            'url' => $url,
                            'token' => $record->token,
                            'record' => $record
                        ]);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Zamknij')
                    ->visible(fn ($record) => $record->isValid()),
                    
                Tables\Actions\Action::make('convert_to_user')
                    ->label('Konwertuj na użytkownika')
                    ->icon('heroicon-o-user-plus')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Konwersja na użytkownika')
                    ->modalDescription('Wybierz grupę dla nowego użytkownika. Możesz zostawić puste, aby przypisać później.')
                    ->form([
                        \Filament\Forms\Components\Select::make('group_id')
                            ->label('Grupa')
                            ->options(\App\Models\Group::all()->pluck('name', 'id'))
                            ->searchable()
                            ->placeholder('Wybierz grupę (opcjonalne)')
                            ->helperText('Możesz przypisać użytkownika do grupy teraz lub później'),
                    ])
                    ->action(function ($record, array $data) {
                        try {
                            $user = $record->convertToUser($data['group_id'] ?? null);
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Konwersja zakończona')
                                ->body("Użytkownik {$user->name} został utworzony" . 
                                      ($data['group_id'] ? " i przypisany do grupy" : ""))
                                ->success()
                                ->send();
                                
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Błąd konwersji')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->visible(fn ($record) => $record->canConvertToUser()),
                    
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListPreRegistrations::route('/'),
            'create' => Pages\CreatePreRegistration::route('/create'),
            'edit' => Pages\EditPreRegistration::route('/{record}/edit'),
        ];
    }
}
