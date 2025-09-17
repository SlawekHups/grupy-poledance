<x-filament-widgets::widget>
    <style>
        /* Kolory zgodne z grupami */
        .calendar-day-colors-poniedziałek {
            background: linear-gradient(to right, #fef2f2, #fee2e2) !important;
            border: 1px solid #fca5a5 !important;
        }
        .calendar-day-colors-wtorek {
            background: linear-gradient(to right, #fff7ed, #fed7aa) !important;
            border: 1px solid #fdba74 !important;
        }
        .calendar-day-colors-środa {
            background: linear-gradient(to right, #fefce8, #fef3c7) !important;
            border: 1px solid #fde68a !important;
        }
        .calendar-day-colors-czwartek {
            background: linear-gradient(to right, #f0fdf4, #dcfce7) !important;
            border: 1px solid #bbf7d0 !important;
        }
        .calendar-day-colors-piątek {
            background: linear-gradient(to right, #ecfeff, #cffafe) !important;
            border: 1px solid #67e8f9 !important;
        }
        .calendar-day-colors-sobota {
            background: linear-gradient(to right, #faf5ff, #f3e8ff) !important;
            border: 1px solid #e9d5ff !important;
        }
        .calendar-day-colors-niedziela {
            background: linear-gradient(to right, #fdf2f8, #fce7f3) !important;
            border: 1px solid #f9a8d4 !important;
        }
        
        .calendar-day-colors:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
        }
        
        .calendar-day-text-poniedziałek { color: #dc2626 !important; }
        .calendar-day-text-wtorek { color: #ea580c !important; }
        .calendar-day-text-środa { color: #ca8a04 !important; }
        .calendar-day-text-czwartek { color: #16a34a !important; }
        .calendar-day-text-piątek { color: #0891b2 !important; }
        .calendar-day-text-sobota { color: #7c3aed !important; }
        .calendar-day-text-niedziela { color: #db2777 !important; }
        
        .calendar-info-buttons-poniedziałek { background-color: #fef2f2 !important; }
        .calendar-info-buttons-wtorek { background-color: #fff7ed !important; }
        .calendar-info-buttons-środa { background-color: #fefce8 !important; }
        .calendar-info-buttons-czwartek { background-color: #f0fdf4 !important; }
        .calendar-info-buttons-piątek { background-color: #ecfeff !important; }
        .calendar-info-buttons-sobota { background-color: #faf5ff !important; }
        .calendar-info-buttons-niedziela { background-color: #fdf2f8 !important; }
        
        .calendar-info-buttons-poniedziałek:hover { background-color: #fee2e2 !important; }
        .calendar-info-buttons-wtorek:hover { background-color: #fed7aa !important; }
        .calendar-info-buttons-środa:hover { background-color: #fef3c7 !important; }
        .calendar-info-buttons-czwartek:hover { background-color: #dcfce7 !important; }
        .calendar-info-buttons-piątek:hover { background-color: #cffafe !important; }
        .calendar-info-buttons-sobota:hover { background-color: #f3e8ff !important; }
        .calendar-info-buttons-niedziela:hover { background-color: #fce7f3 !important; }
        
        .calendar-info-text-poniedziałek { color: #dc2626 !important; }
        .calendar-info-text-wtorek { color: #ea580c !important; }
        .calendar-info-text-środa { color: #ca8a04 !important; }
        .calendar-info-text-czwartek { color: #16a34a !important; }
        .calendar-info-text-piątek { color: #0891b2 !important; }
        .calendar-info-text-sobota { color: #7c3aed !important; }
        .calendar-info-text-niedziela { color: #db2777 !important; }
        
        .calendar-main-circle-poniedziałek { background: linear-gradient(to bottom right, #ef4444, #dc2626) !important; }
        .calendar-main-circle-wtorek { background: linear-gradient(to bottom right, #f97316, #ea580c) !important; }
        .calendar-main-circle-środa { background: linear-gradient(to bottom right, #eab308, #ca8a04) !important; }
        .calendar-main-circle-czwartek { background: linear-gradient(to bottom right, #22c55e, #16a34a) !important; }
        .calendar-main-circle-piątek { background: linear-gradient(to bottom right, #06b6d4, #0891b2) !important; }
        .calendar-main-circle-sobota { background: linear-gradient(to bottom right, #8b5cf6, #7c3aed) !important; }
        .calendar-main-circle-niedziela { background: linear-gradient(to bottom right, #ec4899, #db2777) !important; }
    </style>
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

        <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-xl p-4 sm:p-6 border border-blue-200 shadow-sm">
            <!-- Główna karta kalendarza -->
            <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 border border-gray-200">
                <!-- Nagłówek z dniem tygodnia -->
                <div class="text-center mb-4">
                    <h3 class="text-xl sm:text-2xl font-bold text-gray-800 mb-1">
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
                        $dayColors = $viewData['dayColors'];
                        $bgColor = $viewData['isToday'] ? 'from-green-500 to-green-600' : 
                                   ($viewData['isPast'] ? 'from-gray-500 to-gray-600' : $dayColors['primary']);
                    @endphp
                    @php
                        $currentDay = strtolower($this->getViewData()['dayOfWeek']);
                    @endphp
                    <div class="inline-flex items-center justify-center w-20 h-20 sm:w-24 sm:h-24 rounded-full shadow-lg calendar-main-circle-{{ $currentDay }}">
                        <span class="text-2xl sm:text-3xl font-bold text-white">
                            {{ $this->getViewData()['dayOfMonth'] }}
                        </span>
                    </div>
                </div>

                <!-- Informacje dodatkowe -->
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 sm:gap-3 text-center">
                    @php
                        $dayColors = $this->getViewData()['dayColors'];
                    @endphp
                    @php
                        $currentDay = strtolower($this->getViewData()['dayOfWeek']);
                    @endphp
                    <a 
                        href="{{ route('filament.admin.resources.users.index') }}"
                        class="block rounded-lg p-2 sm:p-3 transition-colors duration-200 cursor-pointer calendar-info-buttons-{{ $currentDay }}"
                    >
                        <div class="text-xs text-gray-500 mb-1">Wszyscy użytkownicy</div>
                        <div class="text-base sm:text-lg font-semibold calendar-info-text-{{ $currentDay }}">
                            {{ $this->getViewData()['totalUsers'] }}
                        </div>
                    </a>
                    <a 
                        href="{{ route('filament.admin.resources.groups.index') }}"
                        class="block rounded-lg p-2 sm:p-3 transition-colors duration-200 cursor-pointer calendar-info-buttons-{{ $currentDay }}"
                    >
                        <div class="text-xs text-gray-500 mb-1">Grupy</div>
                        <div class="text-base sm:text-lg font-semibold calendar-info-text-{{ $currentDay }}">
                            {{ $this->getViewData()['totalGroups'] }}
                        </div>
                    </a>
                    <a 
                        href="{{ route('filament.admin.resources.attendances.index') }}"
                        class="block rounded-lg p-2 sm:p-3 transition-colors duration-200 cursor-pointer calendar-info-buttons-{{ $currentDay }}"
                    >
                        <div class="text-xs text-gray-500 mb-1">Obecność</div>
                        <div class="text-base sm:text-lg font-semibold calendar-info-text-{{ $currentDay }}">
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
                        class="text-center hover:bg-gray-50 rounded-lg p-1 sm:p-2 transition-colors duration-200 cursor-pointer flex-1"
                    >
                        <div class="text-xs text-gray-500 mb-1">
                            {{ $prevDate->locale('pl')->dayName }}
                        </div>
                        <div class="text-base sm:text-lg font-semibold text-gray-600">
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
                            class="text-center hover:bg-green-50 rounded-lg p-1 sm:p-2 transition-colors duration-200 cursor-pointer flex-1"
                        >
                            <div class="text-xs text-green-600 mb-1 font-medium">
                                {{ $today->locale('pl')->dayName }}
                            </div>
                            <div class="text-base sm:text-lg font-bold text-green-700">
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
                        class="text-center hover:bg-gray-50 rounded-lg p-1 sm:p-2 transition-colors duration-200 cursor-pointer flex-1"
                    >
                        <div class="text-xs text-gray-500 mb-1">
                            {{ $nextDate->locale('pl')->dayName }}
                        </div>
                        <div class="text-base sm:text-lg font-semibold text-gray-600">
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
                <div class="mt-4 bg-white rounded-lg shadow-sm p-4 sm:p-6 border border-gray-200">
                    <h4 class="text-sm font-medium text-gray-700 mb-3 flex items-center gap-2">
                        <x-heroicon-o-clock class="w-4 h-4" />
                        Zajęcia w dniu {{ ucfirst($this->getViewData()['dayOfWeek']) }}
                    </h4>
                    
                    <div class="grid grid-cols-1 gap-3 sm:gap-4">
                        @foreach($this->getViewData()['dayGroups'] as $hour => $groups)
                            @php
                                $group = $groups[0]; // Weź pierwszą grupę z tej godziny
                            @endphp
                            @php
                                $dayColors = $this->getViewData()['dayColors'];
                                // Mapuj kolory na wartości hex
                                $colorMap = [
                                    'poniedziałek' => ['bg' => '#eff6ff', 'hover' => '#dbeafe', 'text' => '#1e40af', 'border' => '#bfdbfe'],
                                    'wtorek' => ['bg' => '#f0fdf4', 'hover' => '#dcfce7', 'text' => '#166534', 'border' => '#bbf7d0'],
                                    'środa' => ['bg' => '#faf5ff', 'hover' => '#f3e8ff', 'text' => '#6b21a8', 'border' => '#e9d5ff'],
                                    'czwartek' => ['bg' => '#fff7ed', 'hover' => '#fed7aa', 'text' => '#c2410c', 'border' => '#fdba74'],
                                    'piątek' => ['bg' => '#fef2f2', 'hover' => '#fecaca', 'text' => '#dc2626', 'border' => '#fca5a5'],
                                    'sobota' => ['bg' => '#fdf2f8', 'hover' => '#fce7f3', 'text' => '#be185d', 'border' => '#f9a8d4'],
                                    'niedziela' => ['bg' => '#fefce8', 'hover' => '#fef3c7', 'text' => '#a16207', 'border' => '#fde68a'],
                                ];
                                $currentDay = strtolower($this->getViewData()['dayOfWeek']);
                                $colors = $colorMap[$currentDay] ?? $colorMap['środa'];
                            @endphp
                            @php
                                $currentDay = strtolower($this->getViewData()['dayOfWeek']);
                            @endphp
                            <a 
                                href="{{ route('filament.admin.resources.groups.edit', ['record' => $group->id]) }}"
                                class="block p-3 sm:p-4 hover:shadow-md rounded-lg transition-all duration-200 group w-full calendar-day-colors-{{ $currentDay }}"
                                style="min-height: 80px; display: flex; align-items: center; justify-content: center;"
                            >
                                <div class="text-center">
                                    <div class="text-base sm:text-lg font-bold group-hover:opacity-80 calendar-day-text-{{ $currentDay }}">
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
                @php
                    $dayColors = $this->getViewData()['dayColors'];
                @endphp
                @php
                    $currentDay = strtolower($this->getViewData()['dayOfWeek']);
                @endphp
                <a 
                    href="{{ route('filament.admin.pages.attendance-group-page') }}"
                    class="block rounded-lg p-2 sm:p-3 transition-colors duration-200 cursor-pointer text-center calendar-info-buttons-{{ $currentDay }}"
                >
                    <div class="text-xs text-gray-500 mb-1">Obecność grupy</div>
                    <div class="text-base sm:text-lg font-semibold calendar-info-text-{{ $currentDay }}">
                        {{ ucfirst($this->getViewData()['dayOfWeek']) }}
                    </div>
                </a>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
