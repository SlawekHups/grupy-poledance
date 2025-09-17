<?php

namespace App\Filament\Admin\Resources\AttendanceResource\Pages;

use App\Filament\Admin\Resources\AttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Admin\Resources\AttendanceResource\Widgets\AttendanceStats;
use App\Filament\Admin\Resources\AttendanceResource\Widgets\AttendanceGroupChart;
use App\Filament\Admin\Resources\AttendanceResource\Widgets\TopAttendersChart;
use App\Filament\Admin\Resources\AttendanceResource\Widgets\MonthlyTrendChart;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('info')
                ->label('Opis obecności')
                ->icon('heroicon-o-information-circle')
                ->color('info')
                ->modalHeading('Opis systemu obecności i przycisków')
                ->modalContent(view('filament.admin.pages.attendance-info'))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Zamknij'),
            Actions\CreateAction::make(),
            Actions\Action::make('addExternalUserAttendance')
                ->label('Dodaj obecność spoza grupy (odrabianie)')
                ->icon('heroicon-o-user-plus')
                ->color('danger')
                ->form([
                    \Filament\Forms\Components\Select::make('group_id')
                        ->label('Grupa')
                        ->relationship('group', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    \Filament\Forms\Components\Select::make('user_id')
                        ->label('Użytkownik')
                        ->searchable()
                        ->getSearchResultsUsing(function (string $search) {
                            return \App\Models\User::query()
                                ->where('is_active', true)
                                ->whereNot('role', 'admin')
                                ->where(function ($q) use ($search) {
                                    $q->where('name', 'like', "%{$search}%")
                                      ->orWhere('email', 'like', "%{$search}%");
                                })
                                ->limit(50)
                                ->pluck('name', 'id');
                        })
                        ->getOptionLabelUsing(fn ($value) => \App\Models\User::find($value)?->name ?? $value)
                        ->required(),

                    \Filament\Forms\Components\DatePicker::make('date')
                        ->label('Data zajęć')
                        ->default(now())
                        ->required(),

                    \Filament\Forms\Components\Toggle::make('present')
                        ->label('Obecny?')
                        ->default(true),

                    \Filament\Forms\Components\TextInput::make('note')
                        ->label('Notatka')
                        ->placeholder('Odrabianie')
                        ->default('Odrabianie')
                        ->nullable(),
                ])
                ->action(function (array $data): void {
                    \App\Models\Attendance::updateOrCreate(
                        [
                            'user_id' => $data['user_id'],
                            'date' => $data['date'],
                        ],
                        [
                            'group_id' => $data['group_id'],
                            'present' => (bool)($data['present'] ?? true),
                            'note' => $data['note'] ?? null,
                        ]
                    );

                    \Filament\Notifications\Notification::make()
                        ->title('Dodano obecność (spoza grupy)')
                        ->success()
                        ->send();
                }),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return [
            'default' => 1,
            'sm' => 1,
            'md' => 1,
            'lg' => 3,
            'xl' => 3,
            '2xl' => 3,
        ];
    }
}
