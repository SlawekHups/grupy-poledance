<?php

namespace App\Filament\UserPanel\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;

class PaymentsOverview extends BaseWidget
{
    protected function getCards(): array
    {
        $userId = Auth::id();
        $query = Payment::where('user_id', $userId);
        $paymentsCount = (clone $query)->count();
        $paymentsSum = (float) (clone $query)->sum('amount');
        $unpaidSum = (float) (clone $query)->where('paid', false)->sum('amount');

        return [
            Card::make('Suma płatności', number_format($paymentsSum, 2) . ' zł')
                ->icon('heroicon-o-banknotes')
                ->color('success'),
            Card::make('Liczba płatności', (string) $paymentsCount)
                ->icon('heroicon-o-receipt-percent')
                ->color('info'),
            Card::make('Suma zaległości', number_format($unpaidSum, 2) . ' zł')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('danger'),
        ];
    }
}
