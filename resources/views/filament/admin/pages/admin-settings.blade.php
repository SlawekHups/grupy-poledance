<x-filament::page>
    {{ $this->form }}

    <div class="mt-6">
        <x-filament::button wire:click="save" icon="heroicon-o-check-circle">
            Zapisz zmiany
        </x-filament::button>
    </div>
</x-filament::page>


