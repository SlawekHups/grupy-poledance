<div class="md:hidden sticky top-0 z-50 bg-white/95 dark:bg-gray-900/95 backdrop-blur border-b border-gray-200 dark:border-gray-700" x-data="{ open: false }" @keydown.escape.window="open=false">
    <div class="px-3 py-2 flex items-center gap-3">
        <button @click="open = !open" class="mobile-menu-button inline-flex items-center gap-2 px-3 py-1.5 rounded-md border border-gray-300 dark:border-gray-600 text-sm text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-800 hover:!text-black dark:hover:!text-white"
onmouseover="if(document.documentElement.classList.contains('dark')) { this.style.color='#ffffff'; this.style.backgroundColor='#1f2937'; } else { this.style.color='#000000'; this.style.backgroundColor='#f9fafb'; }"
onmouseout="if(document.documentElement.classList.contains('dark')) { this.style.color='#ffffff'; this.style.backgroundColor=''; } else { this.style.color='#1f2937'; this.style.backgroundColor=''; }">
            <x-filament::icon icon="heroicon-o-bars-3" class="h-5 w-5" />
            <span>Menu</span>
        </button>
        <a href="{{ route('filament.admin.pages.dashboard') }}" class="text-sm font-medium text-gray-900 dark:text-white">Panel administratora</a>
        @if (filament()->auth()->check())
            <x-filament-panels::user-menu class="ml-auto" />
        @endif
    </div>

    <div x-cloak x-show="open" x-transition.origin.top class="absolute inset-x-0 top-full bg-white dark:bg-gray-800 shadow-lg border-b border-gray-200 dark:border-gray-700 z-50 max-h-[calc(100vh-60px)] overflow-y-auto overscroll-contain mobile-menu-scroll">
        <nav class="max-w-screen-xl mx-auto px-3 py-3 pb-20 space-y-4">
            
            <!-- Panel administratora -->
            <div>
                <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-300 px-1 mb-2">Panel administratora</div>
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('filament.admin.pages.dashboard') }}" class="mobile-menu-link flex items-center gap-2 px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-700 hover:!text-black dark:hover:!text-white"
onmouseover="if(document.documentElement.classList.contains('dark')) { this.style.color='#ffffff'; this.style.backgroundColor='#374151'; } else { this.style.color='#000000'; this.style.backgroundColor='#f9fafb'; }"
onmouseout="if(document.documentElement.classList.contains('dark')) { this.style.color='#ffffff'; this.style.backgroundColor=''; } else { this.style.color='#1f2937'; this.style.backgroundColor=''; }">
                        <x-filament::icon icon="heroicon-o-squares-2x2" class="h-5 w-5" />
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('filament.admin.pages.attendance-group-page') }}" class="mobile-menu-link flex items-center gap-2 px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-700 hover:!text-black dark:hover:!text-white"
onmouseover="if(document.documentElement.classList.contains('dark')) { this.style.color='#ffffff'; this.style.backgroundColor='#374151'; } else { this.style.color='#000000'; this.style.backgroundColor='#f9fafb'; }"
onmouseout="if(document.documentElement.classList.contains('dark')) { this.style.color='#ffffff'; this.style.backgroundColor=''; } else { this.style.color='#1f2937'; this.style.backgroundColor=''; }">
                        <x-filament::icon icon="heroicon-o-document-text" class="h-5 w-5" />
                        <span>Obecność grupy</span>
                    </a>
                    <a href="{{ route('filament.admin.resources.pre-registrations.index') }}" class="mobile-menu-link flex items-center gap-2 px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-700 hover:!text-black dark:hover:!text-white"
onmouseover="if(document.documentElement.classList.contains('dark')) { this.style.color='#ffffff'; this.style.backgroundColor='#374151'; } else { this.style.color='#000000'; this.style.backgroundColor='#f9fafb'; }"
onmouseout="if(document.documentElement.classList.contains('dark')) { this.style.color='#ffffff'; this.style.backgroundColor=''; } else { this.style.color='#1f2937'; this.style.backgroundColor=''; }">
                        <x-filament::icon icon="heroicon-o-user-plus" class="h-5 w-5" />
                        <span>Pre-rejestracje</span>
                    </a>
                    <a href="{{ route('filament.admin.resources.data-correction-links.index') }}" class="mobile-menu-link flex items-center gap-2 px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-700 hover:!text-black dark:hover:!text-white"
onmouseover="if(document.documentElement.classList.contains('dark')) { this.style.color='#ffffff'; this.style.backgroundColor='#374151'; } else { this.style.color='#000000'; this.style.backgroundColor='#f9fafb'; }"
onmouseout="if(document.documentElement.classList.contains('dark')) { this.style.color='#ffffff'; this.style.backgroundColor=''; } else { this.style.color='#1f2937'; this.style.backgroundColor=''; }">
                        <x-filament::icon icon="heroicon-o-pencil-square" class="h-5 w-5" />
                        <span>Linki poprawy</span>
                    </a>
                </div>
            </div>

            <!-- Użytkownicy i Grupy -->
            <div>
                <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-300 px-1 mb-2">Użytkownicy i Grupy</div>
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('filament.admin.resources.users.index') }}" class="mobile-menu-link flex items-center gap-2 px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-700 hover:!text-black dark:hover:!text-white"
onmouseover="if(document.documentElement.classList.contains('dark')) { this.style.color='#ffffff'; this.style.backgroundColor='#374151'; } else { this.style.color='#000000'; this.style.backgroundColor='#f9fafb'; }"
onmouseout="if(document.documentElement.classList.contains('dark')) { this.style.color='#ffffff'; this.style.backgroundColor=''; } else { this.style.color='#1f2937'; this.style.backgroundColor=''; }">
                        <x-filament::icon icon="heroicon-o-users" class="h-5 w-5" />
                        <span>Użytkownicy</span>
                    </a>
                    <a href="{{ route('filament.admin.resources.groups.index') }}" class="mobile-menu-link flex items-center gap-2 px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-700 hover:!text-black dark:hover:!text-white"
onmouseover="if(document.documentElement.classList.contains('dark')) { this.style.color='#ffffff'; this.style.backgroundColor='#374151'; } else { this.style.color='#000000'; this.style.backgroundColor='#f9fafb'; }"
onmouseout="if(document.documentElement.classList.contains('dark')) { this.style.color='#ffffff'; this.style.backgroundColor=''; } else { this.style.color='#1f2937'; this.style.backgroundColor=''; }">
                        <x-filament::icon icon="heroicon-o-user-group" class="h-5 w-5" />
                        <span>Grupy</span>
                    </a>
                    <a href="{{ route('filament.admin.resources.attendances.index') }}" class="mobile-menu-link flex items-center gap-2 px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-700 hover:!text-black dark:hover:!text-white"
onmouseover="if(document.documentElement.classList.contains('dark')) { this.style.color='#ffffff'; this.style.backgroundColor='#374151'; } else { this.style.color='#000000'; this.style.backgroundColor='#f9fafb'; }"
onmouseout="if(document.documentElement.classList.contains('dark')) { this.style.color='#ffffff'; this.style.backgroundColor=''; } else { this.style.color='#1f2937'; this.style.backgroundColor=''; }">
                        <x-filament::icon icon="heroicon-o-calendar" class="h-5 w-5" />
                        <span>Obecności</span>
                    </a>
                    <a href="{{ route('filament.admin.resources.password-reset-logs.index') }}" class="mobile-menu-link flex items-center gap-2 px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-700 hover:!text-black dark:hover:!text-white"
onmouseover="if(document.documentElement.classList.contains('dark')) { this.style.color='#ffffff'; this.style.backgroundColor='#374151'; } else { this.style.color='#000000'; this.style.backgroundColor='#f9fafb'; }"
onmouseout="if(document.documentElement.classList.contains('dark')) { this.style.color='#ffffff'; this.style.backgroundColor=''; } else { this.style.color='#1f2937'; this.style.backgroundColor=''; }">
                        <x-filament::icon icon="heroicon-o-key" class="h-5 w-5" />
                        <span>Logi resetów</span>
                    </a>
                </div>
            </div>

            <!-- Finanse -->
            <div>
                <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-300 px-1 mb-2">Finanse</div>
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('filament.admin.resources.payments.index') }}" class="mobile-menu-link flex items-center gap-2 px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-700 hover:!text-black dark:hover:!text-white"
onmouseover="if(document.documentElement.classList.contains('dark')) { this.style.color='#ffffff'; this.style.backgroundColor='#374151'; } else { this.style.color='#000000'; this.style.backgroundColor='#f9fafb'; }"
onmouseout="if(document.documentElement.classList.contains('dark')) { this.style.color='#ffffff'; this.style.backgroundColor=''; } else { this.style.color='#1f2937'; this.style.backgroundColor=''; }">
                        <x-filament::icon icon="heroicon-o-banknotes" class="h-5 w-5" />
                        <span>Płatności</span>
                    </a>
                </div>
            </div>

            <!-- Zajęcia -->
            <div>
                <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-300 px-1 mb-2">Zajęcia</div>
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('filament.admin.resources.lessons.index') }}" class="mobile-menu-link flex items-center gap-2 px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-700 hover:!text-black dark:hover:!text-white"
onmouseover="if(document.documentElement.classList.contains('dark')) { this.style.color='#ffffff'; this.style.backgroundColor='#374151'; } else { this.style.color='#000000'; this.style.backgroundColor='#f9fafb'; }"
onmouseout="if(document.documentElement.classList.contains('dark')) { this.style.color='#ffffff'; this.style.backgroundColor=''; } else { this.style.color='#1f2937'; this.style.backgroundColor=''; }">
                        <x-filament::icon icon="heroicon-o-academic-cap" class="h-5 w-5" />
                        <span>Zadania</span>
                    </a>
                    <a href="{{ route('filament.admin.resources.terms.index') }}" class="mobile-menu-link flex items-center gap-2 px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-700 hover:!text-black dark:hover:!text-white"
onmouseover="if(document.documentElement.classList.contains('dark')) { this.style.color='#ffffff'; this.style.backgroundColor='#374151'; } else { this.style.color='#000000'; this.style.backgroundColor='#f9fafb'; }"
onmouseout="if(document.documentElement.classList.contains('dark')) { this.style.color='#ffffff'; this.style.backgroundColor=''; } else { this.style.color='#1f2937'; this.style.backgroundColor=''; }">
                        <x-filament::icon icon="heroicon-o-document-text" class="h-5 w-5" />
                        <span>Regulaminy</span>
                    </a>
                </div>
            </div>

            <!-- Ustawienia -->
            <div>
                <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-300 px-1 mb-2">Ustawienia</div>
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('filament.admin.pages.admin-settings') }}" class="mobile-menu-link flex items-center gap-2 px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-700 hover:!text-black dark:hover:!text-white"
onmouseover="if(document.documentElement.classList.contains('dark')) { this.style.color='#ffffff'; this.style.backgroundColor='#374151'; } else { this.style.color='#000000'; this.style.backgroundColor='#f9fafb'; }"
onmouseout="if(document.documentElement.classList.contains('dark')) { this.style.color='#ffffff'; this.style.backgroundColor=''; } else { this.style.color='#1f2937'; this.style.backgroundColor=''; }">
                        <x-filament::icon icon="heroicon-o-shield-check" class="h-5 w-5" />
                        <span>Ustawienia admin</span>
                    </a>
                    <a href="{{ route('filament.admin.resources.user-mail-messages.index') }}" class="mobile-menu-link flex items-center gap-2 px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-700 hover:!text-black dark:hover:!text-white"
onmouseover="if(document.documentElement.classList.contains('dark')) { this.style.color='#ffffff'; this.style.backgroundColor='#374151'; } else { this.style.color='#000000'; this.style.backgroundColor='#f9fafb'; }"
onmouseout="if(document.documentElement.classList.contains('dark')) { this.style.color='#ffffff'; this.style.backgroundColor=''; } else { this.style.color='#1f2937'; this.style.backgroundColor=''; }">
                        <x-filament::icon icon="heroicon-o-envelope" class="h-5 w-5" />
                        <span>Wiadomości</span>
                    </a>
                    <a href="{{ route('filament.admin.resources.files.index') }}" class="mobile-menu-link flex items-center gap-2 px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-700 hover:!text-black dark:hover:!text-white"
onmouseover="if(document.documentElement.classList.contains('dark')) { this.style.color='#ffffff'; this.style.backgroundColor='#374151'; } else { this.style.color='#000000'; this.style.backgroundColor='#f9fafb'; }"
onmouseout="if(document.documentElement.classList.contains('dark')) { this.style.color='#ffffff'; this.style.backgroundColor=''; } else { this.style.color='#1f2937'; this.style.backgroundColor=''; }">
                        <x-filament::icon icon="heroicon-o-document" class="h-5 w-5" />
                        <span>Pliki</span>
                    </a>
                    <a href="{{ route('filament.admin.resources.sms-logs.index') }}" class="mobile-menu-link flex items-center gap-2 px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-700 hover:!text-black dark:hover:!text-white"
onmouseover="if(document.documentElement.classList.contains('dark')) { this.style.color='#ffffff'; this.style.backgroundColor='#374151'; } else { this.style.color='#000000'; this.style.backgroundColor='#f9fafb'; }"
onmouseout="if(document.documentElement.classList.contains('dark')) { this.style.color='#ffffff'; this.style.backgroundColor=''; } else { this.style.color='#1f2937'; this.style.backgroundColor=''; }">
                        <x-filament::icon icon="heroicon-o-chat-bubble-left-right" class="h-5 w-5" />
                        <span>Logi SMS</span>
                    </a>
                </div>
            </div>

            <!-- Dodatkowy margines na końcu menu -->
            <div class="h-16"></div>

            <div class="flex justify-end">
                <button @click="open=false" class="mobile-menu-button inline-flex items-center gap-2 px-3 py-1.5 rounded-md border border-gray-300 dark:border-gray-600 text-sm text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-700 hover:!text-black dark:hover:!text-white"
onmouseover="if(document.documentElement.classList.contains('dark')) { this.style.color='#ffffff'; this.style.backgroundColor='#374151'; } else { this.style.color='#000000'; this.style.backgroundColor='#f9fafb'; }"
onmouseout="if(document.documentElement.classList.contains('dark')) { this.style.color='#ffffff'; this.style.backgroundColor=''; } else { this.style.color='#1f2937'; this.style.backgroundColor=''; }">
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
  
  .dark .mobile-menu-scroll {
    scrollbar-color: #4b5563 #1f2937;
  }
  
  .dark .mobile-menu-scroll::-webkit-scrollbar-track {
    background: #1f2937;
  }
  
  .dark .mobile-menu-scroll::-webkit-scrollbar-thumb {
    background: #4b5563;
  }
  
  .dark .mobile-menu-scroll::-webkit-scrollbar-thumb:hover {
    background: #6b7280;
  }
  
  @media (max-width: 767px) {
    /* Ukryj desktopowy sidebar i jego overlay na mobile */
    .fi-sidebar, .fi-sidebar-panel, .fi-sidebar-close-overlay { display: none !important; }
    /* Ukryj przycisk otwierania sidebara oraz domyślne user-menu w topbarze */
    .fi-topbar [data-sidebar-toggle], .fi-topbar button[class*="sidebar"], .fi-topbar .fi-user-menu { display: none !important; }
  }
  
  /* Wymuś kolory hover dla menu mobile */
  .mobile-menu-link:hover,
  .mobile-menu-link:hover span {
    color: #000000 !important;
  }
  
  .dark .mobile-menu-link:hover,
  .dark .mobile-menu-link:hover span {
    color: #ffffff !important;
  }
  
  .mobile-menu-button:hover,
  .mobile-menu-button:hover span {
    color: #000000 !important;
  }
  
  .dark .mobile-menu-button:hover,
  .dark .mobile-menu-button:hover span {
    color: #ffffff !important;
  }
  
  /* Wymuś kolory dla ikon */
  .mobile-menu-link:hover svg {
    color: #000000 !important;
  }
  
  .dark .mobile-menu-link:hover svg {
    color: #ffffff !important;
  }
  
  .mobile-menu-button:hover svg {
    color: #000000 !important;
  }
  
  .dark .mobile-menu-button:hover svg {
    color: #ffffff !important;
  }
</style>
