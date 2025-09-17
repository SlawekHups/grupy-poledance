<?php

namespace App\Filament\Admin\Resources\PreRegistrationResource\Pages;

use App\Filament\Admin\Resources\PreRegistrationResource;
use App\Filament\Admin\Resources\PreRegistrationResource\Widgets\PreRegistrationStatsWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\View\PanelsRenderHook;

class ListPreRegistrations extends ListRecords
{
    protected static string $resource = PreRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('generate_tokens')
                ->label('Generuj tokeny')
                ->icon('heroicon-o-plus-circle')
                ->color('success')
                    ->modalHeading('Generuj tokeny pre-rejestracji')
                    ->modalDescription('Wybierz ile tokenów chcesz wygenerować i na jak długo mają być ważne.')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('count')
                            ->label('Liczba tokenów')
                            ->numeric()
                            ->default(10)
                            ->minValue(1)
                            ->maxValue(50)
                            ->required(),
                            
                        \Filament\Forms\Components\TextInput::make('minutes')
                            ->label('Czas ważności (minuty)')
                            ->numeric()
                            ->default(30)
                            ->minValue(5)
                            ->maxValue(1440)
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $count = (int) $data['count'];
                        $minutes = (int) $data['minutes'];
                        
                        $tokens = [];
                        $expiresAt = now()->addMinutes($minutes);
                        
                        for ($i = 0; $i < $count; $i++) {
                            $token = \App\Models\PreRegistration::generateToken();
                            
                            $preReg = \App\Models\PreRegistration::create([
                                'token' => $token,
                                'name' => '',
                                'email' => '',
                                'phone' => '',
                                'expires_at' => $expiresAt,
                            ]);
                            
                            $tokens[] = $preReg;
                        }
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Tokeny wygenerowane')
                            ->body("Pomyślnie wygenerowano {$count} tokenów ważnych przez {$minutes} minut")
                            ->success()
                            ->send();
                    }),
                    
            Actions\Action::make('copy_all_links')
                ->label('Kopiuj wszystkie linki')
                ->icon('heroicon-o-clipboard-document-list')
                ->color('info')
                ->modalHeading('Kopiuj wszystkie linki pre-rejestracji')
                ->modalDescription('Kopiuj wszystkie ważne linki do schowka')
                ->modalContent(function () {
                    $preRegs = \App\Models\PreRegistration::where('expires_at', '>', now())
                        ->where('used', false)
                        ->orderBy('created_at', 'desc')
                        ->get();
                        
                    return view('filament.admin.resources.pre-registration-resource.modals.copy-all-links', [
                        'preRegs' => $preRegs
                    ]);
                })
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Zamknij'),
                
            Actions\Action::make('export_links')
                ->label('Eksportuj linki')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('warning')
                ->action(function () {
                    $preRegs = \App\Models\PreRegistration::where('expires_at', '>', now())
                        ->where('used', false)
                        ->orderBy('created_at', 'desc')
                        ->get();
                    
                    $csv = "Token,URL,Wygasa\n";
                    foreach ($preRegs as $preReg) {
                        $csv .= $preReg->token . "," . route('pre-register', $preReg->token) . "," . $preReg->expires_at->format('Y-m-d H:i:s') . "\n";
                    }
                    
                    $filename = 'pre-registration-links-' . now()->format('Y-m-d-H-i-s') . '.csv';
                    
                    return response()->streamDownload(function () use ($csv) {
                        echo $csv;
                    }, $filename, [
                        'Content-Type' => 'text/csv',
                        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                    ]);
                }),
                
            Actions\Action::make('cleanup_expired')
                ->label('Wyczyść wygasłe')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->modalHeading('Czyszczenie wygasłych pre-rejestracji')
                ->modalDescription('Usuń wygasłe pre-rejestracje starsze niż określona liczba dni.')
                ->form([
                    \Filament\Forms\Components\TextInput::make('days')
                        ->label('Usuń pre-rejestracje starsze niż (dni)')
                        ->numeric()
                        ->default(0)
                        ->minValue(0)
                        ->maxValue(30)
                        ->required()
                        ->helperText('0 = usuń od razu po wygaśnięciu, 1+ = usuń po X dniach od wygaśnięcia'),
                        
                    \Filament\Forms\Components\Toggle::make('dry_run')
                        ->label('Tryb testowy (pokaż co zostanie usunięte)')
                        ->default(true)
                        ->helperText('Zaznacz aby zobaczyć co zostanie usunięte bez faktycznego usuwania'),
                ])
                ->action(function (array $data) {
                    $days = (int) $data['days'];
                    $dryRun = $data['dry_run'];
                    
                    if ($days === 0) {
                        // Natychmiastowe usuwanie - usuń wszystkie wygasłe
                        $expiredPreRegistrations = \App\Models\PreRegistration::where('expires_at', '<', now())
                            ->where('used', false)
                            ->get();
                    } else {
                        // Usuwanie z opóźnieniem - usuń wygasłe starsze niż X dni
                        $cutoffDate = now()->subDays($days);
                        $expiredPreRegistrations = \App\Models\PreRegistration::where('expires_at', '<', $cutoffDate)
                            ->where('used', false)
                            ->get();
                    }
                        
                    $count = $expiredPreRegistrations->count();
                    
                    if ($count === 0) {
                        \Filament\Notifications\Notification::make()
                            ->title('Brak wygasłych pre-rejestracji')
                            ->body('Nie znaleziono wygasłych pre-rejestracji do usunięcia.')
                            ->info()
                            ->send();
                        return;
                    }
                    
                    if ($dryRun) {
                        $title = $days === 0 
                            ? "Tryb testowy - natychmiastowe usuwanie"
                            : "Tryb testowy - usuwanie z opóźnieniem";
                            
                        $message = $days === 0
                            ? "Znaleziono {$count} wygasłych pre-rejestracji do natychmiastowego usunięcia:\n\n"
                            : "Znaleziono {$count} wygasłych pre-rejestracji starszych niż {$days} dni do usunięcia:\n\n";
                            
                        foreach ($expiredPreRegistrations->take(10) as $preReg) {
                            $message .= "• ID {$preReg->id}: " . ($preReg->name ?: 'Brak imienia') . " - wygasł " . $preReg->expires_at->format('d.m.Y H:i') . "\n";
                        }
                        if ($count > 10) {
                            $message .= "... i " . ($count - 10) . " więcej\n";
                        }
                        $message .= "\nAby faktycznie usunąć, odznacz 'Tryb testowy' i uruchom ponownie.";
                        
                        \Filament\Notifications\Notification::make()
                            ->title($title)
                            ->body($message)
                            ->info()
                            ->persistent()
                            ->send();
                        return;
                    }
                    
                    // Faktyczne usuwanie
                    $deletedCount = 0;
                    foreach ($expiredPreRegistrations as $preReg) {
                        try {
                            $preReg->delete();
                            $deletedCount++;
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Błąd podczas usuwania')
                                ->body("Błąd przy usuwaniu pre-rejestracji ID {$preReg->id}: " . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }
                    
                    \Filament\Notifications\Notification::make()
                        ->title('Czyszczenie zakończone')
                        ->body("Pomyślnie usunięto {$deletedCount} wygasłych pre-rejestracji.")
                        ->success()
                        ->send();
                }),
            
            Actions\CreateAction::make(),
        ];
    }
    
    protected function getHeaderWidgets(): array
    {
        return [];
    }
    
    public function getHeader(): ?\Illuminate\Contracts\View\View
    {
        return view('filament.admin.resources.pre-registration-resource.pages.custom-header', [
            'actions' => $this->getCachedHeaderActions(),
            'heading' => $this->getHeading(),
            'subheading' => $this->getSubheading(),
        ]);
    }
}
