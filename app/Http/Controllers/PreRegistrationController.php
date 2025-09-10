<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PreRegistration;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class PreRegistrationController extends Controller
{
    /**
     * Wyświetla formularz pre-rejestracji
     */
    public function showForm($token)
    {
        $preReg = PreRegistration::where('token', $token)->first();
        
        if (!$preReg) {
            return view('pre-registration.not-found');
        }
        
        if (!$preReg->isValid()) {
            return view('pre-registration.expired');
        }
        
        return view('pre-registration.form', compact('preReg'));
    }
    
    /**
     * Przetwarza formularz pre-rejestracji
     */
    public function store(Request $request, $token)
    {
        $preReg = PreRegistration::where('token', $token)->first();
        
        if (!$preReg || !$preReg->isValid()) {
            return redirect()->back()->withErrors(['error' => 'Link wygasł lub jest nieprawidłowy.']);
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Aktualizuj dane pre-rejestracji
        $preReg->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);
        
        // Oznacz jako użyty
        $preReg->markAsUsed();
        
        Log::info('Pre-registration completed', [
            'token' => $token,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);
        
        return view('pre-registration.success', compact('preReg'));
    }
    
    /**
     * Generuje nowy token pre-rejestracji (dla testów)
     */
    public function generateToken()
    {
        $token = PreRegistration::generateToken();
        $expiresAt = now()->addMinutes(30);
        
        $preReg = PreRegistration::create([
            'token' => $token,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '+48123456789',
            'expires_at' => $expiresAt,
        ]);
        
        $url = route('pre-register', ['token' => $token]);
        
        return response()->json([
            'token' => $token,
            'url' => $url,
            'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
        ]);
    }
}
