<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poprawa danych - Grupy Poledance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-blue-100">
                    <i class="fas fa-user-edit text-blue-600 text-xl"></i>
                </div>
                <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                    Poprawa danych
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Cześć <strong>{{ $user->name }}</strong>! Wypełnij poniższy formularz, aby poprawić swoje dane w systemie
                </p>
                <div class="mt-3 bg-yellow-50 border border-yellow-200 rounded-md p-3">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                <strong>Uwaga:</strong> Pola są puste, żebyś mógł wprowadzić poprawne dane. Nie podpowiadamy błędnych danych z bazy.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                @if ($errors->any())
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <form class="space-y-6" method="POST" action="{{ route('data-correction.update', $link->token) }}">
                    @csrf
                    
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Imię i nazwisko <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1">
                            <input id="name" name="name" type="text" required
                                value="{{ old('name', $user->name) }}"
                                placeholder="Wprowadź pełne imię i nazwisko"
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        <p class="mt-1 text-sm text-gray-500">
                            <i class="fas fa-info-circle"></i>
                            To pole jest zawsze dostępne - możesz poprawić imię lub dodać brakujące dane
                        </p>
                    </div>
                    
                    @if (in_array('email', $allowedFields))
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">
                            Adres email <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1">
                            <input id="email" name="email" type="email" required
                                value="{{ old('email') }}"
                                placeholder="Wprowadź poprawny adres email"
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        <p class="mt-1 text-sm text-gray-500">
                            <i class="fas fa-info-circle"></i>
                            To jest najważniejsze pole - upewnij się, że email jest poprawny!
                        </p>
                    </div>
                    @endif
                    
                    @if (in_array('phone', $allowedFields))
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">
                            Numer telefonu <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1">
                            <input id="phone" name="phone" type="tel" required
                                value="{{ old('phone') }}"
                                placeholder="Wprowadź poprawny numer telefonu"
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                    </div>
                    @endif
                    
                    @if (in_array('address', $allowedFields))
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700">
                            Adres
                        </label>
                        <div class="mt-1">
                            <input id="address" name="address" type="text"
                                value="{{ old('address') }}"
                                placeholder="Wprowadź poprawny adres"
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                    </div>
                    @endif
                    
                    @if (in_array('city', $allowedFields))
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700">
                            Miasto
                        </label>
                        <div class="mt-1">
                            <input id="city" name="city" type="text"
                                value="{{ old('city') }}"
                                placeholder="Wprowadź poprawne miasto"
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                    </div>
                    @endif
                    
                    @if (in_array('postal_code', $allowedFields))
                    <div>
                        <label for="postal_code" class="block text-sm font-medium text-gray-700">
                            Kod pocztowy
                        </label>
                        <div class="mt-1">
                            <input id="postal_code" name="postal_code" type="text"
                                value="{{ old('postal_code') }}"
                                placeholder="Wprowadź poprawny kod pocztowy"
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                    </div>
                    @endif
                    
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-blue-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">
                                    Informacja
                                </h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p>• <strong>Wypełnij dane od nowa</strong> - pola są puste, żebyś mógł wprowadzić poprawne dane</p>
                                    <p>• Pola oznaczone <span class="text-red-500">*</span> są wymagane</p>
                                    <p>• Po zapisaniu danych otrzymasz link do ustawienia hasła (jeśli jeszcze go nie masz)</p>
                                    <p>• Link wygasa: <strong>{{ $link->expires_at->format('d.m.Y H:i') }}</strong></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <button type="submit"
                            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <i class="fas fa-save text-blue-500 group-hover:text-blue-400"></i>
                            </span>
                            Zapisz dane
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
