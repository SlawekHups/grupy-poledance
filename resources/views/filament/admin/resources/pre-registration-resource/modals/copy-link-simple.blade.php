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
                    <p>Kliknij przycisk poniżej, aby skopiować link do schowka</p>
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
                id="linkInput-{{ $token }}"
            >
            <button 
                type="button"
                onclick="
                    const input = document.getElementById('linkInput-{{ $token }}');
                    const button = document.getElementById('copyBtn-{{ $token }}');
                    const text = document.getElementById('copyText-{{ $token }}');
                    
                    input.select();
                    input.setSelectionRange(0, 99999);
                    
                    try {
                        if (navigator.clipboard && window.isSecureContext) {
                            navigator.clipboard.writeText('{{ $url }}').then(() => {
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
                            }).catch(() => {
                                document.execCommand('copy');
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
                            });
                        } else {
                            document.execCommand('copy');
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
                    } catch (err) {
                        alert('Nie udało się skopiować linku. Skopiuj go ręcznie.');
                    }
                "
                id="copyBtn-{{ $token }}"
                class="px-4 py-2 text-sm font-medium rounded-r-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 bg-gray-600 hover:bg-gray-700"
                style="color: white !important; background-color: #4b5563 !important;"
            >
                <span id="copyText-{{ $token }}" style="color: white !important; font-weight: 500 !important;">📋 Kopiuj</span>
            </button>
        </div>
    </div>

    <div class="text-sm text-gray-600">
        <p><strong>Token:</strong> <code class="bg-gray-100 px-2 py-1 rounded">{{ $token }}</code></p>
        <p class="mt-1"><strong>Wygasa:</strong> {{ \Carbon\Carbon::parse($record->expires_at ?? now()->addMinutes(30))->format('d.m.Y H:i') }}</p>
    </div>
</div>
