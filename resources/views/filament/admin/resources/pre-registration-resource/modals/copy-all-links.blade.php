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
                    <p>Możesz skopiować wszystkie linki naraz lub pojedynczo. Kliknij przycisk "Kopiuj wszystkie" aby skopiować wszystkie linki do schowka.</p>
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
                    onclick="
                        const allLinksText = `{!! $preRegs->map(fn($preReg) => route('pre-register', $preReg->token))->join('\n') !!}`;
                        const button = document.getElementById('copyAllBtn');
                        const text = document.getElementById('copyAllText');
                        
                        try {
                            if (navigator.clipboard && window.isSecureContext) {
                                navigator.clipboard.writeText(allLinksText).then(() => {
                                    button.style.backgroundColor = '#16a34a';
                                    button.style.color = 'white';
                                    text.innerHTML = '✅ Skopiowano!';
                                    text.style.color = 'white';
                                    setTimeout(() => {
                                        button.style.backgroundColor = '#4b5563';
                                        button.style.color = 'white';
                                        text.innerHTML = '📋 Kopiuj wszystkie';
                                        text.style.color = 'white';
                                    }, 2000);
                                }).catch(() => {
                                    const textarea = document.createElement('textarea');
                                    textarea.value = allLinksText;
                                    textarea.style.position = 'fixed';
                                    textarea.style.left = '-999999px';
                                    textarea.style.top = '-999999px';
                                    document.body.appendChild(textarea);
                                    textarea.focus();
                                    textarea.select();
                                    document.execCommand('copy');
                                    document.body.removeChild(textarea);
                                    button.style.backgroundColor = '#16a34a';
                                    button.style.color = 'white';
                                    text.innerHTML = '✅ Skopiowano!';
                                    text.style.color = 'white';
                                    setTimeout(() => {
                                        button.style.backgroundColor = '#4b5563';
                                        button.style.color = 'white';
                                        text.innerHTML = '📋 Kopiuj wszystkie';
                                        text.style.color = 'white';
                                    }, 2000);
                                });
                            } else {
                                const textarea = document.createElement('textarea');
                                textarea.value = allLinksText;
                                textarea.style.position = 'fixed';
                                textarea.style.left = '-999999px';
                                textarea.style.top = '-999999px';
                                document.body.appendChild(textarea);
                                textarea.focus();
                                textarea.select();
                                document.execCommand('copy');
                                document.body.removeChild(textarea);
                                button.style.backgroundColor = '#16a34a';
                                button.style.color = 'white';
                                text.innerHTML = '✅ Skopiowano!';
                                text.style.color = 'white';
                                setTimeout(() => {
                                    button.style.backgroundColor = '#4b5563';
                                    button.style.color = 'white';
                                    text.innerHTML = '📋 Kopiuj wszystkie';
                                    text.style.color = 'white';
                                }, 2000);
                            }
                        } catch (err) {
                            alert('Nie udało się skopiować linków. Skopiuj je ręcznie.');
                        }
                    "
                    id="copyAllBtn"
                    class="px-4 py-2 text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 bg-gray-600 hover:bg-gray-700"
                    style="color: white !important; background-color: #4b5563 !important;"
                >
                    <span id="copyAllText" style="color: white !important; font-weight: 500 !important;">📋 Kopiuj wszystkie</span>
                </button>
            </div>
            
            <div class="space-y-2 max-h-96 overflow-y-auto">
                @foreach($preRegs as $preReg)
                    <div class="flex items-center space-x-2 p-3 bg-gray-50 rounded-md">
                        <input 
                            type="text" 
                            value="{{ route('pre-register', $preReg->token) }}" 
                            readonly 
                            class="flex-1 px-2 py-1 text-xs font-mono bg-white border border-gray-300 rounded"
                            id="link-{{ $preReg->id }}"
                        >
                        <button 
                            type="button"
                            onclick="copySingleLink('{{ $preReg->id }}', '{{ route('pre-register', $preReg->token) }}')"
                            id="copyBtn-{{ $preReg->id }}"
                            class="px-2 py-1 text-xs rounded bg-gray-600 hover:bg-gray-700"
                            style="color: white !important;"
                        >
                            <span id="copyText-{{ $preReg->id }}" style="color: white !important;">📋</span>
                        </button>
                    </div>
                    <div class="mt-1 text-xs text-gray-500">
                        Token: <code>{{ $preReg->token }}</code> | 
                        Wygasa: {{ $preReg->expires_at->format('d.m.Y H:i') }}
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
            <p class="mt-1 text-sm text-gray-500">Nie ma żadnych ważnych linków pre-rejestracji</p>
        </div>
    @endif
</div>
