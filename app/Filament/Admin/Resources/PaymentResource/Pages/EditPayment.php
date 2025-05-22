<?php

namespace App\Filament\Admin\Resources\PaymentResource\Pages;

use App\Filament\Admin\Resources\PaymentResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPayment extends EditRecord
{
    protected static string $resource = PaymentResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (!empty($data['paid'])) {
            $data['payment_link'] = null;
        }

        return $data;
    }
    protected function afterSave(): void
    {
        if ($this->record->paid) {
            Notification::make()
                ->success()
                ->title('Link do płatności został usunięty')
                ->body('Oznaczono płatność jako opłaconą. Link został automatycznie wyczyszczony.')
                ->send();
        }
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
