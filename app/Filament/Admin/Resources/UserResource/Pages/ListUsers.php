<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Admin\Resources\UserResource\Widgets\UserStats;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\User;
use Livewire\WithFileUploads;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class ListUsers extends ListRecords
{
    use WithFileUploads;
    
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modal()
                ->steps([
                    \Filament\Forms\Components\Wizard\Step::make('Podstawowe dane')
                        ->description('Wprowadź podstawowe dane użytkownika')
                        ->schema([
                            \Filament\Forms\Components\TextInput::make('name')
                                ->label('Imię i nazwisko')
                                ->required()
                                ->minLength(3)
                                ->maxLength(45),
                            \Filament\Forms\Components\TextInput::make('email')
                                ->label('E-mail')
                                ->required()
                                ->email()
                                ->unique('users', 'email'),
                            \Filament\Forms\Components\TextInput::make('password')
                                ->label('Hasło')
                                ->password()
                                ->required()
                                ->minLength(8)
                                ->dehydrateStateUsing(fn($state) => bcrypt($state)),
                        ])
                        ->columns(2),
                    \Filament\Forms\Components\Wizard\Step::make('Dane kontaktowe')
                        ->description('Dodaj informacje kontaktowe')
                        ->schema([
                            \Filament\Forms\Components\TextInput::make('phone')
                                ->label('Telefon')
                                ->tel()
                                ->minLength(9)
                                ->maxLength(15)
                                ->dehydrateStateUsing(function ($state) {
                                    $number = preg_replace('/\\D/', '', $state);
                                    if (strlen($number) === 9) {
                                        return '+48' . $number;
                                    }
                                    return $state;
                                }),
                            \Filament\Forms\Components\TextInput::make('address')
                                ->label('Adres'),
                            \Filament\Forms\Components\DatePicker::make('joined_at')
                                ->label('Data zapisu')
                                ->default(now()),
                        ]),
                    \Filament\Forms\Components\Wizard\Step::make('Grupa')
                        ->description('Wybierz grupę dla użytkownika')
                        ->schema([
                            \Filament\Forms\Components\Select::make('group_id')
                                ->label('Grupa')
                                ->relationship('group', 'name')
                                ->searchable()
                                ->preload()
                                ->createOptionForm([
                                    \Filament\Forms\Components\TextInput::make('name')
                                        ->label('Nazwa grupy')
                                        ->required(),
                                    \Filament\Forms\Components\TextInput::make('description')
                                        ->label('Opis')
                                        ->nullable(),
                                ]),
                        ]),
                    \Filament\Forms\Components\Wizard\Step::make('Ustawienia')
                        ->description('Skonfiguruj dodatkowe opcje')
                        ->schema([
                            \Filament\Forms\Components\Toggle::make('is_active')
                                ->label('Użytkownik aktywny')
                                ->default(true),
                        ]),
                ]),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            UserStats::class,
        ];
    }

    public function exportUsers()
    {
        $filename = 'users_export_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $users = \App\Models\User::where('role', 'user')->select(['name', 'email', 'phone', 'group_id', 'amount', 'joined_at', 'is_active'])->get();

        $callback = function() use ($users) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['name', 'email', 'phone', 'group_id', 'amount', 'joined_at', 'is_active']);
            foreach ($users as $user) {
                fputcsv($handle, [
                    $user->name,
                    $user->email,
                    $user->phone,
                    $user->group_id,
                    $user->amount,
                    $user->joined_at,
                    $user->is_active ? 1 : 0,
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function importUsers($data = [])
    {
        if (empty($data)) {
            $data = $this->data ?? [];
        }
        
        // Debug - sprawdź co jest w danych
        Log::info('Import data:', $data);
        Log::info('All properties:', get_object_vars($this));
        
        if (!isset($data['file'])) {
            Notification::make()
                ->title('Błąd')
                ->body('Wybierz plik CSV. Otrzymane dane: ' . json_encode($data))
                ->danger()
                ->send();
            return;
        }

        $file = $data['file'];
        $action = $data['duplicateAction'] ?? 'skip';
        $added = $updated = $skipped = 0;
        
        try {
            $handle = fopen($file->getRealPath(), 'r');
            $header = fgetcsv($handle);
            
            while (($row = fgetcsv($handle)) !== false) {
                $data = array_combine($header, $row);
                
                // Nie importuj adminów
                if (isset($data['role']) && $data['role'] === 'admin') {
                    $skipped++;
                    continue;
                }
                
                $user = \App\Models\User::where('email', $data['email'])->first();
                
                if ($user) {
                    // Nie nadpisuj admina
                    if ($user->role === 'admin') {
                        $skipped++;
                        continue;
                    }
                    
                    if ($action === 'update') {
                        $user->update(collect($data)->except(['password', 'role'])->toArray());
                        $updated++;
                    } else {
                        $skipped++;
                    }
                } else {
                    $data['password'] = \Illuminate\Support\Facades\Hash::make(str()->random(12));
                    \App\Models\User::create(collect($data)->except(['id', 'role'])->toArray());
                    $added++;
                }
            }
            
            fclose($handle);
            Notification::make()
                ->title('Import zakończony')
                ->body("Dodano: $added, Zaktualizowano: $updated, Pominięto: $skipped")
                ->success()
                ->send();
            
        } catch (\Exception $e) {
            Notification::make()
                ->title('Błąd podczas importu')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
