<div class="space-y-4">
    <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-link text-blue-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">
                    Link do poprawy danych
                </h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p>Użytkownik: <strong>{{ $record->user->name }}</strong></p>
                    <p>Email: <strong>{{ $record->user->email }}</strong></p>
                    <p>Wygasa: <strong>{{ $record->expires_at->format('d.m.Y H:i') }}</strong></p>
                </div>
            </div>
        </div>
    </div>
    
    <div>
        <label for="link-input" class="block text-sm font-medium text-gray-700 mb-2">
            Link do skopiowania:
        </label>
        <div class="flex">
            <input 
                type="text" 
                id="link-input" 
                value="{{ $url }}" 
                readonly
                class="flex-1 px-3 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            >
            <button 
                type="button"
                onclick="copyToClipboard()"
                class="px-4 py-2 bg-blue-600 text-white rounded-r-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
            >
                <i class="fas fa-copy"></i>
            </button>
        </div>
        <p class="mt-1 text-sm text-gray-500">
            Kliknij przycisk kopiowania lub zaznacz tekst i skopiuj ręcznie
        </p>
    </div>
    
    <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">
                    Instrukcje
                </h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <p>1. Skopiuj link powyżej</p>
                    <p>2. Wyślij go użytkownikowi (SMS, email, telefon)</p>
                    <p>3. Użytkownik otworzy link i poprawi swoje dane</p>
                    <p>4. Link wygasa automatycznie po określonym czasie</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard() {
    const input = document.getElementById('link-input');
    input.select();
    input.setSelectionRange(0, 99999); // For mobile devices
    
    try {
        document.execCommand('copy');
        
        // Pokaż komunikat o sukcesie
        const button = event.target.closest('button');
        const originalHTML = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check"></i> Skopiowano!';
        button.classList.add('bg-green-600', 'hover:bg-green-700');
        button.classList.remove('bg-blue-600', 'hover:bg-blue-700');
        
        setTimeout(() => {
            button.innerHTML = originalHTML;
            button.classList.remove('bg-green-600', 'hover:bg-green-700');
            button.classList.add('bg-blue-600', 'hover:bg-blue-700');
        }, 2000);
        
    } catch (err) {
        alert('Nie udało się skopiować. Zaznacz tekst i skopiuj ręcznie (Ctrl+C)');
    }
}
</script>
