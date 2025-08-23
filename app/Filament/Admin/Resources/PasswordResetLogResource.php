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
    protected static ?string $navigationLabel = 'Logi resetowania haseł';
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
                
                Forms\Components\Select::make('admin_id')
                    ->relationship('admin', 'name')
                    ->label('Administrator')
                    ->required()
                    ->searchable(),
                
                Forms\Components\TextInput::make('admin_email')
                    ->label('Email administratora')
                    ->email()
                    ->required()
                    ->disabled(),
                
                Forms\Components\Textarea::make('reason')
                    ->label('Powód resetowania')
                    ->rows(3),
                
                Forms\Components\Select::make('reset_type')
                    ->label('Typ resetowania')
                    ->options([
                        'single' => 'Pojedynczy',
                        'bulk' => 'Masowy',
                    ])
                    ->required(),
                
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
                
                TextColumn::make('admin.name')
                    ->label('Administrator')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('reason')
                    ->label('Powód')
                    ->limit(50)
                    ->searchable(),
                
                BadgeColumn::make('reset_type')
                    ->label('Typ')
                    ->colors([
                        'primary' => 'single',
                        'warning' => 'bulk',
                    ]),
                
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
                
                SelectFilter::make('reset_type')
                    ->label('Typ resetowania')
                    ->options([
                        'single' => 'Pojedynczy',
                        'bulk' => 'Masowy',
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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListPasswordResetLogs::route('/'),
            'create' => Pages\CreatePasswordResetLog::route('/create'),
            'edit' => Pages\EditPasswordResetLog::route('/{record}/edit'),
        ];
    }
}
