<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use App\Models\User;
use App\Models\Attendance;
use Filament\Notifications\Notification;

class AttendanceGroupPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Obecność grupy';
    protected static string $view = 'filament.admin.pages.attendance-group-page';

    public $group_id = '';
    public $date = '';
    public $users = [];
    public $attendances = [];

    public function mount()
    {
        $this->date = now()->toDateString();
    }
    public function updatedGroupId()
    {
        $this->loadUsers();
    }
    public function updatedDate()
    {
        $this->loadUsers();
    }

    public function loadUsers()
    {
        // Pobierz użytkowników w wybranej grupie
        $this->users = User::where('group_id', $this->group_id)
            ->orderBy('name')->get()->map(function ($user) {
                return [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                ];
            })->toArray();

        // Pobierz obecności dla wybranej grupy i daty
        $attendances = Attendance::where('group_id', $this->group_id)
            ->where('date', $this->date)
            ->get()
            ->keyBy('user_id');

        // Ustaw obecności i notatki (domyślnie brak obecności i pusta notatka)
        $this->attendances = [];
        foreach ($this->users as $user) {
            $attendance = $attendances->get($user['id']);
            $this->attendances[$user['id']]['present'] = $attendance ? (bool)$attendance->present : false;
            $this->attendances[$user['id']]['note'] = $attendance ? $attendance->note : '';
        }
    }

    public function saveAttendance()
    {
        foreach ($this->users as $user) {
            $presentSet = array_key_exists($user['id'], $this->attendances)
                && array_key_exists('present', $this->attendances[$user['id']]);
            $present = $presentSet ? (bool)$this->attendances[$user['id']]['present'] : null;

            // Zapisz tylko, jeśli obecność została określona (obecny lub nieobecny)
            if ($present !== null) {
                Attendance::updateOrCreate(
                    [
                        'user_id' => $user['id'],
                        'date'    => $this->date,
                    ],
                    [
                        'group_id' => $this->group_id,
                        'present'  => $present,
                        'note'     => $this->attendances[$user['id']]['note'] ?? null,
                    ]
                );
            }
        }
        Notification::make()
            ->title('Obecność została zaktualizowana!')
            ->success()
            ->send();
    }
}
