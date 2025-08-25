<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Profil i Kontakt -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="rounded-xl border bg-white">
                <div class="px-4 py-3 border-b font-medium flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-user" class="h-5 w-5" />
                    Profil
                </div>
                <div class="p-4 space-y-2 text-gray-700">
                    <div class="flex items-center justify-between"><span>Imię i nazwisko</span><span class="font-medium">{{ auth()->user()->name }}</span></div>
                    <div class="flex items-center justify-between"><span>Status</span>
                        <x-filament::badge :color="auth()->user()->is_active ? 'success' : 'danger'">
                            {{ auth()->user()->is_active ? 'Aktywny' : 'Nieaktywny' }}
                        </x-filament::badge>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border bg-white">
                <div class="px-4 py-3 border-b font-medium flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-envelope" class="h-5 w-5" />
                    Kontakt
                </div>
                <div class="p-4 space-y-2 text-gray-700">
                    <div class="flex items-center justify-between"><span>Email</span><span class="font-medium break-words">{{ auth()->user()->email }}</span></div>
                    <div class="flex items-center justify-between"><span>Telefon</span><span class="font-medium break-words">{{ auth()->user()->phone ?? '—' }}</span></div>
                </div>
            </div>

            <div class="rounded-xl border bg-white">
                <div class="px-4 py-3 border-b font-medium flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-map-pin" class="h-5 w-5" />
                    Adres
                    <div class="ml-auto text-sm">
                        @php
                            $address = \App\Models\Address::where('user_id', auth()->id())->first();
                            $addressUrl = $address
                                ? route('filament.user.resources.addresses.edit', ['record' => $address->id])
                                : route('filament.user.resources.addresses.create');
                        @endphp
                        <x-filament::button tag="a" href="{{ $addressUrl }}" icon="heroicon-o-pencil-square" size="sm" color="primary">
                            Edytuj
                        </x-filament::button>
                    </div>
                </div>
                <div class="p-4 space-y-1 text-gray-700">
                    <div><span class="text-gray-500">Ulica: </span><span class="font-medium">{{ $address->street ?? '—' }}</span></div>
                    <div><span class="text-gray-500">Kod / Miasto: </span><span class="font-medium">{{ $address?->postal_code ? ($address->postal_code.' '.$address->city) : ($address->city ?? '—') }}</span></div>
                    <div><span class="text-gray-500">Typ: </span><span class="font-medium">{{ $address->type ?? '—' }}</span></div>
                </div>
            </div>
        </div>

        <!-- Statystyki -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="rounded-xl border p-4 bg-white flex items-center gap-3">
                <x-filament::icon icon="heroicon-o-exclamation-triangle" class="h-6 w-6 text-orange-600" />
                <div class="flex-1">
                    <div class="text-sm text-gray-500">Zaległości</div>
                    <div class="mt-1 flex items-center gap-2">
                        <div class="text-2xl font-semibold">{{ $this->unpaidCount }}</div>
                        <x-filament::badge :color="$this->unpaidCount > 0 ? 'danger' : 'success'">
                            {{ $this->unpaidCount > 0 ? 'Nieopłacone' : 'Brak' }}
                        </x-filament::badge>
                    </div>
                </div>
            </div>
            <div class="rounded-xl border p-4 bg-white flex items-center gap-3">
                <x-filament::icon icon="heroicon-o-banknotes" class="h-6 w-6 text-primary-600" />
                <div>
                    <div class="text-sm text-gray-500">Do zapłaty</div>
                    <div class="text-2xl font-semibold mt-1">{{ number_format($this->totalDue, 2) }} zł</div>
                </div>
            </div>
            <div class="rounded-xl border p-4 bg-white flex items-center gap-3">
                <x-filament::icon icon="heroicon-o-users" class="h-6 w-6 text-gray-700" />
                <div>
                    <div class="text-sm text-gray-500">Grupa</div>
                    <div class="text-2xl font-semibold mt-1">{{ $this->groupName ?? 'Brak' }}</div>
                </div>
            </div>
        </div>

        <!-- Płatności -->
        <div class="rounded-xl border bg-white">
            <div class="px-4 py-3 border-b font-medium flex items-center gap-2">
                <x-filament::icon icon="heroicon-o-credit-card" class="h-5 w-5" />
                Płatności
                <div class="ml-auto text-sm">
                    <x-filament::button tag="a" href="{{ route('filament.user.resources.payments.index') }}" icon="heroicon-o-banknotes" color="warning" size="sm">
                        Wszystkie płatności
                    </x-filament::button>
                </div>
            </div>
            <div class="p-4 text-gray-700 space-y-4">
                @if ($this->unpaidCount > 0)
                    <div class="text-sm text-red-700 bg-red-50 border border-red-200 rounded-md p-3">
                        Masz zaległe płatności. Łączna kwota: <strong>{{ number_format($this->totalDue, 2) }} zł</strong>.
                    </div>
                @else
                    <div class="text-sm text-green-700 bg-green-50 border border-green-200 rounded-md p-3">
                        Brak zaległości. Dziękujemy!
                    </div>
                @endif

                <div>
                    <div class="text-sm font-medium text-gray-600 mb-2">Ostatnie 3 miesiące</div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @forelse ($this->recentPayments as $p)
                            <div class="rounded-lg border bg-white p-4 flex flex-col gap-2">
                                <div class="text-sm text-gray-500">{{ \Carbon\Carbon::createFromFormat('Y-m', $p['month'])->translatedFormat('F Y') }}</div>
                                <div class="text-xl font-semibold text-right">{{ number_format($p['amount'], 2) }} zł</div>
                                <div>
                                    <x-filament::badge :color="$p['paid'] ? 'success' : 'danger'">
                                        {{ $p['paid'] ? 'Opłacona' : 'Nieopłacona' }}
                                    </x-filament::badge>
                                </div>
                                @if (!$p['paid'] && $p['payment_link'])
                                    <x-filament::button tag="a" href="{{ $p['payment_link'] }}" target="_blank" icon="heroicon-o-credit-card" color="info" size="sm" class="mt-1">
                                        Zapłać
                                    </x-filament::button>
                                @endif
                            </div>
                        @empty
                            <div class="text-gray-500">Brak danych o płatnościach.</div>
                        @endforelse
                    </div>
                </div>

                {{-- przycisk zapłać teraz usunięty --}}
            </div>
        </div>
    </div>
</x-filament-panels::page>
