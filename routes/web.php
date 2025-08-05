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
