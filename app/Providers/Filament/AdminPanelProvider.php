<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use App\Filament\Widgets\AlertasWidget;
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
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
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

            // ─── Layout — Top Navigation (mesmo padrão do lojista)
            ->topNavigation()
            ->breadcrumbs(true)
            ->maxContentWidth('full')

            // ─── Dark Mode ────────────────────────────────────────
            ->darkMode(true)

            // ─── Injetar Design System Elite no Admin ─────────────
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => Blade::render('@vite("resources/css/filament-elite.css")'),
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => Blade::render('filament.admin-bottom-nav'),
            )

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

                NavigationGroup::make('Comunicação')
                    ->label('Comunicação')
                    ->collapsible(),

                NavigationGroup::make('Configurações')
                    ->label('Configurações')
                    ->collapsible()
                    ->collapsed(),
            ])

            // ─── Resources e Pages ────────────────────────────────
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')

            // ─── Widgets (auto-discover) ──────────────────────────
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([])

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
