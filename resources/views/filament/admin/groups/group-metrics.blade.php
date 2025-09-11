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
    <div class="flex flex-wrap items-center justify-center gap-2">
        <x-filament::badge color="info" icon="heroicon-o-user-plus" size="lg">
            Wolne: <span class="font-bold text-lg">{{ $free }}</span>
        </x-filament::badge>
    </div>
@endif


