<?php

namespace App\Filament\Admin\Resources\UserResource\Widgets;

use App\Models\User;
use App\Models\Payment;
use App\Models\Attendance;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UserIndividualStats extends BaseWidget
{
    public ?User $record = null;
    
    protected static ?string $pollingInterval = null;
    
    protected function getStats(): array
    {
        // Pobierz dane użytkownika z właściwości
        $user = $this->record;
        
        if (!$user) {
            return [];
        }
        
        // ZALEGŁOŚCI - kwota nieopłaconych płatności użytkownika (włącznie z aktualnym miesiącem)
        $currentMonth = Carbon::now()->format('Y-m');
        $overdueAmount = $user->payments()
            ->where('paid', false)
            ->where('month', '<=', $currentMonth)
            ->sum('amount');
        
        // FREKWENCJA - frekwencja konkretnego użytkownika
        $userAttendances = $user->attendances();
        $totalUserAttendances = $userAttendances->count();
        $presentUserAttendances = $userAttendances->where('present', true)->count();
        $userAttendanceRate = $totalUserAttendances > 0 ? round(($presentUserAttendances / $totalUserAttendances) * 100, 1) : 0;
        
        // OSTATNIA OBECNOŚĆ - data ostatniej obecności użytkownika
        $lastAttendance = $user->attendances()
            ->where('present', true)
            ->latest('date')
            ->first();
        
        $lastAttendanceDate = $lastAttendance 
            ? Carbon::parse($lastAttendance->date)->format('d.m.Y')
            : 'Brak danych';
        
        // ŁĄCZNA KWOTA - suma opłaconych płatności użytkownika
        $userTotalPaid = $user->payments()
            ->where('paid', true)
            ->sum('amount');

        return [
            Stat::make('Zaległości', number_format($overdueAmount, 2) . ' zł')
                ->description('Kwota nieopłaconych płatności użytkownika')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($overdueAmount > 0 ? 'danger' : 'success'),

            Stat::make('Frekwencja', $userAttendanceRate . '%')
                ->description('Frekwencja użytkownika')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($userAttendanceRate >= 80 ? 'success' : ($userAttendanceRate >= 60 ? 'warning' : 'danger')),

            Stat::make('Ostatnia obecność', $lastAttendanceDate)
                ->description('Data ostatniej obecności')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color($lastAttendanceDate === 'Brak danych' ? 'gray' : 'success'),

            Stat::make('Łączna kwota', number_format($userTotalPaid, 2) . ' zł')
                ->description('Suma opłaconych płatności użytkownika')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }
}
