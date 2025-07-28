<?php

namespace App\Filament\Admin\Resources\PaymentResource\Widgets;

use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class PaymentStats extends BaseWidget
{
    protected function getStats(): array
    {
        $currentMonth = now()->format('m');
        $currentYear = now()->format('Y');

        // Suma zaległości (niezapłacone płatności)
        $unpaidTotal = Payment::where('paid', false)
            ->sum('amount');

        // Suma wpłat w bieżącym miesiącu
        $monthlyTotal = Payment::where('paid', true)
            ->whereYear('updated_at', $currentYear)
            ->whereMonth('updated_at', $currentMonth)
            ->sum('amount');

        // Ilość wpłat w bieżącym miesiącu
        $monthlyCount = Payment::where('paid', true)
            ->whereYear('updated_at', $currentYear)
            ->whereMonth('updated_at', $currentMonth)
            ->count();

        return [
            Stat::make('Zaległości', number_format($unpaidTotal, 2) . ' zł')
                ->description('Suma niezapłaconych płatności')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color('danger')
                ->chart([7, 4, 6, 8, 5, $unpaidTotal/100])
                ->url(
                    route('filament.admin.resources.payments.index', [
                        'tableFilters[paid][value]' => '0',
                    ])
                ),

            Stat::make('Suma wpłat w tym miesiącu', number_format($monthlyTotal, 2) . ' zł')
                ->description('Opłacone w ' . now()->format('m/Y'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->chart([2, 4, 6, 8, 5, $monthlyTotal/100])
                ->url(
                    route('filament.admin.resources.payments.index', [
                        'tableFilters[paid][value]' => '1',
                        'tableFilters[updated_at][from]' => now()->startOfMonth()->format('Y-m-d'),
                        'tableFilters[updated_at][until]' => now()->endOfMonth()->format('Y-m-d'),
                    ])
                ),

            Stat::make('Ilość wpłat w tym miesiącu', $monthlyCount)
                ->description('Liczba opłaconych płatności')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('info')
                ->chart([1, 3, 6, 8, 4, $monthlyCount])
                ->url(
                    route('filament.admin.resources.payments.index', [
                        'tableFilters[paid][value]' => '1',
                        'tableFilters[updated_at][from]' => now()->startOfMonth()->format('Y-m-d'),
                        'tableFilters[updated_at][until]' => now()->endOfMonth()->format('Y-m-d'),
                    ])
                ),
        ];
    }
} 