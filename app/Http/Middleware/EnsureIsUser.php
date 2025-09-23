<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsUser
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        Log::info('EnsureIsUser: user check', [
            'user_id' => $user?->id,
            'email' => $user?->email,
            'role' => $user?->role,
            'is_user' => $user?->isUser(),
        ]);

        if (!Auth::check() || !$user?->isUser()) {
            Log::warning('EnsureIsUser: access denied', [
                'user_id' => $user?->id,
                'email' => $user?->email,
                'role' => $user?->role,
            ]);
            return redirect()->route('filament.user.auth.login');
        }
        return $next($request);
    }
} 