<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SetPasswordController;
use App\Http\Controllers\PreRegistrationController;
use App\Models\User;

Route::get('/', function () {
    return view('welcome');
});

// Trasy dla ustawiania hasła
Route::get('/set-password/{token}', [SetPasswordController::class, 'showSetPasswordForm'])
    ->name('set-password');

Route::post('/set-password', [SetPasswordController::class, 'setPassword'])
    ->name('set-password.store');

// Trasy dla pre-rejestracji
Route::get('/pre-register/{token}', [PreRegistrationController::class, 'showForm'])
    ->name('pre-register');

Route::post('/pre-register/{token}', [PreRegistrationController::class, 'store'])
    ->name('pre-register.store');

// Route do generowania tokenów (dla testów)
Route::get('/admin/generate-pre-register-token', [PreRegistrationController::class, 'generateToken'])
    ->name('admin.generate-pre-register-token');

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

// Route eksportu danych użytkownika (CSV)
Route::get('/panel/export-my-csv', function () {
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
})->middleware(['web', 'auth'])->name('user.export-my-csv');

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/admin/groups/{group}/print-users', function (\App\Models\Group $group) {
        $members = $group->members()->orderBy('name')->get();
        return view('filament.admin.groups.print-users', compact('group', 'members'));
    })->name('admin.groups.print-users');
});
