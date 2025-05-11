@php
    $url = trim($getState() ?: '');
@endphp

@if (!blank($url))
    <div class="inline-flex items-center justify-center space-x-2">
        <a href="{{ $url }}" target="_blank" class="text-primary-600 hover:underline font-medium">
            Zapłać online
        </a>
    </div>
@else
    <span class="text-gray-400 italic">Brak linku</span>
@endif