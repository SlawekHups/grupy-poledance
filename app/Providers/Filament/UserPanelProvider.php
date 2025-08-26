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
            ->profile()
            ->homeUrl(fn (): string => url('/panel/account'))
            ->pages([])
            ->discoverResources(app_path('Filament/UserPanel/Resources'), 'App\\Filament\\UserPanel\\Resources')
            ->discoverPages(app_path('Filament/UserPanel/Pages'), 'App\\Filament\\UserPanel\\Pages')
            ->discoverWidgets(app_path('Filament/UserPanel/Widgets'), 'App\\Filament\\UserPanel\\Widgets')
            ->widgets([
                \App\Filament\UserPanel\Widgets\AttendanceStatsWidget::class,
                \App\Filament\UserPanel\Widgets\PaymentsStatsWidget::class,
                \App\Filament\UserPanel\Widgets\ProfileCardWidget::class,
            ])
            ->navigationGroups([])
            ->userMenuItems([
                'profile' => \Filament\Navigation\UserMenuItem::make()
                    ->label('Profil')
                    ->url(fn (): string => route('filament.user.auth.profile'))
                    ->icon('heroicon-o-user'),
            ])
            ->defaultAvatarProvider(\Filament\AvatarProviders\UiAvatarsProvider::class)
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
                \App\Http\Middleware\EnsureUserAcceptedTerms::class,
                \App\Http\Middleware\EnsureIsUser::class,
                \App\Http\Middleware\EnsureProfileCompleted::class,
            ]);
    }
}
