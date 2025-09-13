@php($record = isset($getRecord) ? $getRecord() : null)
@if ($record)
    @php($groups = $record->groups)
    @php($isActive = $record->is_active ?? false)
    @php($hasAcceptedTerms = $record->terms_accepted_at !== null)
    @php($joinedAt = $record->joined_at ? \Carbon\Carbon::parse($record->joined_at)->format('d.m.Y') : 'Nie podano')
    @php($phone = $record->phone ?: 'Nie podano')
    @php($address = $record->addresses()->first())

    <div class="rounded-xl border bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700">
        <div class="px-4 py-3 border-b font-medium flex items-center gap-2 text-gray-900 dark:text-white border-gray-200 dark:border-gray-700">
            <x-filament::icon icon="heroicon-o-user-circle" class="h-5 w-5 text-gray-900 dark:text-white" />
            Konto użytkownika
        </div>
        <div class="p-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Karta Profil -->
                <a href="{{ route('filament.admin.resources.users.edit', ['record' => $record->id, 'edit' => 1]) }}" class="user-card-link rounded-xl border bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 block hover:!bg-gray-50 dark:hover:!bg-gray-700 shadow-sm hover:shadow-md transition-colors outline-none focus:outline-none focus-visible:outline-none focus:ring-0" aria-label="Edytuj profil">
                    <div class="px-4 py-3 border-b font-medium flex items-center gap-2 text-gray-900 dark:text-white border-gray-200 dark:border-gray-700">
                        <x-filament::icon icon="heroicon-o-user" class="h-5 w-5 text-orange-600 dark:text-orange-500" />
                        <span class="text-orange-600 dark:text-orange-500">Profil</span>
                    </div>
                    <div class="p-4 space-y-2 text-gray-700 dark:text-gray-300">
                        <div class="text-2xl md:text-3xl font-extrabold text-gray-900 dark:text-white">{{ $record->name }}</div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Status</span>
                            <x-filament::badge :color="$isActive ? 'success' : 'danger'">
                                {{ $isActive ? 'Aktywny' : 'Nieaktywny' }}
                            </x-filament::badge>
                        </div>
                    </div>
                </a>

                <!-- Karta Kontakt -->
                <a href="{{ route('filament.admin.resources.users.edit', ['record' => $record->id, 'edit' => 1]) }}" class="user-card-link rounded-xl border bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 block hover:!bg-gray-50 dark:hover:!bg-gray-700 shadow-sm hover:shadow-md transition-colors outline-none focus:outline-none focus-visible:outline-none focus:ring-0" aria-label="Edytuj kontakt">
                    <div class="px-4 py-3 border-b font-medium flex items-center gap-2 text-gray-900 dark:text-white border-gray-200 dark:border-gray-700">
                        <x-filament::icon icon="heroicon-o-envelope" class="h-5 w-5 text-orange-600 dark:text-orange-500" />
                        <span class="text-orange-600 dark:text-orange-500">Kontakt</span>
                    </div>
                    <div class="p-4 space-y-2 text-gray-700 dark:text-gray-300">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Email</span>
                            <span class="font-semibold break-words text-right text-gray-900 dark:text-white">{{ $record->email }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Telefon</span>
                            <span class="font-semibold break-words text-right text-gray-900 dark:text-white">{{ $phone }}</span>
                        </div>
                    </div>
                </a>

                <!-- Karta Adres -->
                <div class="rounded-xl border bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700">
                    @if($address)
                        <div class="user-card cursor-pointer hover:!bg-gray-50 dark:hover:!bg-gray-700 shadow-sm hover:shadow-md transition-colors" 
                             onclick="window.location.href='{{ route('filament.admin.resources.users.edit', ['record' => $record->id, 'edit' => 1, 'activeTab' => 'addresses']) }}'" 
                             aria-label="Przejdź do edycji adresu">
                            <div class="px-4 py-3 border-b font-medium flex items-center gap-2 text-gray-900 dark:text-white border-gray-200 dark:border-gray-700">
                                <x-filament::icon icon="heroicon-o-map-pin" class="h-5 w-5 text-orange-600 dark:text-orange-500" />
                                <span class="text-orange-600 dark:text-orange-500">Adres</span>
                            </div>
                            <div class="p-4 space-y-2 text-gray-700 dark:text-gray-300">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">Ulica</span>
                                    <span class="font-semibold break-words text-right text-gray-900 dark:text-white">{{ $address->street ?? '—' }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">Kod / Miasto</span>
                                    <span class="font-semibold break-words text-right text-gray-900 dark:text-white">{{ $address?->postal_code ? ($address->postal_code.' '.$address->city) : ($address->city ?? '—') }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">Typ</span>
                                    <span class="font-semibold break-words text-right text-gray-900 dark:text-white">{{ $address->type ?? '—' }}</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('filament.admin.resources.users.edit', ['record' => $record->id, 'edit' => 1, 'activeTab' => 'addresses']) }}" class="user-card-link block hover:!bg-gray-50 dark:hover:!bg-gray-700 shadow-sm hover:shadow-md transition-colors" aria-label="Dodaj adres">
                            <div class="px-4 py-3 border-b font-medium flex items-center gap-2 text-gray-900 dark:text-white border-gray-200 dark:border-gray-700">
                                <x-filament::icon icon="heroicon-o-map-pin" class="h-5 w-5 text-orange-600 dark:text-orange-500" />
                                <span class="text-orange-600 dark:text-orange-500">Adres</span>
                            </div>
                            <div class="p-4 space-y-2 text-gray-700 dark:text-gray-300">
                                <div class="text-center text-gray-500 dark:text-gray-400 py-4">
                                    <x-filament::icon icon="heroicon-o-plus" class="h-8 w-8 mx-auto mb-2 text-gray-400 dark:text-gray-500" />
                                    <span>Brak adresu - kliknij aby dodać</span>
                                </div>
                            </div>
                        </a>
                    @endif
                </div>

                <!-- Karta Grupa -->
                <div class="rounded-xl border bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700">
                    <a href="{{ route('filament.admin.resources.users.edit', ['record' => $record->id, 'edit' => 1]) }}" class="user-card-link block hover:!bg-gray-50 dark:hover:!bg-gray-700 shadow-sm hover:shadow-md transition-colors" aria-label="Edytuj grupy">
                        <div class="px-4 py-3 border-b font-medium flex items-center gap-2 text-gray-900 dark:text-white border-gray-200 dark:border-gray-700">
                            <x-filament::icon icon="heroicon-o-user-group" class="h-5 w-5 text-gray-600 dark:text-gray-400" />
                            <span class="text-gray-600 dark:text-gray-400">Grupa</span>
                        </div>
                        <div class="p-4 space-y-2 text-gray-700 dark:text-gray-300">
                            @if($groups->count() > 0)
                                <div class="text-2xl md:text-3xl font-extrabold text-gray-900 dark:text-white">{{ $groups->first()->name }}</div>
                                @if($groups->count() > 1)
                                    <div class="text-sm text-gray-500 dark:text-gray-400">+{{ $groups->count() - 1 }} więcej</div>
                                @endif
                            @else
                                <div class="text-2xl md:text-3xl font-extrabold text-gray-500 dark:text-gray-400">Bez grupy</div>
                            @endif
                        </div>
                    </a>
                </div>

                <!-- Karta Potwierdzenie regulaminu -->
                <div class="rounded-xl border bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700">
                    <a href="{{ route('filament.admin.resources.users.edit', ['record' => $record->id, 'edit' => 1]) }}" class="user-card-link block hover:!bg-gray-50 dark:hover:!bg-gray-700 shadow-sm hover:shadow-md transition-colors" aria-label="Edytuj regulamin">
                        <div class="px-4 py-3 border-b font-medium flex items-center gap-2 text-gray-900 dark:text-white border-gray-200 dark:border-gray-700">
                            <x-filament::icon icon="heroicon-o-document-text" class="h-5 w-5 text-orange-600 dark:text-orange-500" />
                            <span class="text-orange-600 dark:text-orange-500">Regulamin</span>
                        </div>
                        <div class="p-4 space-y-2 text-gray-700 dark:text-gray-300">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Status</span>
                                <x-filament::badge :color="$hasAcceptedTerms ? 'success' : 'danger'">
                                    {{ $hasAcceptedTerms ? 'Zaakceptowany' : 'Nie zaakceptowany' }}
                                </x-filament::badge>
                            </div>
                            @if($hasAcceptedTerms)
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    Data: {{ \Carbon\Carbon::parse($record->terms_accepted_at)->format('d.m.Y') }}
                                </div>
                            @endif
                        </div>
                    </a>
                </div>

                <!-- Karta Dołączenie -->
                <div class="rounded-xl border bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700">
                    <a href="{{ route('filament.admin.resources.users.edit', ['record' => $record->id, 'edit' => 1]) }}" class="user-card-link block hover:!bg-gray-50 dark:hover:!bg-gray-700 shadow-sm hover:shadow-md transition-colors" aria-label="Edytuj dane">
                        <div class="px-4 py-3 border-b font-medium flex items-center gap-2 text-gray-900 dark:text-white border-gray-200 dark:border-gray-700">
                            <x-filament::icon icon="heroicon-o-calendar-days" class="h-5 w-5 text-green-600 dark:text-green-500" />
                            <span class="text-green-600 dark:text-green-500">Dołączenie</span>
                        </div>
                        <div class="p-4 space-y-2 text-gray-700 dark:text-gray-300">
                            <div class="text-2xl md:text-3xl font-extrabold text-gray-900 dark:text-white">{{ $joinedAt }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Data rejestracji</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif

<style>
/* Wymuś kolory hover dla kart użytkownika */
.user-card:hover {
    background-color: #f9fafb !important;
}

.dark .user-card:hover {
    background-color: #374151 !important;
}

.user-card-link:hover {
    background-color: #f9fafb !important;
}

.dark .user-card-link:hover {
    background-color: #374151 !important;
}
</style>