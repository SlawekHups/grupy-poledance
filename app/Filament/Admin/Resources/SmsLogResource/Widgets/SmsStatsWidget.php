<?php

namespace App\Filament\Admin\Resources\SmsLogResource\Widgets;

use App\Models\SmsLog;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class SmsStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Statystyki z dzisiejszego dnia
        $todayStats = SmsLog::whereDate('created_at', today())
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "sent" THEN 1 ELSE 0 END) as sent,
                SUM(CASE WHEN status = "error" THEN 1 ELSE 0 END) as errors,
                SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending
            ')
            ->first();

        // Statystyki z tego tygodnia
        $weekStats = SmsLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "sent" THEN 1 ELSE 0 END) as sent,
                SUM(CASE WHEN status = "error" THEN 1 ELSE 0 END) as errors
            ')
            ->first();

        // Statystyki z tego miesiąca
        $monthStats = SmsLog::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "sent" THEN 1 ELSE 0 END) as sent,
                SUM(CASE WHEN status = "error" THEN 1 ELSE 0 END) as errors
            ')
            ->first();

        // Koszt SMS-ów w tym miesiącu
        $monthlyCost = SmsLog::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('status', 'sent')
            ->sum('cost');

        // Szacowany koszt (jeśli nie ma danych o koszcie w bazie)
        $sentThisMonth = $monthStats->sent ?? 0;
        $costPerSms = config('smsapi.pricing.cost_per_sms', 0.17);
        $estimatedCost = $sentThisMonth * $costPerSms;
        
        // Jeśli nie ma danych o koszcie w bazie, użyj szacowanego
        if ($monthlyCost == 0 && $sentThisMonth > 0) {
            $monthlyCost = $estimatedCost;
        }

        return [
            Stat::make('SMS dzisiaj', $todayStats->total ?? 0)
                ->description('Wysłane: ' . ($todayStats->sent ?? 0) . ', Błędy: ' . ($todayStats->errors ?? 0))
                ->descriptionIcon($todayStats->errors > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($todayStats->errors > 0 ? 'warning' : 'success'),

            Stat::make('SMS w tym tygodniu', $weekStats->total ?? 0)
                ->description('Wysłane: ' . ($weekStats->sent ?? 0) . ', Błędy: ' . ($weekStats->errors ?? 0))
                ->descriptionIcon($weekStats->errors > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($weekStats->errors > 0 ? 'warning' : 'success'),

            Stat::make('SMS w tym miesiącu', $monthStats->total ?? 0)
                ->description('Wysłane: ' . ($monthStats->sent ?? 0) . ', Błędy: ' . ($monthStats->errors ?? 0))
                ->descriptionIcon($monthStats->errors > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($monthStats->errors > 0 ? 'warning' : 'success'),

            Stat::make('Koszt w tym miesiącu', $monthlyCost ? number_format($monthlyCost, 2) . ' PLN' : '0.00 PLN')
                ->description('Koszt wysłanych SMS-ów (' . number_format($costPerSms, 2) . ' PLN za SMS)')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('info'),
        ];
    }
}
