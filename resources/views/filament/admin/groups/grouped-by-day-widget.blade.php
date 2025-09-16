<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-filament::icon icon="heroicon-o-calendar-days" class="h-5 w-5" />
                Grupy według dni tygodnia
            </div>
        </x-slot>

        <div class="space-y-8">
            @foreach($groupedByDay as $dayName => $groups)
                @if(count($groups) > 0)
                    @php
                        $dayColors = [
                            'poniedziałek' => '#ef4444', // Czerwony
                            'wtorek' => '#f97316',       // Pomarańczowy
                            'środa' => '#eab308',        // Żółty
                            'czwartek' => '#22c55e',     // Zielony
                            'piątek' => '#06b6d4',       // Cyjan
                            'sobota' => '#8b5cf6',       // Fioletowy
                            'niedziela' => '#ec4899',    // Różowy
                            'inne' => '#6b7280',         // Szary
                        ];
                        $dayColor = $dayColors[$dayName] ?? '#6b7280';
                        $dayDisplayName = ucfirst($dayName);
                    @endphp

                    <!-- Nagłówek dnia tygodnia -->
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold flex items-center gap-2">
                            <div class="w-4 h-4 rounded-full" style="background-color: {{ $dayColor }};"></div>
                            {{ $dayDisplayName }}
                            <span class="text-sm text-gray-500 font-normal">({{ count($groups) }} grupa{{ count($groups) > 1 ? 'y' : '' }})</span>
                        </h3>
                    </div>

                    <!-- Kafelki dla tego dnia -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                        @foreach($groups as $group)
                            @php
                                // Wyciągnij godziny z nazwy grupy
                                $timeMatch = preg_match('/(\d{1,2}:\d{2})/', $group->name, $matches);
                                $time = $timeMatch ? $matches[1] : '';
                                
                                $memberCount = $group->members_count;
                                $maxSize = (int) ($group->max_size ?? 0);
                                $status = $memberCount >= $maxSize ? 'Pełna' : 'Aktywna';
                                $statusColor = $memberCount >= $maxSize ? '#dc2626' : '#16a34a';
                                
                                $avgAmount = $group->members()->where('users.is_active', true)->avg('amount') ?? 0;
                            @endphp

                            <div class="relative">
                                <a href="{{ route('filament.admin.resources.groups.edit', ['record' => $group]) }}"
                                   class="block rounded-xl border bg-white shadow-sm hover:shadow-lg transition-all duration-200 hover:bg-gray-50 cursor-pointer h-[200px]"
                                   style="border-left: 4px solid {{ $dayColor }};">

                                    <!-- Znaczek dnia tygodnia w prawym górnym rogu -->
                                    <div class="absolute top-3 right-3 z-10">
                                        <div class="px-2 py-1 rounded-full text-xs font-medium text-white shadow-lg" style="background-color: {{ $dayColor }};">
                                            @switch($dayName)
                                                @case('poniedziałek') Pon @break
                                                @case('wtorek') Wt @break
                                                @case('środa') Śr @break
                                                @case('czwartek') Czw @break
                                                @case('piątek') Pt @break
                                                @case('sobota') Sob @break
                                                @case('niedziela') Nd @break
                                                @default {{ ucfirst(substr($dayName, 0, 3)) }} @break
                                            @endswitch
                                        </div>
                                    </div>

                                    <div class="p-4 flex flex-col items-center justify-center gap-3 h-full relative">
                                        <!-- Godziny -->
                                        @if($time)
                                            <div class="text-2xl font-bold" style="color: {{ $dayColor }};">{{ $time }}</div>
                                        @else
                                            <div class="text-lg font-semibold text-gray-700 text-center">{{ $group->name }}</div>
                                        @endif

                                        <!-- Informacje o grupie -->
                                        <div class="flex flex-col items-center gap-2 text-sm">
                                            <div class="flex items-center gap-2">
                                                <div class="flex items-center gap-1">
                                                    <x-filament::icon icon="heroicon-o-users" class="h-4 w-4 text-gray-600" />
                                                    <span class="text-gray-600">{{ $memberCount }}/{{ $maxSize }}</span>
                                                </div>
                                                <div class="px-2 py-1 rounded-full text-xs font-medium" style="background-color: {{ $statusColor }}20; color: {{ $statusColor }};">
                                                    {{ $status }}
                                                </div>
                                            </div>
                                            
                                            @if($avgAmount > 0)
                                                <div class="flex items-center gap-1 text-xs text-gray-500">
                                                    <x-filament::icon icon="heroicon-o-banknotes" class="h-3 w-3" />
                                                    <span>{{ number_format($avgAmount, 0) }} zł</span>
                                                </div>
                                            @endif
                                        </div>

                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
