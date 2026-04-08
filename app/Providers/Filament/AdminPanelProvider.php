<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use App\Filament\Widgets\RecentVehiclesWidget;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Pages\ConfiguracoesGerais;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
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

            // ─── Identidade Visual ─────────────────────────────────
            ->brandName('Elite Repasse')
            ->brandLogo(fn () => view('filament.logo'))
            ->darkModeBrandLogo(fn () => view('filament.logo'))
            ->favicon(asset('favicon.ico'))

            // ─── Tema e Cores ─────────────────────────────────────
            ->colors([
                'primary'   => Color::hex('#E97316'), // Laranja Elite
                'gray'      => Color::Zinc,
                'info'      => Color::Sky,
                'success'   => Color::Emerald,
                'warning'   => Color::Amber,
                'danger'    => Color::Rose,
            ])
            ->font('Inter', 'https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900&display=swap')

            // ─── Layout ─────────────────────────────────────────────
            ->sidebarCollapsibleOnDesktop()
            ->topNavigation(false)
            ->breadcrumbs(true)

            // ─── Dark Mode ────────────────────────────────────────
            ->darkMode(true)

            // ─── Navegação Agrupada ───────────────────────────────
            ->navigationGroups([
                NavigationGroup::make('Estoque')
                    ->label('Gestão de Estoque')
                    ->collapsible(),

                NavigationGroup::make('Comercial')
                    ->label('Comercial e Vendas')
                    ->collapsible(),

                NavigationGroup::make('Clientes')
                    ->label('Clientes')
                    ->collapsible(),

                NavigationGroup::make('Configurações')
                    ->label('Configurações')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsible()
                    ->collapsed(),
            ])

            // ─── Resources e Pages ────────────────────────────────
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([Dashboard::class])

            // ─── Widgets do Dashboard ─────────────────────────────
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                StatsOverview::class,
                RecentVehiclesWidget::class,
            ])

            // ─── Middleware ────────────────────────────────────────
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
