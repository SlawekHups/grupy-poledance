<div class="space-y-4 sm:space-y-6">
    <!-- Informacje o linku -->
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 sm:p-4">
        <h3 class="text-xs sm:text-sm font-medium text-gray-900 dark:text-white mb-2">Link pre-rejestracji</h3>
        <div class="text-xs sm:text-sm text-gray-600 dark:text-gray-300 break-all">{{ $url }}</div>
    </div>

    <!-- Aplikacja Messenger -->
    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-3 sm:p-4 border border-green-200 dark:border-green-800">
        <h4 class="text-sm sm:text-base font-semibold text-green-900 dark:text-green-100 mb-4 sm:mb-6">🚀 Otwórz w aplikacji Messenger:</h4>
        <div>
            <a href="fb-messenger://share?link={{ $encodedUrl }}" 
               class="block bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 p-3 sm:p-4 rounded-lg text-center font-medium text-sm sm:text-base transition-all duration-200 border border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500"
               style="background-color: #f3f4f6 !important; color: #374151 !important; border: 1px solid #d1d5db !important;">
                📱 Otwórz w aplikacji Messenger
            </a>
        </div>
    </div>
</div>

