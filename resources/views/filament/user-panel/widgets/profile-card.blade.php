<div class="w-full px-4 md:px-12 flex justify-center my-6">
    @if($user)
        <div class="w-full max-w-screen-2xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Karta użytkownika -->
            <div class="w-full bg-white dark:bg-gray-900 rounded-xl shadow p-6 flex flex-col items-center border border-gray-200 dark:border-gray-700">
                <div class="flex flex-col items-center mb-4">
                    <div class="text-xl font-semibold text-center">{{ $user->name }}</div>
                </div>
                <div class="w-full flex flex-col gap-2 mt-2">
                    <div class="flex items-center gap-2 text-gray-700 dark:text-gray-200">
                        <x-heroicon-o-envelope class="w-5 h-5 text-primary-500" />
                        <span>{{ $user->email }}</span>
                    </div>
                    @if($user->phone)
                    <div class="flex items-center gap-2 text-gray-700 dark:text-gray-200">
                        <x-heroicon-o-phone class="w-5 h-5 text-primary-500" />
                        <span>{{ $user->phone }}</span>
                    </div>
                    @endif
                </div>
            </div>
            <!-- Karta grupy -->
            <div class="w-full bg-white dark:bg-gray-900 rounded-xl shadow p-6 flex flex-col items-center justify-center border border-gray-200 dark:border-gray-700 mx-2">
                <div class="flex flex-col items-center">
                    <div class="bg-primary-100 text-primary-600 rounded-full w-12 h-12 flex items-center justify-center mb-2">
                        <x-heroicon-o-user-group class="w-7 h-7" />
                    </div>
                    <div class="text-lg font-semibold text-center">Grupa</div>
                    <div class="text-primary-600 font-medium text-center">{{ $group ?? 'Brak przypisania' }}</div>
                </div>
            </div>
        </div>
    @else
        <div class="w-full max-w-xl h-24 bg-white dark:bg-gray-900 rounded-xl shadow p-6 border border-gray-200 dark:border-gray-700"></div>
    @endif
</div> 