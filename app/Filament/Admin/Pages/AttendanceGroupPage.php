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

    public function mount()
    {
        $this->date = now()->toDateString();
        $this->selectedDate = $this->date;
        $this->currentWeekStart = Carbon::parse($this->date)->startOfWeek(Carbon::MONDAY)->toDateString();
    }

    public function selectDate($date)
    {
        $this->date = $date;
        $this->selectedDate = $date;
        $this->loadUsers();
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
}