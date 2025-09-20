<div class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Numer telefonu</label>
            <p class="text-sm text-gray-900">{{ $record->phone }}</p>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Typ SMS</label>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                @if($record->type === 'pre_registration') bg-green-100 text-green-800
                @elseif($record->type === 'password_reset') bg-yellow-100 text-yellow-800
                @elseif($record->type === 'payment_reminder') bg-blue-100 text-blue-800
                @elseif($record->type === 'data_correction') bg-gray-100 text-gray-800
                @else bg-purple-100 text-purple-800
                @endif">
                @switch($record->type)
                    @case('pre_registration') Pre-rejestracja @break
                    @case('password_reset') Reset hasła @break
                    @case('payment_reminder') Przypomnienie @break
                    @case('data_correction') Poprawa danych @break
                    @case('test') Test @break
                    @default {{ ucfirst($record->type) }}
                @endswitch
            </span>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                @if($record->status === 'sent') bg-green-100 text-green-800
                @elseif($record->status === 'error') bg-red-100 text-red-800
                @elseif($record->status === 'pending') bg-yellow-100 text-yellow-800
                @else bg-gray-100 text-gray-800
                @endif">
                @switch($record->status)
                    @case('sent') Wysłany @break
                    @case('error') Błąd @break
                    @case('pending') Oczekujący @break
                    @default {{ ucfirst($record->status) }}
                @endswitch
            </span>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Koszt</label>
            <p class="text-sm text-gray-900">
                @if($record->cost)
                    {{ number_format($record->cost, 2) }} PLN
                @else
                    <span class="text-gray-500">-</span>
                @endif
            </p>
        </div>
    </div>
    
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Wiadomość</label>
        <div class="bg-gray-50 p-3 rounded-md">
            <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $record->message }}</p>
        </div>
    </div>
    
    @if($record->message_id)
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">ID SMS</label>
        <p class="text-sm text-gray-900 font-mono">{{ $record->message_id }}</p>
    </div>
    @endif
    
    @if($record->error_message)
    <div>
        <label class="block text-sm font-medium text-red-700 mb-1">Błąd</label>
        <div class="bg-red-50 border border-red-200 p-3 rounded-md">
            <p class="text-sm text-red-800">{{ $record->error_message }}</p>
        </div>
    </div>
    @endif
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-gray-200">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Data wysłania</label>
            <p class="text-sm text-gray-900">
                @if($record->sent_at)
                    {{ $record->sent_at->format('d.m.Y H:i:s') }}
                @else
                    <span class="text-gray-500">Nie wysłano</span>
                @endif
            </p>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Data utworzenia</label>
            <p class="text-sm text-gray-900">{{ $record->created_at->format('d.m.Y H:i:s') }}</p>
        </div>
    </div>
</div>
