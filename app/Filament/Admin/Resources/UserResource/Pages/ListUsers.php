<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Events\UserInvited;
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
                                ->default(false),
                        ]),
                ])
                ->after(function ($record) {
                    // Wyślij zaproszenie do użytkownika
                    UserInvited::dispatch($record);
                }),
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
        // Ustaw limity czasu dla importu
        set_time_limit(120);
        ini_set('max_execution_time', 120);
        ini_set('memory_limit', '256M');
        
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
            
            // Debug - sprawdź nagłówek
            \Illuminate\Support\Facades\Log::info('CSV Header:', ['header' => $header]);
            
            while (($row = fgetcsv($handle)) !== false) {
                // Debug - sprawdź każdy wiersz
                \Illuminate\Support\Facades\Log::info('CSV Row:', ['row' => $row, 'header_count' => count($header), 'row_count' => count($row)]);
                
                // Sprawdź czy liczba kolumn odpowiada liczbie wartości
                if (count($header) !== count($row)) {
                    \Illuminate\Support\Facades\Log::info('Skipping row - column count mismatch', ['header_count' => count($header), 'row_count' => count($row)]);
                    $skipped++;
                    continue;
                }
                
                // Sprawdź czy wiersz nie jest pusty
                if (empty(array_filter($row))) {
                    \Illuminate\Support\Facades\Log::info('Skipping empty row');
                    $skipped++;
                    continue;
                }
                
                try {
                    $rowData = array_combine($header, $row);
                    \Illuminate\Support\Facades\Log::info('Row data created:', ['rowData' => $rowData]);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Array combine error:', ['error' => $e->getMessage(), 'header' => $header, 'row' => $row]);
                    $skipped++;
                    continue;
                }
                
                // Nie importuj adminów
                if (isset($rowData['role']) && $rowData['role'] === 'admin') {
                    \Illuminate\Support\Facades\Log::info('Skipping admin user:', ['email' => $rowData['email'] ?? 'no email']);
                    $skipped++;
                    continue;
                }
                
                // Sprawdź czy email jest wymagany
                if (empty($rowData['email'])) {
                    \Illuminate\Support\Facades\Log::info('Skipping row - empty email');
                    $skipped++;
                    continue;
                }
                
                // Sprawdź czy nazwa jest wymagana
                if (empty($rowData['name'])) {
                    \Illuminate\Support\Facades\Log::info('Skipping row - empty name', ['email' => $rowData['email']]);
                    $skipped++;
                    continue;
                }
                
                \Illuminate\Support\Facades\Log::info('Processing user:', ['email' => $rowData['email'], 'name' => $rowData['name']]);
                
                // Ustaw domyślne wartości tylko dla pustych pól
                $rowData['phone'] = $rowData['phone'] ?? '';
                $rowData['group_id'] = !empty($rowData['group_id']) ? (int)$rowData['group_id'] : null;
                
                // Zachowaj oryginalną wartość amount z CSV (nie konwertuj na int)
                if (empty($rowData['amount'])) {
                    $rowData['amount'] = 200; // domyślna wartość tylko jeśli puste
                } else {
                    $rowData['amount'] = (float)$rowData['amount']; // konwertuj na float aby zachować grosze
                }
                
                $rowData['joined_at'] = !empty($rowData['joined_at']) ? $rowData['joined_at'] : now()->format('Y-m-d');
                
                // Zachowaj oryginalną wartość is_active z CSV
                if (empty($rowData['is_active'])) {
                    $rowData['is_active'] = 0; // domyślna wartość tylko jeśli puste
                } else {
                    $rowData['is_active'] = (bool)(int)$rowData['is_active']; // konwertuj na boolean
                }
                
                \Illuminate\Support\Facades\Log::info('Final row data:', ['rowData' => $rowData]);
                
                $user = \App\Models\User::where('email', $rowData['email'])->first();
                
                if ($user) {
                    // Nie nadpisuj admina
                    if ($user->role === 'admin') {
                        $skipped++;
                        continue;
                    }
                    
                    if ($action === 'update') {
                        try {
                            $user->update(collect($rowData)->except(['password', 'role'])->toArray());
                            $updated++;
                        } catch (\Exception $e) {
                            $skipped++;
                            \Illuminate\Support\Facades\Log::error('Update error for user ' . $rowData['email'] . ': ' . $e->getMessage());
                        }
                    } else {
                        $skipped++;
                    }
                } else {
                    try {
                        // Nie generuj hasła - użytkownik ustawi je przez zaproszenie
                        $userData = collect($rowData)->except(['id', 'role', 'password'])->toArray();
                        \Illuminate\Support\Facades\Log::info('Creating user with data:', ['userData' => $userData]);
                        
                        // Sprawdź czy wszystkie wymagane pola są obecne
                        if (empty($userData['name']) || empty($userData['email'])) {
                            \Illuminate\Support\Facades\Log::error('Missing required fields:', ['userData' => $userData]);
                            $skipped++;
                            continue;
                        }
                        
                        $newUser = \App\Models\User::create($userData);
                        \Illuminate\Support\Facades\Log::info('User created successfully:', ['user_id' => $newUser->id, 'email' => $newUser->email]);
                        
                        // Wyślij zaproszenie do nowego użytkownika
                        UserInvited::dispatch($newUser);
                        
                        $added++;
                    } catch (\Exception $e) {
                        $skipped++;
                        \Illuminate\Support\Facades\Log::error('Create error for user ' . $rowData['email'] . ': ' . $e->getMessage(), [
                            'exception' => $e,
                            'rowData' => $rowData,
                            'trace' => $e->getTraceAsString()
                        ]);
                    }
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
