<?php

namespace App\Filament\Admin\Pages;

use Filament\Forms;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Validation\Rules\Password as PasswordRule;

class AdminSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'Ustawienia';
    protected static ?string $title = 'Ustawienia administratora';
    protected static string $view = 'filament.admin.pages.admin-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $user = User::findOrFail(Auth::id());
        $this->form->fill([
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Dane konta')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Imię i nazwisko')
                            ->required(),
                        Forms\Components\TextInput::make('email')
                            ->label('E-mail')
                            ->email()
                            ->required(),
                        Forms\Components\TextInput::make('phone')
                            ->label('Telefon')
                            ->tel(),
                    ])->columns(2),

                Forms\Components\Section::make('Zmiana hasła')
                    ->schema([
                        Forms\Components\TextInput::make('current_password')
                            ->label('Obecne hasło')
                            ->password(),
                        Forms\Components\TextInput::make('new_password')
                            ->label('Nowe hasło')
                            ->password()
                            ->rule(PasswordRule::min(8))
                            ->same('new_password_confirmation')
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('new_password_confirmation')
                            ->label('Potwierdź nowe hasło')
                            ->password()
                            ->dehydrated(false),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $user = User::findOrFail(Auth::id());
        $data = $this->form->getState();

        $validated = validator($data, [
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users,email,' . $user->id],
            'phone' => ['nullable','string','max:30'],
            'current_password' => ['nullable','string'],
            'new_password' => ['nullable','string', PasswordRule::min(8)],
        ])->validate();

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ]);

        if (!empty($data['new_password'])) {
            if (empty($data['current_password']) || !Hash::check($data['current_password'], $user->password)) {
                \Filament\Notifications\Notification::make()
                    ->title('Błędne obecne hasło')
                    ->danger()
                    ->send();
                return;
            }
            $user->password = $data['new_password'];
            $user->save();
            Log::info('Zmiana hasła administratora', [
                'user_id' => $user->id,
                'by' => Auth::id(),
            ]);
        }

        \Filament\Notifications\Notification::make()
            ->title('Zapisano ustawienia')
            ->success()
            ->send();
    }
}


