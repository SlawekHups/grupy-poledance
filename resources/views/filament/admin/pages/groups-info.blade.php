<div class="space-y-6">
    <div class="bg-white rounded-lg p-6 shadow-sm border">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">System grup - opis funkcjonalności</h3>
        
        <div class="space-y-6">
            <!-- Widżety statystyk -->
            <div>
                <h4 class="font-medium text-gray-900 mb-3">📊 Widżety statystyk grup</h4>
                <div class="space-y-3">
                    <div class="border-l-4 border-green-500 pl-4">
                        <h5 class="font-medium text-gray-900">👥 Łączna liczba użytkowników</h5>
                        <p class="text-sm text-gray-600">Całkowita liczba użytkowników w systemie (wykluczając administratorów).</p>
                    </div>
                    
                    <div class="border-l-4 border-orange-500 pl-4">
                        <h5 class="font-medium text-gray-900">📁 Liczba grup</h5>
                        <p class="text-sm text-gray-600">Wszystkie zarejestrowane grupy treningowe w systemie.</p>
                    </div>
                    
                    <div class="border-l-4 border-yellow-500 pl-4">
                        <h5 class="font-medium text-gray-900">👤 Bez grupy</h5>
                        <p class="text-sm text-gray-600">Użytkownicy, którzy nie są przypisani do żadnej grupy treningowej.</p>
                    </div>
                    
                    <div class="border-l-4 border-red-500 pl-4">
                        <h5 class="font-medium text-gray-900">🔴 Grupy pełne</h5>
                        <p class="text-sm text-gray-600">Grupy, które osiągnęły maksymalny limit uczestników (status: full).</p>
                    </div>
                    
                    <div class="border-l-4 border-green-500 pl-4">
                        <h5 class="font-medium text-gray-900">🟢 Grupy z miejscem</h5>
                        <p class="text-sm text-gray-600">Grupy aktywne, które mają jeszcze wolne miejsca dla nowych uczestników.</p>
                    </div>
                </div>
            </div>

            <!-- Wyświetlanie kafelków -->
            <div>
                <h4 class="font-medium text-gray-900 mb-3">📱 Wyświetlanie grup</h4>
                <div class="space-y-3">
                    <div class="border-l-4 border-blue-500 pl-4">
                        <h5 class="font-medium text-gray-900">📋 Kafelki grup</h5>
                        <p class="text-sm text-gray-600">Lista grup wyświetlana w formie kafelków (3 na dużym ekranie, 1 na telefonie) dla lepszej czytelności.</p>
                    </div>
                    
                    <div class="border-l-4 border-purple-500 pl-4">
                        <h5 class="font-medium text-gray-900">🎨 Kolorowe obramowania</h5>
                        <p class="text-sm text-gray-600">Każdy kafelek ma kolorowe obramowanie z lewej strony, które wskazuje na status grupy:</p>
                        <div class="mt-2 text-xs text-gray-500 space-y-1">
                            <div>• <span class="text-green-600">Zielone</span> - grupy z wolnymi miejscami</div>
                            <div>• <span class="text-red-600">Czerwone</span> - grupy pełne</div>
                            <div>• <span class="text-yellow-600">Żółte</span> - grupy nieaktywne</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Zarządzanie grupami -->
            <div>
                <h4 class="font-medium text-gray-900 mb-3">🔧 Zarządzanie grupami</h4>
                <div class="space-y-3">
                    <div class="border-l-4 border-green-500 pl-4">
                        <h5 class="font-medium text-gray-900">➕ Dodaj grupę</h5>
                        <p class="text-sm text-gray-600">Przycisk do tworzenia nowej grupy treningowej z konfiguracją nazwy, limitu uczestników i statusu.</p>
                    </div>
                    
                    <div class="border-l-4 border-blue-500 pl-4">
                        <h5 class="font-medium text-gray-900">✏️ Edytuj grupę</h5>
                        <p class="text-sm text-gray-600">Możliwość modyfikacji ustawień grupy, dodawania/usuwania uczestników.</p>
                    </div>
                    
                    <div class="border-l-4 border-orange-500 pl-4">
                        <h5 class="font-medium text-gray-900">👥 Zarządzanie uczestnikami</h5>
                        <p class="text-sm text-gray-600">Przypisywanie użytkowników do grup, usuwanie z grup, kontrola limitów.</p>
                    </div>
                </div>
            </div>

            <!-- Statusy grup -->
            <div>
                <h4 class="font-medium text-gray-900 mb-3">📋 Statusy grup</h4>
                <div class="space-y-3">
                    <div class="border-l-4 border-green-500 pl-4">
                        <h5 class="font-medium text-gray-900">🟢 Aktywna (active)</h5>
                        <p class="text-sm text-gray-600">Grupa funkcjonuje normalnie, przyjmuje nowych uczestników.</p>
                    </div>
                    
                    <div class="border-l-4 border-red-500 pl-4">
                        <h5 class="font-medium text-gray-900">🔴 Pełna (full)</h5>
                        <p class="text-sm text-gray-600">Grupa osiągnęła maksymalny limit uczestników, nie przyjmuje nowych.</p>
                    </div>
                    
                    <div class="border-l-4 border-yellow-500 pl-4">
                        <h5 class="font-medium text-gray-900">🟡 Nieaktywna (inactive)</h5>
                        <p class="text-sm text-gray-600">Grupa jest tymczasowo zawieszona lub zakończona.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
            <h5 class="font-medium text-gray-900 mb-2">💡 Wskazówki:</h5>
            <ul class="text-sm text-gray-600 space-y-1">
                <li>• Kafelki grup są klikalne - prowadzą do edycji grupy</li>
                <li>• Kolory obramowań pomagają szybko zidentyfikować status grupy</li>
                <li>• System automatycznie oblicza liczbę uczestników w każdej grupie</li>
                <li>• Widżety aktualizują się automatycznie po zmianach</li>
                <li>• Użytkownicy "bez grupy" to ci, którzy nie są przypisani do żadnej grupy</li>
                <li>• Grupy "z miejscem" to aktywne grupy poniżej limitu uczestników</li>
            </ul>
        </div>
    </div>
</div>
