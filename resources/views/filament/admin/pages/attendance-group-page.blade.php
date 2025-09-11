<x-filament::page>
    <!-- Widget z kalendarzem tygodniowym -->
    <div class="mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    Wybierz dzień tygodnia
                </h3>
                <div class="flex gap-2">
                    <button type="button" wire:click="selectWeek('{{ $this->getWeekNavigation()['previous'] }}')"
                        class="px-3 py-1 text-sm bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 rounded-lg transition-colors">
                        ← Poprzedni
                    </button>
                    <button type="button" wire:click="selectWeek('{{ $this->getWeekNavigation()['current'] }}')"
                        class="px-3 py-1 text-sm bg-blue-100 hover:bg-blue-200 dark:bg-blue-900 dark:hover:bg-blue-800 text-blue-700 dark:text-blue-300 rounded-lg transition-colors">
                        Ten tydzień
                    </button>
                    <button type="button" wire:click="selectWeek('{{ $this->getWeekNavigation()['next'] }}')"
                        class="px-3 py-1 text-sm bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 rounded-lg transition-colors">
                        Następny →
                    </button>
                </div>
            </div>
            
            <div class="grid grid-cols-7 gap-2">
                @foreach($this->getWeekDays() as $day)
                <button type="button" 
                    wire:click="selectDate('{{ $day['date'] }}')"
                    @if($day['is_selected'])
                        style="background-color: #10b981; color: white; border-color: #10b981; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);"
                        class="p-3 rounded-lg border transition-all duration-200 hover:shadow-md"
                    @elseif($day['is_today'])
                        class="p-3 rounded-lg border transition-all duration-200 hover:shadow-md bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 border-blue-300 dark:border-blue-700"
                    @else
                        class="p-3 rounded-lg border transition-all duration-200 hover:shadow-md bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600"
                    @endif>
                    <div class="text-xs font-medium opacity-75">{{ $day['day_name'] }}</div>
                    <div class="text-lg font-bold">{{ $day['day_number'] }}</div>
                </button>
                @endforeach
            </div>
        </div>
    </div>

    <form wire:submit.prevent="saveAttendance">
        <!-- Wyświetlanie wybranej daty -->
        @if($date)
        <div class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
            <div class="flex items-center gap-2">
                <x-filament::icon icon="heroicon-o-calendar-days" class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                <span class="text-blue-800 dark:text-blue-200 font-medium">
                    Wybrana data: {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d.m.Y') }}
                </span>
            </div>
        </div>
        @endif

        <!-- Desktop layout -->
        <div class="hidden md:flex flex-wrap md:flex-nowrap gap-4 items-end mb-6">
            <div class="flex-1 min-w-[160px]">
                <label for="group_id" class="block text-sm !font-semibold text-gray-700 dark:text-gray-200">
                    Grupa:
                </label>
                <select id="group_id" wire:model="group_id"
                    class="filament-forms-select w-full rounded-lg border-gray-300 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-600 font-semibold">
                    <option value="">-- Wybierz grupę --</option>
                    @foreach(\App\Models\Group::orderBy('name')->get() as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2 flex-1 md:flex-none">
                <button type="button" wire:click="loadUsers"
                    class="w-full md:w-auto inline-flex items-center justify-center font-medium rounded-lg outline-none transition focus:ring-2 focus:ring-amber-500"
                    style="background-color:#d97706; color:#fff; border-radius:0.5rem; padding:0.5em 1em; min-width:160px;">
                    Pokaż użytkowników
                </button>
            </div>
        </div>

        <!-- Mobile layout - wszystko w osobnych liniach -->
        <div class="md:hidden flex flex-col gap-4 mb-6">
            <div>
                <label for="group_id_mobile" class="block text-sm !font-semibold text-gray-700 dark:text-gray-200 mb-2">
                    Grupa:
                </label>
                <select id="group_id_mobile" wire:model="group_id"
                    class="filament-forms-select w-full rounded-lg border-gray-300 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-600 font-semibold">
                    <option value="">-- Wybierz grupę --</option>
                    @foreach(\App\Models\Group::orderBy('name')->get() as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="button" wire:click="loadUsers"
                    class="w-full inline-flex items-center justify-center font-medium rounded-lg outline-none transition focus:ring-2 focus:ring-amber-500"
                    style="background-color:#d97706; color:#fff; border-radius:0.5rem; padding:0.5em 1em;">
                    Pokaż użytkowników
                </button>
            </div>
        </div>

        @if(!empty($users))
        @php($stats = $this->getAttendanceStats())
        
        <!-- Statystyki obecności -->
        <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4"
             wire:key="attendance-stats-{{ md5(serialize($attendances)) }}">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <!-- Statystyki -->
                <div class="flex flex-wrap gap-4">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            Obecni: <span class="font-bold text-green-600">{{ $stats['present'] }}</span>
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            Nieobecni: <span class="font-bold text-red-600">{{ $stats['absent'] }}</span>
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            Frekwencja: <span class="font-bold text-blue-600">{{ $stats['percentage'] }}%</span>
                        </span>
                    </div>
                </div>
                
                <!-- Bulk Actions -->
                <div class="flex flex-wrap gap-2">
                    <button type="button" wire:click="selectAll"
                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-orange-600 rounded-lg hover:bg-orange-700 focus:ring-2 focus:ring-orange-500 transition-colors">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Zaznacz wszystkich
                    </button>
                    <button type="button" wire:click="deselectAll"
                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 transition-colors">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Odznacz wszystkich
                    </button>
                    <button type="button" wire:click="toggleAll"
                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 focus:ring-2 focus:ring-gray-500 transition-colors">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Odwróć zaznaczenie
                    </button>
                </div>
            </div>
            
            <!-- Progress bar -->
            <div class="mt-4">
                <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-1">
                    <span>Frekwencja</span>
                    <span>{{ $stats['percentage'] }}%</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div class="bg-gradient-to-r from-red-500 via-yellow-500 to-green-500 h-2 rounded-full transition-all duration-300" 
                         style="width: {{ $stats['percentage'] }}%"></div>
                </div>
            </div>
        </div>
        
        <!-- Tabela desktop, karty mobile -->
        <div class="filament-card p-4 shadow rounded-lg">
            <!-- Desktop tabela -->
            <div class="hidden md:block">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 text-left font-semibold">Użytkownik</th>
                            <th class="px-4 py-2 text-left font-semibold">Obecny</th>
                            <th class="px-4 py-2 text-left font-semibold">Notatka</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td class="px-4 py-2 align-top">
                                <div class="font-semibold">{{ $user['name'] }}</div>
                                <div class="text-xs text-gray-500">{{ $user['email'] }}</div>
                            </td>
                            <td class="px-4 py-2 align-top">
                                <div class="flex items-center gap-3">
                                    <div class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus-visible:ring-2 focus-visible:ring-orange-500 focus-visible:ring-offset-1 {{ ($attendances[$user['id']]['present'] ?? false) ? '' : 'bg-gray-200' }}"
                                         style="{{ ($attendances[$user['id']]['present'] ?? false) ? 'background-color: #ea580c;' : '' }}"
                                         wire:click="toggleAttendance({{ $user['id'] }})"
                                         wire:key="attendance-{{ $user['id'] }}">
                                        <span class="sr-only">Przełącz obecność</span>
                                        <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ ($attendances[$user['id']]['present'] ?? false) ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ $attendances[$user['id']]['present'] ?? false ? 'Obecny' : 'Nieobecny' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-2 align-top">
                                <input type="text" wire:model="attendances.{{ $user['id'] }}.note" placeholder="Notatka"
                                    class="filament-forms-input w-full rounded-lg border-gray-300 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-600" />
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Mobile karty -->
            <div class="md:hidden flex flex-col gap-4">
                @foreach($users as $user)
                <div
                    class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-3 flex flex-col gap-1 shadow">
                    <div class="font-semibold">{{ $user['name'] }}</div>
                    <div class="text-xs text-gray-500">{{ $user['email'] }}</div>
                    <div class="flex flex-col gap-3 mt-3">
                        <div class="flex items-center gap-3">
                            <div class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus-visible:ring-2 focus-visible:ring-orange-500 focus-visible:ring-offset-1 {{ ($attendances[$user['id']]['present'] ?? false) ? '' : 'bg-gray-200' }}"
                                 style="{{ ($attendances[$user['id']]['present'] ?? false) ? 'background-color: #ea580c;' : '' }}"
                                 wire:click="toggleAttendance({{ $user['id'] }})"
                                 wire:key="attendance-mobile-{{ $user['id'] }}">
                                <span class="sr-only">Przełącz obecność</span>
                                <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ ($attendances[$user['id']]['present'] ?? false) ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ $attendances[$user['id']]['present'] ?? false ? 'Obecny' : 'Nieobecny' }}
                            </span>
                        </div>
                        <input type="text" wire:model="attendances.{{ $user['id'] }}.note" placeholder="Notatka"
                            class="filament-forms-input w-full rounded-lg border-gray-300 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-600" />
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        <!-- Przycisk na dole -->
        <div class="flex justify-end mt-6">
            <button type="submit"
                class="w-full md:w-auto inline-flex items-center justify-center font-medium rounded-lg outline-none transition focus:ring-2 focus:ring-green-500"
                style="background-color:#22c55e; color:#fff; border-radius:0.5rem; padding:0.5em 1em; min-width:160px;">
                Zapisz obecność
            </button>
        </div>
        @elseif($group_id)
        <div class="w-full flex mt-12 mb-6">
            <div class="mx-auto w-full md:w-2/3 lg:w-1/2 text-center font-semibold"
                style="color: #dc2626; background-color: #fff1f2; border-left: 4px solid #dc2626; padding: 1.25em; border-radius: 0.75em; box-shadow: 0 2px 8px 0 #0000000a; margin: 20px;">
                Brak użytkowników w wybranej grupie.
            </div>
        </div>
        @endif
    </form>
</x-filament::page>