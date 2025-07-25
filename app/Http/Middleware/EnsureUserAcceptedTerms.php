<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserAcceptedTerms
{
    public function handle(Request $request, Closure $next)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if ($user && (
            $user->addresses()->count() === 0 ||
            is_null($user->rodo_accepted_at) ||
            is_null($user->terms_accepted_at)
        )) {
            // Pozwól tylko na onboarding i logout
            if (!(
                $request->routeIs('filament.user.pages.onboarding') ||
                $request->routeIs('filament.user.auth.logout')
            )) {
                return redirect()->route('filament.user.pages.onboarding');
            }
        }
        return $next($request);
    }
} 