<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Filament\Notifications\Notification;

class EnsureIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('filament.admin.auth.login');
        }

        if (!Auth::user()->isAdmin()) {
            // Wyświetl komunikat o przekierowaniu
            Notification::make()
                ->title('Przekierowanie do panelu użytkownika')
                ->body('Zostałeś przekierowany do odpowiedniego panelu.')
                ->info()
                ->send();

            // Przekieruj do panelu użytkownika
            return redirect()->route('filament.user.pages.dashboard');
        }

        return $next($request);
    }
} 