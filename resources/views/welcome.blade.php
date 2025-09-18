<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Grupy Poledance – Panel</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/android-chrome-192x192.png">
    <link rel="manifest" href="/site.webmanifest">
    
    <!-- Meta tags -->
    <meta name="theme-color" content="#f59e0b">
    <meta name="description" content="System zarządzania grupami i płatnościami Grupy Poledance">
    
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,600" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
            <style>
        body { font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif; }
            </style>
    </head>
<body class="bg-gradient-to-br from-white to-gray-100 min-h-screen flex flex-col items-center justify-center p-4">
    <div class="w-full max-w-md mx-auto bg-white rounded-2xl shadow-xl p-8 flex flex-col items-center">
        <!-- Logo firmy -->
        <img src="https://poledanceworkout.pl/assets/images/logo-pd-nowe-136x137.png" alt="Logo Poledance" class="w-20 h-20 mb-6 rounded-full shadow" />
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Grupy Poledance</h1>
        <p class="text-gray-500 mb-8 text-center">Witaj w systemie zarządzania grupami i płatnościami.<br>Wybierz panel, do którego chcesz się zalogować.</p>
        <div class="flex flex-col gap-4 w-full">
            <a href="/admin" class="w-full inline-block px-6 py-4 rounded-lg bg-orange-500 text-white font-semibold text-lg text-center shadow hover:bg-orange-600 transition">Panel Admina</a>
            <a href="/panel" class="w-full inline-block px-6 py-4 rounded-lg bg-gray-800 text-white font-semibold text-lg text-center shadow hover:bg-gray-900 transition">Panel Użytkownika</a>
        </div>
    </div>
    <footer class="mt-8 text-gray-400 text-xs">&copy; {{ date('Y') }} Grupy Poledance. Powered by Laravel.</footer>
    </body>
</html>
