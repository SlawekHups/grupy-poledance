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

        \Log::error('EnsureIsAdmin middleware START', [
            'auth_check' => Auth::check(),
            'user_id' => Auth::id(),
            'user_role' => Auth::user()?->role,
            'is_admin' => Auth::user()?->isAdmin(),
            'request_path' => $request->path(),
        ]);

        if (!Auth::check()) {
            \Log::warning('EnsureIsAdmin: User not authenticated');
            return redirect()->route('filament.admin.auth.login');
        }

        if (!Auth::user()->isAdmin()) {
            \Log::warning('EnsureIsAdmin: User is not admin', [
                'user_id' => Auth::id(),
                'user_role' => Auth::user()->role,
            ]);
            
            // Przekieruj do panelu użytkownika
            return redirect()->route('filament.user.pages.dashboard');
        }

        \Log::error('EnsureIsAdmin: Access granted', [
            'user_id' => Auth::id(),
            'user_role' => Auth::user()->role,
        ]);

        return $next($request);
    }
} 