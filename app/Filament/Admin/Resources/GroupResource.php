<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\GroupResource\Pages;
use App\Filament\Admin\Resources\GroupResource\RelationManagers;
use App\Models\Group;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Resources\GroupResource\RelationManagers\UsersRelationManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class GroupResource extends Resource
{
    protected static ?string $model = Group::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Użytkownicy i Grupy';
    protected static ?int $navigationSort = 2;
    protected static ?string $modelLabel = 'grupa';
    protected static ?string $pluralModelLabel = 'grupy';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nazwa')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Nazwa grupy')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('description')
                            ->label('Opis')
                            ->maxLength(30)
                            ->placeholder('Krótki opis grupy')
                            ->helperText(fn ($state) => strlen($state) . '/30 znaków')
                            ->live()
                            ->columnSpanFull(),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'active' => 'Aktywna',
                                        'inactive' => 'Nieaktywna',
                                        'full' => 'Pełna',
                                    ])
                                    ->default('active'),

                                Forms\Components\TextInput::make('max_size')
                                    ->label('Maksymalna liczba uczestników')
                                    ->numeric()
                                    ->minValue(1)
                                    ->default(7),
                            ]),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nazwa')
                    ->searchable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Opis')
                    ->searchable()
                    ->limit(30)
                    ->tooltip(function (Model $record): ?string {
                        return $record->description;
                    }),

                Tables\Columns\TextColumn::make('users_count')
                    ->label('Liczba uczestników')
                    ->counts('users')
                    ->description(fn (Group $record) => "{$record->users()->count()}/{$record->max_size}")
                    ->color(fn (Group $record) => 
                        $record->users()->count() >= $record->max_size ? 'danger' : 
                        ($record->users()->count() >= $record->max_size * 0.8 ? 'warning' : 'success')
                    )
                    ->alignCenter()
                    ->size('lg'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        'full' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Aktywna',
                        'inactive' => 'Nieaktywna',
                        'full' => 'Pełna',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('lessons_count')
                    ->label('Liczba zadań')
                    ->counts('lessons')
                    ->url(fn ($record) => route('filament.admin.resources.groups.edit', [
                        'record' => $record,
                        'activeRelationManager' => 1
                    ]))
                    ->color('primary'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktywna',
                        'inactive' => 'Nieaktywna',
                        'full' => 'Pełna',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('update_payment_amount')
                    ->label('Zmień kwotę')
                    ->icon('heroicon-o-banknotes')
                    ->color('warning')
                    ->size('sm')
                    ->tooltip('Zmień kwotę płatności dla grupy')
                    ->form([
                        Forms\Components\TextInput::make('amount')
                            ->label('Nowa kwota miesięczna (zł)')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->step(0.01)
                            ->suffix('zł'),
                        Forms\Components\Select::make('scope')
                            ->label('Zakres zmian')
                            ->options([
                                'current_month' => 'Tylko płatności bieżącego miesiąca',
                                'future_months' => 'Płatności przyszłych miesięcy + nowa kwota domyślna',
                                'all_months' => 'Wszystkie płatności + nowa kwota domyślna (ostrożnie!)',
                            ])
                            ->default('current_month')
                            ->required(),
                        Forms\Components\Toggle::make('confirm')
                            ->label('Potwierdzam zmianę kwoty dla wszystkich użytkowników w grupie')
                            ->required()
                            ->helperText('Ta operacja zmieni kwotę płatności dla wszystkich użytkowników w tej grupie.'),
                    ])
                    ->action(function (Group $record, array $data): void {
                        \Illuminate\Support\Facades\Log::info('Updating payment amount for group', [
                            'group_id' => $record->id,
                            'group_name' => $record->name,
                            'new_amount' => $data['amount'],
                            'scope' => $data['scope']
                        ]);
                        
                        $amount = (float) $data['amount'];
                        $scope = $data['scope'];
                        
                        // Pobierz wszystkich użytkowników w grupie
                        $users = $record->users()->where('is_active', true)->get();
                        
                        \Illuminate\Support\Facades\Log::info('Found users in group', [
                            'group_id' => $record->id,
                            'users_count' => $users->count(),
                            'users' => $users->pluck('name', 'id')->toArray()
                        ]);
                        
                        $updatedUsers = 0;
                        $updatedPayments = 0;
                        
                        foreach ($users as $user) {
                            \Illuminate\Support\Facades\Log::info('Processing user', [
                                'user_id' => $user->id,
                                'user_name' => $user->name,
                                'current_amount' => $user->amount
                            ]);
                            
                            // Aktualizuj płatności w zależności od zakresu
                            $paymentQuery = $user->payments();
                            
                            switch ($scope) {
                                case 'current_month':
                                    $paymentQuery->where('month', now()->format('Y-m'));
                                    break;
                                case 'future_months':
                                    $paymentQuery->where('month', '>=', now()->format('Y-m'));
                                    break;
                                case 'all_months':
                                    // Wszystkie płatności
                                    break;
                            }
                            
                            $paymentCount = $paymentQuery->update(['amount' => $amount]);
                            $updatedPayments += $paymentCount;
                            
                            // Jeśli aktualizujemy przyszłe miesiące lub wszystkie, zaktualizuj też kwotę użytkownika
                            if ($scope === 'future_months' || $scope === 'all_months') {
                                $user->update(['amount' => $amount]);
                                $updatedUsers++;
                                \Illuminate\Support\Facades\Log::info('Updated user amount for future payments', [
                                    'user_id' => $user->id,
                                    'new_amount' => $amount
                                ]);
                            }
                            
                            \Illuminate\Support\Facades\Log::info('Updated user payments', [
                                'user_id' => $user->id,
                                'payment_count' => $paymentCount,
                                'scope' => $scope
                            ]);
                        }
                        
                        \Illuminate\Support\Facades\Log::info('Payment amount update completed', [
                            'group_id' => $record->id,
                            'updated_users' => $updatedUsers,
                            'updated_payments' => $updatedPayments,
                            'scope' => $scope
                        ]);
                        
                        $message = "Zaktualizowano {$updatedPayments} płatności";
                        if ($updatedUsers > 0) {
                            $message .= " i kwotę dla {$updatedUsers} użytkowników";
                        }
                        $message .= " w grupie '{$record->name}'";
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Kwota płatności zaktualizowana')
                            ->body($message)
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Zmień kwotę płatności dla grupy')
                    ->modalDescription(fn (Group $record) => "Zmienisz kwotę płatności dla wszystkich użytkowników w grupie '{$record->name}'")
                    ->modalSubmitActionLabel('Zmień kwotę'),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                Tables\Actions\BulkAction::make('update_payment_amount_bulk')
                    ->label('Zmień kwotę')
                    ->icon('heroicon-o-banknotes')
                    ->color('warning')
                    ->size('sm')
                    ->tooltip('Zmień kwotę płatności dla zaznaczonych grup')
                    ->form([
                        Forms\Components\TextInput::make('amount')
                            ->label('Nowa kwota miesięczna (zł)')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->step(0.01)
                            ->suffix('zł'),
                        Forms\Components\Select::make('scope')
                            ->label('Zakres zmian')
                            ->options([
                                'current_month' => 'Tylko płatności bieżącego miesiąca',
                                'future_months' => 'Płatności przyszłych miesięcy + nowa kwota domyślna',
                                'all_months' => 'Wszystkie płatności + nowa kwota domyślna (ostrożnie!)',
                            ])
                            ->default('current_month')
                            ->required(),
                        Forms\Components\Toggle::make('confirm')
                            ->label('Potwierdzam zmianę kwoty dla wszystkich użytkowników w zaznaczonych grupach')
                            ->required()
                            ->helperText('Ta operacja zmieni kwotę płatności dla wszystkich użytkowników w zaznaczonych grupach.'),
                    ])
                    ->action(function (Collection $records, array $data): void {
                        $amount = (float) $data['amount'];
                        $scope = $data['scope'];
                        $totalUpdatedUsers = 0;
                        $totalUpdatedPayments = 0;
                        
                        foreach ($records as $group) {
                            $users = $group->users()->where('is_active', true)->get();
                            $updatedUsers = 0;
                            $updatedPayments = 0;
                            
                            foreach ($users as $user) {
                                // Aktualizuj płatności w zależności od zakresu
                                $paymentQuery = $user->payments();
                                
                                switch ($scope) {
                                    case 'current_month':
                                        $paymentQuery->where('month', now()->format('Y-m'));
                                        break;
                                    case 'future_months':
                                        $paymentQuery->where('month', '>=', now()->format('Y-m'));
                                        break;
                                    case 'all_months':
                                        // Wszystkie płatności
                                        break;
                                }
                                
                                $paymentCount = $paymentQuery->update(['amount' => $amount]);
                                $updatedPayments += $paymentCount;
                                
                                // Jeśli aktualizujemy przyszłe miesiące lub wszystkie, zaktualizuj też kwotę użytkownika
                                if ($scope === 'future_months' || $scope === 'all_months') {
                                    $user->update(['amount' => $amount]);
                                    $updatedUsers++;
                                }
                            }
                            
                            $totalUpdatedUsers += $updatedUsers;
                            $totalUpdatedPayments += $updatedPayments;
                        }
                        
                        $message = "Zaktualizowano {$totalUpdatedPayments} płatności";
                        if ($totalUpdatedUsers > 0) {
                            $message .= " i kwotę dla {$totalUpdatedUsers} użytkowników";
                        }
                        $message .= " w {$records->count()} grupach";
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Kwota płatności zaktualizowana')
                            ->body($message)
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Zmień kwotę płatności dla grup')
                    ->modalDescription('Zmienisz kwotę płatności dla wszystkich użytkowników w zaznaczonych grupach')
                    ->modalSubmitActionLabel('Zmień kwotę'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\UsersRelationManager::class,
            RelationManagers\LessonsRelationManager::class,
            RelationManagers\PaymentsRelationManager::class,
            RelationManagers\AttendancesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGroups::route('/'),
            'create' => Pages\CreateGroup::route('/create'),
            'edit' => Pages\EditGroup::route('/{record}/edit'),
        ];
    }
    public static function getWidgets(): array
    {
        return [
            \App\Filament\Admin\Resources\GroupResource\Widgets\GroupStats::class,

        ];
    }
    public static function getModelLabel(): string
    {
        return 'Grupy';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Grupy';
    }
}
