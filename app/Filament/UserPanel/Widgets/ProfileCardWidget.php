<?php

namespace App\Filament\UserPanel\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class ProfileCardWidget extends Widget
{
    protected static string $view = 'filament.user-panel.widgets.profile-card';

    protected static ?int $sort = -1; // Wyświetl na samej górze

    protected function getViewData(): array
    {
        $user = Auth::user();
        return [
            'user' => $user,
            'group' => $user?->group?->name,
        ];
    }
}
