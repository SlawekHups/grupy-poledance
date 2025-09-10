<!DOCTYPE html>
<html lang="pl" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pre-rejestracja - {{ config('app.payment_reminder_company_name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="h-full bg-gray-50">
    <div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="text-center">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900">
                    🎉 Pre-rejestracja
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Wypełnij podstawowe dane, aby rozpocząć proces rejestracji
                </p>
            </div>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <div class="mb-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">
                                    Informacje
                                </h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p>Link jest ważny do: <strong>{{ $preReg->expires_at->format('d.m.Y H:i') }}</strong></p>
                                    <p>Po wypełnieniu administrator skontaktuje się z Tobą</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <form class="space-y-6" method="POST" action="{{ route('pre-register.store', $preReg->token) }}">
                    @csrf

                    @if ($errors->any())
                        <div class="bg-red-50 border border-red-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">
                                        Wystąpiły błędy
                                    </h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <ul class="list-disc pl-5 space-y-1">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Imię i nazwisko *
                        </label>
                        <div class="mt-1">
                            <input id="name" name="name" type="text" required 
                                   value="{{ old('name', $preReg->used ? $preReg->name : '') }}"
                                   class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
                                   placeholder="Wprowadź imię i nazwisko">
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">
                            Adres email *
                        </label>
                        <div class="mt-1">
                            <input id="email" name="email" type="email" required 
                                   value="{{ old('email', $preReg->used ? $preReg->email : '') }}"
                                   class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
                                   placeholder="Wprowadź adres email">
                        </div>
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">
                            Numer telefonu *
                        </label>
                        <div class="mt-1">
                            <input id="phone" name="phone" type="tel" required 
                                   value="{{ old('phone', $preReg->used ? $preReg->phone : '') }}"
                                   class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
                                   placeholder="Wprowadź numer telefonu">
                        </div>
                    </div>

                    <div>
                        <button type="submit" 
                                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                            🚀 Wyślij dane
                        </button>
                    </div>
                </form>

                <div class="mt-8">
                    <div class="text-center">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Co dalej?</h3>
                        <p class="text-sm text-gray-600 mb-4">Po wysłaniu danych administrator skontaktuje się z Tobą w ciągu 24 godzin, aby:</p>
                        
                        <div class="space-y-3 text-left max-w-md mx-auto">
                            <div class="flex items-start">
                                <span class="w-2 h-2 bg-orange-400 rounded-full mr-3 mt-2 flex-shrink-0"></span>
                                <span class="text-sm text-gray-600">Przypisać Cię do odpowiedniej grupy</span>
                            </div>
                            <div class="flex items-start">
                                <span class="w-2 h-2 bg-orange-400 rounded-full mr-3 mt-2 flex-shrink-0"></span>
                                <span class="text-sm text-gray-600">Wysłać pełne zaproszenie do systemu</span>
                            </div>
                            <div class="flex items-start">
                                <span class="w-2 h-2 bg-orange-400 rounded-full mr-3 mt-2 flex-shrink-0"></span>
                                <span class="text-sm text-gray-600">Pomóc w ukończeniu rejestracji</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="text-center text-sm text-gray-500">
                        <p>Masz pytania? Skontaktuj się z nami:</p>
                        <div class="mt-2 space-y-1">
                            <p>📧 <a href="mailto:{{ config('app.payment_reminder_email') }}" class="text-blue-600 hover:text-blue-500">{{ config('app.payment_reminder_email') }}</a></p>
                            <p>☎️ {{ config('app.payment_reminder_phone') }}</p>
                            <p>🌐 <a href="https://{{ config('app.payment_reminder_website') }}" class="text-blue-600 hover:text-blue-500" target="_blank">{{ config('app.payment_reminder_website') }}</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Sprawdzanie wygaśnięcia linku
        const expiresAt = new Date('{{ $preReg->expires_at->toISOString() }}');
        const now = new Date();
        
        // Jeśli link już wygasł, pokaż komunikat
        if (now >= expiresAt) {
            document.body.innerHTML = `
                <div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8">
                    <div class="sm:mx-auto sm:w-full sm:max-w-md">
                        <div class="text-center">
                            <h2 class="text-3xl font-bold tracking-tight text-gray-900">
                                ⏰ Link wygasł
                            </h2>
                            <p class="mt-2 text-sm text-gray-600">
                                Ten link pre-rejestracji wygasł
                            </p>
                        </div>
                    </div>
                    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
                        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                            <div class="bg-red-50 border border-red-200 rounded-md p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-800">
                                            Link wygasł
                                        </h3>
                                        <div class="mt-2 text-sm text-red-700">
                                            <p>Ten link pre-rejestracji wygasł o <strong>{{ $preReg->expires_at->format('d.m.Y H:i') }}</strong></p>
                                            <p class="mt-1">Skontaktuj się z nami, aby otrzymać nowy link.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-6 text-center">
                                <p class="text-sm text-gray-500">Masz pytania? Skontaktuj się z nami:</p>
                                <div class="mt-2 space-y-1">
                                    <p>📧 <a href="mailto:{{ config('app.payment_reminder_email') }}" class="text-blue-600 hover:text-blue-500">{{ config('app.payment_reminder_email') }}</a></p>
                                    <p>☎️ {{ config('app.payment_reminder_phone') }}</p>
                                    <p>🌐 <a href="https://{{ config('app.payment_reminder_website') }}" class="text-blue-600 hover:text-blue-500" target="_blank">{{ config('app.payment_reminder_website') }}</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        } else {
            // Sprawdzaj co minutę czy link nie wygasł
            setInterval(function() {
                const now = new Date();
                if (now >= expiresAt) {
                    // Pokaż komunikat o wygaśnięciu
                    const form = document.querySelector('form');
                    if (form) {
                        form.innerHTML = `
                            <div class="bg-red-50 border border-red-200 rounded-md p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-800">
                                            Link wygasł podczas wypełniania
                                        </h3>
                                        <div class="mt-2 text-sm text-red-700">
                                            <p>Przepraszamy, ale link wygasł podczas wypełniania formularza.</p>
                                            <p class="mt-1">Skontaktuj się z nami, aby otrzymać nowy link.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                }
            }, 60000); // Sprawdzaj co minutę
        }
    </script>
</body>
</html>
