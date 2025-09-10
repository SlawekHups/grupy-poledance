<div class="space-y-4">
    <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">
                    Instrukcja
                </h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p>Kliknij na link poniżej, aby go skopiować do schowka</p>
                </div>
            </div>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Link pre-rejestracji:
        </label>
        <div class="flex">
            <input 
                type="text" 
                value="{{ $url }}" 
                readonly 
                class="flex-1 px-3 py-2 border border-gray-300 rounded-l-md shadow-sm bg-gray-50 text-sm font-mono"
                id="link-input-{{ $token }}"
            >
            <button 
                type="button"
                onclick="copyLink('{{ $token }}')"
                class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-r-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
            >
                📋 Kopiuj
            </button>
        </div>
    </div>

    <div class="text-sm text-gray-600">
        <p><strong>Token:</strong> <code class="bg-gray-100 px-2 py-1 rounded">{{ $token }}</code></p>
        <p class="mt-1"><strong>Wygasa:</strong> {{ \Carbon\Carbon::parse($record->expires_at ?? now()->addMinutes(30))->format('d.m.Y H:i') }}</p>
    </div>
</div>

<script>
function copyLink(token) {
    const input = document.getElementById('link-input-' + token);
    input.select();
    input.setSelectionRange(0, 99999); // Dla urządzeń mobilnych
    
    try {
        const successful = document.execCommand('copy');
        if (successful) {
            // Pokaż powiadomienie
            const button = event.target;
            const originalText = button.textContent;
            button.textContent = '✅ Skopiowano!';
            button.classList.remove('bg-blue-600', 'hover:bg-blue-700');
            button.classList.add('bg-green-600');
            
            setTimeout(() => {
                button.textContent = originalText;
                button.classList.remove('bg-green-600');
                button.classList.add('bg-blue-600', 'hover:bg-blue-700');
            }, 2000);
        }
    } catch (err) {
        console.error('Błąd kopiowania:', err);
        alert('Nie udało się skopiować linku. Skopiuj go ręcznie.');
    }
}
</script>
