<?php

namespace App\Filament\Admin\Resources\PaymentResource\Widgets;

use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class PaymentStats extends BaseWidget
{
    protected static ?string $pollingInterval = null;
    
    protected function getStats(): array
    {
        $currentMonth = now()->format('Y-m');
        
        // Suma zaległości (nieopłacone płatności)
        $totalUnpaid = Payment::where('paid', false)
            ->sum('amount');
            
        // Suma wpłat w bieżącym miesiącu
        $currentMonthPaid = Payment::where('paid', true)
            ->whereYear('updated_at', now()->year)
            ->whereMonth('updated_at', now()->month)
            ->sum('amount');
            
        // Ilość wpłat w bieżącym miesiącu
        $currentMonthCount = Payment::where('paid', true)
            ->whereYear('updated_at', now()->year)
            ->whereMonth('updated_at', now()->month)
            ->count();

        return [
            Stat::make('Zaległości', number_format($totalUnpaid, 2) . ' zł')
                ->description('Suma nieopłaconych płatności')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color('danger')
                ->chart([7, 4, 6, 8, 5, $totalUnpaid/100])
                ->url(route('filament.admin.resources.payments.index', [
                    'tableFilters[paid][value]' => 'false'
                ]))
                ->extraAttributes(['class' => 'cursor-pointer']),

            Stat::make('Suma wpłat (' . now()->format('m.Y') . ')', number_format($currentMonthPaid, 2) . ' zł')
                ->description('Suma opłaconych płatności w tym miesiącu')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->chart([2, 4, 6, 8, 10, $currentMonthPaid/100])
                ->url(route('filament.admin.resources.payments.index', [
                    'tableFilters[paid][value]' => 'true',
                    'tableFilters[updated_at][from]' => now()->startOfMonth()->format('Y-m-d'),
                    'tableFilters[updated_at][to]' => now()->endOfMonth()->format('Y-m-d'),
                ]))
                ->extraAttributes(['class' => 'cursor-pointer']),

            Stat::make('Ilość wpłat (' . now()->format('m.Y') . ')', $currentMonthCount)
                ->description('Liczba opłaconych płatności w tym miesiącu')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart([1, 2, 3, 4, 5, $currentMonthCount])
                ->url(route('filament.admin.resources.payments.index', [
                    'tableFilters[paid][value]' => 'true',
                    'tableFilters[updated_at][from]' => now()->startOfMonth()->format('Y-m-d'),
                    'tableFilters[updated_at][to]' => now()->endOfMonth()->format('Y-m-d'),
                ]))
                ->extraAttributes(['class' => 'cursor-pointer']),
        ];
    }
} 