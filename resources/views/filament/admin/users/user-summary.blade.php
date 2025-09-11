@php($record = isset($getRecord) ? $getRecord() : null)
@if ($record)
    @php($groups = $record->groups)
    @php($isActive = $record->is_active ?? false)
    @php($hasAcceptedTerms = $record->terms_accepted_at !== null)
    @php($joinedAt = $record->joined_at ? \Carbon\Carbon::parse($record->joined_at)->format('d.m.Y') : 'Nie podano')
    @php($phone = $record->phone ?: 'Nie podano')

    <div class="rounded-xl border bg-white shadow-sm overflow-hidden">
        <div class="relative px-6 py-7 md:px-8 md:py-8">
            <div class="flex items-start justify-between">
                <div class="">
                    <div class="flex items-center gap-2 text-xs/5 text-gray-500 mb-2">
                        <span>Użytkownik</span>
                        <span>•</span>
                        <span class="text-green-600 font-medium">Dołączył: {{ $joinedAt }}</span>
                    </div>
                    <div class="text-3xl md:text-4xl font-extrabold tracking-tight text-primary-600 dark:text-primary-400 mb-4">{{ $record->name }}</div>
                    
                    <div class="text-base md:text-lg text-gray-600 dark:text-gray-300 flex items-center gap-2 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4 text-gray-400">
                            <path d="M1.5 8.67v8.58a3 3 0 003 3h15a3 3 0 003-3V8.67l-8.928 5.493a3 3 0 01-3.144 0L1.5 8.67z"/>
                            <path d="M22.5 6.908V6.75a3 3 0 00-3-3h-15a3 3 0 00-3 3v.158l9.714 5.978a1.5 1.5 0 001.572 0L22.5 6.908z"/>
                        </svg>
                        {{ $record->email }}
                    </div>
                    <div class="text-base md:text-lg text-gray-600 dark:text-gray-300 flex items-center gap-2 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4 text-gray-400">
                            <path fill-rule="evenodd" d="M1.5 4.5a3 3 0 013-3h1.372c.86 0 1.61.586 1.819 1.42l1.105 4.423a1.875 1.875 0 01-.694 1.955l-1.293.97c-.135.101-.164.249-.126.352a11.285 11.285 0 006.697 6.697c.103.038.25.009.352-.126l.97-1.293a1.875 1.875 0 011.955-.694l4.423 1.105c.834.209 1.42.959 1.42 1.82V19.5a3 3 0 01-3 3h-2.25C8.552 22.5 1.5 15.448 1.5 6.75V4.5z" clip-rule="evenodd"/>
                        </svg>
                        {{ $phone }}
                    </div>
                    
                    <!-- Grupa i kwota pod telefonem -->
                    <div class="flex flex-wrap gap-2">
                        @if($groups->count() > 0)
                            <x-filament::badge color="warning" icon="heroicon-o-user-group" size="sm">
                                Grupy: {{ $groups->pluck('name')->join(', ') }}
                            </x-filament::badge>
                        @else
                            <x-filament::badge color="gray" icon="heroicon-o-user-group" size="sm">
                                Bez grupy
                            </x-filament::badge>
                        @endif
                        <div class="inline-flex items-center gap-2 rounded-full bg-violet-100 px-4 py-2 text-violet-800">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-6 w-6">
                                <path d="M12 7.5a2.25 2.25 0 100 4.5 2.25 2.25 0 000-4.5z"/>
                                <path fill-rule="evenodd" d="M1.5 4.875C1.5 3.839 2.34 3 3.375 3h17.25c1.035 0 1.875.84 1.875 1.875v9.75c0 1.036-.84 1.875-1.875 1.875H3.375A1.875 1.875 0 011.5 16.5v-9.75zM6.75 6.75a.75.75 0 000 1.5V8.25h8.25a.75.75 0 000-1.5H6.75zM6 12.75a.75.75 0 01.75-.75h10.5a.75.75 0 010 1.5H6.75a.75.75 0 01-.75-.75zM6 15a.75.75 0 01.75-.75h10.5a.75.75 0 010 1.5H6.75A.75.75 0 016 15z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-xl font-bold">{{ $record->amount ?? 200 }} zł</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <x-filament::badge :color="$isActive ? 'success' : 'danger'" icon="heroicon-o-user" size="lg">
                        {{ $isActive ? 'Aktywny' : 'Nieaktywny' }}
                    </x-filament::badge>
                    <a href="{{ route('filament.admin.resources.users.edit', ['record' => $record->id, 'edit' => 1]) }}" class="inline-flex items-center gap-1 rounded-md bg-white/90 px-3 py-2 text-sm font-medium text-gray-900 hover:bg-white">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                            <path d="M21.731 2.269a2.625 2.625 0 00-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 000-3.712z"/>
                            <path d="M11.186 6.339a2.25 2.25 0 00-3.182 0L3.28 11.064a2.25 2.25 0 00-.623 1.262l-.6 3.897a.75.75 0 00.852.852l3.897-.6a2.25 2.25 0 001.262-.623l4.724-4.724a2.25 2.25 0 000-3.182l-1.606-1.606z"/>
                        </svg>
                        Edytuj
                    </a>
                </div>
            </div>
        </div>

        <div class="px-6 py-5 md:px-8">
            <div class="flex flex-wrap gap-2">
                <x-filament::badge :color="$hasAcceptedTerms ? 'success' : 'danger'" icon="heroicon-o-document-text">
                    {{ $hasAcceptedTerms ? 'Regulamin zaakceptowany' : 'Brak akceptacji regulaminu' }}
                </x-filament::badge>
            </div>
        </div>
    </div>
@endif