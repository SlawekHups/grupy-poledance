<!DOCTYPE html>
<html lang="pl" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link nie znaleziony - {{ config('app.payment_reminder_company_name') }}</title>
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
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-gray-100">
                    <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.29-1.009-5.824-2.709M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <h2 class="mt-6 text-3xl font-bold tracking-tight text-gray-900">
                    🔍 Link nie znaleziony
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Ten link pre-rejestracji nie istnieje
                </p>
            </div>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <div class="mb-6">
                    <div class="bg-gray-50 border border-gray-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-gray-800">
                                    Link nieprawidłowy
                                </h3>
                                <div class="mt-2 text-sm text-gray-700">
                                    <p>Sprawdź czy link został skopiowany poprawnie</p>
                                    <p>Możliwe, że link został już użyty</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Co możesz zrobić?
                    </h3>
                    
                    <div class="space-y-4 text-sm text-gray-600">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-6 w-6 rounded-full bg-orange-100 text-orange-600 text-xs font-medium">1</div>
                            </div>
                            <div class="ml-3 text-left">
                                <p class="font-medium">Skontaktuj się z nami bezpośrednio</p>
                                <p class="text-gray-500">Wyślemy Ci nowy link pre-rejestracji</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-6 w-6 rounded-full bg-orange-100 text-orange-600 text-xs font-medium">2</div>
                            </div>
                            <div class="ml-3 text-left">
                                <p class="font-medium">Zadzwoń lub napisz</p>
                                <p class="text-gray-500">Pomożemy Ci w rejestracji</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-gray-200">
                    <div class="text-center text-sm text-gray-500">
                        <p>Skontaktuj się z nami:</p>
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
</body>
</html>
