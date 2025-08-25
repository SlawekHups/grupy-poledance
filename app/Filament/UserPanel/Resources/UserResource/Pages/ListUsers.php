<?php

namespace App\Filament\UserPanel\Resources\UserResource\Pages;

use App\Filament\UserPanel\Resources\UserResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    public function mount(): void
    {
        parent::mount();

        $user = Auth::user();
        if ($user) {
            $this->redirect(route('filament.user.resources.users.edit', ['record' => $user->id]));
        }
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
