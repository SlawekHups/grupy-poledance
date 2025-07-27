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

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    protected static ?string $title = 'Użytkownicy w grupie';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Imię i nazwisko')
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required(),
                Forms\Components\TextInput::make('phone')
                    ->label('Telefon')
                    ->tel(),
                Forms\Components\TextInput::make('amount')
                    ->label('Kwota miesięczna (zł)')
                    ->numeric(),
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
                    ->weight('bold'),
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
                Action::make('addUser')
                    ->label('Dodaj użytkownika')
                    ->modalHeading('Dodaj użytkownika do grupy')
                    ->icon('heroicon-m-user-plus')
                    ->color('success')
                    ->form([
                        Forms\Components\Select::make('user_id')
                            ->label('Wybierz użytkownika')
                            ->options(
                                \App\Models\User::query()
                                    ->where('role', 'user')
                                    ->whereNull('group_id')
                                    ->orderBy('name')
                                    ->get()
                                    ->mapWithKeys(function ($user) {
                                        $status = $user->is_active ? '✓' : '✗';
                                        $statusColor = $user->is_active ? 'text-success-500' : 'text-danger-500';
                                        return [
                                            $user->id => "{$user->name} ({$user->email}) " . ($user->phone ? "📱 {$user->phone} " : '') . "<span class='{$statusColor}'>{$status}</span>"
                                        ];
                                    })
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Lista pokazuje tylko użytkowników, którzy nie są przypisani do żadnej grupy')
                            ->optionsLimit(50)
                            ->searchable(['name', 'email', 'phone'])
                            ->allowHtml(),
                    ])
                    ->action(function (array $data): void {
                        $user = \App\Models\User::find($data['user_id']);
                        if ($user) {
                            $user->update(['group_id' => $this->getOwnerRecord()->id]);
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('remove')
                    ->label('Usuń z grupy')
                    ->color('danger')
                    ->icon('heroicon-m-user-minus')
                    ->requiresConfirmation()
                    ->modalHeading('Usuń użytkownika z grupy')
                    ->modalDescription('Czy na pewno chcesz usunąć tego użytkownika z grupy?')
                    ->action(fn ($record) => $record->update(['group_id' => null])),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('removeMultiple')
                    ->label('Usuń zaznaczonych z grupy')
                    ->color('danger')
                    ->icon('heroicon-m-user-minus')
                    ->requiresConfirmation()
                    ->modalHeading('Usuń użytkowników z grupy')
                    ->modalDescription('Czy na pewno chcesz usunąć zaznaczonych użytkowników z grupy?')
                    ->action(fn ($records) => $records->each->update(['group_id' => null])),
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