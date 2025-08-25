<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SetPasswordController;
use App\Models\User;

Route::get('/', function () {
    return view('welcome');
});

// Trasy dla ustawiania hasła
Route::get('/set-password/{token}', [SetPasswordController::class, 'showSetPasswordForm'])
    ->name('set-password');

Route::post('/set-password', [SetPasswordController::class, 'setPassword'])
    ->name('set-password.store');

// Route do pobierania CSV (sesyjny)
Route::get('/download-csv', function () {
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
})->name('download-csv');

// Route bezpośredniego eksportu CSV dla pojedynczego użytkownika
Route::get('/admin/export-user-csv/{user}', function (User $user) {
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
})->middleware(['web', 'auth'])->name('admin.export-user-csv');
