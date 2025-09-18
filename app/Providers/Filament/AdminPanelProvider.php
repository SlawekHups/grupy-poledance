<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Models\User;
use Filament\View\PanelsRenderHook;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->authGuard('web')
            ->favicon(asset('favicon.ico'))
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->pages([
                Pages\Dashboard::class,
                \App\Filament\Admin\Pages\AdminSettings::class,
            ])
            ->widgets([
                \App\Filament\Admin\Widgets\CalendarWidget::class,
            ])
            ->profile()
            ->userMenuItems([
                'profile' => \Filament\Navigation\UserMenuItem::make()
                    ->label('Ustawienia konta')
                    ->url(fn (): string => route('filament.admin.auth.profile'))
                    ->icon('heroicon-o-cog-6-tooth'),
                'admin-settings' => \Filament\Navigation\UserMenuItem::make()
                    ->label('Ustawienia administratora')
                    ->url(fn (): string => route('filament.admin.pages.admin-settings'))
                    ->icon('heroicon-o-shield-check'),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                \App\Http\Middleware\EnsureIsAdmin::class,
            ])
            ->navigationGroups([
                'Użytkownicy i Grupy',
                'Finanse',
                'Zajęcia',
                'Ustawienia',
            ])
            ->renderHook(PanelsRenderHook::BODY_START, fn (): string => view('filament.admin.mobile-top-nav')->render());
    }
}
