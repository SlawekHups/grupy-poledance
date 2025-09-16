<?php

namespace App\Filament\Admin\Resources\GroupResource\Pages;

use App\Filament\Admin\Resources\GroupResource;
use App\Models\Group;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditGroup extends EditRecord
{
    protected static string $resource = GroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('enableEdit')
                ->label('Edytuj')
                ->icon('heroicon-o-pencil-square')
                ->color('info')
                ->url(fn ($record) => route('filament.admin.resources.groups.edit', ['record' => $record, 'edit' => 1]))
                ->visible(fn () => ! request()->boolean('edit', false)),
            
            Actions\Action::make('changePaymentAmount')
                ->label('Zmień kwotę płatności')
                ->icon('heroicon-o-banknotes')
                ->color('warning')
                ->modalHeading('Zmień kwotę płatności dla grupy')
                ->modalDescription('Ta operacja zmieni kwotę płatności dla wszystkich użytkowników w tej grupie.')
                ->form([
                    TextInput::make('amount')
                        ->label('Nowa kwota płatności (zł)')
                        ->numeric()
                        ->step(0.01)
                        ->minValue(0)
                        ->placeholder('160')
                        ->required(),
                    
                    Select::make('scope')
                        ->label('Zakres aktualizacji')
                        ->options([
                            'current_month' => 'Tylko płatności bieżącego miesiąca',
                            'future_months' => 'Płatności przyszłych miesięcy + nowa kwota domyślna',
                            'all_months' => 'Wszystkie płatności + nowa kwota domyślna (ostrożnie!)',
                        ])
                        ->default('current_month')
                        ->required(),
                    
                    Toggle::make('confirm')
                        ->label('Potwierdzam zmianę kwoty dla wszystkich użytkowników w grupie')
                        ->required()
                        ->helperText('Ta operacja zmieni kwotę płatności dla wszystkich użytkowników w tej grupie.'),
                ])
                ->action(function (array $data, Group $record) {
                    $amount = (float) $data['amount'];
                    $scope = $data['scope'];

                    $users = $record->members()->where('users.is_active', true)->get();

                    $updatedUsers = 0;
                    $updatedPayments = 0;

                    foreach ($users as $user) {
                        $paymentQuery = $user->payments();
                        
                        switch ($scope) {
                            case 'current_month':
                                $paymentQuery->where('month', now()->format('Y-m'));
                                break;
                            case 'future_months':
                                $paymentQuery->where('month', '>=', now()->format('Y-m'));
                                break;
                            case 'all_months':
                                break;
                        }
                        
                        $paymentCount = $paymentQuery->update(['amount' => $amount]);
                        $updatedPayments += $paymentCount;
                        
                        if ($scope === 'future_months' || $scope === 'all_months') {
                            $user->update(['amount' => $amount]);
                            $updatedUsers++;
                        }
                    }

                    $message = "Zaktualizowano {$updatedPayments} płatności";
                    if ($updatedUsers > 0) {
                        $message .= " i kwotę dla {$updatedUsers} użytkowników";
                    }
                    $message .= " w grupie '{$record->name}'";

                    Notification::make()
                        ->title('Kwota płatności zaktualizowana')
                        ->body($message)
                        ->success()
                        ->send();
                        
                    // Odśwież dane na stronie i widżety
                    $this->redirect(route('filament.admin.resources.groups.edit', ['record' => $record]));
                }),
            
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        // Po zapisie wracamy do widoku bez trybu edycji (kafelka)
        return route('filament.admin.resources.groups.edit', ['record' => $this->record->getKey()]);
    }
}
