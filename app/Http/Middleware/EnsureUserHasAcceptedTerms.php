<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Filament\User\Pages\AcceptTerms;

class EnsureUserHasAcceptedTerms
{
    public function handle(Request $request, Closure $next): Response
    {
        if (
            Auth::check() &&
            is_null(Auth::user()->accepted_terms_at) &&
            !$request->routeIs(AcceptTerms::getRouteName())
        ) {
            return redirect()->to(AcceptTerms::getUrl());
        }

        return $next($request);
    }
}
