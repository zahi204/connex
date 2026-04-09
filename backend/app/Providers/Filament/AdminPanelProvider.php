<?php

namespace App\Providers\Filament;

use App\Enums\UserRole;
use Filament\Enums\ThemeMode;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('Connex')
            ->colors([
                'primary' => Color::hex('#2563eb'),
                'gray' => Color::Slate,
            ])
            ->font('Rubik')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->defaultThemeMode(ThemeMode::Light)
            ->darkMode(false)
            ->sidebarCollapsibleOnDesktop()
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): HtmlString => new HtmlString(
                    // Light mode is the brand default — neutralise any legacy
                    // dark-mode preference saved in localStorage before the
                    // app paints so returning users are not stuck with a
                    // half-dark UI.
                    '<script>(function(){try{localStorage.removeItem("theme");'
                    .'document.documentElement.classList.remove("dark");}'
                    .'catch(e){}})();</script>'
                )
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
