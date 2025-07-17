<?php

namespace App\Filament\UserPanel\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;

class AttendanceStatsWidget extends Widget
{
    protected static string $view = 'filament.user-panel.widgets.attendance-stats';
    protected static ?int $sort = 1; 
    protected function getViewData(): array
    {
        $userId = Auth::id();

        $presentCount = Attendance::where('user_id', $userId)->where('present', true)->count();
        $absentCount = Attendance::where('user_id', $userId)->where('present', false)->count();
        $total = $presentCount + $absentCount;
        $percent = $total > 0 ? round(($presentCount / $total) * 100, 1) : 0;

        return [
            'presentCount' => $presentCount,
            'absentCount' => $absentCount,
            'percent' => $percent,
        ];
    }
} 