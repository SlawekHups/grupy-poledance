<div class="space-y-4">
    <!-- Tytuł -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Tytuł</label>
        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
            <span class="text-lg font-semibold text-gray-900">{{ $lesson->title ?? 'Brak tytułu' }}</span>
        </div>
    </div>

    <!-- Opis -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Opis</label>
        @if($lesson->description)
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 prose prose-sm max-w-none">
                {!! $lesson->description !!}
            </div>
        @else
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 text-gray-500 italic">
                Brak opisu zajęć
            </div>
        @endif
    </div>
</div>
