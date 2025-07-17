<?php

namespace App\Filament\UserPanel\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Models\Term;

class TermsAcceptancePage extends Page
{
    protected static ?string $navigationLabel = null;
    protected static ?string $title = 'Akceptacja regulaminu';
    protected static ?string $slug = 'terms';
    protected static string $view = 'filament.user-panel.pages.terms-acceptance';

    public $accept = false;

    public function acceptTerms()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user && is_null($user->terms_accepted_at)) {
            $user->terms_accepted_at = Carbon::now();
            $user->save();
        }
        return redirect()->route('filament.user.pages.dashboard');
    }

    public function getViewData(): array
    {
        $terms = \App\Models\Term::where('active', true)->orderBy('id', 'asc')->get();
        return [
            'terms' => $terms,
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
} 