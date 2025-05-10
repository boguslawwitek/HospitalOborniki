<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
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
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use Composer\InstalledVersions;
use Illuminate\Support\Facades\Auth;
use Filament\FontProviders\GoogleFontProvider;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Indigo,
            ])
            ->brandLogo(fn () => view('filament.admin.logo'))
            ->brandLogoHeight('2rem')
            ->font('Open Sans', provider: GoogleFontProvider::class)
            ->favicon(asset('/storage/favicon/favicon-32x32.png'))
            ->breadcrumbs(false)
            ->collapsibleNavigationGroups(false)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                // Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->renderHook(
                PanelsRenderHook::TOPBAR_START,
                fn() => Blade::render(Auth::check() ? '<footer class="w-full flex flex-col items-center justify-center"><div class="text-sm text-gray-500 text-center dark:text-gray-400">Wykonane przez <a href="https://bwitek.dev" target="_blank" class="font-medium">Bogusław Witek</a>. Utrzymywane przez <a href="https://e-tmk.com" target="_blank" class="font-medium">TMK</a>.</div><div class="text-sm text-gray-500 text-center dark:text-gray-400">© <?php echo date("Y"); ?> <a href="https://szpital.oborniki.info" target="_blank" class="font-medium">SzpitalOborniki</a>. Wszelkie prawa zastrzeżone.</div></footer>' : null)
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn() => Blade::render(!Auth::check() ? '<footer class="fixed bottom-0 left-0 right-0 text-center w-full bg-white border-t border-gray-200 shadow flex flex-col items-center justify-center dark:bg-gray-800 dark:border-gray-600"><div class="mt-2 text-sm text-gray-500 text-center dark:text-gray-400">© <?php echo date("Y"); ?> <a href="https://szpital.oborniki.info" target="_blank" class="font-medium">SzpitalOborniki</a>. Wszelkie prawa zastrzeżone.</div><div class="mb-2 text-sm text-gray-500 text-center dark:text-gray-400">Wykonane przez <a href="https://bwitek.dev" target="_blank" class="font-medium">Bogusław Witek</a>. Utrzymywane przez <a href="https://e-tmk.com" target="_blank" class="font-medium">TMK</a>.</div></footer>' : null)
            )
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
            ]);
    }
}
