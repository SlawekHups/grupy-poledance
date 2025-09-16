<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center justify-between w-full">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-calendar-days class="w-5 h-5" />
                    Kalendarz
                </div>
                <div class="flex items-center gap-1">
                    <button 
                        wire:click="previousDay" 
                        class="p-1 rounded-md hover:bg-gray-100 transition-colors"
                        title="Poprzedni dzień"
                    >
                        <x-heroicon-s-chevron-left class="w-4 h-4 text-gray-600" />
                    </button>
                    <button 
                        wire:click="goToToday" 
                        class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-md hover:bg-blue-200 transition-colors"
                        title="Dzisiaj"
                    >
                        Dziś
                    </button>
                    <button 
                        wire:click="nextDay" 
                        class="p-1 rounded-md hover:bg-gray-100 transition-colors"
                        title="Następny dzień"
                    >
                        <x-heroicon-s-chevron-right class="w-4 h-4 text-gray-600" />
                    </button>
                </div>
            </div>
        </x-slot>

        <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-xl p-6 border border-blue-200 shadow-sm">
            <!-- Główna karta kalendarza -->
            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                <!-- Nagłówek z dniem tygodnia -->
                <div class="text-center mb-4">
                    <h3 class="text-2xl font-bold text-gray-800 mb-1">
                        {{ ucfirst($this->getViewData()['dayOfWeek']) }}
                    </h3>
                    <p class="text-sm text-gray-600">
                        {{ $this->getViewData()['monthName'] }} {{ $this->getViewData()['year'] }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ $this->getViewData()['formattedDate'] }}
                    </p>
                </div>

                <!-- Duża data -->
                <div class="text-center mb-6">
                    @php
                        $viewData = $this->getViewData();
                        $bgColor = $viewData['isToday'] ? 'from-green-500 to-green-600' : 
                                   ($viewData['isPast'] ? 'from-gray-500 to-gray-600' : 'from-blue-500 to-indigo-600');
                    @endphp
                    <div class="inline-flex items-center justify-center w-24 h-24 bg-gradient-to-br {{ $bgColor }} rounded-full shadow-lg">
                        <span class="text-3xl font-bold text-white">
                            {{ $this->getViewData()['dayOfMonth'] }}
                        </span>
                    </div>
                </div>

                <!-- Informacje dodatkowe -->
                <div class="grid grid-cols-3 gap-3 text-center">
                    <a 
                        href="{{ route('filament.admin.resources.users.index') }}"
                        class="block bg-gray-50 hover:bg-gray-100 rounded-lg p-3 transition-colors duration-200 cursor-pointer"
                    >
                        <div class="text-xs text-gray-500 mb-1">Wszyscy użytkownicy</div>
                        <div class="text-lg font-semibold text-gray-700">
                            {{ $this->getViewData()['totalUsers'] }}
                        </div>
                    </a>
                    <a 
                        href="{{ route('filament.admin.resources.groups.index') }}"
                        class="block bg-gray-50 hover:bg-gray-100 rounded-lg p-3 transition-colors duration-200 cursor-pointer"
                    >
                        <div class="text-xs text-gray-500 mb-1">Grupy</div>
                        <div class="text-lg font-semibold text-gray-700">
                            {{ $this->getViewData()['totalGroups'] }}
                        </div>
                    </a>
                    <a 
                        href="{{ route('filament.admin.resources.attendances.index') }}"
                        class="block bg-gray-50 hover:bg-gray-100 rounded-lg p-3 transition-colors duration-200 cursor-pointer"
                    >
                        <div class="text-xs text-gray-500 mb-1">Obecność</div>
                        <div class="text-lg font-semibold text-gray-700">
                            {{ $this->getViewData()['selectedDayAttendance'] }}
                        </div>
                    </a>
                </div>

                <!-- Status dnia -->
                <div class="mt-4 text-center">
                    @php
                        $viewData = $this->getViewData();
                    @endphp
                    @if($viewData['isToday'])
                        <div class="inline-flex items-center gap-2 px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                            <x-heroicon-o-star class="w-4 h-4" />
                            Dzisiaj
                        </div>
                    @elseif($viewData['isPast'])
                        <div class="inline-flex items-center gap-2 px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm font-medium">
                            <x-heroicon-o-clock class="w-4 h-4" />
                            Przeszłość
                        </div>
                    @else
                        <div class="inline-flex items-center gap-2 px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                            <x-heroicon-o-arrow-trending-up class="w-4 h-4" />
                            Przyszłość
                        </div>
                    @endif
                    
                    @if($viewData['isWeekend'])
                        <div class="mt-2">
                            <div class="inline-flex items-center gap-2 px-3 py-1 bg-orange-100 text-orange-800 rounded-full text-sm font-medium">
                                <x-heroicon-o-sun class="w-4 h-4" />
                                Weekend
                            </div>
                        </div>
                    @else
                        <div class="mt-2">
                            <div class="inline-flex items-center gap-2 px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm font-medium">
                                <x-heroicon-o-briefcase class="w-4 h-4" />
                                Dzień roboczy
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Informacja o wybranej dacie -->
                <div class="mt-4 text-center">
                    <div class="text-xs text-gray-400">
                        Wybrana data: {{ $this->getViewData()['formattedDate'] }}
                    </div>
                </div>
            </div>

            <!-- Mini kalendarz na sąsiednie dni -->
            <div class="mt-4 bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                <h4 class="text-sm font-medium text-gray-700 mb-3">Sąsiednie dni</h4>
                <div class="flex justify-between">
                    @php
                        $currentDate = $this->getViewData()['currentDate'];
                        $today = \Carbon\Carbon::now();
                    @endphp
                    
                    <!-- Poprzedni dzień -->
                    @php
                        $prevDate = $currentDate->copy()->subDay();
                        $prevDateFormatted = $prevDate->format('Y-m-d');
                    @endphp
                    <button 
                        wire:click="$set('selectedDate', '{{ $prevDateFormatted }}')"
                        class="text-center hover:bg-gray-50 rounded-lg p-2 transition-colors duration-200 cursor-pointer flex-1"
                    >
                        <div class="text-xs text-gray-500 mb-1">
                            {{ $prevDate->locale('pl')->dayName }}
                        </div>
                        <div class="text-lg font-semibold text-gray-600">
                            {{ $prevDate->day }}
                        </div>
                        <div class="text-xs text-gray-400">
                            {{ $prevDate->format('M') }}
                        </div>
                    </button>

                    <!-- Dzisiejszy dzień (jeśli nie jest wybrany) -->
                    @if(!$this->getViewData()['isToday'])
                        @php
                            $todayFormatted = $today->format('Y-m-d');
                        @endphp
                        <button 
                            wire:click="$set('selectedDate', '{{ $todayFormatted }}')"
                            class="text-center hover:bg-green-50 rounded-lg p-2 transition-colors duration-200 cursor-pointer flex-1"
                        >
                            <div class="text-xs text-green-600 mb-1 font-medium">
                                {{ $today->locale('pl')->dayName }}
                            </div>
                            <div class="text-lg font-bold text-green-700">
                                {{ $today->day }}
                            </div>
                            <div class="text-xs text-green-500">
                                Dziś
                            </div>
                        </button>
                    @endif

                    <!-- Następny dzień -->
                    @php
                        $nextDate = $currentDate->copy()->addDay();
                        $nextDateFormatted = $nextDate->format('Y-m-d');
                    @endphp
                    <button 
                        wire:click="$set('selectedDate', '{{ $nextDateFormatted }}')"
                        class="text-center hover:bg-gray-50 rounded-lg p-2 transition-colors duration-200 cursor-pointer flex-1"
                    >
                        <div class="text-xs text-gray-500 mb-1">
                            {{ $nextDate->locale('pl')->dayName }}
                        </div>
                        <div class="text-lg font-semibold text-gray-600">
                            {{ $nextDate->day }}
                        </div>
                        <div class="text-xs text-gray-400">
                            {{ $nextDate->format('M') }}
                        </div>
                    </button>
                </div>
            </div>

            <!-- Godziny grup na wybrany dzień -->
            @if(count($this->getViewData()['dayGroups']) > 0)
                <div class="mt-4 bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                    <h4 class="text-sm font-medium text-gray-700 mb-3 flex items-center gap-2">
                        <x-heroicon-o-clock class="w-4 h-4" />
                        Zajęcia w dniu {{ ucfirst($this->getViewData()['dayOfWeek']) }}
                    </h4>
                    
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2">
                        @foreach($this->getViewData()['dayGroups'] as $hour => $groups)
                            @php
                                $group = $groups[0]; // Weź pierwszą grupę z tej godziny
                            @endphp
                            <a 
                                href="{{ route('filament.admin.resources.groups.edit', ['record' => $group->id]) }}"
                                class="block p-3 bg-gradient-to-r from-blue-50 to-indigo-50 hover:from-blue-100 hover:to-indigo-100 rounded-lg border border-blue-200 transition-all duration-200 hover:shadow-md group"
                            >
                                <div class="text-center">
                                    <div class="text-lg font-bold text-blue-700 group-hover:text-blue-800">
                                        {{ $hour }}
                                    </div>
                                    <div class="text-xs text-gray-600 mt-1">
                                        {{ $group->name }}
                                    </div>
                                    @if($group->members()->where('users.is_active', true)->count() > 0)
                                        <div class="text-xs text-green-600 mt-1">
                                            {{ $group->members()->where('users.is_active', true)->count() }}/{{ $group->max_size }} osób
                                        </div>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="mt-4 bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <div class="text-center text-gray-500">
                        <x-heroicon-o-x-circle class="w-8 h-8 mx-auto mb-2 text-gray-400" />
                        <p class="text-sm">Brak zajęć w dniu {{ ucfirst($this->getViewData()['dayOfWeek']) }}</p>
                    </div>
                </div>
            @endif

            <!-- Przycisk Obecność grupy pod zajęciami -->
            <div class="mt-4 bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                <a 
                    href="{{ route('filament.admin.pages.attendance-group-page') }}"
                    class="block bg-gray-50 hover:bg-gray-100 rounded-lg p-3 transition-colors duration-200 cursor-pointer text-center"
                >
                    <div class="text-xs text-gray-500 mb-1">Obecność grupy</div>
                    <div class="text-lg font-semibold text-gray-700">
                        {{ ucfirst($this->getViewData()['dayOfWeek']) }}
                    </div>
                </a>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
