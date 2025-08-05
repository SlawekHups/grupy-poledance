<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class EnsureProfileCompleted
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Sprawdź czy użytkownik ma hasło
            if (!$user->password) {
                return redirect()->route('filament.user.auth.login')
                    ->withErrors(['email' => 'Musisz najpierw ustawić hasło.']);
            }
            
            // Sprawdź czy profil jest ukończony (telefon, grupa, akceptacja regulaminu)
            $missingFields = [];
            
            // Telefon nie jest obowiązkowy - usunięto sprawdzenie
            // if (empty($user->phone)) {
            //     $missingFields[] = 'telefon';
            // }
            
            if (empty($user->group_id)) {
                $missingFields[] = 'przypisanie do grupy';
            }
            
            if (empty($user->terms_accepted_at)) {
                $missingFields[] = 'akceptacja regulaminu';
            }
            
            if (empty($user->rodo_accepted_at)) {
                $missingFields[] = 'akceptacja RODO';
            }
            
            if ($user->addresses()->count() === 0) {
                $missingFields[] = 'adres';
            }
            
            // Jeśli brakuje pól i nie jesteśmy już na stronie uzupełniania profilu
            if (!empty($missingFields) && !$request->routeIs('filament.user.pages.dashboard') && !$request->routeIs('filament.user.pages.onboarding') && !$request->routeIs('filament.user.auth.profile') && !$request->routeIs('filament.user.auth.logout')) {
                return redirect()->route('filament.user.pages.dashboard')
                    ->with('warning', 'Proszę uzupełnić swój profil: ' . implode(', ', $missingFields));
            }
        }

        return $next($request);
    }
}
