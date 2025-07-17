<div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="bg-white rounded-xl shadow-lg p-8 w-full max-w-full md:px-24 lg:px-48 xl:px-64">
        <h1 class="text-2xl font-bold mb-4 text-center">Regulaminy korzystania z panelu</h1>
        <div class="mb-6 text-gray-700 max-h-[60vh] overflow-y-auto border rounded p-4">
            @if(isset($terms) && $terms->count())
                @foreach($terms as $term)
                    <div class="mb-6">
                        <div class="font-bold text-lg mb-2">{{ $term->name ?? 'Regulamin' }}</div>
                        <div class="prose prose-sm max-w-none">{!! nl2br(e($term->content)) !!}</div>
                    </div>
                @endforeach
            @else
                <p>Brak aktywnych regulaminów. Skontaktuj się z administratorem.</p>
            @endif
        </div>
        <form wire:submit.prevent="acceptTerms" class="flex flex-col items-center">
            <button type="submit"
                class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 rounded-lg transition text-lg shadow">
                Akceptuję regulamin
            </button>
        </form>
    </div>
</div> 