<?php

namespace App\Filament\User\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Illuminate\Support\Facades\Auth;

class AcceptTerms extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $title = 'Akceptacja regulaminu';
    protected static string $view = 'filament.pages.page';
    protected static ?string $navigationIcon = null;
    protected static ?string $panel = 'user';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function mount(): void
    {
        $this->form->fill(['agree' => false]);
    }

    public function submit(): void
    {
        if (!($this->form->getState()['agree'] ?? false)) {
            $this->addError('agree', 'Musisz zaakceptować regulamin.');
            return;
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->forceFill([
            'accepted_terms_at' => now(),
        ])->save();

        $this->redirect('/panel');
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Checkbox::make('agree')
                ->label('Akceptuję regulamin')
                ->required(),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            Forms\Components\Actions\Action::make('submit')
                ->label('Zatwierdź')
                ->submit('submit'),
        ];
    }
} 