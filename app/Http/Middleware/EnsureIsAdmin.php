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
        // Sprawdź czy to nie jest strona logowania
        if ($request->is('admin/login') || $request->is('admin/auth/*')) {
            return $next($request);
        }

        if (!Auth::check()) {
            return redirect()->route('filament.admin.auth.login');
        }

        if (!Auth::user()->isAdmin()) {
            // Przekieruj do panelu użytkownika
            return redirect()->route('filament.user.pages.dashboard');
        }

        return $next($request);
    }
} 