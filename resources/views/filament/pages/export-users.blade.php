<x-filament-panels::page>
    <div class="text-center py-8">
        <h2 class="text-2xl font-bold mb-4">Eksport użytkowników</h2>
        <p class="text-gray-600">Wybierz typ eksportu i pobierz plik CSV.</p>
        
        <div class="mt-8 space-y-4">
            <div>
                <a href="{{ route('filament.admin.resources.users.export', ['type' => 'active', 'download' => '1']) }}" 
                   class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 text-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Pobierz aktywnych użytkowników
                </a>
            </div>
            
            <div>
                <a href="{{ route('filament.admin.resources.users.export', ['type' => 'all', 'download' => '1']) }}" 
                   class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Pobierz wszystkich użytkowników
                </a>
            </div>
        </div>
        
        <div class="mt-8">
            <a href="{{ route('filament.admin.resources.users.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Powrót do listy użytkowników
            </a>
        </div>
    </div>
</x-filament-panels::page>
