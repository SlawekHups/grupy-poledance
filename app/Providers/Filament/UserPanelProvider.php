<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Filament\Http\Middleware\Authenticate;

class UserPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('user')
            ->path('panel')
            ->authGuard('web')
            ->login()
            ->pages([])
            ->discoverResources(app_path('Filament/UserPanel/Resources'), 'App\\Filament\\UserPanel\\Resources')
            ->discoverPages(app_path('Filament/UserPanel/Pages'), 'App\\Filament\\UserPanel\\Pages')
            ->discoverWidgets(app_path('Filament/UserPanel/Widgets'), 'App\\Filament\\UserPanel\\Widgets')
            ->widgets([
                \App\Filament\UserPanel\Widgets\PaymentsStatsWidget::class,
            ])
            ->navigationGroups([
                'Moje konto',
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
            ])

            ->authMiddleware([
                Authenticate::class,
                \App\Http\Middleware\EnsureUserIsActive::class,
            ]);
    }
}
