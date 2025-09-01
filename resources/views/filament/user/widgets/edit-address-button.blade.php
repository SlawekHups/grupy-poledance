@props(['getRecord'])
@php($record = $getRecord())
@if ($record)
    <x-filament::button
        tag="a"
        href="{{ route('filament.user.resources.addresses.edit', ['record' => $record]) }}"
        icon="heroicon-o-pencil-square"
        color="warning"
        size="sm"
    >
        Edytuj
    </x-filament::button>
@endif


