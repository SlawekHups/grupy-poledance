<div class="w-full flex justify-center my-6">
    @if($user)
        <div class="w-full max-w-xl bg-white dark:bg-gray-900 rounded-xl shadow p-6 flex flex-col items-center border border-gray-200 dark:border-gray-700">
            <div class="flex items-center space-x-4 mb-4">
                <div class="bg-primary-600 text-white rounded-full w-16 h-16 flex items-center justify-center text-2xl font-bold">
                    {{ mb_substr($user->name, 0, 1) }}
                </div>
                <div>
                    <div class="text-xl font-semibold">{{ $user->name }}</div>
                    @if($group)
                        <div class="text-sm text-primary-600 font-medium">{{ $group }}</div>
                    @endif
                </div>
            </div>
            <div class="w-full flex flex-col gap-2 mt-2">
                <div class="flex items-center gap-2 text-gray-700 dark:text-gray-200">
                    <x-heroicon-o-user-group class="w-5 h-5 text-primary-500" />
                    <span>{{ $group }}</span>
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
    @else
        <div class="w-full max-w-xl h-24 bg-white dark:bg-gray-900 rounded-xl shadow p-6 border border-gray-200 dark:border-gray-700"></div>
    @endif
</div> 