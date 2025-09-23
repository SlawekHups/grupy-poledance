<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SetPasswordController;
use App\Http\Controllers\PreRegistrationController;
use App\Http\Controllers\DataCorrectionController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ExportController;
use App\Models\User;

Route::get('/', function () {
    return view('welcome');
});

// Route do miniatur obrazków (działa dla wszystkich plików obrazków)
Route::get('/admin-files/thumbnails/{path}', [FileController::class, 'thumbnail'])
    ->where('path', '.*')
    ->name('admin.files.thumbnail');

// Route do pobierania plików publicznych z oryginalną nazwą
Route::get('/admin-files/{path}', [FileController::class, 'download'])
    ->where('path', '.*')
    ->name('admin.files.public.download');

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

// Trasy dla poprawy danych
Route::get('/data-correction/{token}', [DataCorrectionController::class, 'show'])
    ->name('data-correction');

Route::post('/data-correction/{token}', [DataCorrectionController::class, 'update'])
    ->name('data-correction.update');

// Route do generowania tokenów (dla testów)
Route::get('/admin/generate-pre-register-token', [PreRegistrationController::class, 'generateToken'])
    ->name('admin.generate-pre-register-token');

// Route do pobierania CSV (sesyjny)
Route::get('/download-csv', [ExportController::class, 'downloadCsv'])
    ->name('download-csv');

// Route bezpośredniego eksportu CSV dla pojedynczego użytkownika
Route::get('/admin/export-user-csv/{user}', [ExportController::class, 'exportUserCsv'])
    ->middleware(['web', 'auth'])
    ->name('admin.export-user-csv');

// Route eksportu danych użytkownika (CSV)
Route::get('/panel/export-my-csv', [ExportController::class, 'exportMyCsv'])
    ->middleware(['web', 'auth'])
    ->name('user.export-my-csv');

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/admin/groups/{group}/print-users', [ExportController::class, 'printGroupUsers'])
        ->name('admin.groups.print-users');
});
