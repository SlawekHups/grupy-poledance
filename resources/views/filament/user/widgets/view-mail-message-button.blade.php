@props(['getRecord'])
@php($record = $getRecord())
@if ($record)
    <x-filament::button
        tag="a"
        href="{{ route('filament.user.resources.user-mail-messages.view', ['record' => $record]) }}"
        icon="heroicon-o-eye"
        color="warning"
        size="sm"
    >
        Podgląd
    </x-filament::button>
@endif


