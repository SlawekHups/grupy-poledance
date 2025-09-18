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
                class="flex-1 px-3 py-2 border border-gray-300 rounded-l-md shadow-sm bg-gray-50 text-sm font-mono"
            >
            <button 
                type="button"
                onclick="
                    console.log('Przycisk kliknięty');
                    const input = document.getElementById('link-input');
                    const button = document.getElementById('copyBtn');
                    const text = document.getElementById('copyText');
                    
                    if (!input || !button || !text) {
                        console.error('Nie znaleziono elementów');
                        alert('Błąd: Nie znaleziono elementów');
                        return;
                    }
                    
                    input.select();
                    input.setSelectionRange(0, 99999);
                    
                    try {
                        if (navigator.clipboard && window.isSecureContext) {
                            navigator.clipboard.writeText('{{ $url }}').then(() => {
                                console.log('Kopiowanie udane');
                                button.style.backgroundColor = '#16a34a';
                                text.innerHTML = '✅ Skopiowano!';
                                setTimeout(() => {
                                    button.style.backgroundColor = '#4b5563';
                                    text.innerHTML = '📋 Kopiuj';
                                }, 2000);
                            }).catch(() => {
                                document.execCommand('copy');
                                button.style.backgroundColor = '#16a34a';
                                text.innerHTML = '✅ Skopiowano!';
                                setTimeout(() => {
                                    button.style.backgroundColor = '#4b5563';
                                    text.innerHTML = '📋 Kopiuj';
                                }, 2000);
                            });
                        } else {
                            const success = document.execCommand('copy');
                            if (success) {
                                button.style.backgroundColor = '#16a34a';
                                text.innerHTML = '✅ Skopiowano!';
                                setTimeout(() => {
                                    button.style.backgroundColor = '#4b5563';
                                    text.innerHTML = '📋 Kopiuj';
                                }, 2000);
                            } else {
                                alert('Nie udało się skopiować. Zaznacz tekst i skopiuj ręcznie (Ctrl+C)');
                            }
                        }
                    } catch (err) {
                        console.error('Błąd:', err);
                        alert('Nie udało się skopiować. Zaznacz tekst i skopiuj ręcznie (Ctrl+C)');
                    }
                "
                id="copyBtn"
                class="px-4 py-2 text-sm font-medium rounded-r-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 bg-gray-600 hover:bg-gray-700"
                style="color: white !important; background-color: #4b5563 !important;"
            >
                <span id="copyText" style="color: white !important; font-weight: 500 !important;">📋 Kopiuj</span>
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
    console.log('Funkcja copyToClipboard została wywołana');
    
    const input = document.getElementById('link-input');
    const button = document.getElementById('copyBtn');
    const text = document.getElementById('copyText');
    
    if (!input || !button || !text) {
        console.error('Nie znaleziono elementów:', { input, button, text });
        alert('Błąd: Nie znaleziono elementów do kopiowania');
        return;
    }
    
    console.log('Elementy znalezione:', { input, button, text });
    
    input.select();
    input.setSelectionRange(0, 99999);
    
    try {
        // Najpierw spróbuj nowoczesnego API
        if (navigator.clipboard && window.isSecureContext) {
            console.log('Używam navigator.clipboard');
            navigator.clipboard.writeText('{{ $url }}').then(() => {
                console.log('Kopiowanie z navigator.clipboard udane');
                showSuccess(button, text);
            }).catch((err) => {
                console.error('Błąd navigator.clipboard:', err);
                fallbackCopy(input, button, text);
            });
        } else {
            console.log('Używam document.execCommand');
            fallbackCopy(input, button, text);
        }
    } catch (err) {
        console.error('Błąd w try-catch:', err);
        alert('Nie udało się skopiować linku. Skopiuj go ręcznie.');
    }
}

function fallbackCopy(input, button, text) {
    try {
        const success = document.execCommand('copy');
        if (success) {
            console.log('Kopiowanie z document.execCommand udane');
            showSuccess(button, text);
        } else {
            console.error('document.execCommand zwróciło false');
            alert('Nie udało się skopiować. Zaznacz tekst i skopiuj ręcznie (Ctrl+C)');
        }
    } catch (err) {
        console.error('Błąd document.execCommand:', err);
        alert('Nie udało się skopiować. Zaznacz tekst i skopiuj ręcznie (Ctrl+C)');
    }
}

function showSuccess(button, text) {
    button.style.backgroundColor = '#16a34a';
    button.style.color = 'white';
    text.innerHTML = '✅ Skopiowano!';
    text.style.color = 'white';
    
    setTimeout(() => {
        button.style.backgroundColor = '#4b5563';
        button.style.color = 'white';
        text.innerHTML = '📋 Kopiuj';
        text.style.color = 'white';
    }, 2000);
}
</script>
