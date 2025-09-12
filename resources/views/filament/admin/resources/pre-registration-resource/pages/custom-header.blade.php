<style>
    @media (max-width: 767px) {
        .custom-mobile-actions .fi-ac-btn,
        .custom-mobile-actions .fi-btn,
        .custom-mobile-actions button {
            width: 100% !important;
            justify-content: center !important;
        }
        .custom-mobile-actions .fi-ac-btn-group {
            width: 100% !important;
        }
        .custom-mobile-actions .fi-ac-btn-group button {
            width: 100% !important;
            justify-content: center !important;
        }
    }
</style>

<header class="fi-header flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="fi-header-heading text-2xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-3xl">
            {{ $heading }}
        </h1>
        @if ($subheading)
            <p class="fi-header-subheading mt-2 max-w-2xl text-lg text-gray-600 dark:text-gray-400">
                {{ $subheading }}
            </p>
        @endif
    </div>

    <!-- Desktop layout -->
    <div class="hidden md:flex shrink-0 items-center gap-3">
        @if ($actions)
            <x-filament::actions :actions="$actions" />
        @endif
    </div>

    <!-- Mobile layout - przyciski jeden w jednej linii -->
    <div class="md:hidden flex flex-col gap-2 w-full custom-mobile-actions">
        @if ($actions)
            @foreach ($actions as $action)
                @if ($action instanceof \Filament\Actions\ActionGroup)
                    <!-- Rozpakuj ActionGroup na mobile jako zwykłe buttony -->
                    @foreach ($action->getActions() as $subAction)
                        @if ($subAction->isVisible())
                            @php
                                $color = $subAction->getColor();
                                $name = $subAction->getName();
                                
                                // Konkretne kolory dla konkretnych akcji - używamy inline style jak w obecności
                                $buttonStyle = match($name) {
                                    'generate_tokens' => 'background-color: #22c55e !important; color: white !important;',
                                    'copy_all_links' => 'background-color: #3b82f6 !important; color: white !important;',
                                    'export_links' => 'background-color: #eab308 !important; color: white !important;',
                                    'cleanup_expired' => 'background-color: #dc2626 !important; color: white !important;',
                                    'create' => 'background-color: #ea580c !important; color: white !important;',
                                    default => match($color) {
                                        'success' => 'background-color: #22c55e !important; color: white !important;',
                                        'info' => 'background-color: #3b82f6 !important; color: white !important;',
                                        'warning' => 'background-color: #eab308 !important; color: white !important;',
                                        'danger' => 'background-color: #dc2626 !important; color: white !important;',
                                        'gray' => 'background-color: #6b7280 !important; color: white !important;',
                                        'primary' => 'background-color: #ea580c !important; color: white !important;',
                                        default => 'background-color: #ea580c !important; color: white !important;'
                                    }
                                };
                            @endphp
                            <div class="w-full">
                                <button type="button" 
                                    wire:click="mountAction('{{ $subAction->getName() }}')"
                                    style="{{ $buttonStyle }}"
                                    class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-200 shadow-sm">
                                    @if($subAction->getIcon())
                                        <x-filament::icon :icon="$subAction->getIcon()" class="h-4 w-4 mr-2" />
                                    @endif
                                    {{ $subAction->getLabel() }}
                                </button>
                            </div>
                        @endif
                    @endforeach
                @else
                    <!-- Zwykły przycisk -->
                    @php
                        $actionColor = $action->getColor();
                        $actionName = $action->getName();
                        
                        $actionButtonStyle = match($actionName) {
                            'create' => 'background-color: #ea580c !important; color: white !important;',
                            default => match($actionColor) {
                                'success' => 'background-color: #22c55e !important; color: white !important;',
                                'info' => 'background-color: #3b82f6 !important; color: white !important;',
                                'warning' => 'background-color: #eab308 !important; color: white !important;',
                                'danger' => 'background-color: #dc2626 !important; color: white !important;',
                                'gray' => 'background-color: #6b7280 !important; color: white !important;',
                                'primary' => 'background-color: #ea580c !important; color: white !important;',
                                default => 'background-color: #ea580c !important; color: white !important;'
                            }
                        };
                    @endphp
                    <div class="w-full">
                        <button type="button" 
                            wire:click="mountAction('{{ $action->getName() }}')"
                            style="{{ $actionButtonStyle }}"
                            class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-200 shadow-sm">
                            @if($action->getIcon())
                                <x-filament::icon :icon="$action->getIcon()" class="h-4 w-4 mr-2" />
                            @endif
                            {{ $action->getLabel() }}
                        </button>
                    </div>
                @endif
            @endforeach
        @endif
    </div>
</header>
