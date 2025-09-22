<div class="md:hidden sticky top-0 z-50 bg-white/95 backdrop-blur border-b" x-data="{ open: false }" @keydown.escape.window="open=false">
    <div class="px-3 py-2 flex items-center gap-3">
        <button @click="open = !open" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md border text-sm hover:bg-gray-50">
            <x-filament::icon icon="heroicon-o-bars-3" class="h-5 w-5" />
            <span>Menu</span>
        </button>
        <a href="{{ route('filament.user.pages.dashboard') }}" class="text-sm font-medium text-gray-700">Panel użytkownika</a>
        @if (filament()->auth()->check())
            <x-filament-panels::user-menu class="ml-auto" />
        @endif
    </div>

    <div x-cloak x-show="open" x-transition.origin.top class="absolute inset-x-0 top-full bg-white shadow-lg border-b z-50 max-h-[calc(100vh-60px)] overflow-y-auto overscroll-contain mobile-menu-scroll">
        <nav class="max-w-screen-xl mx-auto px-3 py-3 space-y-4">
            <!-- Panel użytkownika -->
            <div>
                <div class="text-xs uppercase tracking-wide text-gray-500 px-1 mb-2">Panel użytkownika</div>
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('filament.user.pages.dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded-md border hover:bg-gray-50">
                        <x-filament::icon icon="heroicon-o-user-circle" class="h-5 w-5" />
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('filament.user.resources.users.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-md border hover:bg-gray-50">
                        <x-filament::icon icon="heroicon-o-identification" class="h-5 w-5" />
                        <span>Konto</span>
                    </a>
                    <a href="{{ route('filament.user.resources.addresses.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-md border hover:bg-gray-50">
                        <x-filament::icon icon="heroicon-o-map-pin" class="h-5 w-5" />
                        <span>Adres</span>
                    </a>
                    <a href="{{ route('filament.user.resources.payments.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-md border hover:bg-gray-50">
                        <x-filament::icon icon="heroicon-o-banknotes" class="h-5 w-5" />
                        <span>Płatności</span>
                    </a>
                    <a href="{{ route('filament.user.resources.attendances.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-md border hover:bg-gray-50">
                        <x-filament::icon icon="heroicon-o-calendar" class="h-5 w-5" />
                        <span>Obecność</span>
                    </a>
                </div>
            </div>
            <!-- Informacje -->
            <div>
                <div class="text-xs uppercase tracking-wide text-gray-500 px-1 mb-2">Informacje</div>
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('filament.user.resources.lessons.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-md border hover:bg-gray-50">
                        <x-filament::icon icon="heroicon-o-academic-cap" class="h-5 w-5" />
                        <span>Zadania</span>
                    </a>
                    <a href="{{ route('filament.user.resources.user-mail-messages.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-md border hover:bg-gray-50">
                        <x-filament::icon icon="heroicon-o-envelope" class="h-5 w-5" />
                        <span>Wiadomości</span>
                    </a>
                    <a href="{{ route('filament.user.pages.terms') }}" class="flex items-center gap-2 px-3 py-2 rounded-md border hover:bg-gray-50">
                        <x-filament::icon icon="heroicon-o-document-text" class="h-5 w-5" />
                        <span>Regulamin</span>
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
  
  /* Lepsze przewijanie dla menu mobilnego */
  .mobile-menu-scroll {
    -webkit-overflow-scrolling: touch;
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 #f1f5f9;
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
