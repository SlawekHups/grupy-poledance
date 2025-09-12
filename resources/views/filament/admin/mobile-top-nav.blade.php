<div class="md:hidden sticky top-0 z-50 bg-white/95 backdrop-blur border-b" x-data="{ open: false }" @keydown.escape.window="open=false">
    <div class="px-3 py-2 flex items-center gap-3">
        <button @click="open = !open" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md border text-sm hover:bg-gray-50">
            <x-filament::icon icon="heroicon-o-bars-3" class="h-5 w-5" />
            <span>Menu</span>
        </button>
        <a href="{{ route('filament.admin.pages.dashboard') }}" class="text-sm font-medium text-gray-700">Panel administratora</a>
        @if (filament()->auth()->check())
            <x-filament-panels::user-menu class="ml-auto" />
        @endif
    </div>

    <div x-cloak x-show="open" x-transition.origin.top class="absolute inset-x-0 top-full bg-white shadow-lg border-b z-50">
        <nav class="max-w-screen-xl mx-auto px-3 py-3 space-y-4">
            
            <!-- Panel administratora -->
            <div>
                <div class="text-xs uppercase tracking-wide text-gray-500 px-1 mb-2">Panel administratora</div>
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('filament.admin.pages.dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded-md border hover:bg-gray-50">
                        <x-filament::icon icon="heroicon-o-squares-2x2" class="h-5 w-5" />
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('filament.admin.pages.attendance-group-page') }}" class="flex items-center gap-2 px-3 py-2 rounded-md border hover:bg-gray-50">
                        <x-filament::icon icon="heroicon-o-document-text" class="h-5 w-5" />
                        <span>Obecność grupy</span>
                    </a>
                    <a href="{{ route('filament.admin.resources.pre-registrations.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-md border hover:bg-gray-50">
                        <x-filament::icon icon="heroicon-o-user-plus" class="h-5 w-5" />
                        <span>Pre-rejestracje</span>
                    </a>
                </div>
            </div>

            <!-- Użytkownicy i Grupy -->
            <div>
                <div class="text-xs uppercase tracking-wide text-gray-500 px-1 mb-2">Użytkownicy i Grupy</div>
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('filament.admin.resources.users.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-md border hover:bg-gray-50">
                        <x-filament::icon icon="heroicon-o-users" class="h-5 w-5" />
                        <span>Użytkownicy</span>
                    </a>
                    <a href="{{ route('filament.admin.resources.groups.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-md border hover:bg-gray-50">
                        <x-filament::icon icon="heroicon-o-user-group" class="h-5 w-5" />
                        <span>Grupy</span>
                    </a>
                    <a href="{{ route('filament.admin.resources.attendances.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-md border hover:bg-gray-50">
                        <x-filament::icon icon="heroicon-o-calendar" class="h-5 w-5" />
                        <span>Obecności</span>
                    </a>
                    <a href="{{ route('filament.admin.resources.password-reset-logs.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-md border hover:bg-gray-50">
                        <x-filament::icon icon="heroicon-o-key" class="h-5 w-5" />
                        <span>Logi resetów</span>
                    </a>
                </div>
            </div>

            <!-- Finanse -->
            <div>
                <div class="text-xs uppercase tracking-wide text-gray-500 px-1 mb-2">Finanse</div>
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('filament.admin.resources.payments.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-md border hover:bg-gray-50">
                        <x-filament::icon icon="heroicon-o-banknotes" class="h-5 w-5" />
                        <span>Płatności</span>
                    </a>
                </div>
            </div>

            <!-- Zajęcia -->
            <div>
                <div class="text-xs uppercase tracking-wide text-gray-500 px-1 mb-2">Zajęcia</div>
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('filament.admin.resources.lessons.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-md border hover:bg-gray-50">
                        <x-filament::icon icon="heroicon-o-academic-cap" class="h-5 w-5" />
                        <span>Zadania</span>
                    </a>
                </div>
            </div>

            <!-- Ustawienia -->
            <div>
                <div class="text-xs uppercase tracking-wide text-gray-500 px-1 mb-2">Ustawienia</div>
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('filament.admin.pages.admin-settings') }}" class="flex items-center gap-2 px-3 py-2 rounded-md border hover:bg-gray-50">
                        <x-filament::icon icon="heroicon-o-shield-check" class="h-5 w-5" />
                        <span>Ustawienia admin</span>
                    </a>
                    <a href="{{ route('filament.admin.resources.terms.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-md border hover:bg-gray-50">
                        <x-filament::icon icon="heroicon-o-document-text" class="h-5 w-5" />
                        <span>Regulaminy</span>
                    </a>
                    <a href="{{ route('filament.admin.resources.user-mail-messages.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-md border hover:bg-gray-50">
                        <x-filament::icon icon="heroicon-o-envelope" class="h-5 w-5" />
                        <span>Wiadomości</span>
                    </a>
                </div>
            </div>

            <div class="flex justify-end">
                <button @click="open=false" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md border text-sm hover:bg-gray-50">
                    <x-filament::icon icon="heroicon-o-x-mark" class="h-5 w-5" />
                    <span>Zamknij</span>
                </button>
            </div>
        </nav>
    </div>
</div>
<style>
  [x-cloak] { display: none !important; }
  @media (max-width: 767px) {
    /* Ukryj desktopowy sidebar i jego overlay na mobile */
    .fi-sidebar, .fi-sidebar-panel, .fi-sidebar-close-overlay { display: none !important; }
    /* Ukryj przycisk otwierania sidebara oraz domyślne user-menu w topbarze */
    .fi-topbar [data-sidebar-toggle], .fi-topbar button[class*="sidebar"], .fi-topbar .fi-user-menu { display: none !important; }
  }
</style>
