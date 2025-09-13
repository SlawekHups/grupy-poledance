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
    public $showExternalUserModal = false;
    public $externalUserId = '';
    public $externalUserNote = 'Odrabianie';
    public $externalUserSearch = '';
    public $manualGroupSelection = false; // Czy grupa została wybrana ręcznie


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
        
        // Automatycznie zaznacz grupę dla wybranej daty tylko jeśli nie została wybrana ręcznie
        if (!$this->manualGroupSelection) {
            $this->selectGroupByDate($date);
        } else {
            // Jeśli grupa została wybrana ręcznie, tylko załaduj użytkowników
            $this->loadUsers();
        }
    }

    public function selectWeek($weekStart)
    {
        $this->currentWeekStart = $weekStart;
        $this->manualGroupSelection = false; // Resetuj flagę ręcznego wyboru przy zmianie tygodnia
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
        $this->manualGroupSelection = true; // Oznacz że grupa została wybrana ręcznie
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
        $groupUsers = $group->members()
            ->select('users.id as user_id', 'users.name', 'users.email', 'users.phone')
            ->orderBy('users.name')
            ->get()
            ->map(fn ($u) => ['id' => $u->user_id, 'name' => $u->name, 'email' => $u->email, 'phone' => $u->phone, 'is_group_member' => true])
            ->toArray();

        // Pobierz użytkowników z poza grupy (odrabianie) dla tej daty i grupy
        $externalUsers = Attendance::where('group_id', $this->group_id)
            ->where('date', $this->date)
            ->whereNotNull('note')
            ->where('note', '!=', '')
            ->with('user:id,name,email,phone')
            ->get()
            ->map(fn ($attendance) => [
                'id' => $attendance->user_id,
                'name' => $attendance->user->name,
                'email' => $attendance->user->email,
                'phone' => $attendance->user->phone,
                'is_group_member' => false,
                'is_odrabianie' => true
            ])
            ->toArray();

        // Połącz użytkowników z grupy i odrabiających
        $this->users = array_merge($groupUsers, $externalUsers);

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
                'is_odrabianie' => $user['is_odrabianie'] ?? false,
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
            return ['total' => 0, 'present' => 0, 'absent' => 0, 'odrabianie' => 0, 'percentage' => 0];
        }

        $total = count($this->attendances);
        $present = collect($this->attendances)->where('present', true)->count();
        $odrabianie = collect($this->attendances)->where('is_odrabianie', true)->count();
        $absent = $total - $present;
        $percentage = $total > 0 ? round(($present / $total) * 100, 1) : 0;

        return [
            'total' => $total,
            'present' => $present,
            'absent' => $absent,
            'odrabianie' => $odrabianie,
            'percentage' => $percentage
        ];
    }

    // Toggle individual attendance
    public function toggleAttendance($userId)
    {
        if (isset($this->attendances[$userId])) {
            $this->attendances[$userId]['present'] = !$this->attendances[$userId]['present'];
            
            // Jeśli osoba z poza grupy (odrabianie) została odznaczona jako nieobecna, usuń ją z listy
            if (($this->attendances[$userId]['is_odrabianie'] ?? false) && !$this->attendances[$userId]['present']) {
                // Usuń z listy użytkowników
                $this->users = array_filter($this->users, function($user) use ($userId) {
                    return $user['id'] != $userId;
                });
                
                // Usuń z obecności
                unset($this->attendances[$userId]);
                
                // Usuń rekord z bazy danych
                Attendance::where('user_id', $userId)
                    ->where('group_id', $this->group_id)
                    ->where('date', $this->date)
                    ->delete();
            }
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
            ->whereIn('status', ['active', 'full'])
            ->orderBy('name')
            ->first();
            
        if ($group) {
            $this->group_id = $group->id;
            $this->loadUsers();
            
            // Ustaw odpowiednią stronę dla tej grupy
            $this->setCurrentPageForGroup($group->id);
        } else {
            // Jeśli nie ma grupy dla dzisiejszego dnia, wybierz pierwszą grupę z poniedziałku
            $mondayGroup = \App\Models\Group::where('name', 'LIKE', 'Poniedziałek%')
                ->whereIn('status', ['active', 'full'])
                ->orderBy('name')
                ->first();
                
            if ($mondayGroup) {
                $this->group_id = $mondayGroup->id;
                $this->loadUsers();
                $this->setCurrentPageForGroup($mondayGroup->id);
            } else {
                // Fallback: wybierz pierwszą aktywną grupę
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
            ->whereIn('status', ['active', 'full'])
            ->orderBy('name')
            ->first();
            
        if ($group) {
            $this->group_id = $group->id;
            $this->loadUsers();
            
            // Ustaw odpowiednią stronę dla tej grupy
            $this->setCurrentPageForGroup($group->id);
        } else {
            // Jeśli nie ma grupy dla tego dnia, wybierz pierwszą grupę z poniedziałku
            $mondayGroup = \App\Models\Group::where('name', 'LIKE', 'Poniedziałek%')
                ->whereIn('status', ['active', 'full'])
                ->orderBy('name')
                ->first();
                
            if ($mondayGroup) {
                $this->group_id = $mondayGroup->id;
                $this->loadUsers();
                $this->setCurrentPageForGroup($mondayGroup->id);
            } else {
                // Fallback: wyczyść wybór
                $this->group_id = '';
                $this->users = [];
                $this->attendances = [];
            }
        }
    }

    // Otwórz modal do wyboru użytkownika spoza grupy
    public function openExternalUserModal()
    {
        $this->showExternalUserModal = true;
        $this->externalUserId = '';
        $this->externalUserNote = 'Odrabianie';
        $this->externalUserSearch = '';
    }

    // Zamknij modal
    public function closeExternalUserModal()
    {
        $this->showExternalUserModal = false;
        $this->externalUserId = '';
        $this->externalUserNote = 'Odrabianie';
        $this->externalUserSearch = '';
    }

    // Wybierz użytkownika z wyników wyszukiwania
    public function selectExternalUser($userId)
    {
        $this->externalUserId = $userId;
        $this->externalUserSearch = '';
    }

    // Wyczyść wybór użytkownika
    public function clearExternalUser()
    {
        $this->externalUserId = '';
        $this->externalUserSearch = '';
    }

    // Dodaj obecność spoza grupy (odrabianie)
    public function addExternalUserAttendance()
    {
        if (empty($this->group_id) || empty($this->date)) {
            Notification::make()
                ->title('Błąd')
                ->body('Wybierz grupę i datę')
                ->danger()
                ->send();
            return;
        }

        if (empty($this->externalUserId)) {
            Notification::make()
                ->title('Błąd')
                ->body('Wybierz użytkownika')
                ->danger()
                ->send();
            return;
        }

        // Sprawdź czy użytkownik istnieje
        $user = \App\Models\User::find($this->externalUserId);
        if (!$user) {
            Notification::make()
                ->title('Błąd')
                ->body('Użytkownik nie został znaleziony')
                ->danger()
                ->send();
            return;
        }

        // Dodaj obecność
        \App\Models\Attendance::updateOrCreate(
            [
                'user_id' => $this->externalUserId,
                'group_id' => $this->group_id,
                'date' => $this->date,
            ],
            [
                'present' => true,
                'note' => $this->externalUserNote,
            ]
        );

        // Odśwież listę użytkowników
        $this->loadUsers();

        // Zamknij modal
        $this->closeExternalUserModal();

        Notification::make()
            ->title('Sukces')
            ->body('Dodano obecność dla ' . $user->name)
            ->success()
            ->send();
    }

    // Pobierz listę użytkowników do wyboru (spoza grupy)
    public function getExternalUsers()
    {
        if (empty($this->group_id)) {
            return collect();
        }

        return \App\Models\User::query()
            ->where('is_active', true)
            ->whereNot('role', 'admin')
            ->whereDoesntHave('groups', function ($q) {
                $q->where('groups.id', $this->group_id);
            })
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'phone']);
    }
}