<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SetPasswordController;

Route::get('/', function () {
    return view('welcome');
});

// Trasy dla ustawiania hasła
Route::get('/set-password/{token}', [SetPasswordController::class, 'showSetPasswordForm'])
    ->name('set-password');

Route::post('/set-password', [SetPasswordController::class, 'setPassword'])
    ->name('set-password.store');

// Route do pobierania CSV
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
