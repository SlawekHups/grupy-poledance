<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserAcceptedTerms
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && is_null($user->terms_accepted_at)) {
            // Pozwól na dostęp tylko do strony akceptacji regulaminu
            if (!$request->routeIs('filament.user.pages.terms')) {
                return redirect()->route('filament.user.pages.terms');
            }
        }

        return $next($request);
    }
} 