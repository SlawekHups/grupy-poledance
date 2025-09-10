<?php

namespace App\Filament\Admin\Resources\PreRegistrationResource\Pages;

use App\Filament\Admin\Resources\PreRegistrationResource;
use App\Filament\Admin\Resources\PreRegistrationResource\Widgets\PreRegistrationStatsWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

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
                
            Actions\CreateAction::make(),
        ];
    }
    
    protected function getHeaderWidgets(): array
    {
        return [
            PreRegistrationStatsWidget::class,
        ];
    }
}
