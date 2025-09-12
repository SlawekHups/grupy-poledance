<x-filament::page>
    <!-- Widget z kalendarzem tygodniowym -->
    <div class="mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <!-- Nagłówek z miesiącem, rokiem i "dziś" -->
            <div class="text-center mb-4">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    {{ \Carbon\Carbon::parse($currentWeekStart)->translatedFormat('F Y') }}
                </h2>
                <div class="flex items-center justify-center gap-4 mt-2">
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        Dziś: {{ \Carbon\Carbon::now()->translatedFormat('d.m.Y') }}
                    </span>
                    <button type="button" wire:click="selectDate('{{ \Carbon\Carbon::now()->toDateString() }}')"
                        class="px-3 py-1 text-xs bg-green-100 hover:bg-green-200 dark:bg-green-900 dark:hover:bg-green-800 text-green-700 dark:text-green-300 rounded-lg transition-colors">
                        Przejdź do dziś
                    </button>
                </div>
            </div>
            
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
                    <div class="text-xs font-medium opacity-60">{{ \Carbon\Carbon::parse($day['date'])->translatedFormat('M') }}</div>
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

        <!-- Desktop layout - Kalendarz grup -->
        <div class="hidden md:block mb-6">
            <label class="block text-sm !font-semibold text-gray-700 dark:text-gray-200 mb-2">
                <x-heroicon-o-user-group class="inline w-4 h-4 mr-1" />
                Wybierz grupę:
            </label>
            <div class="text-xs text-gray-500 mb-4">
                💡 Grupa jest automatycznie wybierana na podstawie dnia tygodnia. Jeśli nie ma grupy dla danego dnia, wybiera pierwszą grupę z poniedziałku.
            </div>
            
            @php
                $currentGroups = $this->getCurrentGroups();
                $nav = $this->getGroupNavigation();
            @endphp
            
            <!-- Nawigacja grup -->
            <div class="flex items-center justify-between mb-4">
                <button type="button" wire:click="previousGroups" 
                        class="inline-flex items-center justify-center w-8 h-8 rounded-full border border-gray-300 bg-white text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition-colors {{ !$nav['has_previous'] ? 'opacity-50 cursor-not-allowed' : '' }}"
                        {{ !$nav['has_previous'] ? 'disabled' : '' }}>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
                
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Strona {{ $nav['current_page'] + 1 }} z {{ $nav['max_pages'] + 1 }}
                </div>
                
                <button type="button" wire:click="nextGroups"
                        class="inline-flex items-center justify-center w-8 h-8 rounded-full border border-gray-300 bg-white text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition-colors {{ !$nav['has_next'] ? 'opacity-50 cursor-not-allowed' : '' }}"
                        {{ !$nav['has_next'] ? 'disabled' : '' }}>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Grupy w stylu kalendarza -->
            <div class="grid grid-cols-7 gap-2">
                @foreach($currentGroups as $group)
                    @php
                        $membersCount = $group->members()->count();
                        $maxSize = $group->max_size;
                        $freeSpots = max(0, $maxSize - $membersCount);
                        $isSelected = $group_id == $group->id;
                        
                        // Kolor statusu
                        $statusColor = match($group->status) {
                            'active' => 'green',
                            'full' => 'yellow', 
                            'inactive' => 'red',
                            default => 'gray'
                        };
                    @endphp
                    <button type="button" 
                        wire:click="selectGroup({{ $group->id }})"
                        @if($isSelected)
                            style="background-color: #10b981; color: white; border-color: #10b981; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);"
                            class="p-3 rounded-lg border transition-all duration-200 hover:shadow-md"
                        @else
                            class="p-3 rounded-lg border transition-all duration-200 hover:shadow-md bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600 {{ $group->status === 'inactive' ? 'opacity-60' : '' }}"
                        @endif>
                        
                        <!-- Status indicator -->
                        <div class="flex justify-between items-start mb-1">
                            <div class="w-2 h-2 rounded-full 
                                @if($statusColor === 'green') bg-green-500
                                @elseif($statusColor === 'yellow') bg-yellow-500
                                @elseif($statusColor === 'red') bg-red-500
                                @else bg-gray-500
                                @endif"></div>
                            @if($isSelected)
                                <x-heroicon-s-check-circle class="w-4 h-4 text-white" />
                            @endif
                        </div>
                        
                        <!-- Nazwa grupy -->
                        <div class="text-xs font-medium opacity-90 truncate mb-1">{{ $group->name }}</div>
                        
                        <!-- Liczba uczestników -->
                        <div class="text-xs opacity-75 mb-1">
                            <span class="inline-flex items-center">
                                <x-heroicon-o-users class="w-3 h-3 mr-1" />
                                {{ $membersCount }}/{{ $maxSize }}
                            </span>
                        </div>
                        
                        <!-- Wolne miejsca -->
                        @if($freeSpots > 0 && $group->status === 'active')
                            <div class="text-xs font-medium opacity-90">
                                {{ $freeSpots }} wolnych
                            </div>
                        @elseif($group->status === 'full')
                            <div class="text-xs font-medium opacity-90">
                                Pełna
                            </div>
                        @elseif($group->status === 'inactive')
                            <div class="text-xs font-medium opacity-90">
                                Nieaktywna
                            </div>
                        @endif
                    </button>
                @endforeach
                
                <!-- Puste miejsca jeśli mniej niż 7 grup -->
                @for($i = count($currentGroups); $i < 7; $i++)
                    <div class="p-3 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 opacity-50"></div>
                @endfor
            </div>
        </div>

        <!-- Mobile layout - Ulepszony dropdown -->
        <div class="md:hidden mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <label class="block text-sm !font-semibold text-gray-700 dark:text-gray-200 mb-2">
                    <x-heroicon-o-user-group class="inline w-4 h-4 mr-1" />
                    Wybierz grupę:
                </label>
                
                <!-- Informacja o automatycznym wybieraniu -->
                <div class="text-xs text-gray-500 mb-4 p-2 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                    💡 Grupa jest automatycznie wybierana na podstawie dnia tygodnia. Jeśli nie ma grupy dla danego dnia, wybiera pierwszą grupę z poniedziałku.
                </div>
                
                <!-- Dropdown z grupami -->
                <select wire:model="group_id"
                        class="filament-forms-select w-full rounded-lg border-gray-300 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-600 font-semibold text-sm">
                        <option value="">-- Wybierz grupę --</option>
                        @foreach(\App\Models\Group::orderBy('name')->get() as $group)
                        @php
                            $membersCount = $group->members()->count();
                            $maxSize = $group->max_size;
                            $freeSpots = max(0, $maxSize - $membersCount);
                        @endphp
                        <option value="{{ $group->id }}" 
                                @if($group->status === 'inactive') disabled @endif>
                            {{ $group->name }} 
                            @if($group->status === 'full') (Pełna - {{ $membersCount }}/{{ $maxSize }})
                            @elseif($group->status === 'inactive') (Nieaktywna)
                            @else ({{ $membersCount }}/{{ $maxSize }} - {{ $freeSpots }} wolnych)
                            @endif
                        </option>
                        @endforeach
                    </select>
                    
                <!-- Wyświetlanie wybranej grupy -->
                @if($group_id)
                    @php
                        $selectedGroup = \App\Models\Group::find($group_id);
                    @endphp
                    @if($selectedGroup)
                        <div class="mt-3 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                            <div class="flex items-center gap-2">
                                <x-heroicon-s-check-circle class="w-4 h-4 text-green-600 dark:text-green-400" />
                                <span class="text-sm font-medium text-green-800 dark:text-green-200">
                                    Wybrana grupa: {{ $selectedGroup->name }}
                                </span>
                            </div>
                            <div class="text-xs text-green-700 dark:text-green-300 mt-1">
                                @php
                                    $membersCount = $selectedGroup->members()->count();
                                    $maxSize = $selectedGroup->max_size;
                                    $freeSpots = max(0, $maxSize - $membersCount);
                                @endphp
                                @if($selectedGroup->status === 'full')
                                    Pełna ({{ $membersCount }}/{{ $maxSize }})
                                @elseif($selectedGroup->status === 'inactive')
                                    Nieaktywna
                                @else
                                    {{ $membersCount }}/{{ $maxSize }} uczestników ({{ $freeSpots }} wolnych miejsc)
                                @endif
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>

        @if(!empty($users))
        @php
            $stats = $this->getAttendanceStats();
        @endphp
        
        <!-- Opis i odstęp -->
        <div class="mb-8">
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <x-heroicon-o-information-circle class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 mr-3 flex-shrink-0" />
                    <div class="text-sm text-blue-800 dark:text-blue-200">
                        <p class="font-medium mb-1">💡 Jak zaznaczać obecność:</p>
                        <ul class="space-y-1 text-xs">
                            <li>• <strong>Przełącznik pomarańczowy</strong> - kliknij aby zmienić status obecności</li>
                            <li>• <strong>Zaznacz wszystkich</strong> - zaznacza wszystkich użytkowników jako obecnych</li>
                            <li>• <strong>Odznacz wszystkich</strong> - odznacza wszystkich użytkowników</li>
                            <li>• <strong>Odwróć zaznaczenie</strong> - odwraca obecny stan zaznaczenia</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Statystyki obecności -->
        <div class="mb-8 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6"
             wire:key="attendance-stats-{{ md5(serialize($attendances)) }}"
             wire:poll.1s="getAttendanceStats">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <!-- Statystyki -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 flex-1">
                    <!-- Obecni -->
                    <div class="text-center rounded-xl p-4 border-2" 
                         style="background-color: #f0fdf4; border-color: #22c55e;">
                        <div class="text-4xl font-black mb-2" style="color: #16a34a;" data-present="{{ $stats['present'] }}">
                            {{ $stats['present'] }}
                        </div>
                        <div class="text-sm font-semibold uppercase tracking-wide" style="color: #15803d;">
                            <x-heroicon-s-check-circle class="inline w-4 h-4 mr-1" />
                            Obecni
                        </div>
                    </div>
                    
                    <!-- Nieobecni -->
                    <div class="text-center rounded-xl p-4 border-2" 
                         style="background-color: #fef2f2; border-color: #ef4444;">
                        <div class="text-4xl font-black mb-2" style="color: #dc2626;" data-absent="{{ $stats['absent'] }}">
                            {{ $stats['absent'] }}
                        </div>
                        <div class="text-sm font-semibold uppercase tracking-wide" style="color: #b91c1c;">
                            <x-heroicon-s-x-circle class="inline w-4 h-4 mr-1" />
                            Nieobecni
                        </div>
                    </div>
                    
                    <!-- Frekwencja -->
                    <div class="text-center rounded-xl p-4 border-2" 
                         style="background-color: #eff6ff; border-color: #3b82f6;">
                        <div class="text-4xl font-black mb-2" style="color: #2563eb;">
                            {{ number_format($stats['percentage'], 1) }}%
                        </div>
                        <div class="text-sm font-semibold uppercase tracking-wide" style="color: #1d4ed8;">
                            <x-heroicon-s-chart-pie class="inline w-4 h-4 mr-1" />
                            Frekwencja
                        </div>
                    </div>
                </div>
                
            </div>
            
            <!-- Progress bar -->
            <div class="mt-6">
                <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400 mb-2">
                    <span class="font-medium">Postęp frekwencji</span>
                    <span class="font-bold text-lg">{{ number_format($stats['percentage'], 1) }}%</span>
                </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4 shadow-inner">
                            <div class="h-4 rounded-full transition-all duration-500 ease-out shadow-sm" 
                                 style="width: {{ $stats['percentage'] }}%; 
                                        @if($stats['percentage'] >= 75)
                                            background-color: #22c55e;
                                        @elseif($stats['percentage'] >= 60)
                                            background-color: #eab308;
                                        @elseif($stats['percentage'] >= 30)
                                            background-color: #f59e0b;
                                        @else
                                            background-color: #ef4444;
                                        @endif">
                            </div>
                        </div>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="my-16">
            <!-- Desktop - przyciski w jednej linii -->
            <div class="hidden md:flex flex-wrap gap-3 justify-center">
                <button type="button" wire:click="selectAll"
                    style="background-color: #ea580c !important; color: white !important;"
                    class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium rounded-lg hover:opacity-90 focus:ring-2 focus:ring-orange-500 transition-all duration-200 shadow-sm min-w-[200px]">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Zaznacz wszystkich
                </button>
                <button type="button" wire:click="deselectAll"
                    style="background-color: #dc2626 !important; color: white !important;"
                    class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium rounded-lg hover:opacity-90 focus:ring-2 focus:ring-red-500 transition-all duration-200 shadow-sm min-w-[200px]">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Odznacz wszystkich
                </button>
                <button type="button" wire:click="toggleAll"
                    style="background-color: #6b7280 !important; color: white !important;"
                    class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium rounded-lg hover:opacity-90 focus:ring-2 focus:ring-gray-500 transition-all duration-200 shadow-sm min-w-[200px]">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Odwróć zaznaczenie
                </button>
                <button type="button" wire:click="openExternalUserModal"
                    style="background-color: #9333ea !important; color: white !important;"
                    class="inline-flex items-center justify-center px-6 py-3 text-sm font-medium rounded-lg hover:opacity-90 focus:ring-2 focus:ring-purple-500 transition-all duration-200 shadow-sm min-w-[200px]">
                    <x-heroicon-o-user-plus class="w-4 h-4 mr-2" />
                    Dodaj osobę z poza grupy (odrabianie)
                </button>
            </div>
            
            <!-- Mobile - każdy przycisk w nowej linii -->
            <div class="md:hidden space-y-3">
                <button type="button" wire:click="selectAll"
                    style="background-color: #ea580c !important; color: white !important;"
                    class="w-full flex items-center justify-center px-6 py-3 text-sm font-medium rounded-lg hover:opacity-90 focus:ring-2 focus:ring-orange-500 transition-all duration-200 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Zaznacz wszystkich
                </button>
                <button type="button" wire:click="deselectAll"
                    style="background-color: #dc2626 !important; color: white !important;"
                    class="w-full flex items-center justify-center px-6 py-3 text-sm font-medium rounded-lg hover:opacity-90 focus:ring-2 focus:ring-red-500 transition-all duration-200 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Odznacz wszystkich
                </button>
                <button type="button" wire:click="toggleAll"
                    style="background-color: #6b7280 !important; color: white !important;"
                    class="w-full flex items-center justify-center px-6 py-3 text-sm font-medium rounded-lg hover:opacity-90 focus:ring-2 focus:ring-gray-500 transition-all duration-200 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Odwróć zaznaczenie
                </button>
                <button type="button" wire:click="openExternalUserModal"
                    style="background-color: #9333ea !important; color: white !important;"
                    class="w-full flex items-center justify-center px-6 py-3 text-sm font-medium rounded-lg hover:opacity-90 focus:ring-2 focus:ring-purple-500 transition-all duration-200 shadow-sm">
                    <x-heroicon-o-user-plus class="w-4 h-4 mr-2" />
                    Dodaj osobę z poza grupy (odrabianie)
                </button>
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
                                <div class="text-xs text-gray-500 flex items-center gap-1">
                                    <x-heroicon-o-envelope class="w-3 h-3" />
                                    {{ $user['email'] }}
                                </div>
                                @if($user['phone'])
                                <div class="text-xs text-gray-500 flex items-center gap-1 mt-1">
                                    <x-heroicon-o-phone class="w-3 h-3" />
                                    {{ $user['phone'] }}
                                </div>
                                @endif
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
                    <div class="text-xs text-gray-500 flex items-center gap-1">
                        <x-heroicon-o-envelope class="w-3 h-3" />
                        {{ $user['email'] }}
                    </div>
                    @if($user['phone'])
                    <div class="text-xs text-gray-500 flex items-center gap-1">
                        <x-heroicon-o-phone class="w-3 h-3" />
                        {{ $user['phone'] }}
                    </div>
                    @endif
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

    <!-- Modal do dodawania użytkownika spoza grupy -->
    @if($showExternalUserModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form wire:submit.prevent="addExternalUserAttendance">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-purple-100 dark:bg-purple-900 sm:mx-0 sm:h-10 sm:w-10">
                                <x-heroicon-o-user-plus class="h-6 w-6 text-purple-600 dark:text-purple-400" />
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title">
                                    Dodaj osobę z poza grupy (odrabianie)
                                </h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Wyszukaj użytkownika
                                        </label>
                                        <div class="relative">
                                            <input type="text" 
                                                   wire:model.live="externalUserSearch"
                                                   placeholder="Wpisz nazwę, email lub telefon..."
                                                   class="filament-forms-input w-full rounded-lg border-gray-300 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-600 pl-10">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <x-heroicon-o-magnifying-glass class="h-5 w-5 text-gray-400" />
                                            </div>
                                        </div>
                                        
                                        <!-- Lista wyników wyszukiwania -->
                                        @if($externalUserSearch && strlen($externalUserSearch) >= 2)
                                        <div class="mt-2 max-h-60 overflow-y-auto border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 shadow-lg">
                                            @php
                                                $searchResults = $this->getExternalUsers()->filter(function($user) {
                                                    $search = strtolower($this->externalUserSearch);
                                                    return str_contains(strtolower($user->name), $search) ||
                                                           str_contains(strtolower($user->email), $search) ||
                                                           str_contains(strtolower($user->phone ?? ''), $search);
                                                })->take(10);
                                            @endphp
                                            
                                            @if($searchResults->count() > 0)
                                                @foreach($searchResults as $user)
                                                <button type="button" 
                                                        wire:click="selectExternalUser({{ $user->id }})"
                                                        class="w-full text-left px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-600 last:border-b-0">
                                                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ $user->name }}</div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                                        <span class="flex items-center gap-1">
                                                            <x-heroicon-o-envelope class="w-3 h-3" />
                                                            {{ $user->email }}
                                                        </span>
                                                        @if($user->phone)
                                                        <span class="flex items-center gap-1 mt-1">
                                                            <x-heroicon-o-phone class="w-3 h-3" />
                                                            {{ $user->phone }}
                                                        </span>
                                                        @endif
                                                    </div>
                                                </button>
                                                @endforeach
                                            @else
                                                <div class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                                    Brak wyników wyszukiwania
                                                </div>
                                            @endif
                                        </div>
                                        @endif
                                        
                                        <!-- Wybrany użytkownik -->
                                        @if($externalUserId)
                                            @php
                                                $selectedUser = \App\Models\User::find($externalUserId);
                                            @endphp
                                            @if($selectedUser)
                                            <div class="mt-2 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                                                <div class="flex items-center justify-between">
                                                    <div>
                                                        <div class="font-medium text-green-800 dark:text-green-200">{{ $selectedUser->name }}</div>
                                                        <div class="text-sm text-green-600 dark:text-green-300">
                                                            <span class="flex items-center gap-1">
                                                                <x-heroicon-o-envelope class="w-3 h-3" />
                                                                {{ $selectedUser->email }}
                                                            </span>
                                                            @if($selectedUser->phone)
                                                            <span class="flex items-center gap-1 mt-1">
                                                                <x-heroicon-o-phone class="w-3 h-3" />
                                                                {{ $selectedUser->phone }}
                                                            </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <button type="button" 
                                                            wire:click="clearExternalUser"
                                                            class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-200">
                                                        <x-heroicon-o-x-mark class="w-5 h-5" />
                                                    </button>
                                                </div>
                                            </div>
                                            @endif
                                        @endif
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Notatka
                                        </label>
                                        <input type="text" wire:model="externalUserNote" 
                                               class="filament-forms-input w-full rounded-lg border-gray-300 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-600"
                                               placeholder="Odrabianie">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-purple-600 text-base font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Dodaj obecność
                        </button>
                        <button type="button" wire:click="closeExternalUserModal"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Anuluj
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</x-filament::page>