<?php

namespace App\Filament\Admin\Resources\GroupResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Log;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use App\Models\User;
use App\Models\Group;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'members';

    protected static ?string $title = 'Użytkownicy w grupie';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Użytkownik')
                    ->options(function () {
                        $group = $this->getOwnerRecord();
                        return User::query()
                            ->whereNot('role', 'admin')
                            ->where('is_active', true)
                            ->whereDoesntHave('groups', function ($q) use ($group) {
                                $q->where('groups.id', $group->id);
                            })
                            ->orderBy('name')
                            ->pluck('name', 'id');
                    })
                    ->required()
                    ->searchable()
                    ->preload()
                    ->exists('users', 'id')
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $user = User::find($state);
                            if ($user) {
                                $set('amount', number_format((float) $user->amount, 2));
                            }
                        }
                    }),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Imię i nazwisko')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->url(fn ($record) => route('filament.admin.resources.users.edit', ['record' => $record]))
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-envelope')
                    ->color('gray'),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefon')
                    ->searchable()
                    ->icon('heroicon-m-phone'),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Kwota')
                    ->suffix(' zł')
                    ->numeric()
                    ->sortable()
                    ->alignment('right'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\BooleanColumn::make('terms_accepted_at')
                    ->label('Regulamin')
                    ->trueIcon('heroicon-o-document-check')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->state(fn($record) => !is_null($record->terms_accepted_at))
                    ->tooltip(fn ($record) => $record->terms_accepted_at ? 'Zaakceptowano: ' . $record->terms_accepted_at->format('d.m.Y') : 'Brak akceptacji regulaminu'),
                Tables\Columns\IconColumn::make('has_unpaid_payments')
                    ->label('Płatności')
                    ->boolean()
                    ->getStateUsing(fn ($record) => !$record->payments()->where('paid', false)->exists())
                    ->trueIcon('heroicon-o-banknotes')
                    ->falseIcon('heroicon-o-exclamation-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->tooltip(fn ($record) => $record->payments()->where('paid', false)->exists() 
                        ? 'Ma niezapłacone płatności' 
                        : 'Wszystkie płatności opłacone')
                    ->url(fn ($record) => route('filament.admin.resources.users.edit', [
                        'record' => $record,
                        'activeRelationManager' => 1
                    ]))
                    ->openUrlInNewTab(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        '1' => 'Aktywny',
                        '0' => 'Nieaktywny',
                    ]),
                Tables\Filters\TernaryFilter::make('terms_accepted_at')
                    ->label('Regulamin')
                    ->placeholder('Wszystkie')
                    ->trueLabel('Zaakceptowany')
                    ->falseLabel('Niezaakceptowany')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('terms_accepted_at'),
                        false: fn (Builder $query) => $query->whereNull('terms_accepted_at'),
                    ),
                Tables\Filters\TernaryFilter::make('has_unpaid_payments')
                    ->label('Płatności')
                    ->placeholder('Wszystkie')
                    ->trueLabel('Wszystkie opłacone')
                    ->falseLabel('Ma zaległości')
                    ->queries(
                        true: fn (Builder $query) => $query->whereDoesntHave('payments', fn ($q) => $q->where('paid', false)),
                        false: fn (Builder $query) => $query->whereHas('payments', fn ($q) => $q->where('paid', false)),
                    ),
            ])
            ->headerActions([
                Tables\Actions\Action::make('exportCsv')
                    ->label('Pobierz listę CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function () {
                        /** @var Group $group */
                        $group = $this->getOwnerRecord();
                        $filename = 'group_' . $group->id . '_users_' . now()->format('Ymd_His') . '.csv';
                        $members = $group->members()->orderBy('name')->get(['name','email','phone','is_active','amount']);

                        $callback = function () use ($members) {
                            $handle = fopen('php://output', 'w');
                            fputcsv($handle, ['name','email','phone','is_active','amount']);
                            foreach ($members as $u) {
                                fputcsv($handle, [
                                    $u->name,
                                    $u->email,
                                    $u->phone,
                                    $u->is_active ? 1 : 0,
                                    $u->amount,
                                ]);
                            }
                            fclose($handle);
                        };

                        return response()->streamDownload($callback, $filename, [
                            'Content-Type' => 'text/csv',
                        ]);
                    }),
                Tables\Actions\Action::make('printList')
                    ->label('Drukuj listę')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->url(function () {
                        /** @var Group $group */
                        $group = $this->getOwnerRecord();
                        return route('admin.groups.print-users', ['group' => $group->id]);
                    })
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('addUser')
                    ->label('Dodaj użytkownika')
                    ->form([
                        Forms\Components\Select::make('user_id')
                            ->label('Użytkownik')
                            ->options(
                                User::query()
                                    ->whereNull('group_id')
                                    ->whereNot('role', 'admin')
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                            )
                            ->required()
                            ->searchable()
                            ->preload(),
                    ])
                    ->action(function (array $data, Group $group): void {
                        if (!$group->hasSpace()) {
                            Notification::make()
                                ->title('Grupa jest pełna')
                                ->warning()
                                ->send();
                            return;
                        }

                        $user = User::find($data['user_id']);
                        // Przypnij przez pivot
                        $group->members()->syncWithoutDetaching([$user->id]);
                        
                        $group->updateStatusBasedOnCapacity();

                        Notification::make()
                            ->title('Użytkownik został dodany do grupy')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Group $group) => $group->status !== 'inactive' && $group->hasSpace()),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\DetachAction::make()
                        ->label('Usuń z grupy')
                        ->color('danger')
                        ->icon('heroicon-o-trash')
                        ->successNotificationTitle('Użytkownik został usunięty z grupy')
                        ->after(function (Group $group) {
                            $group->updateStatusBasedOnCapacity();
                        }),
                ])
                    ->button()
                    ->label('Actions')
                    ->icon('heroicon-o-cog-6-tooth'),
            ])
            ->bulkActions([
                Tables\Actions\DetachBulkAction::make()
                    ->label('Usuń zaznaczonych z grupy')
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->after(function (Group $group) {
                        $group->updateStatusBasedOnCapacity();
                    })
                    ->successNotificationTitle('Użytkownicy zostali usunięci z grupy'),
                Tables\Actions\BulkAction::make('updatePaymentAmount')
                    ->label('Zmień kwotę dla grupy')
                    ->icon('heroicon-o-banknotes')
                    ->color('warning')
                    ->size('sm')
                    ->tooltip('Zmień kwotę płatności dla zaznaczonych użytkowników')
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
                            ->label('Potwierdzam zmianę kwoty dla zaznaczonych użytkowników')
                            ->required()
                            ->helperText('Ta operacja zmieni kwotę płatności dla zaznaczonych użytkowników.'),
                    ])
                    ->action(function (Collection $records, array $data): void {
                        $amount = (float) $data['amount'];
                        $scope = $data['scope'];
                        $updatedUsers = 0;
                        $updatedPayments = 0;
                        
                        foreach ($records as $user) {
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
                        
                        $message = "Zaktualizowano {$updatedPayments} płatności";
                        if ($updatedUsers > 0) {
                            $message .= " i kwotę dla {$updatedUsers} użytkowników";
                        }
                        
                        Notification::make()
                            ->title('Kwota płatności zaktualizowana')
                            ->body($message)
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Zmień kwotę płatności')
                    ->modalDescription('Zmienisz kwotę płatności dla zaznaczonych użytkowników')
                    ->modalSubmitActionLabel('Zmień kwotę'),
            ])
            ->defaultSort('name', 'asc');
    }

    protected function handleRecordCreation(array $data): mixed
    {
        try {
            Log::info('Próba utworzenia rekordu', ['data' => $data]);
            return parent::handleRecordCreation($data);
        } catch (\Exception $e) {
            Log::error('Błąd podczas tworzenia rekordu', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    protected function handleRecordUpdate(mixed $record, array $data): mixed
    {
        try {
            Log::info('Próba aktualizacji rekordu', ['record' => $record->id, 'data' => $data]);
            return parent::handleRecordUpdate($record, $data);
        } catch (\Exception $e) {
            Log::error('Błąd podczas aktualizacji rekordu', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'record' => $record->id,
                'data' => $data
            ]);
            throw $e;
        }
    }
} 