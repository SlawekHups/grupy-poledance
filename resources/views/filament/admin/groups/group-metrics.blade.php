@php($record = isset($getRecord) ? $getRecord() : null)
@if ($record)
    <?php
        $count = $record->members()->count();
        $max = (int) ($record->max_size ?? 0);
        $free = max($max - $count, 0);
        $occupancyColor = $count >= $max
            ? 'danger'
            : ($count >= ($max * 0.8)
                ? 'warning'
                : 'success');
    ?>
    <div class="flex flex-wrap items-center gap-2">
        <x-filament::badge :color="$occupancyColor" icon="heroicon-o-user-group">
            Uczestnicy: <span class="font-semibold">{{ $count }}</span>/<span class="font-semibold">{{ $max }}</span>
        </x-filament::badge>
        <x-filament::badge color="info" icon="heroicon-o-user-plus">
            Wolne: <span class="font-semibold">{{ $free }}</span>
        </x-filament::badge>
        <x-filament::badge color="primary" icon="heroicon-o-academic-cap">
            Zajęcia: <span class="font-semibold">{{ $record->lessons()->count() }}</span>
        </x-filament::badge>
    </div>
@endif


