<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\Widget;
use Carbon\Carbon;
use App\Models\Group;
use App\Models\User;
use App\Models\Attendance;

class CalendarWidget extends Widget
{
    protected static string $view = 'filament.admin.widgets.calendar-widget';
    protected int | string | array $columnSpan = [
        'md' => 1,
        'xl' => 1,
    ];

    protected static ?int $sort = 1;

    public $selectedDate;

    public function mount(): void
    {
        $this->selectedDate = Carbon::now()->format('Y-m-d');
    }

    public function previousDay(): void
    {
        $this->selectedDate = Carbon::parse($this->selectedDate)->subDay()->format('Y-m-d');
    }

    public function nextDay(): void
    {
        $this->selectedDate = Carbon::parse($this->selectedDate)->addDay()->format('Y-m-d');
    }

    public function goToToday(): void
    {
        $this->selectedDate = Carbon::now()->format('Y-m-d');
    }

    public function getViewData(): array
    {
        $date = Carbon::parse($this->selectedDate);
        
        // Pobierz grupy dla wybranego dnia
        $dayGroups = $this->getGroupsForDay($date);
        
        return [
            'currentDate' => $date,
            'dayOfWeek' => $date->locale('pl')->dayName,
            'dayOfMonth' => $date->day,
            'monthName' => $date->locale('pl')->monthName,
            'year' => $date->year,
            'totalUsers' => User::where('role', 'user')->count(),
            'totalGroups' => Group::where('name', '!=', 'Bez grupy')->count(),
            'selectedDayAttendance' => $this->getAttendanceCountForDate($date),
            'isWeekend' => $date->isWeekend(),
            'isToday' => $date->isToday(),
            'isPast' => $date->isPast(),
            'isFuture' => $date->isFuture(),
            'formattedDate' => $date->format('d.m.Y'),
            'dayGroups' => $dayGroups,
            'dayColors' => $this->getDayColors($date->locale('pl')->dayName),
        ];
    }

    private function getGroupsForDay(Carbon $date): array
    {
        $dayName = $date->locale('pl')->dayName;
        
        // Mapowanie dni tygodnia na polskie nazwy
        $dayMapping = [
            'poniedziałek' => ['poniedziałek', 'pon', 'monday'],
            'wtorek' => ['wtorek', 'wt', 'tuesday'],
            'środa' => ['środa', 'sr', 'śr', 'wednesday'],
            'czwartek' => ['czwartek', 'czw', 'thursday'],
            'piątek' => ['piątek', 'pt', 'friday'],
            'sobota' => ['sobota', 'sob', 'saturday'],
            'niedziela' => ['niedziela', 'ndz', 'sunday'],
        ];

        $searchTerms = $dayMapping[strtolower($dayName)] ?? [strtolower($dayName)];
        
        $groups = Group::where(function($query) use ($searchTerms) {
            foreach ($searchTerms as $term) {
                $query->orWhere('name', 'LIKE', '%' . $term . '%');
            }
        })
        ->where('name', '!=', 'Bez grupy') // Wyklucz grupy "Bez grupy"
        ->orderBy('name')
        ->get();

        // Grupuj grupy według godzin
        $groupedByHour = [];
        foreach ($groups as $group) {
            $hour = $this->extractHourFromGroupName($group->name);
            if ($hour) {
                $groupedByHour[$hour][] = $group;
            }
        }

        // Sortuj według godzin
        ksort($groupedByHour);

        return $groupedByHour;
    }

    private function extractHourFromGroupName(string $groupName): ?string
    {
        // Szukaj wzorców czasu: 18:00, 19:30, 20.00, itp.
        if (preg_match('/(\d{1,2}):?(\d{2})/', $groupName, $matches)) {
            $hour = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            $minute = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
            return $hour . ':' . $minute;
        }

        // Szukaj wzorców: 18, 19, 20 (bez minut)
        if (preg_match('/\b(\d{1,2})\b/', $groupName, $matches)) {
            $hour = intval($matches[1]);
            if ($hour >= 8 && $hour <= 22) { // Rozsądne godziny zajęć
                return str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00';
            }
        }

        return null;
    }

    private function getAttendanceCountForDate(Carbon $date): int
    {
        return Attendance::whereDate('date', $date->format('Y-m-d'))
            ->where('present', true)
            ->count();
    }

    private function getDayColors(string $dayName): array
    {
        $colors = [
            'poniedziałek' => [
                'primary' => 'from-blue-500 to-blue-600',
                'secondary' => 'from-blue-50 to-blue-100',
                'text' => 'text-blue-700',
                'border' => 'border-blue-200',
                'bg' => 'bg-blue-50',
                'hover' => 'hover:bg-blue-100',
            ],
            'wtorek' => [
                'primary' => 'from-green-500 to-green-600',
                'secondary' => 'from-green-50 to-green-100',
                'text' => 'text-green-700',
                'border' => 'border-green-200',
                'bg' => 'bg-green-50',
                'hover' => 'hover:bg-green-100',
            ],
            'środa' => [
                'primary' => 'from-purple-500 to-purple-600',
                'secondary' => 'from-purple-50 to-purple-100',
                'text' => 'text-purple-700',
                'border' => 'border-purple-200',
                'bg' => 'bg-purple-50',
                'hover' => 'hover:bg-purple-100',
            ],
            'czwartek' => [
                'primary' => 'from-orange-500 to-orange-600',
                'secondary' => 'from-orange-50 to-orange-100',
                'text' => 'text-orange-700',
                'border' => 'border-orange-200',
                'bg' => 'bg-orange-50',
                'hover' => 'hover:bg-orange-100',
            ],
            'piątek' => [
                'primary' => 'from-red-500 to-red-600',
                'secondary' => 'from-red-50 to-red-100',
                'text' => 'text-red-700',
                'border' => 'border-red-200',
                'bg' => 'bg-red-50',
                'hover' => 'hover:bg-red-100',
            ],
            'sobota' => [
                'primary' => 'from-pink-500 to-pink-600',
                'secondary' => 'from-pink-50 to-pink-100',
                'text' => 'text-pink-700',
                'border' => 'border-pink-200',
                'bg' => 'bg-pink-50',
                'hover' => 'hover:bg-pink-100',
            ],
            'niedziela' => [
                'primary' => 'from-yellow-500 to-yellow-600',
                'secondary' => 'from-yellow-50 to-yellow-100',
                'text' => 'text-yellow-700',
                'border' => 'border-yellow-200',
                'bg' => 'bg-yellow-50',
                'hover' => 'hover:bg-yellow-100',
            ],
        ];

        return $colors[strtolower($dayName)] ?? $colors['poniedziałek'];
    }
}
