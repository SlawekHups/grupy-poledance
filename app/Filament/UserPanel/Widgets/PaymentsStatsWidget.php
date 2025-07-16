<?php

namespace App\Filament\UserPanel\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;

class PaymentsStatsWidget extends StatsOverviewWidget
{
    protected function getCards(): array
    {
        $userId = Auth::id();
        $paymentsCount = Payment::where('user_id', $userId)->count();
        $paymentsSum = Payment::where('user_id', $userId)->where('paid', true)->sum('amount');
        $attendancesThisMonth = Attendance::where('user_id', $userId)
            ->where('present', true)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->count();

        return [
            Card::make('Liczba płatności', $paymentsCount)
                ->icon('heroicon-o-banknotes')
                ->color('primary')
                ->url(route('filament.user.resources.payments.index')),
            Card::make('Suma płatności', number_format($paymentsSum, 2, ',', ' ') . ' zł')
                ->icon('heroicon-o-currency-euro')
                ->color('success'),
            Card::make('Obecności w tym miesiącu', $attendancesThisMonth)
                ->icon('heroicon-o-calendar-days')
                ->color('info')
                ->url(route('filament.user.resources.attendances.index')),
        ];
    }
} 