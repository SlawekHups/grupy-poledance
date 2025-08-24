<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\DB;

class SetPasswordController extends Controller
{
    /**
     * Wyświetla formularz ustawiania hasła
     */
    public function showSetPasswordForm(Request $request)
    {
        $token = $request->route('token');
        $email = $request->get('email');

        // Sprawdź czy token jest ważny
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            return redirect()->route('filament.user.auth.login')->withErrors(['email' => 'Nieprawidłowy adres email.']);
        }

        // Sprawdź czy token jest ważny (72 godziny - zgodnie z HandlePasswordResetRequest)
        $tokenRecord = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('created_at', '>=', now()->subHours(72))
            ->first();

        if (!$tokenRecord || $tokenRecord->raw_token !== $token) {
            return redirect()->route('filament.user.auth.login')->withErrors(['email' => 'Link wygasł lub jest nieprawidłowy.']);
        }

        // Sprawdź czy użytkownik już ma hasło
        if ($user->password) {
            return redirect()->route('filament.user.auth.login')->with('status', 'Hasło zostało już ustawione. Możesz się zalogować.');
        }

        return view('auth.set-password', [
            'token' => $token,
            'email' => $email,
            'user' => $user,
        ]);
    }

    /**
     * Obsługuje ustawienie hasła
     */
    public function setPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Nieprawidłowy adres email.']);
        }

        // Sprawdź czy token jest ważny (72 godziny - zgodnie z HandlePasswordResetRequest)
        $tokenRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('created_at', '>=', now()->subHours(72))
            ->first();

        if (!$tokenRecord || $tokenRecord->raw_token !== $request->token) {
            return back()->withErrors(['email' => 'Link wygasł lub jest nieprawidłowy.']);
        }

        // Sprawdź czy użytkownik już ma hasło
        if ($user->password) {
            return redirect()->route('filament.user.auth.login')->with('status', 'Hasło zostało już ustawione. Możesz się zalogować.');
        }

        // Ustaw hasło
        $user->update([
            'password' => $request->password, // Mutator automatycznie zahashuje
            'remember_token' => Str::random(60),
        ]);

        // Usuń token po użyciu
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        // Zaloguj użytkownika
        Auth::login($user);

        // Przekieruj do uzupełnienia profilu
        return redirect()->route('filament.user.pages.dashboard')
            ->with('status', 'Hasło zostało ustawione pomyślnie! Teraz uzupełnij swój profil.');
    }
}
