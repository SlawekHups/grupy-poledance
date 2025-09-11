@php($record = isset($getRecord) ? $getRecord() : null)
@if ($record)
    @php($groups = $record->groups)
    @php($isActive = $record->is_active ?? false)
    @php($hasAcceptedTerms = $record->terms_accepted_at !== null)
    @php($joinedAt = $record->joined_at ? \Carbon\Carbon::parse($record->joined_at)->format('d.m.Y') : 'Nie podano')
    @php($phone = $record->phone ?: 'Nie podano')
    @php($address = $record->addresses()->first())

    <div class="rounded-xl border bg-white">
        <div class="px-4 py-3 border-b font-medium flex items-center gap-2">
            <x-filament::icon icon="heroicon-o-user-circle" class="h-5 w-5" />
            Konto użytkownika
        </div>
        <div class="p-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Karta Profil -->
                <a href="{{ route('filament.admin.resources.users.edit', ['record' => $record->id, 'edit' => 1]) }}" class="rounded-xl border bg-white block hover:bg-gray-50 shadow-sm hover:shadow-md transition-colors outline-none focus:outline-none focus-visible:outline-none focus:ring-0" aria-label="Edytuj profil">
                    <div class="px-4 py-3 border-b font-medium flex items-center gap-2">
                        <x-filament::icon icon="heroicon-o-user" class="h-5 w-5" style="color: var(--fi-color-warning-hover, #d97706)" />
                        <span style="color: var(--fi-color-warning-hover, #d97706)">Profil</span>
                    </div>
                    <div class="p-4 space-y-2 text-gray-700">
                        <div class="text-2xl md:text-3xl font-extrabold text-gray-900">{{ $record->name }}</div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Status</span>
                            <x-filament::badge :color="$isActive ? 'success' : 'danger'">
                                {{ $isActive ? 'Aktywny' : 'Nieaktywny' }}
                            </x-filament::badge>
                        </div>
                    </div>
                </a>

                <!-- Karta Kontakt -->
                <a href="{{ route('filament.admin.resources.users.edit', ['record' => $record->id, 'edit' => 1]) }}" class="rounded-xl border bg-white block hover:bg-gray-50 shadow-sm hover:shadow-md transition-colors outline-none focus:outline-none focus-visible:outline-none focus:ring-0" aria-label="Edytuj kontakt">
                    <div class="px-4 py-3 border-b font-medium flex items-center gap-2">
                        <x-filament::icon icon="heroicon-o-envelope" class="h-5 w-5" style="color: var(--fi-color-warning-hover, #d97706)" />
                        <span style="color: var(--fi-color-warning-hover, #d97706)">Kontakt</span>
                    </div>
                    <div class="p-4 space-y-2 text-gray-700">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Email</span>
                            <span class="font-semibold break-words text-right">{{ $record->email }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Telefon</span>
                            <span class="font-semibold break-words text-right">{{ $phone }}</span>
                        </div>
                    </div>
                </a>

                <!-- Karta Adres -->
                <div class="rounded-xl border bg-white">
                    @if($address)
                        <div class="cursor-pointer hover:bg-gray-50 shadow-sm hover:shadow-md transition-colors" 
                             onclick="window.location.href='{{ route('filament.admin.resources.users.edit', ['record' => $record->id, 'edit' => 1, 'activeTab' => 'addresses']) }}'" 
                             aria-label="Przejdź do edycji adresu">
                            <div class="px-4 py-3 border-b font-medium flex items-center gap-2">
                                <x-filament::icon icon="heroicon-o-map-pin" class="h-5 w-5" style="color: var(--fi-color-warning-hover, #d97706)" />
                                <span style="color: var(--fi-color-warning-hover, #d97706)">Adres</span>
                            </div>
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
                        </div>
                    @else
                        <a href="{{ route('filament.admin.resources.users.edit', ['record' => $record->id, 'edit' => 1, 'activeTab' => 'addresses']) }}" class="block hover:bg-gray-50 shadow-sm hover:shadow-md transition-colors" aria-label="Dodaj adres">
                            <div class="px-4 py-3 border-b font-medium flex items-center gap-2">
                                <x-filament::icon icon="heroicon-o-map-pin" class="h-5 w-5" style="color: var(--fi-color-warning-hover, #d97706)" />
                                <span style="color: var(--fi-color-warning-hover, #d97706)">Adres</span>
                            </div>
                            <div class="p-4 space-y-2 text-gray-700">
                                <div class="text-center text-gray-500 py-4">
                                    <x-filament::icon icon="heroicon-o-plus" class="h-8 w-8 mx-auto mb-2 text-gray-400" />
                                    <span>Brak adresu - kliknij aby dodać</span>
                                </div>
                            </div>
                        </a>
                    @endif
                </div>

                <!-- Karta Grupa -->
                <div class="rounded-xl border bg-white">
                    <a href="{{ route('filament.admin.resources.users.edit', ['record' => $record->id, 'edit' => 1]) }}" class="block hover:bg-gray-50 shadow-sm hover:shadow-md transition-colors" aria-label="Edytuj grupy">
                        <div class="px-4 py-3 border-b font-medium flex items-center gap-2">
                            <x-filament::icon icon="heroicon-o-user-group" class="h-5 w-5" style="color: var(--fi-color-gray-600, #718096)" />
                            <span style="color: var(--fi-color-gray-600, #718096)">Grupa</span>
                        </div>
                        <div class="p-4 space-y-2 text-gray-700">
                            @if($groups->count() > 0)
                                <div class="text-2xl md:text-3xl font-extrabold text-gray-900">{{ $groups->first()->name }}</div>
                                @if($groups->count() > 1)
                                    <div class="text-sm text-gray-500">+{{ $groups->count() - 1 }} więcej</div>
                                @endif
                            @else
                                <div class="text-2xl md:text-3xl font-extrabold text-gray-500">Bez grupy</div>
                            @endif
                        </div>
                    </a>
                </div>

                <!-- Karta Potwierdzenie regulaminu -->
                <div class="rounded-xl border bg-white">
                    <a href="{{ route('filament.admin.resources.users.edit', ['record' => $record->id, 'edit' => 1]) }}" class="block hover:bg-gray-50 shadow-sm hover:shadow-md transition-colors" aria-label="Edytuj regulamin">
                        <div class="px-4 py-3 border-b font-medium flex items-center gap-2">
                            <x-filament::icon icon="heroicon-o-document-text" class="h-5 w-5" style="color: var(--fi-color-warning-hover, #d97706)" />
                            <span style="color: var(--fi-color-warning-hover, #d97706)">Regulamin</span>
                        </div>
                        <div class="p-4 space-y-2 text-gray-700">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500">Status</span>
                                <x-filament::badge :color="$hasAcceptedTerms ? 'success' : 'danger'">
                                    {{ $hasAcceptedTerms ? 'Zaakceptowany' : 'Nie zaakceptowany' }}
                                </x-filament::badge>
                            </div>
                            @if($hasAcceptedTerms)
                                <div class="text-sm text-gray-500">
                                    Data: {{ \Carbon\Carbon::parse($record->terms_accepted_at)->format('d.m.Y') }}
                                </div>
                            @endif
                        </div>
                    </a>
                </div>

                <!-- Karta Dołączenie -->
                <div class="rounded-xl border bg-white">
                    <a href="{{ route('filament.admin.resources.users.edit', ['record' => $record->id, 'edit' => 1]) }}" class="block hover:bg-gray-50 shadow-sm hover:shadow-md transition-colors" aria-label="Edytuj dane">
                        <div class="px-4 py-3 border-b font-medium flex items-center gap-2">
                            <x-filament::icon icon="heroicon-o-calendar-days" class="h-5 w-5" style="color: var(--fi-color-success-600, #16a34a)" />
                            <span style="color: var(--fi-color-success-600, #16a34a)">Dołączenie</span>
                        </div>
                        <div class="p-4 space-y-2 text-gray-700">
                            <div class="text-2xl md:text-3xl font-extrabold text-gray-900">{{ $joinedAt }}</div>
                            <div class="text-sm text-gray-500">Data rejestracji</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif