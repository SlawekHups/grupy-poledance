<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Profil i Kontakt -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="{{ route('filament.user.resources.users.edit', ['record' => auth()->id()]) }}" class="rounded-xl border bg-white block hover:bg-gray-50 shadow-sm hover:shadow-md transition-colors outline-none focus:outline-none focus-visible:outline-none focus:ring-0" aria-label="Edytuj profil">
                <div class="px-4 py-3 border-b font-medium flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-user" class="h-5 w-5" style="color: var(--fi-color-warning-hover, #d97706)" />
                    <span style="color: var(--fi-color-warning-hover, #d97706)">Profil</span>
                </div>
                <div class="p-4 space-y-2 text-gray-700">
                    <div class="text-2xl md:text-3xl font-extrabold text-gray-900">{{ auth()->user()->name }}</div>
                    <div class="flex items-center justify-between"><span>Status</span>
                        <x-filament::badge :color="auth()->user()->is_active ? 'success' : 'danger'">
                            {{ auth()->user()->is_active ? 'Aktywny' : 'Nieaktywny' }}
                        </x-filament::badge>
                    </div>
                </div>
            </a>

            <a href="{{ route('filament.user.resources.users.edit', ['record' => auth()->id()]) }}" class="rounded-xl border bg-white block hover:bg-gray-50 shadow-sm hover:shadow-md transition-colors outline-none focus:outline-none focus-visible:outline-none focus:ring-0" aria-label="Edytuj kontakt">
                <div class="px-4 py-3 border-b font-medium flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-envelope" class="h-5 w-5" style="color: var(--fi-color-warning-hover, #d97706)" />
                    <span style="color: var(--fi-color-warning-hover, #d97706)">Kontakt</span>
                </div>
                <div class="p-4 space-y-2 text-gray-700">
                    <div class="flex items-center justify-between"><span>Email</span><span class="font-semibold break-words">{{ auth()->user()->email }}</span></div>
                    <div class="flex items-center justify-between"><span>Telefon</span><span class="font-semibold break-words">{{ auth()->user()->phone ?? '—' }}</span></div>
                </div>
            </a>

            <div class="rounded-xl border bg-white">
                <a href="{{ route('filament.user.resources.addresses.index') }}" class="block hover:bg-gray-50 shadow-sm hover:shadow-md transition-colors" aria-label="Przejdź do edycji adresów">
                    <div class="px-4 py-3 border-b font-medium flex items-center gap-2">
                        <x-filament::icon icon="heroicon-o-map-pin" class="h-5 w-5" style="color: var(--fi-color-warning-hover, #d97706)" />
                        <span style="color: var(--fi-color-warning-hover, #d97706)">Adres</span>
                    </div>
                    @php($address = \App\Models\Address::where('user_id', auth()->id())->first())
                    <div class="p-4 space-y-2 text-gray-700">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Ulica</span>
                            <span class="font-semibold break-words text-right">{{ $address->street ?? '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Kod / Miasto</span>
                            <span class="font-semibold break-words text-right">{{ $address?->postal_code ? ($address->postal_code.' '.$address->city) : ($address->city ?? '—') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Typ</span>
                            <span class="font-semibold break-words text-right">{{ $address->type ?? '—' }}</span>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Statystyki -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="rounded-xl border p-4 bg-white flex items-center gap-3">
                <x-filament::icon icon="heroicon-o-users" class="h-6 w-6 text-gray-700" />
                <div>
                    <div class="text-sm text-gray-500">Grupa</div>
                    <div class="text-2xl font-semibold mt-1">{{ $this->groupName ?? 'Brak' }}</div>
                </div>
            </div>
            <a href="{{ route('filament.user.resources.attendances.index', ['tableFilters' => ['present' => '1']]) }}" class="rounded-xl border bg-white block hover:bg-gray-50 shadow-sm hover:shadow-md transition-colors p-4">
                @php($total = max(1, $this->presentCount + $this->absentCount))
                @php($presentPct = round(($this->presentCount / $total) * 100))
                <div class="flex items-center gap-3">
                    <x-filament::icon icon="heroicon-o-check-circle" class="h-6 w-6" style="color: var(--fi-color-success, #16a34a)" />
                    <div class="text-lg font-medium">Obecności</div>
                    <div class="ml-auto flex items-baseline gap-2">
                        <span class="text-3xl md:text-4xl font-extrabold" style="color: var(--fi-color-success, #16a34a)">{{ $this->presentCount }}</span>
                        <span class="text-xs px-2 py-0.5 rounded" style="background-color: rgba(34,197,94,0.15); color: var(--fi-color-success, #16a34a)">{{ $presentPct }}%</span>
                    </div>
                </div>
                <div class="mt-2 h-2 rounded" style="background-color: rgba(34,197,94,0.15)">
                    <div class="h-2 rounded" style="width: {{ $presentPct }}%; background-color: var(--fi-color-success, #16a34a)"></div>
                </div>
                <div class="mt-2 text-sm text-gray-600">Zobacz listę zajęć, na których byłeś obecny.</div>
            </a>
            <a href="{{ route('filament.user.resources.attendances.index', ['tableFilters' => ['present' => '0']]) }}" class="rounded-xl border bg-white block hover:bg-gray-50 shadow-sm hover:shadow-md transition-colors p-4">
                @php($absentPct = round(($this->absentCount / $total) * 100))
                <div class="flex items-center gap-3">
                    <x-filament::icon icon="heroicon-o-x-circle" class="h-6 w-6" style="color: var(--fi-color-danger, #dc2626)" />
                    <div class="text-lg font-medium">Nieobecności</div>
                    <div class="ml-auto flex items-baseline gap-2">
                        <span class="text-3xl md:text-4xl font-extrabold" style="color: var(--fi-color-danger, #dc2626)">{{ $this->absentCount }}</span>
                        <span class="text-xs px-2 py-0.5 rounded" style="background-color: rgba(220,38,38,0.15); color: var(--fi-color-danger, #dc2626)">{{ $absentPct }}%</span>
                    </div>
                </div>
                <div class="mt-2 h-2 rounded" style="background-color: rgba(220,38,38,0.15)">
                    <div class="h-2 rounded" style="width: {{ $absentPct }}%; background-color: var(--fi-color-danger, #dc2626)"></div>
                </div>
                <div class="mt-2 text-sm text-gray-600">Zobacz listę zajęć, na których Cię nie było.</div>
            </a>
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
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('filament.user.resources.payments.index') }}" class="rounded-xl border p-4 bg-white shadow-sm hover:bg-gray-50 hover:shadow-md transition-colors block">
                        <div class="flex items-center gap-3">
                            <x-filament::icon icon="heroicon-o-receipt-percent" class="h-6 w-6" style="color: var(--fi-color-info, #3b82f6)" />
                            <div class="text-sm text-gray-600">Liczba płatności</div>
                            <div class="ml-auto text-2xl font-extrabold" style="color: var(--fi-color-info, #3b82f6)">{{ $this->paymentsCount }}</div>
                        </div>
                    </a>
                    <a href="{{ route('filament.user.resources.payments.index') }}" class="rounded-xl border p-4 bg-white shadow-sm hover:bg-gray-50 hover:shadow-md transition-colors block">
                        <div class="flex items-center gap-3">
                            <x-filament::icon icon="heroicon-o-banknotes" class="h-6 w-6" style="color: var(--fi-color-success, #16a34a)" />
                            <div class="text-sm text-gray-600">Suma płatności</div>
                            <div class="ml-auto text-2xl font-extrabold" style="color: var(--fi-color-success, #16a34a)">{{ number_format($this->paymentsSum, 2) }} zł</div>
                        </div>
                    </a>
                    <a href="{{ route('filament.user.resources.payments.index') }}" class="rounded-xl border p-4 bg-white shadow-sm hover:bg-gray-50 hover:shadow-md transition-colors block">
                        <div class="flex items-center gap-3">
                            <x-filament::icon icon="heroicon-o-exclamation-triangle" class="h-6 w-6" style="color: var(--fi-color-danger, #dc2626)" />
                            <div class="text-sm text-gray-600">Suma zaległości</div>
                            <div class="ml-auto text-2xl font-extrabold" style="color: var(--fi-color-danger, #dc2626)">{{ number_format($this->totalDue, 2) }} zł</div>
                        </div>
                    </a>
                </div>
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
