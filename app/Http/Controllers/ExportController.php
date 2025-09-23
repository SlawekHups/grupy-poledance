<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExportController extends Controller
{
    /**
     * Pobierz CSV z sesji
     */
    public function downloadCsv()
    {
        if (!session('export_csv')) {
            abort(404, 'Plik CSV nie został wygenerowany');
        }

        $csvContent = session('export_csv');
        $filename = session('export_filename', 'export.csv');

        // Wyczyść sesję po pobraniu
        session()->forget(['export_csv', 'export_filename']);

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"$filename\"");
    }

    /**
     * Eksportuj dane pojedynczego użytkownika (admin)
     */
    public function exportUserCsv(User $user)
    {
        $filename = 'user_' . $user->id . '_' . now()->format('Ymd_His') . '.csv';

        $callback = function() use ($user) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['name', 'email', 'phone', 'group_id', 'amount', 'joined_at', 'is_active']);
            fputcsv($handle, [
                $user->name,
                $user->email,
                $user->phone,
                $user->group_id,
                $user->amount,
                $user->joined_at,
                $user->is_active ? 1 : 0,
            ]);
            fclose($handle);
        };

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Eksportuj dane użytkownika (panel użytkownika)
     */
    public function exportMyCsv()
    {
        $user = Auth::user();
        abort_unless((bool) $user, 403);

        $filename = 'my_data_' . now()->format('Ymd_His') . '.csv';

        $callback = function() use ($user) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['name', 'email', 'phone', 'group_id', 'amount', 'joined_at', 'is_active']);
            fputcsv($handle, [
                $user->name,
                $user->email,
                $user->phone,
                $user->group_id,
                $user->amount,
                $user->joined_at,
                $user->is_active ? 1 : 0,
            ]);
            fclose($handle);
        };

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Wyświetl listę użytkowników grupy do druku
     */
    public function printGroupUsers(\App\Models\Group $group)
    {
        $members = $group->members()->orderBy('name')->get();
        return view('filament.admin.groups.print-users', compact('group', 'members'));
    }
}
