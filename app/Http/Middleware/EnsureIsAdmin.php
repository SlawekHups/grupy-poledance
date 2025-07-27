<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class EnsureIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        // Dodajemy logowanie dla debugowania
        Log::info('EnsureIsAdmin middleware check', [
            'user_id' => $user?->id,
            'email' => $user?->email,
            'role' => $user?->role,
            'is_admin' => $user?->isAdmin(),
        ]);

        if (!Auth::check() || !$user?->isAdmin()) {
            Log::warning('EnsureIsAdmin: Unauthorized access attempt', [
                'user_id' => $user?->id,
                'email' => $user?->email,
                'role' => $user?->role,
            ]);
            
            // Przekieruj na stronę logowania do panelu usera
            return redirect()->route('filament.user.auth.login');
        }

        return $next($request);
    }
} 