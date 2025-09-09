<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PasswordResetLogResource\Pages;
use App\Models\PasswordResetLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class PasswordResetLogResource extends Resource
{
    protected static ?string $model = PasswordResetLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';
    protected static ?string $navigationGroup = 'Użytkownicy i Grupy';
    protected static ?string $navigationLabel = 'Logi resetów haseł';
    protected static ?string $modelLabel = 'Log resetu hasła';
    protected static ?string $pluralModelLabel = 'Logi resetów haseł';
    protected static ?int $navigationSort = 5;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $pendingCount = static::getModel()::where('status', 'pending')->count();
        $expiredCount = static::getModel()::where('status', 'expired')->count();
        
        if ($expiredCount > 0) return 'danger';
        if ($pendingCount > 0) return 'warning';
        return 'success';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Użytkownik')
                    ->required()
                    ->searchable(),
                
                Forms\Components\TextInput::make('user_email')
                    ->label('Email użytkownika')
                    ->email()
                    ->required()
                    ->disabled(),
                
                Forms\Components\Textarea::make('reason')
                    ->label('Powód resetowania')
                    ->rows(3),
                
                Forms\Components\DateTimePicker::make('token_expires_at')
                    ->label('Data wygaśnięcia tokenu')
                    ->required()
                    ->disabled(),
                
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Oczekujący',
                        'completed' => 'Zakończony',
                        'expired' => 'Wygasły',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('user.name')
                    ->label('Użytkownik')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('user_email')
                    ->label('Email użytkownika')
                    ->searchable(),
                
                TextColumn::make('reason')
                    ->label('Powód')
                    ->limit(50)
                    ->searchable(),
                
                TextColumn::make('token_expires_at')
                    ->label('Wygasa')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->color(fn (PasswordResetLog $record): string => 
                        $record->isExpired() ? 'danger' : 
                        ($record->token_expires_at->diffInHours(now()) < 24 ? 'warning' : 'success')
                    ),
                
                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'completed',
                        'warning' => 'pending',
                        'danger' => 'expired',
                    ]),
                
                TextColumn::make('created_at')
                    ->label('Utworzono')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Oczekujący',
                        'completed' => 'Zakończony',
                        'expired' => 'Wygasły',
                    ]),
                
                
                Filter::make('expired_soon')
                    ->label('Wygasa w ciągu 24h')
                    ->query(fn (Builder $query): Builder => 
                        $query->where('status', 'pending')
                            ->where('token_expires_at', '<=', Carbon::now()->addDay())
                            ->where('token_expires_at', '>', Carbon::now())
                    ),
                
                Filter::make('expired')
                    ->label('Wygasłe')
                    ->query(fn (Builder $query): Builder => 
                        $query->where('status', 'pending')
                            ->where('token_expires_at', '<', Carbon::now())
                    ),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('resend_invitation')
                        ->label('Wyślij ponownie zaproszenie')
                        ->icon('heroicon-o-envelope')
                        ->color('info')
                        ->requiresConfirmation()
                        ->modalHeading('Wyślij ponownie zaproszenie do ustawienia hasła')
                        ->action(function (\App\Models\PasswordResetLog $record) {
                            $user = $record->user;
                            if (!$user) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Błąd')
                                    ->body('Nie znaleziono użytkownika dla tego logu')
                                    ->danger()
                                    ->send();
                                return;
                            }
                            \App\Events\UserInvited::dispatch($user, \Illuminate\Support\Facades\Auth::user());
                            \Filament\Notifications\Notification::make()
                                ->title('Zaproszenie wysłane ponownie')
                                ->body('Wysłano ponownie zaproszenie do: ' . $user->email)
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\Action::make('reset_password_again')
                        ->label('Resetuj ponownie hasło')
                        ->icon('heroicon-o-key')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Na pewno ponowić reset hasła?')
                        ->modalDescription('Operacja ustawi hasło użytkownika na puste i wyśle nowe zaproszenie do ustawienia hasła.')
                        ->modalSubmitActionLabel('Tak, resetuj ponownie')
                        ->action(function (\App\Models\PasswordResetLog $record) {
                            $user = $record->user;
                            if (!$user) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Błąd')
                                    ->body('Nie znaleziono użytkownika dla tego logu')
                                    ->danger()
                                    ->send();
                                return;
                            }
                            \App\Events\PasswordResetRequested::dispatch($user, \Illuminate\Support\Facades\Auth::user(), 'Ponowienie z logów', 'single');
                            $record->update(['status' => 'pending']);
                            \Filament\Notifications\Notification::make()
                                ->title('Ponowiono reset hasła')
                                ->body('Użytkownik: ' . $user->email)
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\Action::make('mark_completed')
                        ->label('Oznacz jako zakończony')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn (\App\Models\PasswordResetLog $record) => $record->status !== 'completed')
                        ->action(function (\App\Models\PasswordResetLog $record) {
                            $record->markAsCompleted();
                            \Filament\Notifications\Notification::make()
                                ->title('Zmieniono status')
                                ->body('Log oznaczono jako zakończony')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\Action::make('mark_expired')
                        ->label('Oznacz jako wygasły')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn (\App\Models\PasswordResetLog $record) => $record->status !== 'expired')
                        ->action(function (\App\Models\PasswordResetLog $record) {
                            $record->markAsExpired();
                            \Filament\Notifications\Notification::make()
                                ->title('Zmieniono status')
                                ->body('Log oznaczono jako wygasły')
                                ->warning()
                                ->send();
                        }),
                ])
                    ->button()
                    ->label('Actions')
                    ->icon('heroicon-o-cog-6-tooth'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('mark_completed_bulk')
                        ->label('Oznacz jako zakończone')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $updated = 0;
                            foreach ($records as $record) {
                                if ($record->status !== 'completed') {
                                    $record->update(['status' => 'completed']);
                                    $updated++;
                                }
                            }
                            \Filament\Notifications\Notification::make()
                                ->title('Zaktualizowano statusy')
                                ->body("Oznaczono jako zakończone: {$updated}")
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\BulkAction::make('mark_expired_bulk')
                        ->label('Oznacz jako wygasłe')
                        ->icon('heroicon-o-x-circle')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $updated = 0;
                            foreach ($records as $record) {
                                if ($record->status !== 'expired') {
                                    $record->update(['status' => 'expired']);
                                    $updated++;
                                }
                            }
                            \Filament\Notifications\Notification::make()
                                ->title('Zaktualizowano statusy')
                                ->body("Oznaczono jako wygasłe: {$updated}")
                                ->warning()
                                ->send();
                        }),
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
            'index' => Pages\ListPasswordResetLogs::route('/'),
            'create' => Pages\CreatePasswordResetLog::route('/create'),
            'edit' => Pages\EditPasswordResetLog::route('/{record}/edit'),
        ];
    }
}
