<?php

namespace App\Filament\Admin\Resources\AttendanceResource\Widgets;

use App\Models\Group;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class AttendanceGroupChart extends ChartWidget
{
    protected static ?string $heading = 'Frekwencja według grup';
    protected static ?string $maxHeight = '300px';
    protected static ?string $pollingInterval = '10m';

    public static function canView(): bool
    {
        return true;
    }

    protected function getData(): array
    {
        return Cache::remember('attendance_group_chart', 600, function () {
            $groups = Group::query()
                ->where('id', '!=', 1)
                ->withCount(['attendances' => function ($query) {
                    $query->where('present', true);
                }])
                ->orderByDesc('attendances_count')
                ->get();

            $colors = collect(['#36A2EB', '#FF6384', '#4BC0C0', '#FF9F40', '#9966FF'])
                ->pad($groups->count(), '#808080');

            return [
                'datasets' => [
                    [
                        'label' => 'Liczba obecności',
                        'data' => $groups->pluck('attendances_count')->toArray(),
                        'backgroundColor' => $colors->toArray(),
                    ],
                ],
                'labels' => $groups->pluck('name')->toArray(),
            ];
        });
    }

    protected function getType(): string
    {
        return 'bar';
    }

    public function getDescription(): ?string
    {
        return 'Kliknij, aby zobaczyć wszystkie obecności';
    }

    protected function getOptions(): array
    {
        return [
            'onClick' => 'function() { window.location.href = "' . route('filament.admin.resources.attendances.index', [
                'tableFilters[present][value]' => 'true'
            ]) . '"; }',
        ];
    }
} 