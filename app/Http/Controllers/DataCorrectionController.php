<?php

namespace App\Http\Controllers;

use App\Models\DataCorrectionLink;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DataCorrectionController extends Controller
{
    public function show(Request $request, string $token)
    {
        $link = DataCorrectionLink::where('token', $token)->first();
        
        if (!$link) {
            return view('data-correction.error', [
                'error' => 'Nieprawidłowy link',
                'message' => 'Link do poprawy danych jest nieprawidłowy lub nie istnieje.'
            ]);
        }
        
        if (!$link->isValid()) {
            return view('data-correction.error', [
                'error' => 'Link wygasł',
                'message' => 'Link do poprawy danych wygasł. Skontaktuj się z administratorem, aby otrzymać nowy link.'
            ]);
        }
        
        $user = $link->user;
        
        return view('data-correction.form', [
            'link' => $link,
            'user' => $user,
            'allowedFields' => $link->allowed_fields ?? []
        ]);
    }
    
    public function update(Request $request, string $token)
    {
        $link = DataCorrectionLink::where('token', $token)->first();
        
        if (!$link || !$link->isValid()) {
            return redirect()->back()->withErrors([
                'error' => 'Link jest nieprawidłowy lub wygasł.'
            ]);
        }
        
        $user = $link->user;
        $allowedFields = $link->allowed_fields ?? [];
        
        // Przygotuj reguły walidacji tylko dla dozwolonych pól
        $rules = [];
        $data = [];
        
        // Pole 'name' jest zawsze wymagane
        $rules['name'] = 'required|string|max:255';
        $data['name'] = $request->input('name');
        
        if (in_array('email', $allowedFields)) {
            $rules['email'] = [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id)
            ];
            $data['email'] = $request->input('email');
        }
        
        if (in_array('phone', $allowedFields)) {
            $rules['phone'] = [
                'required',
                'string',
                'max:20',
                Rule::unique('users', 'phone')->ignore($user->id)
            ];
            $data['phone'] = $request->input('phone');
        }
        
        if (in_array('address', $allowedFields)) {
            $rules['address'] = 'nullable|string|max:255';
            $data['address'] = $request->input('address');
        }
        
        if (in_array('city', $allowedFields)) {
            $rules['city'] = 'nullable|string|max:100';
            $data['city'] = $request->input('city');
        }
        
        if (in_array('postal_code', $allowedFields)) {
            $rules['postal_code'] = 'nullable|string|max:10';
            $data['postal_code'] = $request->input('postal_code');
        }
        
        // Walidacja z niestandardowymi komunikatami
        $messages = [
            'name.required' => 'Imię i nazwisko jest wymagane.',
            'name.string' => 'Imię i nazwisko musi być tekstem.',
            'name.max' => 'Imię i nazwisko nie może być dłuższe niż 255 znaków.',
            'email.required' => 'Adres email jest wymagany.',
            'email.email' => 'Adres email musi być prawidłowy.',
            'email.max' => 'Adres email nie może być dłuższy niż 255 znaków.',
            'email.unique' => 'Ten adres email jest już używany przez innego użytkownika.',
            'phone.required' => 'Numer telefonu jest wymagany.',
            'phone.string' => 'Numer telefonu musi być tekstem.',
            'phone.max' => 'Numer telefonu nie może być dłuższy niż 20 znaków.',
            'phone.unique' => 'Ten numer telefonu jest już używany przez innego użytkownika.',
            'address.string' => 'Adres musi być tekstem.',
            'address.max' => 'Adres nie może być dłuższy niż 255 znaków.',
            'city.string' => 'Miasto musi być tekstem.',
            'city.max' => 'Miasto nie może być dłuższe niż 100 znaków.',
            'postal_code.string' => 'Kod pocztowy musi być tekstem.',
            'postal_code.max' => 'Kod pocztowy nie może być dłuższy niż 10 znaków.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Sprawdź czy przynajmniej jedno pole zostało wypełnione
        $hasChanges = false;
        $hasValidData = false;
        
        foreach ($data as $field => $value) {
            if ($value !== null && $value !== '') {
                $hasValidData = true;
                if ($value !== $user->$field) {
                    $hasChanges = true;
                }
            }
        }
        
        if (!$hasValidData) {
            return redirect()->back()
                ->withErrors(['error' => 'Musisz wypełnić przynajmniej jedno pole.'])
                ->withInput();
        }
        
        if (!$hasChanges) {
            return redirect()->back()
                ->withErrors(['error' => 'Nie wprowadzono żadnych zmian. Popraw dane i spróbuj ponownie.'])
                ->withInput();
        }
        
        try {
            // Aktualizuj dane użytkownika
            $user->update($data);
            
            // Oznacz link jako użyty
            $link->markAsUsed();
            
            // Jeśli użytkownik nie ma hasła, wyślij zaproszenie
            if (!$user->password) {
                \App\Events\UserInvited::dispatch($user);
            }
            
            return view('data-correction.success', [
                'user' => $user,
                'updatedFields' => array_keys($data)
            ]);
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Wystąpił błąd podczas aktualizacji danych: ' . $e->getMessage()])
                ->withInput();
        }
    }
}
