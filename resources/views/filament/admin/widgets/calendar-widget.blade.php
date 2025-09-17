<x-filament-widgets::widget>
    <style>
        /* WYMUSZONE STYLE - nadpisują wszystkie inne */
        .calendar-widget * {
            color: inherit !important;
        }
        
        /* Wymuszone gradienty dla kart */
        .calendar-card-blue { 
            background: linear-gradient(135deg, #3b82f6, #2563eb) !important; 
            color: white !important;
        }
        .calendar-card-blue * { color: white !important; }
        
        .calendar-card-emerald { 
            background: linear-gradient(135deg, #10b981, #059669) !important; 
            color: white !important;
        }
        .calendar-card-emerald * { color: white !important; }
        
        .calendar-card-purple { 
            background: linear-gradient(135deg, #8b5cf6, #7c3aed) !important; 
            color: white !important;
        }
        .calendar-card-purple * { color: white !important; }
        
        .calendar-card-orange { 
            background: linear-gradient(135deg, #f59e0b, #d97706) !important; 
            color: white !important;
        }
        .calendar-card-orange * { color: white !important; }
        
        .calendar-card-indigo { 
            background: linear-gradient(135deg, #6366f1, #4f46e5) !important; 
            color: white !important;
        }
        .calendar-card-indigo * { color: white !important; }
        
        .calendar-card-teal { 
            background: linear-gradient(135deg, #14b8a6, #0d9488) !important; 
            color: white !important;
        }
        .calendar-card-teal * { color: white !important; }
        
        /* Kolory dla godzin - każda godzina ma swój kolor */
        .calendar-hour-16 { background: linear-gradient(135deg, #3b82f6, #2563eb) !important; color: white !important; }
        .calendar-hour-17 { background: linear-gradient(135deg, #8b5cf6, #7c3aed) !important; color: white !important; }
        .calendar-hour-18 { background: linear-gradient(135deg, #ec4899, #db2777) !important; color: white !important; }
        .calendar-hour-19 { background: linear-gradient(135deg, #ef4444, #dc2626) !important; color: white !important; }
        .calendar-hour-20 { background: linear-gradient(135deg, #f59e0b, #d97706) !important; color: white !important; }
        .calendar-hour-21 { background: linear-gradient(135deg, #eab308, #ca8a04) !important; color: white !important; }
        .calendar-hour-22 { background: linear-gradient(135deg, #22c55e, #16a34a) !important; color: white !important; }
        .calendar-hour-23 { background: linear-gradient(135deg, #06b6d4, #0891b2) !important; color: white !important; }
        .calendar-hour-00 { background: linear-gradient(135deg, #6366f1, #4f46e5) !important; color: white !important; }
        .calendar-hour-01 { background: linear-gradient(135deg, #14b8a6, #0d9488) !important; color: white !important; }
        .calendar-hour-02 { background: linear-gradient(135deg, #84cc16, #65a30d) !important; color: white !important; }
        .calendar-hour-03 { background: linear-gradient(135deg, #f97316, #ea580c) !important; color: white !important; }
        .calendar-hour-04 { background: linear-gradient(135deg, #f43f5e, #e11d48) !important; color: white !important; }
        .calendar-hour-05 { background: linear-gradient(135deg, #a855f7, #9333ea) !important; color: white !important; }
        .calendar-hour-06 { background: linear-gradient(135deg, #0ea5e9, #0284c7) !important; color: white !important; }
        .calendar-hour-07 { background: linear-gradient(135deg, #10b981, #059669) !important; color: white !important; }
        .calendar-hour-08 { background: linear-gradient(135deg, #eab308, #ca8a04) !important; color: white !important; }
        .calendar-hour-09 { background: linear-gradient(135deg, #f59e0b, #d97706) !important; color: white !important; }
        .calendar-hour-10 { background: linear-gradient(135deg, #ef4444, #dc2626) !important; color: white !important; }
        .calendar-hour-11 { background: linear-gradient(135deg, #ec4899, #db2777) !important; color: white !important; }
        .calendar-hour-12 { background: linear-gradient(135deg, #8b5cf6, #7c3aed) !important; color: white !important; }
        .calendar-hour-13 { background: linear-gradient(135deg, #3b82f6, #2563eb) !important; color: white !important; }
        .calendar-hour-14 { background: linear-gradient(135deg, #06b6d4, #0891b2) !important; color: white !important; }
        .calendar-hour-15 { background: linear-gradient(135deg, #22c55e, #16a34a) !important; color: white !important; }
        
        /* Wymuszone kolory tekstu dla wszystkich godzin */
        .calendar-hour-16 *, .calendar-hour-17 *, .calendar-hour-18 *, .calendar-hour-19 *, 
        .calendar-hour-20 *, .calendar-hour-21 *, .calendar-hour-22 *, .calendar-hour-23 *, 
        .calendar-hour-00 *, .calendar-hour-01 *, .calendar-hour-02 *, .calendar-hour-03 *, 
        .calendar-hour-04 *, .calendar-hour-05 *, .calendar-hour-06 *, .calendar-hour-07 *, 
        .calendar-hour-08 *, .calendar-hour-09 *, .calendar-hour-10 *, .calendar-hour-11 *, 
        .calendar-hour-12 *, .calendar-hour-13 *, .calendar-hour-14 *, .calendar-hour-15 * { 
            color: white !important; 
        }
        
        /* Wymuszone cienie */
        .calendar-shadow {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
        }
        
        .calendar-shadow-hover:hover {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
            transform: scale(1.02) !important;
        }
        
        /* Wymuszone przezroczystości */
        .calendar-backdrop {
            background: rgba(255, 255, 255, 0.2) !important;
            backdrop-filter: blur(8px) !important;
        }
        
        /* Wymuszone hover efekty */
        .calendar-hover:hover {
            transform: scale(1.05) !important;
            transition: all 0.3s ease !important;
        }
        
        /* Wymuszone style tekstu */
        .calendar-text-white { color: white !important; }
        .calendar-text-white * { color: white !important; }
        
        /* Wymuszone style dla wszystkich elementów kalendarza */
        .calendar-widget .text-white { color: white !important; }
        .calendar-widget .text-white * { color: white !important; }
        .calendar-widget .text-center { text-align: center !important; }
        .calendar-widget .font-bold { font-weight: 700 !important; }
        .calendar-widget .font-medium { font-weight: 500 !important; }
        .calendar-widget .text-lg { font-size: 1.125rem !important; }
        .calendar-widget .text-sm { font-size: 0.875rem !important; }
        .calendar-widget .text-xs { font-size: 0.75rem !important; }
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

        <!-- Główny layout - kompaktowy grid -->
        <div class="calendar-widget grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @php
                $viewData = $this->getViewData();
                $currentDay = strtolower($viewData['dayOfWeek']);
            @endphp
            
            <!-- Karta 1: Główna data -->
            <div class="calendar-card-blue rounded-2xl calendar-shadow calendar-shadow-hover transition-all duration-300 p-6 calendar-text-white">
                <div class="text-center">
                    <!-- Duża data -->
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full mb-4">
                        <span class="text-2xl font-bold text-white">
                            {{ $viewData['dayOfMonth'] }}
                        </span>
                    </div>
                    
                    <h3 class="text-xl font-bold mb-2">
                        {{ ucfirst($viewData['dayOfWeek']) }}
                    </h3>
                    <p class="text-blue-100 text-sm mb-3">
                        {{ $viewData['monthName'] }} {{ $viewData['year'] }}
                    </p>

                    @if($viewData['isToday'])
                        <div class="inline-flex items-center gap-2 px-3 py-1 bg-green-500 text-white rounded-full text-sm font-medium">
                            <x-heroicon-o-star class="w-4 h-4" />
                            Dzisiaj
                        </div>
                    @elseif($viewData['isPast'])
                        <div class="inline-flex items-center gap-2 px-3 py-1 bg-gray-500 text-white rounded-full text-sm font-medium">
                            <x-heroicon-o-clock class="w-4 h-4" />
                            Przeszłość
                        </div>
                    @else
                        <div class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-500 text-white rounded-full text-sm font-medium">
                            <x-heroicon-o-arrow-trending-up class="w-4 h-4" />
                            Przyszłość
                        </div>
                    @endif
                </div>
            </div>

            <!-- Karta 2: Statystyki użytkowników -->
            <div class="calendar-card-emerald rounded-2xl calendar-shadow calendar-shadow-hover transition-all duration-300 p-6 calendar-text-white">
                <div class="flex items-center justify-between mb-4">
                    <x-heroicon-o-users class="w-8 h-8 text-emerald-100" />
                    <span class="text-emerald-100 text-sm font-medium">Użytkownicy</span>
                </div>
                <div class="text-3xl font-bold mb-2">{{ $viewData['totalUsers'] }}</div>
                <a href="{{ route('filament.admin.resources.users.index') }}" class="text-emerald-100 text-sm hover:text-white transition-colors">
                    Zobacz wszystkich →
                </a>
            </div>

            <!-- Karta 3: Statystyki grup -->
            <div class="calendar-card-purple rounded-2xl calendar-shadow calendar-shadow-hover transition-all duration-300 p-6 calendar-text-white">
                <div class="flex items-center justify-between mb-4">
                    <x-heroicon-o-squares-2x2 class="w-8 h-8 text-purple-100" />
                    <span class="text-purple-100 text-sm font-medium">Grupy</span>
                </div>
                <div class="text-3xl font-bold mb-2">{{ $viewData['totalGroups'] }}</div>
                <a href="{{ route('filament.admin.resources.groups.index') }}" class="text-purple-100 text-sm hover:text-white transition-colors">
                    Zarządzaj grupami →
                </a>
            </div>

            <!-- Karta 4: Obecność -->
            <div class="calendar-card-orange rounded-2xl calendar-shadow calendar-shadow-hover transition-all duration-300 p-6 calendar-text-white">
                <div class="flex items-center justify-between mb-4">
                    <x-heroicon-o-clipboard-document-check class="w-8 h-8 text-orange-100" />
                    <span class="text-orange-100 text-sm font-medium">Obecność</span>
                </div>
                <div class="text-3xl font-bold mb-2">{{ $viewData['selectedDayAttendance'] }}</div>
                <a href="{{ route('filament.admin.resources.attendances.index') }}" class="text-orange-100 text-sm hover:text-white transition-colors">
                    Sprawdź obecność →
                </a>
            </div>

            <!-- Karta 5: Zajęcia w dniu -->
            <div class="md:col-span-2 lg:col-span-3 xl:col-span-4">
                <div class="bg-white rounded-2xl calendar-shadow calendar-shadow-hover transition-all duration-300 p-6">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <x-heroicon-o-clock class="w-5 h-5 text-gray-600" />
                        Zajęcia w dniu {{ ucfirst($viewData['dayOfWeek']) }}
                    </h4>
                    
                    @if(count($viewData['dayGroups']) > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-3">
                            @foreach($viewData['dayGroups'] as $hour => $groups)
                                @php
                                    $group = $groups[0];
                                    $memberCount = $group->members()->where('users.is_active', true)->count();
                                    // Konwertuj godzinę na format 24h bez dwukropka
                                    $hourFormatted = str_replace(':', '', $hour);
                                    $hourClass = 'calendar-hour-' . $hourFormatted;
                                @endphp
                                <a 
                                    href="{{ route('filament.admin.resources.groups.edit', ['record' => $group->id]) }}"
                                    class="group block p-4 rounded-xl transition-all duration-300 calendar-hover {{ $hourClass }}"
                                    style="background: linear-gradient(135deg, 
                                        @if($hourFormatted == '1800') #ec4899, #db2777
                                        @elseif($hourFormatted == '1900') #ef4444, #dc2626
                                        @elseif($hourFormatted == '2000') #f59e0b, #d97706
                                        @elseif($hourFormatted == '2100') #eab308, #ca8a04
                                        @elseif($hourFormatted == '2200') #22c55e, #16a34a
                                        @else #3b82f6, #2563eb
                                        @endif) !important; color: white !important;"
                                >
                                    <div class="text-center text-white">
                                        <div class="text-lg font-bold mb-1">{{ $hour }}</div>
                                        <div class="text-sm font-medium mb-2">{{ $group->name }}</div>
                                        <div class="text-xs bg-white/20 backdrop-blur-sm px-2 py-1 rounded-full inline-block">
                                            {{ $memberCount }}/{{ $group->max_size }}
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <x-heroicon-o-x-circle class="w-12 h-12 mx-auto mb-4 text-gray-400" />
                            <p class="text-lg text-gray-600 font-medium">Brak zajęć w dniu {{ ucfirst($viewData['dayOfWeek']) }}</p>
                            <p class="text-sm text-gray-500 mt-2">Sprawdź inne dni lub dodaj nowe grupy</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Karta 6: Nawigacja -->
            <div class="calendar-card-indigo rounded-2xl calendar-shadow calendar-shadow-hover transition-all duration-300 p-6 calendar-text-white">
                <h5 class="text-lg font-semibold mb-4 flex items-center gap-2">
                    <x-heroicon-o-arrows-right-left class="w-5 h-5" />
                    Nawigacja
                </h5>
                <div class="space-y-3">
                    @php
                        $currentDate = $viewData['currentDate'];
                        $today = \Carbon\Carbon::now();
                    @endphp
                    
                    <!-- Poprzedni dzień -->
                    @php
                        $prevDate = $currentDate->copy()->subDay();
                        $prevDateFormatted = $prevDate->format('Y-m-d');
                    @endphp
                    <button 
                        wire:click="$set('selectedDate', '{{ $prevDateFormatted }}')"
                        class="w-full text-left hover:bg-white/10 rounded-lg p-3 transition-all duration-200 group"
                    >
                        <div class="text-xs text-indigo-200 mb-1">Poprzedni</div>
                        <div class="font-semibold">{{ $prevDate->locale('pl')->dayName }} {{ $prevDate->day }}</div>
                    </button>

                    <!-- Dzisiejszy dzień (jeśli nie jest wybrany) -->
                    @if(!$viewData['isToday'])
                        @php
                            $todayFormatted = $today->format('Y-m-d');
                        @endphp
                        <button 
                            wire:click="$set('selectedDate', '{{ $todayFormatted }}')"
                            class="w-full text-left hover:bg-green-500/20 rounded-lg p-3 transition-all duration-200 group"
                        >
                            <div class="text-xs text-green-200 mb-1">Dzisiaj</div>
                            <div class="font-semibold">{{ $today->locale('pl')->dayName }} {{ $today->day }}</div>
                        </button>
                    @endif
                    
                    <!-- Następny dzień -->
                    @php
                        $nextDate = $currentDate->copy()->addDay();
                        $nextDateFormatted = $nextDate->format('Y-m-d');
                    @endphp
                    <button 
                        wire:click="$set('selectedDate', '{{ $nextDateFormatted }}')"
                        class="w-full text-left hover:bg-white/10 rounded-lg p-3 transition-all duration-200 group"
                    >
                        <div class="text-xs text-indigo-200 mb-1">Następny</div>
                        <div class="font-semibold">{{ $nextDate->locale('pl')->dayName }} {{ $nextDate->day }}</div>
                    </button>
                </div>
            </div>

            <!-- Karta 7: Obecność grupy -->
            <div class="calendar-card-teal rounded-2xl calendar-shadow calendar-shadow-hover transition-all duration-300 p-6 calendar-text-white">
                <div class="text-center">
                    <x-heroicon-o-users class="w-8 h-8 text-teal-100 mx-auto mb-4" />
                    <h5 class="text-lg font-semibold mb-2">Obecność grupy</h5>
                    <div class="text-2xl font-bold mb-3">{{ ucfirst($viewData['dayOfWeek']) }}</div>
                    <a 
                        href="{{ route('filament.admin.pages.attendance-group-page') }}"
                        class="inline-block bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200"
                    >
                        Sprawdź obecność
                    </a>
                </div>
            </div>
        </div>


    </x-filament::section>
</x-filament-widgets::widget>
