<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dane zaktualizowane - Grupy Poledance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-green-100">
                    <i class="fas fa-check text-green-600 text-xl"></i>
                </div>
                <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                    Dane zostały zaktualizowane!
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Twoje dane zostały pomyślnie zaktualizowane w systemie
                </p>
            </div>
            
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <div class="space-y-4">
                    <div class="bg-green-50 border border-green-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-green-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-green-800">
                                    Sukces!
                                </h3>
                                <div class="mt-2 text-sm text-green-700">
                                    <p>Zaktualizowane pola:</p>
                                    <ul class="list-disc list-inside mt-1">
                                        @foreach($updatedFields as $field)
                                            <li>
                                                @switch($field)
                                                    @case('name') Imię i nazwisko @break
                                                    @case('email') Adres email @break
                                                    @case('phone') Numer telefonu @break
                                                    @case('address') Adres @break
                                                    @case('city') Miasto @break
                                                    @case('postal_code') Kod pocztowy @break
                                                    @default {{ $field }} @break
                                                @endswitch
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if(!$user->password)
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-envelope text-blue-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">
                                    Link do ustawienia hasła
                                </h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p>Na adres <strong>{{ $user->email }}</strong> został wysłany link do ustawienia hasła.</p>
                                    <p class="mt-1">Sprawdź swoją skrzynkę pocztową i kliknij w link, aby dokończyć rejestrację.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <div class="bg-gray-50 border border-gray-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-gray-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-gray-800">
                                    Co dalej?
                                </h3>
                                <div class="mt-2 text-sm text-gray-700">
                                    <p>• Sprawdź email i ustaw hasło (jeśli jeszcze tego nie zrobiłeś)</p>
                                    <p>• Możesz teraz zalogować się do systemu</p>
                                    <p>• W razie problemów skontaktuj się z administratorem</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
