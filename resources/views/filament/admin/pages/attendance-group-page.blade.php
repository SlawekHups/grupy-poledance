<x-filament::page>
    <form wire:submit.prevent="saveAttendance">
        <!-- Desktop layout -->
        <div class="hidden md:flex flex-wrap md:flex-nowrap gap-4 items-end mb-6">
            <div class="flex-1 min-w-[160px]">
                <label for="group_id" class="block text-sm !font-semibold text-gray-700 dark:text-gray-200">
                    Grupa:
                </label>
                <select id="group_id" wire:model="group_id"
                    class="filament-forms-select w-full rounded-lg border-gray-300 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-600 font-semibold">
                    <option value="">-- Wybierz grupę --</option>
                    @foreach(\App\Models\Group::orderBy('name')->get() as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[140px]">
                <label for="date" class="block text-sm !font-semibold text-gray-700 dark:text-gray-200">
                    Data zajęć:
                </label>
                <input id="date" type="date" wire:model="date"
                    class="filament-forms-input w-full rounded-lg border-gray-300 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-600 font-semibold" />
            </div>
            <div class="flex gap-2 flex-1 md:flex-none">
                <button type="button" wire:click="loadUsers"
                    class="w-full md:w-auto inline-flex items-center justify-center font-medium rounded-lg outline-none transition focus:ring-2 focus:ring-amber-500"
                    style="background-color:#d97706; color:#fff; border-radius:0.5rem; padding:0.5em 1em; min-width:160px;">
                    Pokaż użytkowników
                </button>
            </div>
        </div>

        <!-- Mobile layout - wszystko w osobnych liniach -->
        <div class="md:hidden flex flex-col gap-4 mb-6">
            <div>
                <label for="group_id_mobile" class="block text-sm !font-semibold text-gray-700 dark:text-gray-200 mb-2">
                    Grupa:
                </label>
                <select id="group_id_mobile" wire:model="group_id"
                    class="filament-forms-select w-full rounded-lg border-gray-300 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-600 font-semibold">
                    <option value="">-- Wybierz grupę --</option>
                    @foreach(\App\Models\Group::orderBy('name')->get() as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="date_mobile" class="block text-sm !font-semibold text-gray-700 dark:text-gray-200 mb-2">
                    Data zajęć:
                </label>
                <input id="date_mobile" type="date" wire:model="date"
                    class="filament-forms-input w-full rounded-lg border-gray-300 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-600 font-semibold" />
            </div>
            <div>
                <button type="button" wire:click="loadUsers"
                    class="w-full inline-flex items-center justify-center font-medium rounded-lg outline-none transition focus:ring-2 focus:ring-amber-500"
                    style="background-color:#d97706; color:#fff; border-radius:0.5rem; padding:0.5em 1em;">
                    Pokaż użytkowników
                </button>
            </div>
        </div>

        @if(!empty($users))
        <!-- Tabela desktop, karty mobile -->
        <div class="filament-card p-4 shadow rounded-lg">
            <!-- Desktop tabela -->
            <div class="hidden md:block">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 text-left font-semibold">Użytkownik</th>
                            <th class="px-4 py-2 text-left font-semibold">Obecny</th>
                            <th class="px-4 py-2 text-left font-semibold">Notatka</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td class="px-4 py-2 align-top">
                                <div class="font-semibold">{{ $user['name'] }}</div>
                                <div class="text-xs text-gray-500">{{ $user['email'] }}</div>
                            </td>
                            <td class="px-4 py-2 align-top">
                                <input type="checkbox" wire:model="attendances.{{ $user['id'] }}.present" />
                            </td>
                            <td class="px-4 py-2 align-top">
                                <input type="text" wire:model="attendances.{{ $user['id'] }}.note" placeholder="Notatka"
                                    class="filament-forms-input w-full rounded-lg border-gray-300 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-600" />
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Mobile karty -->
            <div class="md:hidden flex flex-col gap-4">
                @foreach($users as $user)
                <div
                    class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-3 flex flex-col gap-1 shadow">
                    <div class="font-semibold">{{ $user['name'] }}</div>
                    <div class="text-xs text-gray-500">{{ $user['email'] }}</div>
                    <div class="flex items-center gap-2 mt-2">
                        <label class="flex items-center gap-1">
                            <input type="checkbox" wire:model="attendances.{{ $user['id'] }}.present" />
                            <span>Obecny</span>
                        </label>
                        <input type="text" wire:model="attendances.{{ $user['id'] }}.note" placeholder="Notatka"
                            class="filament-forms-input w-full rounded-lg border-gray-300 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-600 ml-2" />
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        <!-- Przycisk na dole -->
        <div class="flex justify-end mt-6">
            <button type="submit"
                class="w-full md:w-auto inline-flex items-center justify-center font-medium rounded-lg outline-none transition focus:ring-2 focus:ring-green-500"
                style="background-color:#22c55e; color:#fff; border-radius:0.5rem; padding:0.5em 1em; min-width:160px;">
                Zapisz obecność
            </button>
        </div>
        @elseif($group_id)
        <div class="w-full flex mt-12 mb-6">
            <div class="mx-auto w-full md:w-2/3 lg:w-1/2 text-center font-semibold"
                style="color: #dc2626; background-color: #fff1f2; border-left: 4px solid #dc2626; padding: 1.25em; border-radius: 0.75em; box-shadow: 0 2px 8px 0 #0000000a; margin: 20px;">
                Brak użytkowników w wybranej grupie.
            </div>
        </div>
        @endif
    </form>
</x-filament::page>