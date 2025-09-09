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

        // Suma wpłat w bieżącym miesiącu (po updated_at)
        $monthlyTotalUpdated = Payment::where('paid', true)
            ->whereYear('updated_at', $currentYear)
            ->whereMonth('updated_at', $currentMonth)
            ->sum('amount');

        // Suma wpłat w bieżącym miesiącu (po polu month)
        $monthlyTotalByMonth = Payment::where('paid', true)
            ->where('month', now()->format('Y-m'))
            ->sum('amount');

        // Ilość wpłat w bieżącym miesiącu (po updated_at)
        $monthlyCountUpdated = Payment::where('paid', true)
            ->whereYear('updated_at', $currentYear)
            ->whereMonth('updated_at', $currentMonth)
            ->count();

        // Ilość wpłat w bieżącym miesiącu (po polu month)
        $monthlyCountByMonth = Payment::where('paid', true)
            ->where('month', now()->format('Y-m'))
            ->count();

        // Suma wpłat za cały bieżący rok
        $yearlyTotal = Payment::where('paid', true)
            ->whereYear('updated_at', $currentYear)
            ->sum('amount');


        return [
            // 1) Zaległości
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

            // 2) Po polu month (wyróżnione kolorem warning)
            Stat::make('Suma wpłat (miesiąc)', number_format($monthlyTotalByMonth, 2) . ' zł')
                ->description('Opłacone wg pola month ' . now()->format('m/Y'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning')
                ->chart([2, 5, 3, 6, 7, $monthlyTotalByMonth/100])
                ->url(
                    route('filament.admin.resources.payments.index', [
                        'tableFilters[paid][value]' => '1',
                        'tableFilters[month][value]' => now()->format('Y-m'),
                    ])
                ),

            Stat::make('Ilość wpłat (miesiąc)', $monthlyCountByMonth)
                ->description('Liczba opłaconych wg pola month')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('info')
                ->chart([1, 2, 4, 6, 8, $monthlyCountByMonth])
                ->url(
                    route('filament.admin.resources.payments.index', [
                        'tableFilters[paid][value]' => '1',
                        'tableFilters[month][value]' => now()->format('Y-m'),
                    ])
                ),

            // 3) Po dacie aktualizacji (updated_at)
            Stat::make('Suma wpłat (aktualizacje)', number_format($monthlyTotalUpdated, 2) . ' zł')
                ->description('Opłacone wg daty aktualizacji ' . now()->format('m/Y'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->chart([2, 4, 6, 8, 5, $monthlyTotalUpdated/100])
                ->url(
                    route('filament.admin.resources.payments.index', [
                        'tableFilters[paid][value]' => '1',
                        'tableFilters[updated_at][from]' => now()->startOfMonth()->format('Y-m-d'),
                        'tableFilters[updated_at][until]' => now()->endOfMonth()->format('Y-m-d'),
                    ])
                ),

            Stat::make('Ilość wpłat (aktualizacje)', $monthlyCountUpdated)
                ->description('Liczba opłaconych wg daty aktualizacji')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('info')
                ->chart([1, 3, 6, 8, 4, $monthlyCountUpdated])
                ->url(
                    route('filament.admin.resources.payments.index', [
                        'tableFilters[paid][value]' => '1',
                        'tableFilters[updated_at][from]' => now()->startOfMonth()->format('Y-m-d'),
                        'tableFilters[updated_at][until]' => now()->endOfMonth()->format('Y-m-d'),
                    ])
                ),

            // 4) Statystyki roczne
            Stat::make('Suma wpłat (rok)', number_format($yearlyTotal, 2) . ' zł')
                ->description('Opłacone w całym ' . $currentYear . ' roku')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary')
                ->chart([5, 8, 12, 15, 18, $yearlyTotal/1000])
                ->url(
                    route('filament.admin.resources.payments.index', [
                        'tableFilters[paid][value]' => '1',
                        'tableFilters[updated_at][from]' => now()->startOfYear()->format('Y-m-d'),
                        'tableFilters[updated_at][until]' => now()->endOfYear()->format('Y-m-d'),
                    ])
                ),
        ];
    }
} 