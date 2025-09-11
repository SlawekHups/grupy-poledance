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
    <div class="flex flex-wrap items-center justify-center gap-1 !gap-1">
        <x-filament::badge color="info" icon="heroicon-o-user-plus" size="sm">
            Wolne: <span class="font-bold text-sm">{{ $free }}</span>
        </x-filament::badge>
    </div>
@endif


