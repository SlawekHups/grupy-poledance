<?php

namespace App\Filament\UserPanel\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationLabel = 'Panel główny';
    protected static ?string $title = 'Panel użytkownika';
    protected static ?string $slug = 'dashboard';
    // Usunięto getWidgets()

    public function mount()
    {
        $user = auth()->user();
        if (
            $user->addresses()->count() === 0 ||
            is_null($user->rodo_accepted_at) ||
            is_null($user->terms_accepted_at)
        ) {
            return redirect()->route('filament.user.pages.onboarding');
        }
    }
} 