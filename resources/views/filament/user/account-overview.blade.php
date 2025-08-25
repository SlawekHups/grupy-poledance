<x-filament-panels::page>
    <div class="space-y-6">
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

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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

            <div class="rounded-xl border bg-white md:col-span-2">
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
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-left text-gray-500">
                                        <th class="py-2 pr-4">Miesiąc</th>
                                        <th class="py-2 pr-4 text-right">Kwota</th>
                                        <th class="py-2 pr-4">Status</th>
                                        <th class="py-2">Akcja</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @forelse ($this->recentPayments as $p)
                                    <tr class="border-t">
                                        <td class="py-2 pr-4">{{ \Carbon\Carbon::createFromFormat('Y-m', $p['month'])->translatedFormat('F Y') }}</td>
                                        <td class="py-2 pr-4 text-right">{{ number_format($p['amount'], 2) }} zł</td>
                                        <td class="py-2 pr-4">
                                            <x-filament::badge :color="$p['paid'] ? 'success' : 'danger'">
                                                {{ $p['paid'] ? 'Opłacona' : 'Nieopłacona' }}
                                            </x-filament::badge>
                                        </td>
                                        <td class="py-2">
                                            @if (!$p['paid'] && $p['payment_link'])
                                                <x-filament::button tag="a" href="{{ $p['payment_link'] }}" target="_blank" icon="heroicon-o-credit-card" color="info" size="xs">
                                                    Zapłać
                                                </x-filament::button>
                                            @else
                                                —
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-2 text-gray-500">Brak danych o płatnościach.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- przycisk zapłać teraz usunięty --}}
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
