<div class="space-y-6">
    <!-- Informacje o linku -->
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
        <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Link pre-rejestracji</h3>
        <div class="text-sm text-gray-600 dark:text-gray-300 break-all">{{ $url }}</div>
    </div>



    <!-- Aplikacja Messenger -->
    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4 border border-green-200 dark:border-green-800">
        <h4 class="text-base font-semibold text-green-900 dark:text-green-100 mb-4">🚀 Otwórz w aplikacji Messenger:</h4>
        <div>
            <a href="fb-messenger://share?link={{ $encodedUrl }}" 
               class="block bg-blue-500 hover:bg-blue-600 text-white p-6 rounded-xl text-center font-bold text-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
               style="background-color: #3b82f6 !important; color: white !important; border: 2px solid #1d4ed8 !important;">
                📱 Otwórz w aplikacji Messenger
            </a>
        </div>
    </div>

</div>

