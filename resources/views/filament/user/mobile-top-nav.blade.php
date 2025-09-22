<div class="md:hidden sticky top-0 z-50 bg-white/95 backdrop-blur border-b" 
     x-data="{ open: false }" 
     @keydown.escape.window="open=false"
     x-init="$watch('open', value => { 
         if (value) { 
             document.documentElement.style.overflow = 'hidden';
         } else { 
             document.documentElement.style.overflow = '';
         } 
     })">
    <div class="px-4 py-3 flex items-center gap-4">
        <button @click="open = !open" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-colors duration-200 shadow-sm">
            <x-filament::icon icon="heroicon-o-bars-3" class="h-5 w-5" />
            <span>Menu</span>
        </button>
        <a href="{{ route('filament.user.pages.dashboard') }}" class="text-sm font-semibold text-gray-800">Panel użytkownika</a>
        @if (filament()->auth()->check())
            <x-filament-panels::user-menu class="ml-auto" />
        @endif
    </div>

    <!-- Overlay -->
    <div x-cloak x-show="open" @click="open = false" class="fixed inset-0 bg-black/20 z-40"></div>

    <div x-cloak x-show="open" x-transition.origin.top class="fixed inset-x-0 top-[60px] bg-white shadow-lg border-b z-50 max-h-96 overflow-y-auto mobile-menu-scroll">
        <nav class="max-w-screen-xl mx-auto px-4 py-6 pb-24 space-y-6">
            <!-- Panel użytkownika -->
            <div>
                <div class="text-xs font-semibold uppercase tracking-wide text-gray-600 px-2 mb-3">Panel użytkownika</div>
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('filament.user.pages.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-colors duration-200 shadow-sm">
                        <x-filament::icon icon="heroicon-o-user-circle" class="h-5 w-5 text-gray-600" />
                        <span class="text-sm font-medium text-gray-700">Dashboard</span>
                    </a>
                    <a href="{{ route('filament.user.resources.users.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-colors duration-200 shadow-sm">
                        <x-filament::icon icon="heroicon-o-identification" class="h-5 w-5 text-gray-600" />
                        <span class="text-sm font-medium text-gray-700">Konto</span>
                    </a>
                    <a href="{{ route('filament.user.resources.addresses.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-colors duration-200 shadow-sm">
                        <x-filament::icon icon="heroicon-o-map-pin" class="h-5 w-5 text-gray-600" />
                        <span class="text-sm font-medium text-gray-700">Adres</span>
                    </a>
                    <a href="{{ route('filament.user.resources.payments.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-colors duration-200 shadow-sm">
                        <x-filament::icon icon="heroicon-o-banknotes" class="h-5 w-5 text-gray-600" />
                        <span class="text-sm font-medium text-gray-700">Płatności</span>
                    </a>
                    <a href="{{ route('filament.user.resources.attendances.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-colors duration-200 shadow-sm">
                        <x-filament::icon icon="heroicon-o-calendar" class="h-5 w-5 text-gray-600" />
                        <span class="text-sm font-medium text-gray-700">Obecność</span>
                    </a>
                </div>
            </div>
            <!-- Informacje -->
            <div>
                <div class="text-xs font-semibold uppercase tracking-wide text-gray-600 px-2 mb-3">Informacje</div>
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('filament.user.resources.lessons.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-colors duration-200 shadow-sm">
                        <x-filament::icon icon="heroicon-o-academic-cap" class="h-5 w-5 text-gray-600" />
                        <span class="text-sm font-medium text-gray-700">Zadania</span>
                    </a>
                    <a href="{{ route('filament.user.resources.user-mail-messages.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-colors duration-200 shadow-sm">
                        <x-filament::icon icon="heroicon-o-envelope" class="h-5 w-5 text-gray-600" />
                        <span class="text-sm font-medium text-gray-700">Wiadomości</span>
                    </a>
                    <a href="{{ route('filament.user.pages.terms') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-colors duration-200 shadow-sm">
                        <x-filament::icon icon="heroicon-o-document-text" class="h-5 w-5 text-gray-600" />
                        <span class="text-sm font-medium text-gray-700">Regulamin</span>
                    </a>
                </div>
            </div>

            <!-- Więcej treści do przewijania -->
            <div>
                <div class="text-xs font-semibold uppercase tracking-wide text-gray-600 px-2 mb-3">Dodatkowe opcje</div>
                <div class="grid grid-cols-2 gap-3">
                    <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-colors duration-200 shadow-sm">
                        <x-filament::icon icon="heroicon-o-cog-6-tooth" class="h-5 w-5 text-gray-600" />
                        <span class="text-sm font-medium text-gray-700">Ustawienia</span>
                    </a>
                    <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-colors duration-200 shadow-sm">
                        <x-filament::icon icon="heroicon-o-question-mark-circle" class="h-5 w-5 text-gray-600" />
                        <span class="text-sm font-medium text-gray-700">Pomoc</span>
                    </a>
                    <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-colors duration-200 shadow-sm">
                        <x-filament::icon icon="heroicon-o-arrow-right-on-rectangle" class="h-5 w-5 text-gray-600" />
                        <span class="text-sm font-medium text-gray-700">Wyloguj</span>
                    </a>
                    <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-colors duration-200 shadow-sm">
                        <x-filament::icon icon="heroicon-o-information-circle" class="h-5 w-5 text-gray-600" />
                        <span class="text-sm font-medium text-gray-700">Info</span>
                    </a>
                </div>
            </div>

            <!-- Dodatkowy margines na końcu menu -->
            <div class="h-16"></div>

            <div class="flex justify-end pt-4">
                <button @click="open=false" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-colors duration-200 shadow-sm">
                    <x-filament::icon icon="heroicon-o-x-mark" class="h-4 w-4" />
                    <span>Zamknij</span>
                </button>
            </div>
        </nav>
    </div>
</div>
<style>
  [x-cloak] { display: none !important; }
  
  /* Lepsze przewijanie dla menu mobilnego */
  .mobile-menu-scroll {
    -webkit-overflow-scrolling: touch;
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 #f1f5f9;
    overscroll-behavior: auto;
  }
  
  .mobile-menu-scroll::-webkit-scrollbar {
    width: 6px;
  }
  
  .mobile-menu-scroll::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
  }
  
  .mobile-menu-scroll::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
  }
  
  .mobile-menu-scroll::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
  }
  
  @media (max-width: 767px) {
    /* Ukryj desktopowy sidebar i jego overlay na mobile */
    .fi-sidebar, .fi-sidebar-panel, .fi-sidebar-close-overlay { display: none !important; }
    /* Ukryj przycisk otwierania sidebara oraz domyślne user-menu w topbarze */
    .fi-topbar [data-sidebar-toggle], .fi-topbar button[class*="sidebar"], .fi-topbar .fi-user-menu { display: none !important; }
  }
</style>
