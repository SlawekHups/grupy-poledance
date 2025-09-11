<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use App\Models\User;
use App\Models\Group;
use App\Models\Attendance;
use Filament\Notifications\Notification;
use Carbon\Carbon;

class AttendanceGroupPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Obecność grupy';
    protected static ?string $title = 'Obecność grupy';
    protected static string $view = 'filament.admin.pages.attendance-group-page';

    public $group_id = '';
    public $date = '';
    public $users = [];
    public $attendances = [];
    public $currentWeekStart;
    public $selectedDate;
    public $currentGroupPage = 0;
    public $groupsPerPage = 7;

    public function mount()
    {
        $this->date = now()->toDateString();
        $this->selectedDate = $this->date;
        $this->currentWeekStart = Carbon::parse($this->date)->startOfWeek(Carbon::MONDAY)->toDateString();
        
        // Automatyczne zaznaczanie grupy na podstawie dnia tygodnia
        $this->selectGroupByDayOfWeek();
    }

    public function selectDate($date)
    {
        $this->date = $date;
        $this->selectedDate = $date;
        
        // Automatycznie zaznacz grupę dla wybranej daty
        $this->selectGroupByDate($date);
    }

    public function selectWeek($weekStart)
    {
        $this->currentWeekStart = $weekStart;
    }

    public function getWeekDays()
    {
        $weekStart = Carbon::parse($this->currentWeekStart);
        $days = [];
        
        for ($i = 0; $i < 7; $i++) {
            $day = $weekStart->copy()->addDays($i);
            $days[] = [
                'date' => $day->toDateString(),
                'day_name' => $day->translatedFormat('D'),
                'day_number' => $day->format('j'),
                'is_today' => $day->isToday(),
                'is_selected' => $day->toDateString() === $this->selectedDate,
            ];
        }
        
        return $days;
    }

    public function getWeekNavigation()
    {
        $currentWeek = Carbon::parse($this->currentWeekStart);
        
        return [
            'previous' => $currentWeek->copy()->subWeek()->startOfWeek(Carbon::MONDAY)->toDateString(),
            'current' => Carbon::now()->startOfWeek(Carbon::MONDAY)->toDateString(),
            'next' => $currentWeek->copy()->addWeek()->startOfWeek(Carbon::MONDAY)->toDateString(),
        ];
    }

    public function updatedGroupId()
    {
        $this->loadUsers();
    }
    public function updatedDate()
    {
        $this->selectedDate = $this->date;
        $this->currentWeekStart = Carbon::parse($this->date)->startOfWeek(Carbon::MONDAY)->toDateString();
        $this->loadUsers();
    }

    public function loadUsers()
    {
        if (empty($this->group_id)) {
            $this->users = [];
            $this->attendances = [];
            return;
        }

        $group = Group::find($this->group_id);
        if (!$group) {
            $this->users = [];
            $this->attendances = [];
            return;
        }

        // Pobierz użytkowników przypiętych do grupy przez pivot members()
        $this->users = $group->members()
            ->select('users.id as user_id', 'users.name', 'users.email')
            ->orderBy('users.name')
            ->get()
            ->map(fn ($u) => ['id' => $u->user_id, 'name' => $u->name, 'email' => $u->email])
            ->toArray();

        // Pobierz obecności dla wybranej grupy i daty
        $attendances = Attendance::where('group_id', $this->group_id)
            ->where('date', $this->date)
            ->get()
            ->keyBy('user_id');

        // Przygotuj dane obecności
        $this->attendances = [];
        foreach ($this->users as $user) {
            $attendance = $attendances->get($user['id']);
            $this->attendances[$user['id']] = [
                'present' => $attendance ? $attendance->present : false,
                'note' => $attendance ? $attendance->note : '',
            ];
        }
    }

    public function saveAttendance()
    {
        if (empty($this->group_id) || empty($this->date)) {
            Notification::make()
                ->title('Błąd')
                ->body('Wybierz grupę i datę')
                ->danger()
                ->send();
            return;
        }

        foreach ($this->attendances as $userId => $attendanceData) {
            Attendance::updateOrCreate(
                [
                    'user_id' => $userId,
                    'group_id' => $this->group_id,
                    'date' => $this->date,
                ],
                [
                    'present' => $attendanceData['present'],
                    'note' => $attendanceData['note'],
                ]
            );
        }

        Notification::make()
            ->title('Sukces')
            ->body('Obecność została zapisana')
            ->success()
            ->send();
    }

    // Bulk actions for attendance
    public function selectAll()
    {
        foreach ($this->attendances as $userId => $data) {
            $this->attendances[$userId]['present'] = true;
        }
        
        Notification::make()
            ->title('Zaznaczono wszystkich')
            ->body('Wszyscy użytkownicy zostali zaznaczeni jako obecni.')
            ->success()
            ->send();
    }

    public function deselectAll()
    {
        foreach ($this->attendances as $userId => $data) {
            $this->attendances[$userId]['present'] = false;
        }
        
        Notification::make()
            ->title('Odznaczono wszystkich')
            ->body('Wszyscy użytkownicy zostali odznaczeni.')
            ->success()
            ->send();
    }

    public function toggleAll()
    {
        $hasSelected = collect($this->attendances)->where('present', true)->isNotEmpty();
        
        foreach ($this->attendances as $userId => $data) {
            $this->attendances[$userId]['present'] = !$hasSelected;
        }
        
        Notification::make()
            ->title($hasSelected ? 'Odznaczono wszystkich' : 'Zaznaczono wszystkich')
            ->body($hasSelected ? 'Wszyscy użytkownicy zostali odznaczeni.' : 'Wszyscy użytkownicy zostali zaznaczeni jako obecni.')
            ->success()
            ->send();
    }

    // Get attendance statistics
    public function getAttendanceStats()
    {
        if (empty($this->attendances)) {
            return ['total' => 0, 'present' => 0, 'absent' => 0, 'percentage' => 0];
        }

        $total = count($this->attendances);
        $present = collect($this->attendances)->where('present', true)->count();
        $absent = $total - $present;
        $percentage = $total > 0 ? round(($present / $total) * 100, 1) : 0;

        return [
            'total' => $total,
            'present' => $present,
            'absent' => $absent,
            'percentage' => $percentage
        ];
    }

    // Toggle individual attendance
    public function toggleAttendance($userId)
    {
        if (isset($this->attendances[$userId])) {
            $this->attendances[$userId]['present'] = !$this->attendances[$userId]['present'];
        }
    }

    // Select group from cards
    public function selectGroup($groupId)
    {
        $this->group_id = $groupId;
        $this->loadUsers();
    }

    // Group navigation methods
    public function previousGroups()
    {
        if ($this->currentGroupPage > 0) {
            $this->currentGroupPage--;
        }
    }

    public function nextGroups()
    {
        $totalGroups = \App\Models\Group::count();
        $maxPages = ceil($totalGroups / $this->groupsPerPage) - 1;
        if ($this->currentGroupPage < $maxPages) {
            $this->currentGroupPage++;
        }
    }

    public function getCurrentGroups()
    {
        $groups = \App\Models\Group::orderBy('name')->get();
        $startIndex = $this->currentGroupPage * $this->groupsPerPage;
        return $groups->slice($startIndex, $this->groupsPerPage);
    }

    public function getGroupNavigation()
    {
        $totalGroups = \App\Models\Group::count();
        $maxPages = ceil($totalGroups / $this->groupsPerPage) - 1;
        
        return [
            'current_page' => $this->currentGroupPage,
            'max_pages' => $maxPages,
            'has_previous' => $this->currentGroupPage > 0,
            'has_next' => $this->currentGroupPage < $maxPages,
        ];
    }

    // Automatyczne zaznaczanie grupy na podstawie dnia tygodnia
    public function selectGroupByDayOfWeek()
    {
        $today = Carbon::now();
        $dayOfWeek = $today->dayOfWeek; // 0 = niedziela, 1 = poniedziałek, ..., 6 = sobota
        
        // Mapowanie dni tygodnia na polskie nazwy w grupach
        $dayNames = [
            1 => 'Poniedziałek',
            2 => 'Wtorek', 
            3 => 'Środa',
            4 => 'Czwartek',
            5 => 'Piątek',
            6 => 'Sobota',
            0 => 'Niedziela', // niedziela = 0
        ];
        
        $todayName = $dayNames[$dayOfWeek];
        
        // Znajdź pierwszą aktywną grupę dla dzisiejszego dnia
        $group = \App\Models\Group::where('name', 'LIKE', $todayName . '%')
            ->where('status', 'active')
            ->orderBy('name')
            ->first();
            
        if ($group) {
            $this->group_id = $group->id;
            $this->loadUsers();
            
            // Ustaw odpowiednią stronę dla tej grupy
            $this->setCurrentPageForGroup($group->id);
        } else {
            // Jeśli nie ma grupy dla dzisiejszego dnia, wybierz pierwszą aktywną grupę
            $firstActiveGroup = \App\Models\Group::where('status', 'active')
                ->where('id', '!=', 1) // Wyklucz "Bez grupy"
                ->orderBy('name')
                ->first();
                
            if ($firstActiveGroup) {
                $this->group_id = $firstActiveGroup->id;
                $this->loadUsers();
                $this->setCurrentPageForGroup($firstActiveGroup->id);
            }
        }
    }

    // Ustaw stronę na której znajduje się wybrana grupa
    public function setCurrentPageForGroup($groupId)
    {
        $groups = \App\Models\Group::orderBy('name')->get();
        $groupIndex = $groups->search(function($group) use ($groupId) {
            return $group->id == $groupId;
        });
        
        if ($groupIndex !== false) {
            $this->currentGroupPage = floor($groupIndex / $this->groupsPerPage);
        }
    }

    // Automatyczne zaznaczanie grupy na podstawie wybranej daty
    public function selectGroupByDate($date)
    {
        $selectedDate = Carbon::parse($date);
        $dayOfWeek = $selectedDate->dayOfWeek; // 0 = niedziela, 1 = poniedziałek, ..., 6 = sobota
        
        // Mapowanie dni tygodnia na polskie nazwy w grupach
        $dayNames = [
            1 => 'Poniedziałek',
            2 => 'Wtorek', 
            3 => 'Środa',
            4 => 'Czwartek',
            5 => 'Piątek',
            6 => 'Sobota',
            0 => 'Niedziela', // niedziela = 0
        ];
        
        $dayName = $dayNames[$dayOfWeek];
        
        // Znajdź pierwszą aktywną grupę dla wybranego dnia
        $group = \App\Models\Group::where('name', 'LIKE', $dayName . '%')
            ->where('status', 'active')
            ->orderBy('name')
            ->first();
            
        if ($group) {
            $this->group_id = $group->id;
            $this->loadUsers();
            
            // Ustaw odpowiednią stronę dla tej grupy
            $this->setCurrentPageForGroup($group->id);
        } else {
            // Jeśli nie ma grupy dla tego dnia, wyczyść wybór
            $this->group_id = '';
            $this->users = [];
            $this->attendances = [];
        }
    }
}