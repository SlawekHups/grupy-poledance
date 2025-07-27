<?php

namespace App\Filament\Admin\Resources\AttendanceResource\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class TopAttendersChart extends ChartWidget
{
    protected static ?string $heading = 'Top 10 Użytkowników - Frekwencja';
    protected static ?string $maxHeight = '300px';
    protected static ?string $pollingInterval = '10m';

    public static function canView(): bool
    {
        return true;
    }

    protected function getData(): array
    {
        return Cache::remember('top_attenders_chart', 600, function () {
            $users = User::query()
                ->where('role', 'user')
                ->where('is_active', true)
                ->withCount(['attendances' => function ($query) {
                    $query->where('present', true)
                        ->whereDate('date', '>=', now()->subMonths(6));
                }])
                ->having('attendances_count', '>', 0)
                ->orderByDesc('attendances_count')
                ->limit(10)
                ->get();

            return [
                'datasets' => [
                    [
                        'label' => 'Liczba obecności (ostatnie 6 miesięcy)',
                        'data' => $users->pluck('attendances_count')->toArray(),
                        'backgroundColor' => collect(range(1, $users->count()))->map(function ($index) {
                            $opacity = 1 - (($index - 1) * 0.07);
                            return "rgba(54, 162, 235, {$opacity})";
                        })->toArray(),
                    ],
                ],
                'labels' => $users->pluck('name')->toArray(),
            ];
        });
    }

    protected function getType(): string
    {
        return 'bar';
    }

    public function getDescription(): ?string
    {
        return 'Kliknij, aby zobaczyć wszystkie obecności aktywnych użytkowników';
    }

    protected function getOptions(): array
    {
        return [
            'onClick' => 'function() { window.location.href = "' . route('filament.admin.resources.attendances.index', [
                'tableFilters[present][value]' => 'true',
                'tableFilters[date][from]' => now()->subMonths(6)->format('Y-m-d'),
                'tableFilters[date][to]' => now()->format('Y-m-d'),
                'tableFilters[user_id][value]' => User::query()
                    ->where('role', 'user')
                    ->where('is_active', true)
                    ->pluck('id')
                    ->toArray()
            ]) . '"; }',
        ];
    }
} 