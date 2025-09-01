@props(['getRecord'])
@php($record = $getRecord())
@if ($record && $record->payment_link && !$record->paid)
    <x-filament::button tag="a" href="{{ $record->payment_link }}" target="_blank" icon="heroicon-o-credit-card" color="warning" size="sm">
        Zapłać online
    </x-filament::button>
@endif
