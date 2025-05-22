<x-filament::page>
    <div class="max-w-4xl mx-auto">
        <form wire:submit.prevent="saveAttendance" class="space-y-4">
            <div class="flex gap-4 items-end">
                <div class="flex flex-col">
                    <label class="font-bold mb-1">Grupa:</label>
                    <select wire:model="group_id" class="filament-forms-select block rounded border-gray-300">
                        <option value="">-- Wybierz grupę --</option>
                        @foreach(\App\Models\Group::orderBy('name')->get() as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col">
                    <label class="font-bold mb-1">Data zajęć:</label>
                    <input type="date" wire:model="date" class="filament-forms-input block rounded border-gray-300">
                </div>
                <button type="button" wire:click="loadUsers"
                    class="inline-flex items-center justify-center gap-1 font-medium rounded-lg outline-none transition focus:ring-2 focus:ring-primary-500 bg-primary-600 hover:bg-primary-700 text-white px-4 py-2">
                    Pokaż użytkowników
                </button>
                <button type="submit"
                    class="inline-flex items-center justify-center gap-1 font-medium rounded-lg outline-none transition focus:ring-2 focus:ring-primary-500 bg-primary-600 hover:bg-primary-700 text-white px-4 py-2">
                    Zapisz obecność
                </button>

            </div>

            @if(!empty($users))
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-8">
                @foreach($users as $user)
                @php
                $isPresent = isset($attendances[$user['id']]['present']) ? $attendances[$user['id']]['present'] : null;
                @endphp
                <div class="filament-card p-4 shadow rounded-lg flex flex-col gap-2
                            @if($isPresent === true) bg-green-50 border-l-4 border-green-600
                            @elseif($isPresent === false) bg-red-50 border-l-4 border-red-600
                            @else bg-gray-50 border-l-4 border-gray-400 @endif
                        ">
                    <div class="flex justify-between items-center">
                        <div class="font-semibold">{{ $user['name'] }}</div>
                        <div class="text-xs text-gray-400">{{ $user['email'] }}</div>
                    </div>
                    <div class="flex items-center gap-4 mt-2">
                        <label class="flex items-center gap-1">
                            <input type="checkbox" wire:model="attendances.{{ $user['id'] }}.present"
                                @if($isPresent===true) checked @endif>
                            <span>Obecny</span>
                        </label>
                        <input type="text" placeholder="Notatka" wire:model="attendances.{{ $user['id'] }}.note"
                            class="filament-forms-input rounded border-gray-300 flex-1">
                    </div>
                    <div>
                        @if($isPresent === true)
                        {{-- <span class="text-green-600 font-bold">✔ Obecny</span> --}}
                        <span class="bg-green-600 text-white font-bold p-1 rounded">✔ Obecny</span>
                        @elseif($isPresent === false)
                        <span class="text-red-600 font-bold">✖ Nieobecny</span>
                        @else
                        <span class="text-gray-500">Nieustalona obecność</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @elseif($group_id)
            <div class="mt-8 text-red-500">Brak użytkowników w wybranej grupie.</div>
            @endif
        </form>

    </div>
</x-filament::page>