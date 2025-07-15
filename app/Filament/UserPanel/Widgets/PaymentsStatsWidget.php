<?php

namespace App\Filament\UserPanel\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class PaymentsStatsWidget extends StatsOverviewWidget
{
    protected function getCards(): array
    {
        $userId = Auth::id();
        $paymentsCount = Payment::where('user_id', $userId)->count();
        $paymentsSum = Payment::where('user_id', $userId)->where('paid', true)->sum('amount');

        return [
            Card::make('Liczba płatności', $paymentsCount)
                ->icon('heroicon-o-banknotes')
                ->color('primary')
                ->url(route('filament.user.resources.payments.index')),
            Card::make('Suma płatności', number_format($paymentsSum, 2, ',', ' ') . ' zł')
                ->icon('heroicon-o-currency-euro')
                ->color('success'),
        ];
    }
} 