<!DOCTYPE html>
<html lang="pl" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link wygasł - {{ config('app.payment_reminder_company_name') }}</title>
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
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <h2 class="mt-6 text-3xl font-bold tracking-tight text-gray-900">
                    ⏰ Link wygasł
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Ten link pre-rejestracji nie jest już ważny
                </p>
            </div>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <div class="mb-6">
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
                                    <p>Link pre-rejestracji jest ważny tylko przez <strong>30 minut</strong></p>
                                    <p>Ten link został już użyty lub wygasł</p>
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
                                <p class="text-gray-500">Wyślemy Ci nowy link</p>
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
