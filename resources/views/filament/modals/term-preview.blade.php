<div class="space-y-4">
    <!-- Nagłówek dokumentu -->
    <div class="border-b border-gray-200 pb-4">
        <h3 class="text-lg font-semibold text-gray-900">{{ $term->name ?? 'Brak nazwy' }}</h3>
        <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500">
            <span>Status: 
                @if($term->active ?? false)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        Aktywny
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        Nieaktywny
                    </span>
                @endif
            </span>
            @if($term->created_at)
                <span>Utworzono: {{ $term->created_at->format('d.m.Y H:i') }}</span>
            @endif
            @if($term->updated_at && $term->updated_at != $term->created_at)
                <span>Zaktualizowano: {{ $term->updated_at->format('d.m.Y H:i') }}</span>
            @endif
        </div>
    </div>

    <!-- Treść dokumentu -->
    <div class="prose prose-sm max-w-none">
        @if($term->content)
            @if(str_contains($term->content, '```'))
                <!-- Jeśli zawiera bloki kodu, renderuj jako Markdown -->
                {!! Str::markdown($term->content) !!}
            @else
                <!-- Jeśli to zwykły tekst, zachowaj formatowanie -->
                <div class="whitespace-pre-wrap text-gray-700">{{ $term->content }}</div>
            @endif
        @else
            <div class="text-gray-500 italic">Brak treści</div>
        @endif
    </div>

    <!-- Statystyki -->
    <div class="border-t border-gray-200 pt-4">
        <div class="text-sm text-gray-500">
            @if($term->content)
                <span>Długość treści: {{ Str::length($term->content) }} znaków</span>
                @if(Str::length($term->content) > 0)
                    <span class="mx-2">•</span>
                    <span>Liczba linii: {{ substr_count($term->content, "\n") + 1 }}</span>
                @endif
            @else
                <span>Brak treści do analizy</span>
            @endif
        </div>
    </div>
</div>
