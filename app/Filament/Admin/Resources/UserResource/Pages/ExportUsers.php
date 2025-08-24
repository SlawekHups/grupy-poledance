<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use Filament\Resources\Pages\Page;
use Illuminate\Http\Request;

class ExportUsers extends Page
{
    protected static string $resource = UserResource::class;

    protected static string $view = 'filament.pages.export-users';

    public function mount(Request $request): void
    {
        // Sprawdź czy to jest żądanie czyszczenia sesji
        if ($request->get('clear') === '1') {
            session()->forget(['export_csv', 'export_filename']);
            return;
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('back')
                ->label('Powrót do listy użytkowników')
                ->url(route('filament.admin.resources.users.index'))
                ->color('gray')
                ->icon('heroicon-o-arrow-left')
                ->extraAttributes(['class' => 'text-black bg-gray-200 hover:bg-gray-300']),
        ];
    }

    protected function getFooterActions(): array
    {
        return [
            \Filament\Actions\Action::make('download_active')
                ->label('Pobierz aktywnych użytkowników')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->extraAttributes(['class' => 'text-white bg-green-600 hover:bg-green-700'])
                ->action(function () {
                    return $this->downloadCsv('active');
                }),
            \Filament\Actions\Action::make('download_all')
                ->label('Pobierz wszystkich użytkowników')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('info')
                ->extraAttributes(['class' => 'text-white bg-blue-600 hover:bg-blue-700'])
                ->action(function () {
                    return $this->downloadCsv('all');
                }),
        ];
    }

    public function downloadCsv(string $exportType = 'active')
    {
        $filename = 'users_export_' . $exportType . '_' . now()->format('Ymd_His') . '.csv';
        
        // Eksportuj użytkowników w zależności od typu
        $query = \App\Models\User::where('role', 'user');
        
        if ($exportType === 'active') {
            $query->where('is_active', true);
        }
        
        $users = $query->select(['name', 'email', 'phone', 'group_id', 'amount', 'joined_at', 'is_active'])->get();

        $callback = function() use ($users) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['name', 'email', 'phone', 'group_id', 'amount', 'joined_at', 'is_active']);
            foreach ($users as $user) {
                fputcsv($handle, [
                    $user->name,
                    $user->email,
                    $user->phone,
                    $user->group_id,
                    $user->amount,
                    $user->joined_at,
                    $user->is_active ? 1 : 0,
                ]);
            }
            fclose($handle);
        };

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        return response()->stream($callback, 200, $headers);
    }
}
