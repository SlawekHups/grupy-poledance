<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <a href="{{ route('filament.user.resources.payments.index') }}" class="rounded-xl border p-4 bg-white flex items-center gap-3 hover:bg-gray-50 hover:shadow-md transition-colors">
        <x-filament::icon icon="heroicon-o-banknotes" class="h-6 w-6" style="color: var(--fi-color-success, #16a34a)" />
        <div>
            <div class="text-sm text-gray-500">Suma płatności</div>
            <div class="text-2xl font-semibold mt-1" style="color: var(--fi-color-success, #16a34a)">{{ number_format($this->paymentsSum, 2) }} zł</div>
        </div>
    </a>
    <a href="{{ route('filament.user.resources.payments.index') }}" class="rounded-xl border p-4 bg-white flex items-center gap-3 hover:bg-gray-50 hover:shadow-md transition-colors">
        <x-filament::icon icon="heroicon-o-receipt-percent" class="h-6 w-6" style="color: var(--fi-color-info, #3b82f6)" />
        <div>
            <div class="text-sm text-gray-500">Liczba płatności</div>
            <div class="text-2xl font-extrabold mt-1" style="color: var(--fi-color-info, #3b82f6)">{{ $this->paymentsCount }}</div>
        </div>
    </a>
    <a href="{{ route('filament.user.resources.payments.index', ['tableFilters' => ['paid' => ['value' => '0']]]) }}" class="rounded-xl border p-4 bg-white flex items-center gap-3 hover:bg-gray-50 hover:shadow-md transition-colors">
        <x-filament::icon icon="heroicon-o-exclamation-triangle" class="h-6 w-6" style="color: var(--fi-color-danger, #dc2626)" />
        <div>
            <div class="text-sm text-gray-500">Suma zaległości</div>
            <div class="text-2xl font-extrabold mt-1" style="color: var(--fi-color-danger, #dc2626)">{{ number_format($this->unpaidSum, 2) }} zł</div>
        </div>
    </a>
</div>
