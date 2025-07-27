<?php

namespace App\Filament\Admin\Resources\AttendanceResource\Widgets;

use App\Models\Attendance;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MonthlyTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Trend obecności w ostatnich 6 miesiącach';
    protected static ?string $maxHeight = '300px';
    protected static ?string $pollingInterval = '10m';

    public static function canView(): bool
    {
        return true;
    }

    protected function getData(): array
    {
        return Cache::remember('monthly_trend_chart', 600, function () {
            $months = collect(range(5, 0))->map(function ($monthsAgo) {
                return now()->subMonths($monthsAgo)->startOfMonth();
            });

            $data = Attendance::query()
                ->where('present', true)
                ->whereBetween('date', [$months->first(), now()])
                ->select([
                    DB::raw('YEAR(date) as year'),
                    DB::raw('MONTH(date) as month'),
                    DB::raw('COUNT(*) as count')
                ])
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get()
                ->keyBy(fn ($item) => "{$item->year}-{$item->month}");

            $monthLabels = ['Styczeń', 'Luty', 'Marzec', 'Kwiecień', 'Maj', 'Czerwiec', 
                          'Lipiec', 'Sierpień', 'Wrzesień', 'Październik', 'Listopad', 'Grudzień'];

            $labels = $months->map(fn ($date) => $monthLabels[$date->month - 1] . ' ' . $date->year);
            $counts = $months->map(function ($date) use ($data) {
                $key = "{$date->year}-{$date->month}";
                return $data->get($key)?->count ?? 0;
            });

            return [
                'datasets' => [
                    [
                        'label' => 'Liczba obecności',
                        'data' => $counts->toArray(),
                        'borderColor' => '#36A2EB',
                        'backgroundColor' => '#36A2EB',
                        'tension' => 0.3,
                        'fill' => true,
                    ],
                ],
                'labels' => $labels->toArray(),
            ];
        });
    }

    protected function getType(): string
    {
        return 'line';
    }

    public function getDescription(): ?string
    {
        return 'Kliknij, aby zobaczyć obecności z ostatnich 6 miesięcy';
    }

    protected function getOptions(): array
    {
        return [
            'onClick' => 'function() { window.location.href = "' . route('filament.admin.resources.attendances.index', [
                'tableFilters[present][value]' => 'true',
                'tableFilters[date][from]' => now()->subMonths(6)->startOfMonth()->format('Y-m-d'),
                'tableFilters[date][to]' => now()->format('Y-m-d'),
            ]) . '"; }',
        ];
    }
} 