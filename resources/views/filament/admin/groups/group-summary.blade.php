@php($record = isset($getRecord) ? $getRecord() : null)
@if ($record)
    @php($count = $record->members()->count())
    @php($max = (int) ($record->max_size ?? 0))
    @php($lessons = $record->lessons()->count())
    @php($status = $record->status ?? 'active')
    @php($statusMap = [
        'active' => ['label' => 'Aktywna', 'color' => 'success', 'bg' => 'bg-emerald-500'],
        'inactive' => ['label' => 'Nieaktywna', 'color' => 'danger', 'bg' => 'bg-rose-500'],
        'full' => ['label' => 'Pełna', 'color' => 'warning', 'bg' => 'bg-amber-500'],
    ])
    @php($statusCfg = $statusMap[$status] ?? $statusMap['active'])

    <div class="rounded-xl border bg-white shadow-sm overflow-hidden">
        <div class="relative px-6 py-7 md:px-8 md:py-8">
            <div class="flex items-start justify-between">
                <div class="">
                    <div class="text-xs/5 text-gray-500">Grupa</div>
                    <div class="mt-1 text-3xl md:text-4xl font-extrabold tracking-tight text-primary-600 dark:text-primary-400">{{ $record->name }}</div>
                    <div class="mt-2 text-base md:text-lg text-gray-600 dark:text-gray-300">
                        @if(filled($record->description))
                            {{ $record->description }}
                        @else
                            <span class="opacity-70 italic">Krótki opis grupy</span>
                        @endif
                    </div>
                </div>
                <a href="{{ route('filament.admin.resources.groups.edit', ['record' => $record->id, 'edit' => 1]) }}" class="inline-flex items-center gap-1 rounded-md bg-white/90 px-3 py-2 text-sm font-medium text-gray-900 hover:bg-white">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                        <path d="M21.731 2.269a2.625 2.625 0 00-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 000-3.712z"/>
                        <path d="M11.186 6.339a2.25 2.25 0 00-3.182 0L3.28 11.064a2.25 2.25 0 00-.623 1.262l-.6 3.897a.75.75 0 00.852.852l3.897-.6a2.25 2.25 0 001.262-.623l4.724-4.724a2.25 2.25 0 000-3.182l-1.606-1.606z"/>
                    </svg>
                    Edytuj
                </a>
            </div>
        </div>

        <div class="px-6 py-5 md:px-8">
            <div class="flex flex-wrap gap-2">
                <x-filament::badge color="primary" icon="heroicon-o-user-group">
                    <span class="font-semibold">{{ $count }}</span>/<span class="font-semibold">{{ $max }}</span>
                </x-filament::badge>

                <x-filament::badge :color="$statusCfg['color']" icon="heroicon-o-bolt">
                    {{ $statusCfg['label'] }}
                </x-filament::badge>

                <x-filament::badge color="secondary" icon="heroicon-o-users">
                    Limit: <span class="font-semibold">{{ $max }}</span>
                </x-filament::badge>

                <x-filament::badge color="info" icon="heroicon-o-academic-cap">
                    Zajęcia: <span class="font-semibold">{{ $lessons }}</span>
                </x-filament::badge>
            </div>
        </div>
    </div>
@endif


