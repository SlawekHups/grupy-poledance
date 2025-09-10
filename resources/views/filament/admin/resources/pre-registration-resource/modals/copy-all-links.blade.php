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
                    <p>Kliknij "Kopiuj wszystkie" aby skopiować wszystkie linki do schowka</p>
                    <p>Lub kliknij na pojedynczy link, aby go skopiować</p>
                </div>
            </div>
        </div>
    </div>

    @if($preRegs->count() > 0)
        <div class="space-y-3">
            <div class="flex justify-between items-center">
                <h4 class="text-sm font-medium text-gray-700">
                    Dostępne linki ({{ $preRegs->count() }})
                </h4>
                <button 
                    type="button"
                    onclick="copyAllLinks()"
                    class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    📋 Kopiuj wszystkie
                </button>
            </div>
            
            <div class="space-y-2 max-h-96 overflow-y-auto">
                @foreach($preRegs as $preReg)
                    <div class="flex items-center space-x-2 p-3 bg-gray-50 rounded-md">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-2">
                                <input 
                                    type="text" 
                                    value="{{ route('pre-register', $preReg->token) }}" 
                                    readonly 
                                    class="flex-1 px-2 py-1 text-xs font-mono bg-white border border-gray-300 rounded"
                                    id="link-{{ $preReg->id }}"
                                >
                                <button 
                                    type="button"
                                    onclick="copySingleLink('{{ $preReg->id }}')"
                                    class="px-2 py-1 bg-gray-600 text-white text-xs rounded hover:bg-gray-700"
                                >
                                    📋
                                </button>
                            </div>
                            <div class="mt-1 text-xs text-gray-500">
                                Token: <code>{{ $preReg->token }}</code> | 
                                Wygasa: {{ $preReg->expires_at->format('d.m.Y H:i') }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="text-center py-8 text-gray-500">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Brak dostępnych linków</h3>
            <p class="mt-1 text-sm text-gray-500">Nie ma żadnych ważnych linków pre-rejestracji</p>
        </div>
    @endif
</div>

<script>
function copySingleLink(id) {
    const input = document.getElementById('link-' + id);
    input.select();
    input.setSelectionRange(0, 99999);
    
    try {
        const successful = document.execCommand('copy');
        if (successful) {
            showCopySuccess('link-' + id);
        }
    } catch (err) {
        console.error('Błąd kopiowania:', err);
        alert('Nie udało się skopiować linku. Skopiuj go ręcznie.');
    }
}

function copyAllLinks() {
    const links = @json($preRegs->map(fn($preReg) => route('pre-register', $preReg->token)));
    const text = links.join('\n');
    
    try {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(() => {
                showCopyAllSuccess();
            });
        } else {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            textArea.style.top = '-999999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            
            const successful = document.execCommand('copy');
            document.body.removeChild(textArea);
            
            if (successful) {
                showCopyAllSuccess();
            }
        }
    } catch (err) {
        console.error('Błąd kopiowania:', err);
        alert('Nie udało się skopiować linków. Skopiuj je ręcznie.');
    }
}

function showCopySuccess(inputId) {
    const button = document.querySelector(`#${inputId}`).nextElementSibling;
    const originalText = button.textContent;
    button.textContent = '✅';
    button.classList.remove('bg-gray-600', 'hover:bg-gray-700');
    button.classList.add('bg-green-600');
    
    setTimeout(() => {
        button.textContent = originalText;
        button.classList.remove('bg-green-600');
        button.classList.add('bg-gray-600', 'hover:bg-gray-700');
    }, 2000);
}

function showCopyAllSuccess() {
    const button = document.querySelector('button[onclick="copyAllLinks()"]');
    const originalText = button.textContent;
    button.textContent = '✅ Skopiowano!';
    button.classList.remove('bg-blue-600', 'hover:bg-blue-700');
    button.classList.add('bg-green-600');
    
    setTimeout(() => {
        button.textContent = originalText;
        button.classList.remove('bg-green-600');
        button.classList.add('bg-blue-600', 'hover:bg-blue-700');
    }, 3000);
}
</script>
